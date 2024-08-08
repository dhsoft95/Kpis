<?php

namespace Database\Seeders;

use App\Models\feedbackQuestions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $questions = [
            ['type' => 'csat', 'transaction_stage' => 'first', 'question' => 'How satisfied were you with your first transaction?'],
            ['type' => 'nps', 'transaction_stage' => 'first', 'question' => 'How likely are you to recommend our service to a friend?'],
            ['type' => 'csat', 'transaction_stage' => 'early', 'question' => 'How would you rate your experience with our service so far?'],
            ['type' => 'ces', 'transaction_stage' => 'early', 'question' => 'How easy was it to complete your transaction?'],
            ['type' => 'nps', 'transaction_stage' => 'regular', 'question' => 'Based on your experience, how likely are you to continue using our service?'],
            ['type' => 'ces', 'transaction_stage' => 'regular', 'question' => 'How effortless was your most recent transaction?'],
            ['type' => 'csat', 'transaction_stage' => 'loyal', 'question' => 'Overall, how satisfied are you with our service?'],
            ['type' => 'nps', 'transaction_stage' => 'loyal', 'question' => 'How likely are you to recommend our service to colleagues?'],
            ['type' => 'ces', 'transaction_stage' => 'loyal', 'question' => 'How easy is it for you to use our service for your regular transactions?'],
        ];

        foreach ($questions as $question) {
            feedbackQuestions::create($question);
        }
    }
}
