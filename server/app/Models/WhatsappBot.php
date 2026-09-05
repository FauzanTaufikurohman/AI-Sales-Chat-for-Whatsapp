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
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'provider_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
