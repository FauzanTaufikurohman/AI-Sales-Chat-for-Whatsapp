<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappBot;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppWebhookController extends Controller
{
    public function handle(
        Request $request,
        AiChatService $ai
    ) {

        $phone = $request->input('sender');
        $messageText = $request->input('message');

        if (! $phone || ! $messageText) {
            return response()->json([
                'message' => 'Invalid webhook',
            ], 422);
        }

        $bot = WhatsappBot::query()
            ->where('phone_number', $request->input('target'))
            ->where('is_active', true)
            ->first();

        if (! $bot) {
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

        $token = $bot->provider_token
            ?: config('services.fonnte.token');

        if ($token) {
            Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $reply,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
