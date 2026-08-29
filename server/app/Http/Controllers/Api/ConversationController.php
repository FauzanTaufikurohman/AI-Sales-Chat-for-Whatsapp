<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeConversationWithAI;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ids = $request->user()->whatsappAccounts()->pluck('id');
        $conversations = Conversation::query()->whereIn('whatsapp_account_id', $ids)->with(['customer', 'messages' => fn ($query) => $query->latest()->limit(1)])->latest('last_message_at')->paginate(20);

        return response()->json($conversations);
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        return response()->json($conversation->load(['customer', 'messages', 'aiSummaries']));
    }

    public function update(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);
        $conversation->update($request->validate(['status' => ['sometimes', 'in:active,waiting,human_required,closed'], 'priority' => ['sometimes', 'in:low,normal,high,urgent']]));

        return response()->json($conversation->fresh());
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        return response()->json($conversation->messages()->latest()->paginate(50));
    }

    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);
        $message = $conversation->messages()->create([
            'uuid' => (string) Str::uuid(), 'sender_type' => 'sales', 'message_type' => 'text',
            'content' => $request->validate(['content' => ['required', 'string', 'max:4000']])['content'],
        ]);

        return response()->json($message, 201);
    }

    public function reprocess(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);
        $conversation->update(['ai_status' => 'pending']);
        AnalyzeConversationWithAI::dispatch($conversation->id);

        return response()->json(['message' => 'AI analysis queued.']);
    }

    private function authorizeConversation(Request $request, Conversation $conversation): void
    {
        abort_unless($request->user()->whatsappAccounts()->whereKey($conversation->whatsapp_account_id)->exists(), 403);
    }
}
