<?php

namespace App\Services;

use App\Models\WhatsappBot;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FonnteService
{
    private const API_URL = 'https://api.fonnte.com';

    public function createDevice(WhatsappBot $bot): array
    {
        $response = Http::timeout(20)
            ->withHeaders(['Authorization' => $this->accountToken()])
            ->post(self::API_URL.'/add-device', [
                'name' => $bot->name,
                'device' => $bot->phone_number,
                'autoread' => true,
                'personal' => true,
                'group' => false,
            ]);

        $data = $this->dataOrFail($response, 'Gagal membuat device Fonnte.');

        $bot->update([
            'fonnte_device' => $data['device'] ?? $bot->phone_number,
            'fonnte_token' => $data['token'] ?? null,
            'connection_status' => 'disconnected',
            'is_active' => false,
        ]);

        $this->configureWebhook($bot->fresh());

        return $data;
    }

    public function configureWebhook(WhatsappBot $bot): array
    {
        if (! $bot->fonnte_token) {
            throw new RuntimeException('Device token Fonnte belum tersedia.');
        }

        $webhookUrl = config('services.fonnte.webhook_url');

        if (! filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('FONNTE_WEBHOOK_URL harus berupa URL publik yang valid.');
        }

        return $this->dataOrFail(
            Http::timeout(20)
                ->withHeaders(['Authorization' => $bot->fonnte_token])
                ->post(self::API_URL.'/update-device', [
                    'device' => $bot->fonnte_device,
                    'name' => $bot->name,
                    'webhook' => $webhookUrl,
                    'autoread' => true,
                    'personal' => true,
                    'group' => false,
                ]),
            'Gagal mengatur webhook Fonnte.'
        );
    }

    public function qr(WhatsappBot $bot): array
    {
        return $this->deviceRequest($bot, '/qr');
    }

    public function status(WhatsappBot $bot): array
    {
        $data = $this->deviceRequest($bot, '/device');
        $status = strtolower((string) ($data['device_status'] ?? $data['status'] ?? 'disconnected'));
        $connected = in_array($status, ['connected', 'online', 'connect'], true);

        $bot->update([
            'connection_status' => $connected ? 'connected' : $status,
            'connected_at' => $connected ? ($bot->connected_at ?: now()) : null,
            'is_active' => $connected,
        ]);

        return $data;
    }

    public function sendMessage(
        WhatsappBot $bot,
        string $target,
        string $message,
        ?UploadedFile $image = null
    ): array {
        if (! $bot->fonnte_token) {
            throw new RuntimeException('Device token Fonnte belum tersedia.');
        }

        $request = Http::timeout(20)
            ->withHeaders(['Authorization' => $bot->fonnte_token]);

        if ($image) {
            $request = $request->attach(
                'file',
                fopen($image->getRealPath(), 'r'),
                $image->getClientOriginalName()
            );
        }

        $response = $request->post(self::API_URL.'/send', [
            'target' => $target,
            'message' => $message,
        ]);

        Log::info('Fonnte manual reply response', [
            'target' => $target,
            'has_image' => (bool) $image,
            'status' => $response->json('status'),
            'reason' => $response->json('reason'),
            'detail' => $response->json('detail'),
            'process' => $response->json('process'),
            'message_id' => $response->json('id'),
        ]);

        return $this->dataOrFail($response, 'Gagal mengirim pesan melalui Fonnte.');
    }

    private function deviceRequest(WhatsappBot $bot, string $endpoint): array
    {
        if (! $bot->fonnte_token) {
            throw new RuntimeException('Device Fonnte belum dibuat.');
        }

        return $this->dataOrFail(
            Http::timeout(20)
                ->withHeaders(['Authorization' => $bot->fonnte_token])
                ->post(self::API_URL.$endpoint),
            'Gagal membaca status device Fonnte.'
        );
    }

    private function accountToken(): string
    {
        $token = config('services.fonnte.account_token');

        if (! $token) {
            throw new RuntimeException('Fonnte Account Token belum dikonfigurasi.');
        }

        return $token;
    }

    private function dataOrFail(Response $response, string $message): array
    {
        $data = $response->json();

        if ($response->failed() || ($data['status'] ?? false) !== true) {
            $reason = strtolower((string) ($data['reason'] ?? ''));

            if (str_contains($reason, 'device already exist')) {
                throw new RuntimeException(
                    'Nomor device sudah terdaftar di Fonnte. Gunakan token device yang sudah ada atau hapus device lama di dashboard Fonnte terlebih dahulu.'
                );
            }

            if (str_contains($reason, 'free device already connected')) {
                throw new RuntimeException(
                    'Akun Fonnte sudah memiliki free device yang terhubung. Putuskan koneksi device lama di Fonnte atau gunakan paket berbayar sebelum membuat koneksi baru.'
                );
            }

            throw new RuntimeException($data['reason'] ?? $message);
        }

        return $data;
    }
}
