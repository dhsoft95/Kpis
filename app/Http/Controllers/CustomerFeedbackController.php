<?php

namespace App\Http\Controllers;

use App\Models\feedback_answers;
use App\Models\feedbackQuestions;
use App\Models\trans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerFeedbackController extends Controller
{
    public function getQuestions(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sender_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $senderPhone = $request->sender_phone;

        // Check if the phone number exists in the transactions table
        $transactionCount = trans::where('sender_phone', $senderPhone)->count();

        if ($transactionCount == 0) {
            return response()->json(['message' => 'Telephone number not found.'], 404);
        }

        // Fetch questions based on transaction count
        $questions = $this->generateQuestions($transactionCount);

        return response()->json($questions);
    }

    private function generateQuestions($transactionCount)
    {
        $questions = [];

        if ($transactionCount == 1) {
            $questions = feedbackQuestions::where('type', 'nps')
                ->where('transaction_stage', 'first')
                ->get();
        } elseif ($transactionCount >= 2 && $transactionCount <= 3) {
            $questions = feedbackQuestions::where('type', 'ces')
                ->where('transaction_stage', 'early')
                ->get();
        } elseif ($transactionCount >= 4 && $transactionCount <= 10) {
            $questions = feedbackQuestions::where('type', 'nps')
                ->where('transaction_stage', 'regular')
                ->get();
        } else {
            $questions = feedbackQuestions::whereIn('type', ['csat', 'nps', 'ces'])
                ->where('transaction_stage', 'loyal')
                ->get();
        }

        return $questions;
    }

    public function submitFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_phone' => 'required|string',
            'feedback' => 'required|array',
            'feedback.*.feedback_question_id' => 'required|exists:feedback_questions,id',
            'feedback.*.rating' => 'required|integer|min:1|max:10',
            'feedback.*.answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $senderPhone = $request->input('sender_phone');

        // Check if the phone number exists in the transactions table
        $transactionCount = trans::where('sender_phone', $senderPhone)->count();

        if ($transactionCount == 0) {
            return response()->json(['message' => 'Telephone number not found in transactions.'], 404);
        }

        foreach ($request->input('feedback') as $feedback) {
            // Ensure the user has not already answered this question
            $existingAnswer = feedback_answers::where('feedback_question_id', $feedback['feedback_question_id'])
                ->where('sender_phone', $senderPhone)
                ->first();

            if ($existingAnswer) {
                return response()->json(['message' => 'You have already answered this question.'], 400);
            }

            feedback_answers::create([
                'feedback_question_id' => $feedback['feedback_question_id'],
                'sender_phone' => $senderPhone,
                'rating' => $feedback['rating'],
                'answer' => $feedback['answer']
            ]);
        }

        return response()->json(['message' => 'Feedback submitted successfully.']);
    }
}
