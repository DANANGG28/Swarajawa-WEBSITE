<?php

namespace App\Services;

use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;

/**
 * Pencatatan jawaban siswa + EXP bertingkat (hanya selisih skor tertinggi).
 *
 * Dipakai bersama oleh sesi kuis web (KuisSesiController) dan REST API
 * (Api\KuisController) agar aturan penilaian konsisten di web & mobile.
 */
class JawabanService
{
    public function __construct(
        private readonly GamificationService $gamification,
        private readonly ProgresService $progres,
    ) {}

    /**
     * Catat jawaban satu soal, hitung EXP selisih, cek penyelesaian level.
     *
     * @param  array{benar: bool, skor: int, detail: array<string, mixed>}  $hasil
     * @return array<string, mixed>
     */
    public function record(Siswa $siswa, Soal $soal, array $hasil): array
    {
        $levelMateri = $soal->levelMateri;

        $jawaban = JawabanSiswa::firstOrNew([
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
        ]);

        $skorLama = $jawaban->exists ? (int) $jawaban->skor_tertinggi : 0;
        $expLama = $jawaban->exists ? (int) $jawaban->exp_diberikan : 0;
        $skorBaru = (int) $hasil['skor'];

        $skorTertinggi = max($skorLama, $skorBaru);
        $targetExp = (int) round($soal->bobot_exp * ($skorTertinggi / 100));
        $expDidapat = max(0, $targetExp - $expLama);

        $jawaban->skor_tertinggi = $skorTertinggi;
        $jawaban->exp_diberikan = $expLama + $expDidapat;
        $jawaban->jumlah_percobaan = ($jawaban->jumlah_percobaan ?? 0) + 1;
        $jawaban->save();

        $levelSelesai = false;
        $rewardExp = 0;
        $nextLevel = null;

        if ($levelMateri) {
            $totalSoalLevel = Soal::where('level_materi_id', $levelMateri->id)->count();
            $soalIdsLevel = Soal::where('level_materi_id', $levelMateri->id)->pluck('id');
            $lulusCount = JawabanSiswa::where('siswa_id', $siswa->id)
                ->whereIn('soal_id', $soalIdsLevel)
                ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
                ->count();

            if ($totalSoalLevel > 0 && $lulusCount >= $totalSoalLevel) {
                $progresLevel = ProgresSiswa::where('siswa_id', $siswa->id)
                    ->where('level_materi_id', $levelMateri->id)
                    ->first();

                if ($progresLevel && $progresLevel->status !== ProgresSiswa::STATUS_SELESAI) {
                    $this->progres->markSelesai($siswa, $levelMateri);
                    $levelSelesai = true;
                    $rewardExp = (int) $levelMateri->reward_exp;
                    $nextLevel = LevelMateri::where('urutan', '>', $levelMateri->urutan)
                        ->orderBy('urutan')
                        ->first();
                }
            }
        }

        $gamifikasi = $this->gamification->recordActivity($siswa, $expDidapat, $rewardExp);

        return [
            'skor_tertinggi' => $skorTertinggi,
            'exp_didapat' => $expDidapat,
            'reward_exp' => $rewardExp,
            'level_selesai' => $levelSelesai,
            'level_berikutnya' => $nextLevel?->nama_materi,
            'level_materi_id' => $levelMateri?->id,
            'total_exp' => $gamifikasi['total_exp'],
            'current_streak' => $gamifikasi['current_streak'],
            'highest_streak' => $gamifikasi['highest_streak'],
        ];
    }

    /**
     * Soal pertama ing level sing durung tuntas (skor < 100).
     */
    public function firstUnfinishedInLevel(Siswa $siswa, LevelMateri $levelMateri): ?Soal
    {
        $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id');

        return Soal::where('level_materi_id', $levelMateri->id)
            ->whereNotIn('id', $selesaiSoalIds)
            ->orderBy('id')
            ->first();
    }

    /**
     * Soal pertama ing pembahasan sing durung tuntas (skor < 100).
     */
    public function firstUnfinishedInPembahasan(Siswa $siswa, Pembahasan $pembahasan): ?Soal
    {
        $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id');

        return Soal::where('pembahasan_id', $pembahasan->id)
            ->whereNotIn('id', $selesaiSoalIds)
            ->orderBy('id')
            ->first();
    }

    /**
     * Soal berikutnya dalam level (utawa pembahasan) yang belum selesai 100%.
     */
    public function nextSoal(Siswa $siswa, ?LevelMateri $levelMateri, int $excludeSoalId, ?int $pembahasanId = null): ?Soal
    {
        if (! $levelMateri) {
            return null;
        }

        $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id');

        return Soal::where('level_materi_id', $levelMateri->id)
            ->when($pembahasanId, fn ($q) => $q->where('pembahasan_id', $pembahasanId))
            ->where('id', '!=', $excludeSoalId)
            ->whereNotIn('id', $selesaiSoalIds)
            ->orderBy('id')
            ->first();
    }
}
