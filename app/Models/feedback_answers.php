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
        'sender_phone',
        'rating',
        'answer'
    ];

    public function feedbackQuestion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(feedbackQuestions::class, 'feedback_question_id');
    }
}
