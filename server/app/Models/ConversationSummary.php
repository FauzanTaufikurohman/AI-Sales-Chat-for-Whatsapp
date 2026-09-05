<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationSummary extends Model
{
    protected $fillable = [
        'conversation_id',
        'summary',
        'important_information',
        'intent',
        'purchase_intent',
        'lead_status',
        'generated_at',
    ];

    protected $casts = [
        'important_information' => 'array',
        'purchase_intent' => 'boolean',
        'generated_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
