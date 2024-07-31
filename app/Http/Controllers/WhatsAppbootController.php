<?php

namespace App\Http\Controllers;

use App\Models\conversations;
use App\Models\User;
use App\Models\trans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use MissaelAnda\Whatsapp\Facade\Whatsapp;
use GuzzleHttp\Client;

class WhatsAppbootController extends Controller
{
    public function handleWebhook(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $webhookData = $request->all();

            if ($this->validateWebhook($webhookData)) {
                foreach ($webhookData['entry'] as $entry) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'messages') {
                            if (isset($change['value']['statuses'])) {
                                $this->processMessageStatuses($change['value']['statuses']);
                            }
                            if (isset($change['value']['messages'])) {
                                $this->processReceivedMessages($change['value']['messages']);
                            }
                        }
                    }
                }

                return response()->json(['message' => 'Webhook processed successfully'], 200);
            } else {
                Log::error('Invalid webhook payload');
                return response()->json(['error' => 'Invalid webhook payload'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing webhook: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the webhook'], 500);
        }
    }

    protected function validateWebhook($webhookData)
    {
        return isset($webhookData['object']) &&
            $webhookData['object'] === 'whatsapp_business_account' &&
            isset($webhookData['entry']) &&
            is_array($webhookData['entry']);
    }

    protected function processMessageStatuses($statuses)
    {
        foreach ($statuses as $status) {
            if ($status['status'] === 'read') {
                $this->handleMessageReadStatus($status['recipient_id'], $status['id'], $status['timestamp']);
            }
        }
    }

    protected function processReceivedMessages($messages): void
    {
        foreach ($messages as $message) {
            if ($message['type'] === 'text' && isset($message['text']['body'])) {
                $recipientNumber = $message['from'];
                $userMessage = $message['text']['body'];
                $conversationId = $message['id'];

                $conversation = conversations::where('conversation_id', $conversationId)
                    ->where('status', 'active')
                    ->first();

                if ($conversation) {
                    $responseText = $this->handleOngoingConversation($conversation, $userMessage);
                } else {
                    $responseText = $this->getGeminiResponse($userMessage);
                    $this->startConversation($conversationId, $recipientNumber);
                }

                Whatsapp::send($recipientNumber, $responseText);
                $this->markMessageAsRead($message['id']);
            }
        }
    }

    protected function handleOngoingConversation($conversation, $userMessage)
    {
        try {
            if (!$this->checkRateLimit($conversation->user_id)) {
                return "You've made too many requests. Please wait a moment before trying again.";
            }

            switch ($conversation->context) {
                case 'waiting_for_transaction_id':
                    if (preg_match('/\b\d{10}\b/', $userMessage, $matches)) {
                        $transactionId = $matches[0];
                        $status = $this->checkTransactionStatus($transactionId);
                        $conversation->update(['status' => 'completed']);
                        return "Transaction ID: {$transactionId}\nStatus: {$status['status']}\n{$status['message']}";
                    } else {
                        return "I'm sorry, but that doesn't look like a valid transaction ID. Please provide a 10-digit transaction ID.";
                    }

                case 'waiting_for_fee_calculation':
                    $details = explode(',', $userMessage);
                    if (count($details) < 2) {
                        return "Please provide the amount and destination country, separated by a comma.";
                    }
                    $amount = floatval(trim($details[0]));
                    $destinationCountry = trim($details[1]);

                    $fee = $this->calculateTransactionFee($amount, $destinationCountry);
                    $conversation->update(['status' => 'completed']);

                    return "For a transfer of $amount USD to $destinationCountry, the fee would be $fee USD.";

                case 'waiting_for_currency_conversion':
                    $details = explode(',', $userMessage);
                    if (count($details) < 3) {
                        return "Please provide the amount, from currency, and to currency, separated by commas. For example: '100, USD, TZS'";
                    }
                    $amount = floatval(trim($details[0]));
                    $fromCurrency = strtoupper(trim($details[1]));
                    $toCurrency = strtoupper(trim($details[2]));

                    $convertedAmount = $this->convertCurrency($amount, $fromCurrency, $toCurrency);
                    if ($convertedAmount !== null) {
                        $conversation->update(['status' => 'completed']);
                        return "$amount $fromCurrency is approximately $convertedAmount $toCurrency";
                    } else {
                        return "I'm sorry, but I couldn't perform that conversion. Please check the currencies and try again.";
                    }

                default:
                    if (preg_match('/transaction.*history/i', $userMessage)) {
                        $history = $this->getTransactionHistory($conversation->user_id);
                        return $history;
                    }

                    return $this->getGeminiResponse($userMessage);
            }
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function getGeminiResponse($userMessage)
    {
        if (preg_match('/transaction.*status|check.*transaction/i', $userMessage)) {
            return "To check your transaction status, please provide your transaction ID.";
        }

        if (preg_match('/transaction.*fee|transfer.*cost/i', $userMessage)) {
            return "To calculate the transaction fee, please provide the amount you wish to send and the destination country, separated by a comma. For example: '100, Tanzania'";
        }

        if (preg_match('/currency.*conversion|exchange.*rate/i', $userMessage)) {
            return "To get a currency conversion, please provide the amount, from currency, and to currency, separated by commas. For example: '100, USD, TZS'";
        }

        // If it's not a specific case we're handling, proceed with the Gemini API call
        $geminiApiKey = env('GEMINI_API_KEY');
        $geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=' . $geminiApiKey;

        $customPrompt = file_get_contents(storage_path('app/gemini_prompt.txt'));
        $customPrompt .= "\n\nUser: " . $userMessage;

        try {
            $client = new Client();
            $response = $client->post($geminiUrl, [
                'json' => [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => $customPrompt,
                                ]
                            ]
                        ]
                    ],
                    "generationConfig" => [
                        "temperature" => 0.9,
                        "topK" => 1,
                        "topP" => 1,
                        "maxOutputTokens" => 2048,
                        "stopSequences" => []
                    ],
                    "safetySettings" => [
                        [
                            "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                            "threshold" => "BLOCK_MEDIUM_AND_ABOVE"
                        ]
                    ]
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                if (isset($responseData['candidates']) && !empty($responseData['candidates'])) {
                    return $responseData['candidates'][0]['content']['parts'][0]['text'];
                }
            }

            Log::error('Error in Gemini response: ' . $response->getBody());
            return 'I apologize, but I\'m having trouble processing your request right now. Please try again later.';
        } catch (\Exception $e) {
            Log::error('Error fetching response from Gemini: ' . $e->getMessage());
            return 'I\'m sorry, but I\'m experiencing technical difficulties. Please try again later.';
        }
    }

    protected function checkTransactionStatus($transactionId)
    {
        // This is a mock implementation. In a real scenario, you'd query your transaction database or API
        $statuses = ['completed', 'pending', 'failed'];
        $randomStatus = $statuses[array_rand($statuses)];

        if ($randomStatus === 'failed') {
            return ['status' => 'failed', 'message' => 'Transaction failed. Please contact support.'];
        } elseif ($randomStatus === 'pending') {
            return ['status' => 'pending', 'message' => 'Transaction is still processing. Please wait for 12 minutes before contacting support.'];
        } else {
            return ['status' => 'completed', 'message' => 'Transaction completed successfully. Recipient should receive funds shortly.'];
        }
    }

    protected function calculateTransactionFee($amount, $destinationCountry)
    {
        $baseFee = 2.00; // Base fee in USD
        $percentageFee = 0.02; // 2% of transaction amount

        $countrySpecificFees = [
            'Tanzania' => 0.5,
            'Kenya' => 0.7,
            'Uganda' => 0.6,
            // Add more countries as needed
        ];

        $fee = $baseFee + ($amount * $percentageFee);

        if (isset($countrySpecificFees[$destinationCountry])) {
            $fee += $countrySpecificFees[$destinationCountry];
        }

        return round($fee, 2);
    }

    protected function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        // In a real scenario, you'd use a currency conversion API
        $rates = [
            'USD' => ['TZS' => 2300, 'KES' => 110, 'UGX' => 3700],
            // Add more currencies as needed
        ];

        if (isset($rates[$fromCurrency][$toCurrency])) {
            $convertedAmount = $amount * $rates[$fromCurrency][$toCurrency];
            return round($convertedAmount, 2);
        }

        return null; // Conversion not available
    }

    protected function getTransactionHistory($userId, $limit = 5)
    {
        // In a real scenario, you'd query your database
        $transactions = trans::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $history = "Recent transactions:\n";
        foreach ($transactions as $transaction) {
            $history .= "ID: {$transaction->id}, Amount: {$transaction->amount}, Date: {$transaction->created_at}\n";
        }

        return $history;
    }

    protected function checkRateLimit($userId)
    {
        $key = "rate_limit_{$userId}";
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            return false; // Rate limit exceeded
        }

        Cache::put($key, $attempts + 1, 60); // Increment attempts, expire in 1 minute
        return true;
    }

    protected function handleException(\Exception $e)
    {
        Log::error('An error occurred: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return "I apologize, but an error occurred while processing your request. Our team has been notified and will look into it. Please try again later.";
    }

    protected function startConversation($conversationId, $phoneNumber, $context = null)
    {
        conversations::updateOrCreate(
            ['conversation_id' => $conversationId],
            [
                'phone_number' => $phoneNumber,
                'started_at' => now(),
                'status' => 'active',
                'context' => $context
            ]
        );
    }

    protected function handleMessageReadStatus($recipientId, $messageId, $timestamp)
    {
        Log::info("Message ID $messageId read by $recipientId at $timestamp");
        // Additional logic for handling the "Message Read" status can be added here
    }

    protected function markMessageAsRead($messageId)
    {
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');

        $url = 'https://graph.facebook.com/v20.0/' . $phoneNumberId . '/messages';
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];

        $body = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId,
        ];

        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['success']) && $responseData['success']) {
                Log::info('Message marked as read successfully: ' . $messageId);
            } else {
                Log::error('Failed to mark message as read: ' . $messageId);
            }
        } catch (\Exception $e) {
            Log::error('Error marking message as read: ' . $e->getMessage());
        }
    }
}
