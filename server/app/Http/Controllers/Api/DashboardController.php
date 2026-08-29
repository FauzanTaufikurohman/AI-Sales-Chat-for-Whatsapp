<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accountIds = $request->user()->whatsappAccounts()->pluck('id');
        $query = Conversation::query()->whereIn('whatsapp_account_id', $accountIds);

        return response()->json([
            'stats' => [
                'total' => (clone $query)->count(),
                'unread' => (clone $query)->where('status', 'waiting')->count(),
                'human_required' => (clone $query)->where('status', 'human_required')->count(),
                'urgent' => (clone $query)->where('priority', 'urgent')->count(),
                'closed' => (clone $query)->where('status', 'closed')->count(),
            ],
            'recent_conversations' => $query->with('customer')->latest('last_message_at')->limit(20)->get(),
        ]);
    }
}
