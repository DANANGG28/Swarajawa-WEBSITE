<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\GuruDashboardController;
use App\Http\Controllers\Api\KuisController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\LevelMateriController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\ProgresController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\SoalController;
use App\Http\Controllers\Api\SpeechController;
use App\Http\Controllers\Api\SuperadminDashboardController;
use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'app' => 'Sinau Jowo API',
    'status' => 'ok',
    'time' => now()->toIso8601String(),
]));

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('siswa/register', [AuthController::class, 'registerSiswa']);
    Route::post('siswa/login', [AuthController::class, 'loginSiswa']);
    Route::post('guru/login', [AuthController::class, 'loginGuru']);
    Route::post('superadmin/login', [AuthController::class, 'loginSuperadmin']);
});

Route::middleware('auth.any')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::middleware('role:siswa')->group(function () {
        Route::get('materi', [MateriController::class, 'index']);
        Route::get('materi/{levelMateri}', [MateriController::class, 'show']);
        Route::post('materi/{levelMateri}/mulai', [MateriController::class, 'mulai']);

        Route::post('kuis/jawab', [KuisController::class, 'jawab']);
        Route::post('kuis/selesai', [KuisController::class, 'selesai']);

        Route::get('leaderboard', [LeaderboardController::class, 'index']);
        Route::get('progres', [ProgresController::class, 'index']);

        Route::post('chat', [ChatController::class, 'ask']);

        Route::prefix('speech')->group(function () {
            Route::post('tts', [SpeechController::class, 'tts']);
            Route::post('stt', [SpeechController::class, 'stt']);
            Route::post('sts', [SpeechController::class, 'sts']);
        });
    });

    Route::middleware('role:guru,superadmin')->group(function () {
        Route::apiResource('soal', SoalController::class);
        Route::apiResource('test', TestController::class);
        Route::apiResource('level-materi', LevelMateriController::class)
            ->parameters(['level-materi' => 'levelMateri'])
            ->only(['index', 'show']);
    });

    Route::middleware('role:guru')->group(function () {
        Route::get('guru/siswa', [GuruDashboardController::class, 'siswa']);
        Route::get('guru/siswa/{siswa}/progres', [GuruDashboardController::class, 'progresSiswa']);
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::get('superadmin/dashboard', [SuperadminDashboardController::class, 'index']);
        Route::apiResource('superadmin/guru', GuruController::class);
        Route::apiResource('superadmin/siswa', SiswaController::class);
        Route::apiResource('level-materi', LevelMateriController::class)
            ->parameters(['level-materi' => 'levelMateri'])
            ->except(['index', 'show']);
    });
});
