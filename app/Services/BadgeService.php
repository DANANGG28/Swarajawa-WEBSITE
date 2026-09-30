<?php

namespace App\Services;

use App\Models\Exp;
use App\Models\JawabanSiswa;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Strek;

class BadgeService
{
    /**
     * Ambil seluruh lencana dan evaluasi status perolehannya untuk siswa.
     *
     * @return array{
     *     all: array<int, array<string, mixed>>,
     *     earned: array<int, array<string, mixed>>,
     *     locked: array<int, array<string, mixed>>,
     *     total_count: int,
     *     earned_count: int,
     *     completion_percentage: int
     * }
     */
    public function getBadgesForSiswa(Siswa $siswa): array
    {
        // 1. Ambil seluruh data riwayat pengerjaan siswa
        $jawabanList = JawabanSiswa::with('soal.levelMateri')
            ->where('siswa_id', $siswa->id)
            ->get();

        $jawabanLulus = $jawabanList->where('skor_tertinggi', '>=', QuizScoringService::PASS_THRESHOLD);

        // Hitung total kuis lulus dan total EXP per tipe soal
        $tipeCounts = [];
        $tipeExps = [];
        foreach ($jawabanLulus as $jawaban) {
            $tipe = $jawaban->soal?->tipe_soal;
            if ($tipe) {
                $tipeCounts[$tipe] = ($tipeCounts[$tipe] ?? 0) + 1;
                $tipeExps[$tipe] = ($tipeExps[$tipe] ?? 0) + (int) $jawaban->exp_diberikan;
            }
        }

        // Statistik kuis sempurna (skor >= 90)
        $perfectScoresCount = $jawabanList->where('skor_tertinggi', '>=', 90)->count();

        // Total EXP keseluruhan
        $totalExp = (int) ($siswa->exp()->value('total_exp') ?? 0);

        // Streak
        $strek = $siswa->strek()->first();
        $currentStreak = (int) ($strek?->current_streak ?? 0);
        $highestStreak = (int) ($strek?->highest_streak ?? 0);
        $effectiveStreak = max($currentStreak, $highestStreak);

        // Level selesai
        $completedLevelsCount = ProgresSiswa::where('siswa_id', $siswa->id)
            ->where('status', ProgresSiswa::STATUS_SELESAI)
            ->count();

        // Level Aksara Jawa selesai
        $aksaraLevelCompleted = ProgresSiswa::where('siswa_id', $siswa->id)
            ->where('status', ProgresSiswa::STATUS_SELESAI)
            ->whereHas('levelMateri', fn ($q) => $q->where('nama_materi', 'like', '%Aksara%'))
            ->exists();

        // 2. Daftar Definisi Lencana
        $badgeDefinitions = $this->getBadgeDefinitions();

        $evaluatedBadges = [];
        foreach ($badgeDefinitions as $def) {
            $badge = $this->evaluateSingleBadge($def, [
                'tipeCounts' => $tipeCounts,
                'tipeExps' => $tipeExps,
                'perfectScoresCount' => $perfectScoresCount,
                'totalExp' => $totalExp,
                'effectiveStreak' => $effectiveStreak,
                'completedLevelsCount' => $completedLevelsCount,
                'aksaraLevelCompleted' => $aksaraLevelCompleted,
            ]);

            $evaluatedBadges[] = $badge;
        }

        $earned = array_values(array_filter($evaluatedBadges, fn ($b) => $b['is_unlocked']));
        $locked = array_values(array_filter($evaluatedBadges, fn ($b) => ! $b['is_unlocked']));

        $totalCount = count($evaluatedBadges);
        $earnedCount = count($earned);
        $completionPercentage = $totalCount > 0 ? (int) round(($earnedCount / $totalCount) * 100) : 0;

        return [
            'all' => $evaluatedBadges,
            'earned' => $earned,
            'locked' => $locked,
            'total_count' => $totalCount,
            'earned_count' => $earnedCount,
            'completion_percentage' => $completionPercentage,
        ];
    }

    /**
     * Definisi katalog seluruh lencana sistem Sinau Jowo.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBadgeDefinitions(): array
    {
        return [
            [
                'id' => 'wicara_prigel',
                'nama' => 'Wicara Prigel',
                'kategori' => 'WICARA AUDIO',
                'deskripsi' => 'Latihan pelafalan suara dan wicara Jawa dengan evaluasi kecerdasan buatan.',
                'syarat_deskripsi' => 'Selesaikan minimal 3 Kuis Wicara & raih minimal 50 EXP wicara.',
                'shape' => 'clip-octagon',
                'bg_color' => '#7B6CF0',
                'text_color' => 'text-white',
                'icon' => 'mic',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_KUIS_SUARA,
                'target_soal' => 3,
                'target_exp' => 50,
                'stat_label' => 'Evaluasi Suara AI',
            ],
            [
                'id' => 'jawara_hanacaraka',
                'nama' => 'Jawara Hanacaraka',
                'kategori' => 'AKSARA JAWA',
                'deskripsi' => 'Sukses menulis dan menghafal aksara nglegena dasar dengan presisi tracing tinggi.',
                'syarat_deskripsi' => 'Selesaikan minimal 3 latihan tracing aksara & raih minimal 50 EXP aksara.',
                'shape' => 'clip-hexagon',
                'bg_color' => '#F6D98B',
                'text_color' => 'text-amber-950',
                'icon' => 'history_edu',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
                'target_soal' => 3,
                'target_exp' => 50,
                'stat_label' => 'Akurasi Tracing',
            ],
            [
                'id' => 'tatas_unggah_ungguh',
                'nama' => 'Tatas Unggah-Ungguh',
                'kategori' => 'TATA KRAMA',
                'deskripsi' => 'Menuntaskan kuis pemahaman tata krama Ngoko, Krama Lugu, dan Krama Inggil.',
                'syarat_deskripsi' => 'Selesaikan minimal 5 kuis pilihan ganda tata krama & raih minimal 50 EXP.',
                'shape' => 'clip-pentagon',
                'bg_color' => '#4CAF6D',
                'text_color' => 'text-white',
                'icon' => 'verified',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
                'target_soal' => 5,
                'target_exp' => 50,
                'stat_label' => 'Pilihan Ganda Krama',
            ],
            [
                'id' => 'prajurit_sandhangan',
                'nama' => 'Pujangga Susun Ukara',
                'kategori' => 'SUSUN UKARA',
                'deskripsi' => 'Menyusun kalimat acak bahasa Jawa menjadi kalimat yang runut dan benar.',
                'syarat_deskripsi' => 'Selesaikan minimal 3 kuis susun ukara & raih minimal 40 EXP.',
                'shape' => 'clip-shield',
                'bg_color' => '#6C5CE8',
                'text_color' => 'text-white',
                'icon' => 'format_list_numbered',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_SUSUN_KALIMAT,
                'target_soal' => 3,
                'target_exp' => 40,
                'stat_label' => 'Kuis Susun Kalimat',
            ],
            [
                'id' => 'busana_gagrag_anyar',
                'nama' => 'Busana Gagrag Anyar',
                'kategori' => 'BUDAYA JAWA',
                'deskripsi' => 'Menyelesaikan tebak budaya dan puzzle busana adat Jawa: Jarik, Beskap, dan Blangkon.',
                'syarat_deskripsi' => 'Selesaikan minimal 1 kuis puzzle budaya adat Jawa & raih minimal 20 EXP.',
                'shape' => 'clip-rhombus',
                'bg_color' => '#F0955A',
                'text_color' => 'text-white',
                'icon' => 'theater_comedy',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_PUZZLE_PAKAIAN_ADAT,
                'target_soal' => 1,
                'target_exp' => 20,
                'stat_label' => 'Puzzle Adat Jawa',
            ],
            [
                'id' => 'empu_wicara_krama',
                'nama' => 'Empu Wicara Krama',
                'kategori' => 'WICARA LANJUTAN',
                'deskripsi' => 'Pakar pelafalan percakapan Krama Inggil dengan akurasi pengucapan tinggi.',
                'syarat_deskripsi' => 'Selesaikan minimal 6 kuis wicara audio & raih minimal 100 EXP wicara.',
                'shape' => 'clip-octagon',
                'bg_color' => '#F08CA0',
                'text_color' => 'text-white',
                'icon' => 'record_voice_over',
                'type' => 'tipe_soal',
                'tipe_soal' => Soal::TIPE_KUIS_SUARA,
                'target_soal' => 6,
                'target_exp' => 100,
                'stat_label' => 'Wicara Krama Lanjut',
            ],
            [
                'id' => 'empu_aksara_swara',
                'nama' => 'Empu Aksara & Swara',
                'kategori' => 'AKSARA LANJUTAN',
                'deskripsi' => 'Kuasai penulisan aksara legena, sandhangan swara, pasangan, dan aksara murda.',
                'syarat_deskripsi' => 'Selesaikan minimal 5 kuis aksara & tuntaskan Level Aksara Jawa.',
                'shape' => 'clip-hexagon',
                'bg_color' => '#0D9488',
                'text_color' => 'text-white',
                'icon' => 'auto_stories',
                'type' => 'aksara_lanjutan',
                'target_soal' => 5,
                'stat_label' => 'Aksara & Sandhangan',
            ],
            [
                'id' => 'pujangga_paribasan',
                'nama' => 'Pujangga Paribasan',
                'kategori' => 'PARIBASAN & SASTRA',
                'deskripsi' => 'Menyelesaikan kuis peribahasa, bebasan, dan saloka dengan nilai akurasi sempurna.',
                'syarat_deskripsi' => 'Selesaikan minimal 5 kuis dengan nilai skor tinggi (>= 90).',
                'shape' => 'clip-pentagon',
                'bg_color' => '#3B82F6',
                'text_color' => 'text-white',
                'icon' => 'psychology',
                'type' => 'perfect_scores',
                'target_soal' => 5,
                'stat_label' => 'Kuis Skor Sempurna',
            ],
            [
                'id' => 'gathutkaca_streak_master',
                'nama' => 'Gathutkaca Streak Master',
                'kategori' => 'KONSISTENSI',
                'deskripsi' => 'Pertahankan konsistensi belajar aktif tanpa henti selama minimal 3 hari.',
                'syarat_deskripsi' => 'Raih streak belajar aktif minimal 3 hari berturut-turut.',
                'shape' => 'clip-shield',
                'bg_color' => '#EAB308',
                'text_color' => 'text-amber-950',
                'icon' => 'local_fire_department',
                'type' => 'streak',
                'target_streak' => 3,
                'stat_label' => 'Streak Konsistensi',
            ],
            [
                'id' => 'wasasis_utama',
                'nama' => 'Wasasis Utama',
                'kategori' => 'POIN EXP',
                'deskripsi' => 'Kumpulkan poin pengalaman belajar (EXP) dari seluruh rangkaian materi kuis.',
                'syarat_deskripsi' => 'Kumpulkan total minimal 250 EXP pembelajaran.',
                'shape' => 'clip-rhombus',
                'bg_color' => '#5443c9',
                'text_color' => 'text-white',
                'icon' => 'stars',
                'type' => 'total_exp',
                'target_exp' => 250,
                'stat_label' => 'Total EXP Siswa',
            ],
            [
                'id' => 'narendra_jagad_jawa',
                'nama' => 'Narendra Sinau Jowo',
                'kategori' => 'PENJELAJAH MATERI',
                'deskripsi' => 'Menjelajahi dan menuntaskan unit materi pembelajaran tingkat dasar hingga lanjut.',
                'syarat_deskripsi' => 'Selesaikan minimal 3 Level Materi pembelajaran.',
                'shape' => 'clip-octagon',
                'bg_color' => '#059669',
                'text_color' => 'text-white',
                'icon' => 'flag',
                'type' => 'completed_levels',
                'target_levels' => 3,
                'stat_label' => 'Level Selesai',
            ],
        ];
    }

    /**
     * Evaluasi satu lencana terhadap statistik siswa.
     *
     * @param  array<string, mixed>  $def
     * @param  array<string, mixed>  $stats
     * @return array<string, mixed>
     */
    private function evaluateSingleBadge(array $def, array $stats): array
    {
        $type = $def['type'] ?? 'tipe_soal';
        $isUnlocked = false;
        $currentSoal = 0;
        $currentExp = 0;
        $targetSoal = (int) ($def['target_soal'] ?? 0);
        $targetExp = (int) ($def['target_exp'] ?? 0);
        $progressPercent = 0;
        $syaratText = $def['syarat_deskripsi'];
        $earnedStatLeft = '';
        $earnedStatRight = '';

        if ($type === 'tipe_soal') {
            $tipeSoal = $def['tipe_soal'];
            $currentSoal = (int) ($stats['tipeCounts'][$tipeSoal] ?? 0);
            $currentExp = (int) ($stats['tipeExps'][$tipeSoal] ?? 0);

            $soalSatisfied = $currentSoal >= $targetSoal;
            $expSatisfied = $targetExp === 0 || $currentExp >= $targetExp;
            $isUnlocked = $soalSatisfied && $expSatisfied;

            $soalProgress = $targetSoal > 0 ? min(1.0, $currentSoal / $targetSoal) : 1.0;
            $expProgress = $targetExp > 0 ? min(1.0, $currentExp / $targetExp) : 1.0;
            $progressPercent = (int) round((($soalProgress + $expProgress) / 2) * 100);

            if (! $isUnlocked) {
                $sisaSoal = max(0, $targetSoal - $currentSoal);
                $sisaExp = max(0, $targetExp - $currentExp);
                if ($sisaSoal > 0 && $sisaExp > 0) {
                    $syaratText = "Syarat: {$sisaSoal} soal lagi (Saat ini {$currentSoal}/{$targetSoal}) & {$sisaExp} XP lagi";
                } elseif ($sisaSoal > 0) {
                    $syaratText = "Syarat: {$sisaSoal} soal lagi (Saat ini {$currentSoal}/{$targetSoal})";
                } elseif ($sisaExp > 0) {
                    $syaratText = "Syarat: {$sisaExp} XP lagi (Saat ini {$currentExp}/{$targetExp} XP)";
                }
            } else {
                $earnedStatLeft = "{$def['stat_label']}: {$currentSoal} Soal";
                $earnedStatRight = '';
            }
        } elseif ($type === 'aksara_lanjutan') {
            $currentSoal = (int) ($stats['tipeCounts'][Soal::TIPE_MENULIS_AKSARA] ?? 0);
            $aksaraDone = (bool) $stats['aksaraLevelCompleted'];
            $isUnlocked = $currentSoal >= $targetSoal && $aksaraDone;

            $soalProgress = $targetSoal > 0 ? min(1.0, $currentSoal / $targetSoal) : 1.0;
            $levelProgress = $aksaraDone ? 1.0 : 0.0;
            $progressPercent = (int) round((($soalProgress + $levelProgress) / 2) * 100);

            if (! $isUnlocked) {
                if ($currentSoal < $targetSoal && ! $aksaraDone) {
                    $syaratText = "Syarat: ".($targetSoal - $currentSoal)." soal aksara lagi & tuntaskan Level Aksara";
                } elseif ($currentSoal < $targetSoal) {
                    $syaratText = "Syarat: ".($targetSoal - $currentSoal)." soal aksara lagi (Saat ini {$currentSoal}/{$targetSoal})";
                } else {
                    $syaratText = "Syarat: Tuntaskan Level Aksara Jawa Terlebih Dahulu";
                }
            } else {
                $earnedStatLeft = "Aksara Jawa: Selesai";
                $earnedStatRight = '';
            }
        } elseif ($type === 'perfect_scores') {
            $currentSoal = (int) $stats['perfectScoresCount'];
            $isUnlocked = $currentSoal >= $targetSoal;
            $progressPercent = $targetSoal > 0 ? min(100, (int) round(($currentSoal / $targetSoal) * 100)) : 100;

            if (! $isUnlocked) {
                $sisa = $targetSoal - $currentSoal;
                $syaratText = "Syarat: {$sisa} kuis skor >= 90 lagi (Saat ini {$currentSoal}/{$targetSoal})";
            } else {
                $earnedStatLeft = "Kuis Skor >= 90: {$currentSoal} Soal";
                $earnedStatRight = '';
            }
        } elseif ($type === 'streak') {
            $targetStreak = (int) ($def['target_streak'] ?? 3);
            $currentStreak = (int) $stats['effectiveStreak'];
            $isUnlocked = $currentStreak >= $targetStreak;
            $progressPercent = $targetStreak > 0 ? min(100, (int) round(($currentStreak / $targetStreak) * 100)) : 100;

            if (! $isUnlocked) {
                $sisa = max(1, $targetStreak - $currentStreak);
                $syaratText = "Syarat: {$sisa} hari lagi (Saat ini {$currentStreak}/{$targetStreak} hari)";
            } else {
                $earnedStatLeft = "Streak: {$currentStreak} Hari Aktif";
                $earnedStatRight = '';
            }
        } elseif ($type === 'total_exp') {
            $targetExp = (int) ($def['target_exp'] ?? 250);
            $currentExp = (int) $stats['totalExp'];
            $isUnlocked = $currentExp >= $targetExp;
            $progressPercent = $targetExp > 0 ? min(100, (int) round(($currentExp / $targetExp) * 100)) : 100;

            if (! $isUnlocked) {
                $sisa = max(0, $targetExp - $currentExp);
                $syaratText = "Syarat: {$sisa} XP lagi (Saat ini {$currentExp}/{$targetExp} XP)";
            } else {
                $earnedStatLeft = "Total EXP: {$currentExp}";
                $earnedStatRight = '';
            }
        } elseif ($type === 'completed_levels') {
            $targetLevels = (int) ($def['target_levels'] ?? 3);
            $currentLevels = (int) $stats['completedLevelsCount'];
            $isUnlocked = $currentLevels >= $targetLevels;
            $progressPercent = $targetLevels > 0 ? min(100, (int) round(($currentLevels / $targetLevels) * 100)) : 100;

            if (! $isUnlocked) {
                $sisa = max(0, $targetLevels - $currentLevels);
                $syaratText = "Syarat: {$sisa} level lagi (Saat ini {$currentLevels}/{$targetLevels} level)";
            } else {
                $earnedStatLeft = "Level Pembelajaran";
                $earnedStatRight = "{$currentLevels} Level Selesai";
            }
        }

        if ($isUnlocked) {
            $progressPercent = 100;
        }

        return array_merge($def, [
            'is_unlocked' => $isUnlocked,
            'current_soal' => $currentSoal,
            'current_exp' => $currentExp,
            'progress_percent' => $progressPercent,
            'syarat_text' => $syaratText,
            'earned_stat_left' => $earnedStatLeft,
            'earned_stat_right' => $earnedStatRight,
        ]);
    }
}
