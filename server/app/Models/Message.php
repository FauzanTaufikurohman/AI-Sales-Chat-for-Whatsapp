<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'message',
        'message_type',
        'provider_message_id',
        'is_ai_processed',
        'sent_at',
    ];

    protected $casts = [
        'is_ai_processed' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
