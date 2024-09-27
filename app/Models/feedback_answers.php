<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class feedback_answers extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_question_id',
        'user_id',
        'rating',
        'answer',
        'sender_phone'
    ];

    public function feedbackQuestion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(feedbackQuestions::class, 'feedback_question_id');
    }

    public function transaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(trans::class, 'user_id', 'user_id');
    }
}
