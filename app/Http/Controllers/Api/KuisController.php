<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelMateri;
use App\Models\Soal;
use App\Services\GamificationService;
use App\Services\ProgresService;
use App\Services\QuizScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    public function __construct(
        private readonly QuizScoringService $scoring,
        private readonly GamificationService $gamification,
        private readonly ProgresService $progres,
    ) {}

    /**
     * Jawab satu butir soal: nilai, beri EXP, perbarui streak (FR-3..FR-8, FR-10).
     */
    public function jawab(Request $request): JsonResponse
    {
        $data = $request->validate([
            'soal_id' => ['required', 'integer', 'exists:soal,id'],
            'jawaban' => ['present'],
        ]);

        $siswa = $request->user();
        $soal = Soal::query()->with('levelMateri')->findOrFail($data['soal_id']);

        if (! $this->progres->canStart($siswa, $soal->levelMateri)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        $hasil = $this->scoring->score($soal, $data['jawaban']);

        $expDidapat = (int) round($soal->bobot_exp * ($hasil['skor'] / 100));
        $gamifikasi = $this->gamification->recordActivity($siswa, $expDidapat);

        return response()->json([
            'message' => $hasil['benar'] ? 'Jawaban benar!' : 'Jawaban belum tepat.',
            'benar' => $hasil['benar'],
            'skor' => $hasil['skor'],
            'detail' => $hasil['detail'],
            'kunci_jawaban' => $soal->kunci_jawaban,
            'exp_didapat' => $expDidapat,
            'total_exp' => $gamifikasi['total_exp'],
            'current_streak' => $gamifikasi['current_streak'],
            'highest_streak' => $gamifikasi['highest_streak'],
        ]);
    }

    /**
     * Selesaikan level: beri reward_exp dan buka level berikutnya (FR-2, FR-10).
     */
    public function selesai(Request $request): JsonResponse
    {
        $data = $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
        ]);

        $siswa = $request->user();
        $level = LevelMateri::query()->findOrFail($data['level_materi_id']);

        $this->progres->markSelesai($siswa, $level);
        $gamifikasi = $this->gamification->recordActivity($siswa, 0, (int) $level->reward_exp);

        return response()->json([
            'message' => "Level {$level->nama_materi} selesai.",
            'reward_exp' => (int) $level->reward_exp,
            'total_exp' => $gamifikasi['total_exp'],
            'current_streak' => $gamifikasi['current_streak'],
        ]);
    }
}
