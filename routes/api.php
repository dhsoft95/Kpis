<?php

use App\Http\Controllers\GooglePlayController;
use App\Http\Controllers\GooglePlayReportingController;
use App\Http\Controllers\UserStatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/user-stats', [UserStatsController::class, 'getWeekOnWeekChange']);


Route::get('/metrics/crash-rate-metadata', [GooglePlayController::class, 'getCrashRateMetricSet']);
Route::get('/metrics/query-crash-rate', [GooglePlayController::class, 'queryCrashRateMetricSet']);



Route::get('/crash-rate-metrics', [GooglePlayReportingController::class, 'getCrashRateMetrics']);
