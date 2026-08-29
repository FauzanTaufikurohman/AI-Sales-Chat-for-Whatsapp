<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'conversation_id', 'external_message_id', 'sender_type', 'message_type', 'content', 'media_url', 'mime_type', 'ai_processed', 'ai_summary'];

    protected function casts(): array
    {
        return ['ai_processed' => 'boolean'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
