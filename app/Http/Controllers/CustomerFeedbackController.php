<?php

namespace App\Http\Controllers;

use App\Models\CustomerFeedback;
use App\Models\trans;
use Illuminate\Http\Request;
// app/Http/Controllers/CustomerFeedbackController.php

class CustomerFeedbackController extends Controller
{

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:nps,ces,csat',
            'score' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);

        $feedback = new CustomerFeedback();
        $feedback->user_id = auth()->id();
        $feedback->type = $validatedData['type'];
        $feedback->score = $validatedData['score'];
        $feedback->comment = $validatedData['comment'] ?? null;
        $feedback->save();

        return response()->json(['message' => 'Feedback submitted successfully'], 201);
    }

    public function showQuestion($type)
    {
        // Check if user should see this question
        if (!$this->shouldShowQuestion($type)) {
            return response()->json(['show' => false]);
        }

        $question = $this->getQuestion($type);
        return response()->json(['show' => true, 'question' => $question]);
    }

    private function shouldShowQuestion($type)
    {
        $user = auth()->user();

        switch ($type) {
            case 'nps':
                // Show NPS after 5 transfers
                return $user->transfers()->count() >= 5;
            case 'ces':
                // Show CES if user just completed a transfer
                return $user->transfers()->latest()->first()->created_at->isToday();
            case 'csat':
                // Show CSAT if user completed a transfer 2 days ago
                $latestTransfer = $user->transfers()->latest()->first();
                return $latestTransfer && $latestTransfer->created_at->diffInDays(now()) == 2;
            default:
                return false;
        }
    }

    private function getQuestion($type)
    {
        switch ($type) {
            case 'nps':
                return "How likely are you to recommend Simba Money to a friend or colleague?";
            case 'ces':
                return "How easy was it to complete your money transfer with Simba Money today?";
            case 'csat':
                return "How satisfied are you with your recent Simba Money transfer experience?";
            default:
                return "";
        }
    }
}
