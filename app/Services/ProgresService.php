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

        if ($levels->isEmpty()) {
            return;
        }

        $now = now();
        $rows = $levels->values()->map(fn (LevelMateri $level, int $index): array => [
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => $index === 0 ? ProgresSiswa::STATUS_BERJALAN : ProgresSiswa::STATUS_TERKUNCI,
            'tanggal_selesai' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::transaction(function () use ($rows) {
            // Padanan updateOrCreate: baris baru disisipkan, baris lama hanya diperbarui
            // pada status/updated_at (tanggal_selesai tidak diusik).
            ProgresSiswa::query()->upsert($rows, ['siswa_id', 'level_materi_id'], ['status', 'updated_at']);
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
            $nextProgres = ProgresSiswa::firstOrNew([
                'siswa_id' => $siswa->id,
                'level_materi_id' => $next->id,
            ]);

            if (! $nextProgres->exists || $nextProgres->status === ProgresSiswa::STATUS_TERKUNCI) {
                $nextProgres->status = ProgresSiswa::STATUS_BERJALAN;
                $nextProgres->save();
            }
        }

        return $progres;
    }
}
