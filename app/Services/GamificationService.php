<?php

namespace App\Services;

use App\Models\Exp;
use App\Models\Siswa;
use App\Models\Strek;

/**
 * Gamifikasi: EXP, streak harian, dan leaderboard — FR-10.
 */
class GamificationService
{
    /**
     * Catat aktivitas belajar: tambah EXP dan perbarui streak harian.
     *
     * Aturan streak: +1 bila aktivitas terakhir kemarin, reset ke 1 bila
     * bolong lebih dari 24 jam, dan tidak berubah bila sudah aktif hari ini.
     *
     * @return array{total_exp: int, current_streak: int, highest_streak: int}
     */
    public function recordActivity(Siswa $siswa, int $expGained = 0, int $rewardExp = 0): array
    {
        $exp = Exp::firstOrCreate(['siswa_id' => $siswa->id], ['total_exp' => 0]);
        $exp->total_exp += ($expGained + $rewardExp);
        $exp->save();

        $strek = Strek::firstOrCreate(
            ['siswa_id' => $siswa->id],
            ['current_streak' => 0, 'highest_streak' => 0],
        );

        $today = now()->startOfDay();
        $last = $strek->last_activity_date?->copy()->startOfDay();

        if ($last === null) {
            $strek->current_streak = 1;
        } elseif ($last->equalTo($today)) {
            // Sudah aktif hari ini — streak tidak bertambah lagi.
        } elseif ($last->equalTo($today->copy()->subDay())) {
            $strek->current_streak += 1;
        } else {
            $strek->current_streak = 1;
        }

        $strek->last_activity_date = $today;
        $strek->highest_streak = max($strek->highest_streak, $strek->current_streak);
        $strek->save();

        return [
            'total_exp' => (int) $exp->total_exp,
            'current_streak' => (int) $strek->current_streak,
            'highest_streak' => (int) $strek->highest_streak,
        ];
    }

    public function addExp(Siswa $siswa, int $amount): Exp
    {
        $exp = Exp::firstOrCreate(['siswa_id' => $siswa->id], ['total_exp' => 0]);
        $exp->total_exp = max(0, $exp->total_exp + $amount);
        $exp->save();

        return $exp;
    }

    /**
     * Reset streak yang sudah kedaluwarsa (>24 jam tanpa aktivitas).
     */
    public function syncStreak(Siswa $siswa): Strek
    {
        $strek = Strek::firstOrCreate(
            ['siswa_id' => $siswa->id],
            ['current_streak' => 0, 'highest_streak' => 0],
        );

        $last = $strek->last_activity_date?->copy()->startOfDay();

        if ($last !== null && $last->lt(now()->startOfDay()->subDay())) {
            $strek->current_streak = 0;
            $strek->save();
        }

        return $strek;
    }

    /**
     * @return array<int, array{peringkat: int, siswa_id: int, nama: string, kelas: ?string, total_exp: int}>
     */
    public function leaderboard(?string $kelas = null, int $limit = 20): array
    {
        $query = Exp::query()
            ->with('siswa')
            ->orderByDesc('total_exp');

        if ($kelas) {
            $query->whereHas('siswa', fn ($q) => $q->where('kelas', $kelas));
        }

        return $query->limit($limit)->get()->values()->map(fn (Exp $exp, int $index): array => [
            'peringkat' => $index + 1,
            'siswa_id' => $exp->siswa_id,
            'nama' => $exp->siswa?->nama_lengkap ?? '-',
            'kelas' => $exp->siswa?->kelas,
            'total_exp' => (int) $exp->total_exp,
        ])->all();
    }
}
