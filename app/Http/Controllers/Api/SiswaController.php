<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Services\ProgresService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function __construct(private readonly ProgresService $progres) {}

    /**
     * Manajemen akun siswa oleh superadmin (FR-18).
     */
    public function index(Request $request)
    {
        $query = Siswa::query()->with(['exp', 'strek']);

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->query('kelas'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('nis', 'like', $term));
        }

        return response()->json($query->latest()->paginate(30));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $siswa = Siswa::create($data);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        return response()->json(['message' => 'Akun siswa berhasil dibuat.', 'data' => $siswa], 201);
    }

    public function show(Siswa $siswa): JsonResponse
    {
        return response()->json([
            'data' => $siswa->load(['exp', 'strek', 'progres.levelMateri']),
        ]);
    }

    public function update(Request $request, Siswa $siswa): JsonResponse
    {
        $data = $request->validate([
            'nis' => ['sometimes', 'string', 'max:30', 'unique:siswa,nis,'.$siswa->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:siswa,email,'.$siswa->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $siswa->update($data);

        return response()->json(['message' => 'Akun siswa berhasil diperbarui.', 'data' => $siswa->fresh()]);
    }

    public function destroy(Siswa $siswa): JsonResponse
    {
        $siswa->delete();

        return response()->json(['message' => 'Akun siswa berhasil dihapus.']);
    }
}
