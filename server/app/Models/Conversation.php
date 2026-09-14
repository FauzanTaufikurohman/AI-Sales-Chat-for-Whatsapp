<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'bot_id',
        'customer_phone',
        'customer_name',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function whatsappBot()
    {
        return $this->belongsTo(WhatsappBot::class, 'bot_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function summary()
    {
        return $this->hasOne(ConversationSummary::class);
    }
}
