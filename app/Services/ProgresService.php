<?php

namespace App\Services;

use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

/**
 * Progres siswa pada level materi berjenjang — FR-2 & keputusan PRD §6A.
 */
class ProgresService
{
    /**
     * Inisialisasi progres: level pertama "berjalan", sisanya "terkunci".
     */
    public function initialize(Siswa $siswa): void
    {
        $levels = LevelMateri::query()->orderBy('urutan')->get();

        DB::transaction(function () use ($siswa, $levels) {
            foreach ($levels as $index => $level) {
                ProgresSiswa::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'level_materi_id' => $level->id],
                    ['status' => $index === 0 ? ProgresSiswa::STATUS_BERJALAN : ProgresSiswa::STATUS_TERKUNCI],
                );
            }
        });
    }

    public function statusFor(Siswa $siswa, LevelMateri $level): string
    {
        return ProgresSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->where('level_materi_id', $level->id)
            ->value('status') ?? ProgresSiswa::STATUS_TERKUNCI;
    }

    /**
     * Siswa boleh memulai level bila statusnya berjalan/selesai, bukan terkunci.
     */
    public function canStart(Siswa $siswa, LevelMateri $level): bool
    {
        return $this->statusFor($siswa, $level) !== ProgresSiswa::STATUS_TERKUNCI;
    }

    /**
     * Tandai level selesai, lalu buka level berikutnya (sesuai urutan).
     */
    public function markSelesai(Siswa $siswa, LevelMateri $level): ProgresSiswa
    {
        $progres = ProgresSiswa::updateOrCreate(
            ['siswa_id' => $siswa->id, 'level_materi_id' => $level->id],
            ['status' => ProgresSiswa::STATUS_SELESAI, 'tanggal_selesai' => now()->toDateString()],
        );

        $next = LevelMateri::query()
            ->where('urutan', '>', $level->urutan)
            ->orderBy('urutan')
            ->first();

        if ($next) {
            ProgresSiswa::query()
                ->where('siswa_id', $siswa->id)
                ->where('level_materi_id', $next->id)
                ->where('status', ProgresSiswa::STATUS_TERKUNCI)
                ->update(['status' => ProgresSiswa::STATUS_BERJALAN]);
        }

        return $progres;
    }
}
