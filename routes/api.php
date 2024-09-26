<?php

use App\Http\Controllers\APIs\CurrencyController;
use App\Http\Controllers\APIs\CustomerFeedbackController;
use App\Http\Controllers\APIs\UserStatsConstroller;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GoogleServices\GoogleAnalyticsController;
use App\Http\Controllers\GoogleServices\GooglePlayController;
use App\Http\Controllers\GoogleServices\GooglePlayReportingController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('/user-stats', [UserStatsConstroller::class, 'getWeekOnWeekChange']);


Route::get('/metrics/crash-rate-metadata', [GooglePlayController::class, 'getCrashRateMetricSet']);
Route::get('/metrics/query-crash-rate', [GooglePlayController::class, 'queryCrashRateMetricSet']);


Route::get('/crash-rate-metrics', [GooglePlayReportingController::class, 'getCrashRateMetrics']);

// routes/api.php


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('/analytics/top-city-platform', [GoogleAnalyticsController::class, 'getTopData']);





Route::middleware('auth:sanctum')->group(function () {
    Route::post('/feedback', [CustomerFeedbackController::class, 'store']);
    Route::get('/feedback/question/{type}', [CustomerFeedbackController::class, 'showQuestion']);
});
Route::post('/feedback/questions', [CustomerFeedbackController::class, 'getQuestions']);
Route::post('/feedback/submit-feedback', [CustomerFeedbackController::class, 'submitFeedback']);

Route::post('/convert', [CurrencyController::class, 'convertFromBase']);
Route::get('/currencies', [CurrencyController::class, 'getSupportedCurrencies']);
