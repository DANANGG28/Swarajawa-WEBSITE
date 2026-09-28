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
            // Unit selesai jika semua soal pada level ini sudah pernah dikerjakan minimal 1x
            $dikerjakanCount = JawabanSiswa::where('siswa_id', $siswa->id)
                ->whereIn('soal_id', $soalIdsLevel)
                ->count();

            if ($totalSoalLevel > 0 && $dikerjakanCount >= $totalSoalLevel) {
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
     * Soal berikutnya dalam level (utawa pembahasan) untuk alur kuis yang mulus.
     * Memprioritaskan soal belum tuntas ke depan (forward sequence).
     * Jika tidak ada lagi soal belum tuntas ke depan, kembalikan null agar level selesai/lanjut ke unit berikutnya.
     */
    public function nextSoal(Siswa $siswa, ?LevelMateri $levelMateri, int $excludeSoalId, ?int $pembahasanId = null): ?Soal
    {
        $currentSoal = Soal::find($excludeSoalId);
        $levelMateri = $levelMateri ?? $currentSoal?->levelMateri;

        if (! $levelMateri) {
            return null;
        }

        $pembahasanId = $pembahasanId ?? $currentSoal?->pembahasan_id;

        $selesaiSoalIds = JawabanSiswa::where('siswa_id', $siswa->id)
            ->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD)
            ->pluck('soal_id')
            ->all();

        // 1. Cari soal belum tuntas ke depan di pembahasan yang sama
        if ($pembahasanId) {
            $nextUnfinishedInPembahasan = Soal::where('pembahasan_id', $pembahasanId)
                ->where('id', '>', $excludeSoalId)
                ->whereNotIn('id', $selesaiSoalIds)
                ->orderBy('id')
                ->first();

            if ($nextUnfinishedInPembahasan) {
                return $nextUnfinishedInPembahasan;
            }
        }

        // 2. Cari soal belum tuntas di pembahasan berikutnya dalam level yang sama
        if ($pembahasanId) {
            $currentPembahasan = Pembahasan::find($pembahasanId);
            $currentUrutan = $currentPembahasan?->urutan ?? 0;

            $nextPembahasans = Pembahasan::where('level_materi_id', $levelMateri->id)
                ->when($currentUrutan > 0, fn ($q) => $q->where('urutan', '>', $currentUrutan))
                ->orderBy('urutan')
                ->get();

            foreach ($nextPembahasans as $nextPembahasan) {
                $soalNextPembahasan = Soal::where('pembahasan_id', $nextPembahasan->id)
                    ->whereNotIn('id', $selesaiSoalIds)
                    ->orderBy('id')
                    ->first();

                if ($soalNextPembahasan) {
                    return $soalNextPembahasan;
                }
            }
        }

        // 3. Cari soal belum tuntas ke depan dalam level (jika tanpa pembahasan)
        $nextUnfinishedInLevel = Soal::where('level_materi_id', $levelMateri->id)
            ->where('id', '>', $excludeSoalId)
            ->whereNotIn('id', $selesaiSoalIds)
            ->orderBy('id')
            ->first();

        if ($nextUnfinishedInLevel) {
            return $nextUnfinishedInLevel;
        }

        // 4. Jika semua soal di level ini SUDAH TUNTAS (skor >= 70 untuk seluruh soal)
        // dan siswa sedang dalam mode latihan ulang (replay), lanjutkan urutan sekuensial forward
        $totalSoalLevel = Soal::where('level_materi_id', $levelMateri->id)->count();
        $totalSelesaiLevel = count(array_intersect(
            Soal::where('level_materi_id', $levelMateri->id)->pluck('id')->all(),
            $selesaiSoalIds
        ));

        if ($totalSoalLevel > 0 && $totalSelesaiLevel >= $totalSoalLevel) {
            // Replay mode: maju ke soal berikutnya dalam pembahasan
            if ($pembahasanId) {
                $nextSeqInPembahasan = Soal::where('pembahasan_id', $pembahasanId)
                    ->where('id', '>', $excludeSoalId)
                    ->orderBy('id')
                    ->first();

                if ($nextSeqInPembahasan) {
                    return $nextSeqInPembahasan;
                }

                // Atau ke soal pertama di pembahasan berikutnya
                $currentPembahasan = Pembahasan::find($pembahasanId);
                $currentUrutan = $currentPembahasan?->urutan ?? 0;

                $nextPembahasan = Pembahasan::where('level_materi_id', $levelMateri->id)
                    ->when($currentUrutan > 0, fn ($q) => $q->where('urutan', '>', $currentUrutan))
                    ->orderBy('urutan')
                    ->first();

                if ($nextPembahasan) {
                    $firstSoalNext = Soal::where('pembahasan_id', $nextPembahasan->id)
                        ->orderBy('id')
                        ->first();

                    if ($firstSoalNext) {
                        return $firstSoalNext;
                    }
                }
            }

            // Replay mode: maju ke soal berikutnya dalam level
            $nextSeqInLevel = Soal::where('level_materi_id', $levelMateri->id)
                ->where('id', '>', $excludeSoalId)
                ->orderBy('id')
                ->first();

            if ($nextSeqInLevel) {
                return $nextSeqInLevel;
            }
        }

        // Sudah di ujung rangkaian pengerjaan level -> kembalikan null (level selesai)
        return null;
    }
}
