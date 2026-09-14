<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(private readonly GamificationService $gamification) {}

    public function index(Request $request): JsonResponse
    {
        $kelas = $request->query('kelas');
        $limit = (int) $request->query('limit', 20);

        return response()->json([
            'kelas' => $kelas,
            'data' => $this->gamification->leaderboard($kelas, min(max($limit, 1), 100)),
        ]);
    }
}
