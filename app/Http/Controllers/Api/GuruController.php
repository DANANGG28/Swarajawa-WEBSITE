<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Manajemen akun guru oleh superadmin (FR-17).
     */
    public function index(Request $request)
    {
        $query = Guru::query()->withCount('soal');

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('nama_lengkap', 'like', $term)->orWhere('nip', 'like', $term));
        }

        return response()->json($query->latest()->paginate(30));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:30', 'unique:guru,nip'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:guru,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $guru = Guru::create($data);

        return response()->json(['message' => 'Akun guru berhasil dibuat.', 'data' => $guru], 201);
    }

    public function show(Guru $guru): JsonResponse
    {
        return response()->json([
            'data' => $guru->loadCount('soal'),
        ]);
    }

    public function update(Request $request, Guru $guru): JsonResponse
    {
        $data = $request->validate([
            'nip' => ['sometimes', 'string', 'max:30', 'unique:guru,nip,'.$guru->id],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'status_pegawaian' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:guru,email,'.$guru->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $guru->update($data);

        return response()->json(['message' => 'Akun guru berhasil diperbarui.', 'data' => $guru->fresh()]);
    }

    public function destroy(Guru $guru): JsonResponse
    {
        $guru->delete();

        return response()->json(['message' => 'Akun guru berhasil dihapus.']);
    }
}
