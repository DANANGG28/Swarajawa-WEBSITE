<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelMateriResource;
use App\Models\LevelMateri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LevelMateriController extends Controller
{
    /**
     * Daftar level materi (guru & superadmin) — FR-19.
     */
    public function index()
    {
        return LevelMateriResource::collection(
            LevelMateri::query()->withCount('soal')->orderBy('urutan')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $level = LevelMateri::create($this->validated($request));

        return (new LevelMateriResource($level->loadCount('soal')))
            ->additional(['message' => 'Level materi berhasil dibuat.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(LevelMateri $levelMateri): LevelMateriResource
    {
        return new LevelMateriResource($levelMateri->loadCount('soal'));
    }

    public function update(Request $request, LevelMateri $levelMateri): JsonResponse
    {
        $levelMateri->update($this->validated($request));

        return (new LevelMateriResource($levelMateri->fresh()->loadCount('soal')))
            ->additional(['message' => 'Level materi berhasil diperbarui.'])
            ->response();
    }

    public function destroy(LevelMateri $levelMateri): JsonResponse
    {
        $levelMateri->delete();

        return response()->json(['message' => 'Level materi berhasil dihapus.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'reward_exp' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
