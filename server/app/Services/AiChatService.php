<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Http;

class AiChatService
{
    public function reply(
        Conversation $conversation,
        string $message
    ): string {
        $apiKey = config('services.openai.key');

        if (! $apiKey) {
            return 'Maaf, AI belum dikonfigurasi.';
        }

        $conversation->load('messages');

        $messages = [];

        $prompt = $conversation->whatsappBot->system_prompt;

        $messages[] = [
            'role' => 'system',
            'content' => $prompt ?: 'Anda adaah customer service yang membantu.',
        ];

        foreach ($conversation->messages()->latest()->limit(20)->get()->reverse() as $item) {
            $messages[] = [
                'role' => $item->sender_type === 'customer'
                    ? 'user' : 'assistant',
                'content' => $item->message,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        $response = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config(
                    'services.openai.model',
                    'gpt-4o-mini'
                ),
                'messages' => $messages,
            ]);
        if ($response->failed()) {
            return 'Maaf, saya sedang mengalami gangguan. Silakan coba lagi.';
        }

        return $response->json(
            'choices.0.message.content',
            'Maaf, saya tidak dapat menjawab saat ini.'
        );
    }
}
