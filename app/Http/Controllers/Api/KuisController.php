<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Soal;
use App\Services\GamificationService;
use App\Services\JawabanService;
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
        private readonly JawabanService $jawaban,
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

        if ($soal->levelMateri && ! $this->progres->canStart($siswa, $soal->levelMateri)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        $hasil = $this->scoring->score($soal, $data['jawaban']);
        $catatan = $this->jawaban->record($siswa, $soal, $hasil);

        return response()->json([
            'message' => $hasil['benar'] ? 'Jawaban benar!' : 'Jawaban belum tepat.',
            'benar' => $hasil['benar'],
            'skor' => $hasil['skor'],
            'skor_tertinggi' => $catatan['skor_tertinggi'],
            'detail' => $hasil['detail'],
            'kunci_jawaban' => $soal->kunci_jawaban,
            'exp_didapat' => $catatan['exp_didapat'],
            'reward_exp' => $catatan['reward_exp'],
            'level_selesai' => $catatan['level_selesai'],
            'level_berikutnya' => $catatan['level_berikutnya'],
            'total_exp' => $catatan['total_exp'],
            'current_streak' => $catatan['current_streak'],
            'highest_streak' => $catatan['highest_streak'],
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

        if (! $this->progres->canStart($siswa, $level)) {
            return response()->json(['message' => 'Materi belum tercapai.'], 403);
        }

        if ($this->progres->statusFor($siswa, $level) === ProgresSiswa::STATUS_SELESAI) {
            return response()->json([
                'message' => "Level {$level->nama_materi} sudah diselesaikan.",
                'reward_exp' => 0,
                'total_exp' => (int) ($siswa->exp()->value('total_exp') ?? 0),
                'current_streak' => (int) ($siswa->strek()->value('current_streak') ?? 0),
            ]);
        }

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
