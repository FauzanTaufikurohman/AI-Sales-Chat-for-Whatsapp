<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $bot = Auth::user()->whatsappBot;
        $conversations = collect();

        if ($bot) {
            $conversations = $bot->conversations()->with('summary')->latest('last_message_at')->get();
        }

        return view('dashboard.index', [
            'bot' => $bot,
            'conversations' => $conversations,
        ]);

    }

    public function conversation(Conversation $conversation)
    {
        $user = Auth::user();

        abort_unless($conversation->whatsappBot && $conversation->whatsappBot->user_id === $user->id, 403);

        $conversation->load([
            'messages',
            'summary',
        ]);

        return view('dashboard.conversation', compact('conversation'));

    }
}
