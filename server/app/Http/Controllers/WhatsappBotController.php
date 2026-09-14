<?php

namespace App\Http\Controllers;

use App\Models\WhatsappBot;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use RuntimeException;

class WhatsappBotController extends Controller
{
    public function connect(Request $request, FonnteService $fonnte)
    {
        $user = $request->user();

        $bot = WhatsappBot::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'phone_number' => $user->phone_number,
                'provider' => 'fonnte',
                'name' => $user->name.' AI',
                'system_prompt' => 'Anda adalah customer service yang ramah, membantu, singkat, dan profesional.',
                'is_active' => false,
                'connection_status' => 'disconnected',
            ]
        );

        if ($bot->fonnte_device) {
            try {
                $fonnte->configureWebhook($bot);
            } catch (RuntimeException $exception) {
                return back()->withErrors(['whatsapp' => $exception->getMessage()]);
            }

            return redirect()
                ->route('dashboard')
                ->with('success', 'Device WhatsApp sudah dibuat.');
        }

        try {
            $fonnte->createDevice($bot);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['whatsapp' => $exception->getMessage()]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Device berhasil dibuat. Silakan hubungkan WhatsApp.');
    }

    public function qr(Request $request, FonnteService $fonnte)
    {
        $bot = $request->user()->whatsappBot;

        abort_unless($bot, 404);

        try {
            $fonnte->configureWebhook($bot);
            $data = $fonnte->qr($bot);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        $qr = (string) ($data['url'] ?? '');

        if ($qr !== '' && ! str_starts_with($qr, 'data:image/')) {
            $qr = 'data:image/png;base64,'.$qr;
        }

        return response()->view('dashboard.whatsapp-qr', [
            'bot' => $bot,
            'qr' => $qr,
            'connected' => (bool) ($data['connected'] ?? false),
            'message' => $data['message'] ?? null,
        ]);
    }

    public function status(Request $request, FonnteService $fonnte)
    {
        return $this->deviceResponse($request, $fonnte, 'status');
    }

    public function activate(Request $request, FonnteService $fonnte)
    {
        $bot = $request->user()->whatsappBot;

        abort_unless($bot, 404);

        try {
            $fonnte->configureWebhook($bot);
            $data = $fonnte->status($bot);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['whatsapp' => $exception->getMessage()]);
        }

        if ($bot->connection_status !== 'connected') {
            return back()->withErrors(['whatsapp' => 'WhatsApp belum terhubung. Scan QR terlebih dahulu.']);
        }

        return back()->with('success', 'WhatsApp aktif dan siap menerima pesan.');
    }

    private function deviceResponse(Request $request, FonnteService $fonnte, string $action)
    {
        $bot = $request->user()->whatsappBot;

        abort_unless($bot, 404);

        try {
            $data = $fonnte->{$action}($bot);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json($data);
    }
}
