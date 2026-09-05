<?php

use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/whatsapp', [
    WhatsAppWebhookController::class,
    'handle',
])->name('webhook.whatsapp');
