<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgresController extends Controller
{
    public function __construct(private readonly GamificationService $gamification) {}

    /**
     * Progres level + ringkasan EXP/streak milik siswa yang login.
     */
    public function index(Request $request): JsonResponse
    {
        $siswa = $request->user();
        $this->gamification->syncStreak($siswa);

        $levels = LevelMateri::query()->orderBy('urutan')->get();
        $progres = ProgresSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('level_materi_id');

        $totalLevel = $levels->count();
        $selesai = $progres->where('status', ProgresSiswa::STATUS_SELESAI)->count();

        return response()->json([
            'ringkasan' => [
                'total_exp' => (int) ($siswa->exp()->value('total_exp') ?? 0),
                'current_streak' => (int) ($siswa->strek()->value('current_streak') ?? 0),
                'highest_streak' => (int) ($siswa->strek()->value('highest_streak') ?? 0),
                'level_selesai' => $selesai,
                'total_level' => $totalLevel,
                'persentase' => $totalLevel > 0 ? (int) round(($selesai / $totalLevel) * 100) : 0,
            ],
            'progres' => $levels->map(fn (LevelMateri $level): array => [
                'level_materi_id' => $level->id,
                'nama_materi' => $level->nama_materi,
                'urutan' => (int) $level->urutan,
                'status' => $progres[$level->id]->status ?? ProgresSiswa::STATUS_TERKUNCI,
                'tanggal_selesai' => $progres[$level->id]->tanggal_selesai ?? null,
            ])->values(),
        ]);
    }
}
