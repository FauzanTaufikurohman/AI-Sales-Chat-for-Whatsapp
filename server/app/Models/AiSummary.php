<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSummary extends Model
{
    protected $table = 'ai_summaries';

    protected $fillable = ['conversation_id', 'summary', 'customer_requirement', 'customer_intent', 'important_information', 'customer_questions', 'recommended_action', 'urgency', 'confidence'];

    protected function casts(): array
    {
        return ['important_information' => 'array', 'customer_questions' => 'array', 'confidence' => 'float'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
