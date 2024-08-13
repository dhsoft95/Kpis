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

        // Get the next unanswered question for this user
        $question = $this->getNextQuestion($senderPhone, $transactionCount);

        if (!$question) {
            return response()->json(['message' => 'No more questions available.'], 404);
        }

        return response()->json($question);
    }

    private function getNextQuestion($senderPhone, $transactionCount)
    {
        $answeredQuestionIds = feedback_answers::where('sender_phone', $senderPhone)
            ->pluck('feedback_question_id');

        $query = feedbackQuestions::whereNotIn('id', $answeredQuestionIds);

        if ($transactionCount == 1) {
            $query->where('type', 'nps')->where('transaction_stage', 'first');
        } elseif ($transactionCount >= 2 && $transactionCount <= 3) {
            $query->where('type', 'ces')->where('transaction_stage', 'early');
        } elseif ($transactionCount >= 4 && $transactionCount <= 10) {
            $query->where('type', 'nps')->where('transaction_stage', 'regular');
        } else {
            $query->whereIn('type', ['csat', 'nps', 'ces'])->where('transaction_stage', 'loyal');
        }

        return $query->first();
    }

    public function submitFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_phone' => 'required|string',
            'feedback_question_id' => 'required|exists:feedback_questions,id',
            'rating' => 'required|integer|min:1|max:10',
            'answer' => 'nullable|string',
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
        // Ensure the user has not already answered this question
        $existingAnswer = feedback_answers::where('feedback_question_id', $request->input('feedback_question_id'))
            ->where('sender_phone', $senderPhone)
            ->first();

        if ($existingAnswer) {
            return response()->json(['message' => 'You have already answered this question.'], 400);
        }

        feedback_answers::create([
            'feedback_question_id' => $request->input('feedback_question_id'),
            'sender_phone' => $senderPhone,
            'rating' => $request->input('rating'),
            'answer' => $request->input('answer')
        ]);

        return response()->json(['message' => 'Feedback submitted successfully.']);
    }
}
