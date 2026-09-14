<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestResource;
use App\Models\Guru;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Daftar test/paket latihan (FR-23).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Test::query()->with('levelMateri')->withCount('soal');

        if ($request->boolean('milik_saya') && $user instanceof Guru) {
            $query->where('guru_id', $user->id);
        }

        if ($request->filled('level_materi_id')) {
            $query->where('level_materi_id', $request->integer('level_materi_id'));
        }

        return TestResource::collection($query->latest()->paginate(50));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $user = $request->user();

        $test = DB::transaction(function () use ($data, $user) {
            $test = Test::create([
                'level_materi_id' => $data['level_materi_id'],
                'nama_test' => $data['nama_test'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'guru_id' => $user instanceof Guru ? $user->id : null,
                'superadmin_id' => $user instanceof Superadmin ? $user->id : null,
            ]);

            $this->syncSoal($test, $data['soal_ids'] ?? []);

            return $test;
        });

        return (new TestResource($test->load('levelMateri', 'soal')))
            ->additional(['message' => 'Test berhasil dibuat.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Test $test): TestResource
    {
        return new TestResource($test->load('levelMateri', 'soal'));
    }

    public function update(Request $request, Test $test): JsonResponse
    {
        $this->authorizeManage($request, $test);

        $data = $this->validated($request);

        DB::transaction(function () use ($test, $data) {
            $test->update([
                'level_materi_id' => $data['level_materi_id'],
                'nama_test' => $data['nama_test'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            $this->syncSoal($test, $data['soal_ids'] ?? []);
        });

        return (new TestResource($test->fresh()->load('levelMateri', 'soal')))
            ->additional(['message' => 'Test berhasil diperbarui.'])
            ->response();
    }

    public function destroy(Request $request, Test $test): JsonResponse
    {
        $this->authorizeManage($request, $test);

        $test->delete();

        return response()->json(['message' => 'Test berhasil dihapus.']);
    }

    /**
     * Guru hanya boleh mengelola test buatannya; superadmin bebas.
     */
    private function authorizeManage(Request $request, Test $test): void
    {
        $user = $request->user();

        if ($user instanceof Superadmin) {
            return;
        }

        abort_unless($user instanceof Guru && $test->guru_id === $user->id, 403, 'Akses ditolak.');
    }

    /**
     * Sinkronkan komposisi soal (boleh campur tipe_soal — PRD v1.7).
     *
     * @param  array<int, int>  $soalIds
     */
    private function syncSoal(Test $test, array $soalIds): void
    {
        $pivot = [];
        foreach (array_values($soalIds) as $index => $soalId) {
            $pivot[$soalId] = ['urutan' => $index + 1];
        }

        $test->soal()->sync($pivot);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
            'nama_test' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'soal_ids' => ['nullable', 'array'],
            'soal_ids.*' => ['integer', 'exists:soal,id'],
        ]);
    }
}
