<?php

namespace App\Jobs;

use App\Models\Conversation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeConversationWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $conversationId) {}

    public function backoff(): array
    {
        return [5, 15, 60];
    }

    public function handle(): void
    {
        $conversation = Conversation::with(['messages' => fn ($query) => $query->latest()->limit(20)])->find($this->conversationId);
        if (! $conversation) {
            return;
        }
        $latest = $conversation->messages->firstWhere('sender_type', 'customer');
        $content = strtolower((string) ($latest?->content ?? ''));
        $intent = str_contains($content, 'harga') ? 'pricing' : (str_contains($content, 'video') ? 'video_request' : 'general_inquiry');
        $humanRequired = $intent !== 'general_inquiry' || str_contains($content, 'sales');
        $conversation->update([
            'ai_status' => 'completed', 'ai_intent' => $intent, 'ai_sentiment' => 'neutral',
            'priority' => $humanRequired ? 'high' : 'normal', 'status' => $humanRequired ? 'human_required' : 'active',
            'ai_summary' => $latest?->content ? 'Customer: '.$latest->content : null,
            'ai_required_action' => $humanRequired ? 'Sales perlu menindaklanjuti permintaan customer.' : null,
        ]);
    }
}
