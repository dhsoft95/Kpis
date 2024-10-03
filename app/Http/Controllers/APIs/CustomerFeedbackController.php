<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\feedback_answers;
use App\Models\feedbackQuestions;

use App\Models\trans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerFeedbackController extends Controller
{
    public function getQuestions(Request $request): \Illuminate\Http\JsonResponse
    {
        Log::info('getQuestions request data: ' . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $userId = $request->user_id;

        // Check if the user has any transactions
        $transactionCount = trans::where('user_id', $userId)->count();

        if ($transactionCount == 0) {
            return response()->json(['message' => 'No transactions found for this user.'], 404);
        }

        // Get the next unanswered question for this user
        $question = $this->getNextQuestion($userId, $transactionCount);

        if (!$question) {
            return response()->json(['message' => 'No more questions available.'], 404);
        }

        return response()->json($question);
    }

    private function getNextQuestion($userId, $transactionCount)
    {
        $answeredQuestionIds = feedback_answers::where('user_id', $userId)
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
        Log::info('submitFeedback request data: ' . json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:mysql_second.tbl_simba_transactions,user_id',
            'feedback_question_id' => 'required|exists:feedback_questions,id',
            'rating' => 'required|integer|min:1|max:10',
            'answer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $userId = $request->input('user_id');

        // Check if the user has any transactions
        $transactionCount = trans::where('user_id', $userId)->count();

        if ($transactionCount == 0) {
            return response()->json(['message' => 'No transactions found for this user.'], 404);
        }

        // Ensure the user has not already answered this question
        $existingAnswer = feedback_answers::where('feedback_question_id', $request->input('feedback_question_id'))
            ->where('user_id', $userId)
            ->first();

        if ($existingAnswer) {
            return response()->json(['message' => 'You have already answered this question.'], 400);
        }

        try {
            feedback_answers::create([
                'feedback_question_id' => $request->input('feedback_question_id'),
                'user_id' => $userId,
                'rating' => $request->input('rating'),
                'answer' => $request->input('answer')
            ]);

            return response()->json(['message' => 'Feedback submitted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error submitting feedback: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while submitting feedback.'], 500);
        }
    }
}
