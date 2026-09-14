<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappBot;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function handle(
        Request $request,
        AiChatService $ai
    ) {

        $phone = $this->normalizePhone((string) $request->input('sender'));
        $messageText = $request->input('message');
        $target = $this->normalizePhone((string) ($request->input('target') ?: $request->input('device')));

        Log::info('Fonnte webhook received', [
            'sender' => $phone,
            'target' => $target,
            'has_message' => filled($messageText),
            'payload_keys' => array_keys($request->all()),
        ]);

        if (! $phone || ! $messageText || ! $target) {
            return response()->json([
                'message' => 'Invalid webhook',
            ], 422);
        }

        $bot = WhatsappBot::query()
            ->where('is_active', true)
            ->where(function ($query) use ($target) {
                $query
                    ->whereIn('phone_number', $this->phoneVariants($target))
                    ->orWhereIn('fonnte_device', $this->phoneVariants($target));
            })
            ->first();

        if (! $bot) {
            Log::warning('Fonnte webhook bot not found', ['target' => $target]);

            return response()->json([
                'message' => 'Bot not found',
            ], 404);
        }

        $conversation = Conversation::firstOrCreate(
            [
                'bot_id' => $bot->id,
                'customer_phone' => $phone,
            ],
            [
                'customer_name' => $request->input('name'),
                'status' => 'active',
            ]
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'message' => $messageText,
            'message_type' => 'text',
            'provider_message_id' => $request->input('message_id'),
            'sent_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        $reply = $ai->reply(
            $conversation,
            $messageText
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'bot',
            'message' => $reply,
            'message_type' => 'text',
            'is_ai_processed' => true,
            'sent_at' => now(),
        ]);

        $ai->summarize($conversation->fresh());

        $token = $bot->fonnte_token;

        if ($token) {
            $response = Http::timeout(20)->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $reply,
            ]);

            Log::info('Fonnte AI reply response', [
                'bot_id' => $bot->id,
                'target' => $phone,
                'status' => $response->json('status'),
                'reason' => $response->json('reason'),
            ]);
        } else {
            Log::error('Fonnte device token missing', ['bot_id' => $bot->id]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62'.$phone;
        }

        return $phone;
    }

    private function phoneVariants(string $phone): array
    {
        $variants = [$phone];

        if (str_starts_with($phone, '62')) {
            $variants[] = '0'.substr($phone, 2);
        }

        return array_values(array_unique($variants));
    }
}
