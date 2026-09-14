<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuruDashboardController extends Controller
{
    /**
     * Daftar siswa yang dipantau guru — HANYA pada kelas/mapel yang ia ampu
     * (FR-11 & keputusan PRD §6A/§10.1).
     */
    public function siswa(Request $request): JsonResponse
    {
        $guru = $request->user();
        $totalLevel = LevelMateri::count();

        $query = $guru->siswa()
            ->with(['exp', 'strek', 'progres'])
            ->withPivot(['kelas', 'mata_pelajaran']);

        if ($request->filled('kelas')) {
            $query->wherePivot('kelas', $request->query('kelas'));
        }

        if ($request->filled('nis')) {
            $query->where('siswa.nis', 'like', '%'.$request->query('nis').'%');
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('siswa.nama_lengkap', 'like', $term)->orWhere('siswa.nis', 'like', $term));
        }

        $paginator = $query->paginate(30);

        $data = collect($paginator->items())->map(fn (Siswa $siswa): array => [
            'siswa_id' => $siswa->id,
            'nis' => $siswa->nis,
            'nama_lengkap' => $siswa->nama_lengkap,
            'kelas' => $siswa->pivot->kelas,
            'mata_pelajaran' => $siswa->pivot->mata_pelajaran,
            'total_exp' => (int) ($siswa->exp?->total_exp ?? 0),
            'current_streak' => (int) ($siswa->strek?->current_streak ?? 0),
            'highest_streak' => (int) ($siswa->strek?->highest_streak ?? 0),
            'level_selesai' => $siswa->progres->where('status', ProgresSiswa::STATUS_SELESAI)->count(),
            'total_level' => $totalLevel,
            'aktivitas_terakhir' => $siswa->strek?->last_activity_date,
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Detail progres seorang siswa — diblokir bila bukan siswa ampu guru.
     */
    public function progresSiswa(Request $request, Siswa $siswa): JsonResponse
    {
        $guru = $request->user();

        $pivot = $guru->siswa()->where('siswa.id', $siswa->id)->first();

        abort_unless($pivot, 403, 'Siswa ini tidak berada dalam kelas/mata pelajaran yang Anda ampu.');

        return response()->json([
            'siswa' => $siswa->only(['id', 'nis', 'nama_lengkap', 'kelas']),
            'kelas_ampu' => $pivot->pivot->kelas,
            'mata_pelajaran' => $pivot->pivot->mata_pelajaran,
            'ringkasan' => [
                'total_exp' => (int) ($siswa->exp()->value('total_exp') ?? 0),
                'current_streak' => (int) ($siswa->strek()->value('current_streak') ?? 0),
                'highest_streak' => (int) ($siswa->strek()->value('highest_streak') ?? 0),
            ],
            'progres' => $siswa->progres()->with('levelMateri')->get()->map(fn (ProgresSiswa $p): array => [
                'level_materi_id' => $p->level_materi_id,
                'nama_materi' => $p->levelMateri?->nama_materi,
                'status' => $p->status,
                'tanggal_selesai' => $p->tanggal_selesai,
            ]),
        ]);
    }
}
