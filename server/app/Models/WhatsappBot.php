<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappBot extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'provider',
        'provider_phone_id',
        'provider_account_id',
        'provider_token',
        'name',
        'system_prompt',
        'is_active',
        'fonnte_device',
        'fonnte_token',
        'connection_status',
        'connected_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'connected_at' => 'datetime',
    ];

    protected $hidden = [
        'provider_token',
        'fonnte_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'bot_id');
    }
}
