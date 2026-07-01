<?php

use App\Http\Controllers\Api\CVScoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// CV Analysis API
Route::prefix('cv')->group(function () {
    // Analyze a CV by ID (triggers AI processing)
    Route::post('/{id}/analyze', [CVScoreController::class, 'analyze']);

    // Get analysis result for a CV
    Route::get('/{id}/result', [CVScoreController::class, 'result']);

    // List all results for a job
    Route::get('/job/{jobId}/results', [CVScoreController::class, 'jobResults']);
});