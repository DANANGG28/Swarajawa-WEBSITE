<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SoalResource;
use App\Models\Guru;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    /**
     * Bank soal. Guru & superadmin dapat melihat; guru dapat memfilter
     * miliknya sendiri lewat `?milik_saya=1`.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Soal::class);

        $user = $request->user();
        $query = Soal::query()->with('levelMateri');

        if ($request->boolean('milik_saya') && $user instanceof Guru) {
            $query->where('guru_id', $user->id);
        }

        if ($request->filled('level_materi_id')) {
            $query->where('level_materi_id', $request->integer('level_materi_id'));
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', (string) $request->query('tipe_soal'));
        }

        return SoalResource::collection($query->latest()->paginate(50));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Soal::class);

        $data = $this->validated($request);
        $user = $request->user();

        $data['guru_id'] = $user instanceof Guru ? $user->id : null;
        $data['superadmin_id'] = $user instanceof Superadmin ? $user->id : null;

        $soal = Soal::create($data);

        return (new SoalResource($soal->load('levelMateri')))
            ->additional(['message' => 'Soal berhasil dibuat.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Soal $soal): SoalResource
    {
        $this->authorize('view', $soal);

        return new SoalResource($soal->load('levelMateri'));
    }

    public function update(Request $request, Soal $soal): JsonResponse
    {
        $this->authorize('update', $soal);

        $soal->update($this->validated($request));

        return (new SoalResource($soal->fresh()->load('levelMateri')))
            ->additional(['message' => 'Soal berhasil diperbarui.'])
            ->response();
    }

    public function destroy(Request $request, Soal $soal): JsonResponse
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return response()->json(['message' => 'Soal berhasil dihapus.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
            'tipe_soal' => ['required', 'in:pilihan_ganda,susun_kalimat,pencocokan_arti,puzzle_pakaian_adat,menulis_aksara,kuis_suara'],
            'pertanyaan' => ['required', 'string'],
            'opsi_jawaban' => ['nullable', 'array'],
            'kunci_jawaban' => ['required', 'array'],
            'media_audio_url' => ['nullable', 'string', 'max:2048'],
            'bobot_exp' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);
    }
}
