<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class feedbacks extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'score', 'sender_phone', 'comment', 'transaction_id'];
    /**
     * Define the relationship with FeedbackAnswer.
     *
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(feedback_answers::class, 'feedback_id');
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(trans::class, 'transaction_id', 'trxId');
    }
}
