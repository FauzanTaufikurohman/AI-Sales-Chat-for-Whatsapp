<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeConversationWithAI;
use App\Models\Customer;
use App\Models\Message;
use App\Models\WhatsAppAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        abort_unless(hash_equals((string) config('services.whatsapp.verify_token'), (string) $request->query('hub_verify_token')), 403);

        return response()->json((string) $request->query('hub_challenge'));
    }

    public function receive(Request $request): JsonResponse
    {
        $payload = $request->validate(['entry' => ['array'], 'entry.*.changes' => ['array']]);
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $metadata = $value['metadata'] ?? [];
                $account = WhatsAppAccount::query()->where('phone_number_id', $metadata['phone_number_id'] ?? null)->first();
                if (! $account) {
                    continue;
                }
                foreach ($value['messages'] ?? [] as $incoming) {
                    if (! empty($incoming['id']) && Message::where('external_message_id', $incoming['id'])->exists()) {
                        continue;
                    }
                    $customer = Customer::firstOrCreate(
                        ['whatsapp_account_id' => $account->id, 'phone_number' => $incoming['from'] ?? 'unknown'],
                        ['uuid' => (string) Str::uuid(), 'status' => 'active'],
                    );
                    $conversation = $account->conversations()->firstOrCreate(
                        ['customer_id' => $customer->id, 'status' => 'active'],
                        ['uuid' => (string) Str::uuid(), 'last_message_at' => now()],
                    );
                    $message = $conversation->messages()->create([
                        'uuid' => (string) Str::uuid(), 'external_message_id' => $incoming['id'] ?? null,
                        'sender_type' => 'customer', 'message_type' => $incoming['type'] ?? 'text',
                        'content' => $incoming['text']['body'] ?? null,
                    ]);
                    $conversation->update(['last_message_at' => now(), 'ai_status' => 'pending', 'status' => 'waiting']);
                    AnalyzeConversationWithAI::dispatch($conversation->id);
                }
            }
        }

        return response()->json(['received' => true]);
    }
}
