<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\SelfCareController;
use App\Http\Controllers\Api\PosterController;
use App\Http\Controllers\Api\CaseAndForumController;
use App\Http\Controllers\Api\GamificationController;

/*
|--------------------------------------------------------------------------
| SIKERA REST API Routes (Mobile Version / Flutter Backend)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // 1. Health & Ping
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'SIKERA Mobile API v1 is active and ready.',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // 2. Autentikasi Publik
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // 3. Panduan Publik (Tanpa Auth jika diperlukan)
    Route::get('/selfcare/guides/blood', [SelfCareController::class, 'bloodGuide']);
    Route::get('/selfcare/guides/hygiene', [SelfCareController::class, 'hygieneGuide']);

    // 4. Endpoint Terproteksi (Bearer Token Sanctum)
    Route::middleware('auth:sanctum')->group(function () {

        // 4.1 Auth & Profil
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/biodata', [AuthController::class, 'updateBiodata']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // 4.2 Beranda / Dashboard Mobile
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // 4.3 Modul Edukasi & Pre-Test / Post-Test Per Modul
        Route::prefix('modules')->group(function () {
            Route::get('/', [ModuleController::class, 'index']);
            Route::get('/{id}', [ModuleController::class, 'show']);
            Route::get('/{id}/pretest', [ModuleController::class, 'getPretestQuestions']);
            Route::post('/{id}/pretest', [ModuleController::class, 'submitPretest']);
            Route::get('/{id}/topic/{topicId}', [ModuleController::class, 'readTopic']);
            Route::get('/{id}/posttest', [ModuleController::class, 'getPosttestQuestions']);
            Route::post('/{id}/posttest', [ModuleController::class, 'submitPosttest']);
        });

        // 4.4 Self-Care Tools (Period Tracker, IMT)
        Route::prefix('selfcare')->group(function () {
            Route::get('/summary', [SelfCareController::class, 'summary']);
            Route::post('/period', [SelfCareController::class, 'storePeriod']);
            Route::get('/period/history', [SelfCareController::class, 'periodHistory']);
            Route::post('/bmi', [SelfCareController::class, 'storeBmi']);
            Route::get('/bmi/history', [SelfCareController::class, 'bmiHistory']);
        });

        // 4.5 Projek Visual Poster Kaspro
        Route::prefix('posters')->group(function () {
            Route::get('/', [PosterController::class, 'index']);
            Route::post('/{id}/download', [PosterController::class, 'download']);
            Route::post('/{id}/share', [PosterController::class, 'share']);
        });

        // 4.6 Studi Kasus & Diskusi Forum Anonim
        Route::get('/cases', [CaseAndForumController::class, 'getCases']);
        Route::get('/cases/{id}', [CaseAndForumController::class, 'getCaseDetail']);

        Route::prefix('forum')->group(function () {
            Route::get('/', [CaseAndForumController::class, 'getThreads']);
            Route::post('/', [CaseAndForumController::class, 'createThread']);
            Route::get('/{id}', [CaseAndForumController::class, 'getThreadDetail']);
            Route::post('/{id}/reply', [CaseAndForumController::class, 'replyThread']);
        });

        // 4.7 Gamifikasi & Retensi Harian
        Route::prefix('gamification')->group(function () {
            Route::get('/trivia', [GamificationController::class, 'getTrivia']);
            Route::post('/trivia', [GamificationController::class, 'submitTrivia']);
            Route::get('/myth-fact', [GamificationController::class, 'getMythFact']);
            Route::post('/myth-fact', [GamificationController::class, 'submitMythFact']);
            Route::get('/diary', [GamificationController::class, 'getDiary']);
            Route::post('/diary', [GamificationController::class, 'storeDiary']);
        });
    });
});
