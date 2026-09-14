<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationSummary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        $summary = $conversation->summary;

        $messages[] = [
            'role' => 'system',
            'content' => ($prompt ?: 'Anda adalah customer service yang membantu.')."\n\n".$this->intakeInstruction($summary?->summary),
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

        try {
            $response = Http::withToken($apiKey)
                ->timeout(45)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config(
                        'services.openai.model',
                        'gpt-4o-mini'
                    ),
                    'messages' => $messages,
                ]);
        } catch (Throwable $exception) {
            Log::error('OpenAI reply exception', [
                'message' => $exception->getMessage(),
            ]);

            return 'Maaf, saya sedang mengalami gangguan. Silakan coba lagi.';
        }
        if ($response->failed()) {
            Log::error('OpenAI reply failed', [
                'status' => $response->status(),
                'body' => $response->json('error.message'),
            ]);

            return 'Maaf, saya sedang mengalami gangguan. Silakan coba lagi.';
        }

        return $response->json(
            'choices.0.message.content',
            'Maaf, saya tidak dapat menjawab saat ini.'
        );
    }

    private function intakeInstruction(?string $previousSummary): string
    {
        $requiredInformation = implode(', ', config('services.openai.required_information', []));
        $summary = $previousSummary ?: 'Belum ada informasi yang terkumpul.';

        return "MODE PENGUMPULAN INFORMASI:\n"
            ."- Informasi wajib: {$requiredInformation}.\n"
            ."- Gunakan percakapan dan ringkasan sebelumnya untuk mengetahui informasi yang sudah ada.\n"
            ."- Jika masih ada informasi wajib yang belum jelas, tanyakan tepat SATU pertanyaan paling penting.\n"
            ."- Jangan menanyakan informasi yang sudah dijawab customer.\n"
            ."- Jawab singkat, maksimal 2 kalimat, tanpa daftar panjang atau penjelasan berulang.\n"
            ."- Jangan menanyakan beberapa hal sekaligus.\n"
            ."- Setelah semua informasi wajib lengkap, berhenti bertanya dan berikan konfirmasi singkat.\n"
            ."Ringkasan sebelumnya: {$summary}";
    }

    public function summarize(Conversation $conversation): void
    {
        $apiKey = config('services.openai.key');

        if (! $apiKey) {
            Log::warning('OpenAI summary skipped: API key missing');

            return;
        }

        $conversation->load('messages');
        $transcript = $conversation->messages
            ->sortBy('created_at')
            ->take(-40)
            ->map(fn ($message) => strtoupper($message->sender_type).': '.$message->message)
            ->implode("\n");

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Buat ringkasan percakapan customer dalam JSON dengan keys: summary, important_information (array), intent, purchase_intent (boolean), lead_status. Summary harus menyebutkan informasi wajib yang sudah terkumpul dan yang masih kurang. Jawab dalam bahasa Indonesia.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $transcript,
                        ],
                    ],
                ]);
        } catch (Throwable $exception) {
            Log::error('OpenAI summary exception', [
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if ($response->failed()) {
            Log::error('OpenAI summary failed', [
                'status' => $response->status(),
                'body' => $response->json('error.message'),
            ]);

            return;
        }

        $result = json_decode(
            $response->json('choices.0.message.content', '{}'),
            true
        );

        if (! is_array($result) || ! isset($result['summary'])) {
            Log::warning('OpenAI summary returned invalid JSON');

            return;
        }

        try {
            ConversationSummary::updateOrCreate(
                ['conversation_id' => $conversation->id],
                [
                    'summary' => (string) $result['summary'],
                    'important_information' => is_array($result['important_information'] ?? null)
                        ? $result['important_information'] : [],
                    'intent' => $result['intent'] ?? null,
                    'purchase_intent' => (bool) ($result['purchase_intent'] ?? false),
                    'lead_status' => $result['lead_status'] ?? null,
                    'generated_at' => now(),
                ]
            );
        } catch (Throwable $exception) {
            Log::error('Conversation summary persistence failed', [
                'conversation_id' => $conversation->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
