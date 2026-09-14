<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

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

    public function reply(Request $request, Conversation $conversation, FonnteService $fonnte)
    {
        $user = $request->user();

        abort_unless($conversation->whatsappBot?->user_id === $user->id, 403);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:4096', 'required_without:image'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);
        $bot = $conversation->whatsappBot;

        try {
            $fonnte->sendMessage(
                $bot,
                $conversation->customer_phone,
                $validated['message'] ?? '',
                $request->file('image')
            );
        } catch (RuntimeException $exception) {
            return back()->withErrors(['reply' => $exception->getMessage()])->withInput();
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'human',
            'message' => $validated['message'] ?? '[Gambar]',
            'message_type' => $request->hasFile('image') ? 'image' : 'text',
            'sent_at' => now(),
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
