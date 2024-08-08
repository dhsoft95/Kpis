<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class feedbackQuestions extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'question', 'transaction_stage'];

    // Define a one-to-many relationship with FeedbackAnswer
    public function feedbackAnswers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(feedback_answers::class, 'feedback_question_id');
    }
}
