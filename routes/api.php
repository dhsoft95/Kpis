<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerFeedbackController;
use App\Http\Controllers\GoogleAnalyticsController;
use App\Http\Controllers\GooglePlayController;
use App\Http\Controllers\GooglePlayReportingController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('/user-stats', [UserStatsController::class, 'getWeekOnWeekChange']);


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
