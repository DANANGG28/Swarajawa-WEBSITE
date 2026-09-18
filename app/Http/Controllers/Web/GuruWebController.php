<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Services\TtsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GuruWebController extends Controller
{
    /**
     * FR-11: Pemantauan progres siswa pada kelas/mapel yang diampu.
     */
    public function dashboard(Request $request): View
    {
        $guru = $this->guru();
        $totalLevel = LevelMateri::count();

        $query = $guru->siswa()
            ->with(['exp', 'strek', 'progres'])
            ->withPivot(['kelas', 'mata_pelajaran']);

        if ($request->filled('kelas')) {
            $query->wherePivot('kelas', $request->query('kelas'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where(fn ($q) => $q->where('siswa.nama_lengkap', 'like', $term)->orWhere('siswa.nis', 'like', $term));
        }

        $siswa = $query->paginate(12)->withQueryString();

        $kelasList = $guru->siswa()->distinct()->pluck('guru_siswa.kelas')->filter()->values();

        return view('guru.dashboard', [
            'judul' => 'Pemantauan Siswa',
            'subjudul' => 'Progres belajar siswa pada kelas/mapel yang Anda ampu',
            'role' => 'guru',
            'active' => 'dashboard',
            'siswa' => $siswa,
            'kelasList' => $kelasList,
            'totalLevel' => $totalLevel,
        ]);
    }

    /**
     * FR-12: Pilih level materi untuk manajemen soal.
     */
    public function levelMateri(): View
    {
        $guru = $this->guru();

        return view('guru.level-materi', [
            'judul' => 'Manajemen Soal & Level',
            'subjudul' => 'Pilih level materi kanggo ngatur bank soal utawa tambah level anyar',
            'role' => 'guru',
            'active' => 'soal',
            'levelList' => LevelMateri::withCount(['soal' => function ($query) {
                $query->where('guru_id', $this->guru()->id);
            }])
                ->orderBy('urutan')
                ->get(),
            'levels' => LevelMateri::withCount(['soal' => function ($query) {
                $query->where('guru_id', $this->guru()->id);
            }])
                ->orderBy('urutan')
                ->get(),
        ]);
    }

    public function levelMateriStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_materi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'reward_exp' => ['required', 'integer', 'min:0', 'max:100000'],
            'urutan' => ['required', 'integer', 'min:0'],
        ]);

        $level = LevelMateri::create($data);

        $siswaIds = Siswa::pluck('id');
        foreach ($siswaIds as $sId) {
            ProgresSiswa::firstOrCreate(
                ['siswa_id' => $sId, 'level_materi_id' => $level->id],
                ['status' => ProgresSiswa::STATUS_TERKUNCI]
            );
        }

        return back()->with('sukses', 'Level materi kasil digawe.');
    }

    /**
     * FR-12: Daftar soal milik guru untuk level tertentu.
     */
    public function soal(Request $request): View|RedirectResponse
    {
        $guru = $this->guru();

        // Redirect ke pilihan level jika tidak ada level_materi_id
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('guru.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        $query = Soal::query()
            ->with('levelMateri')
            ->where('guru_id', $guru->id)
            ->where('level_materi_id', $levelId);

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', (string) $request->query('tipe_soal'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->query('q').'%';
            $query->where('pertanyaan', 'like', $term);
        }

        return view('guru.soal', [
            'judul' => 'Manajemen Soal',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'soal',
            'soalList' => $query->latest()->paginate(12)->withQueryString(),
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalCreate(Request $request): View|RedirectResponse
    {
        if (! $request->filled('level_materi_id')) {
            return redirect()->route('guru.level-materi');
        }

        $levelId = $request->integer('level_materi_id');
        $level = LevelMateri::findOrFail($levelId);

        return view('guru.soal-create', [
            'judul' => 'Tambah Soal Anyar',
            'subjudul' => "Level {$level->urutan} — {$level->nama_materi}",
            'role' => 'guru',
            'active' => 'soal',
            'level' => $level,
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function soalStore(Request $request): RedirectResponse
    {
        $guru = $this->guru();
        $data = $this->validatedSoal($request);
        $data['guru_id'] = $guru->id;
        $data['superadmin_id'] = null;

        if ($request->hasFile('file_gambar')) {
            $data['media_gambar_url'] = $request->file('file_gambar')->store('soal_media', 'public');
        }
        if ($request->hasFile('file_audio')) {
            $data['media_audio_url'] = $request->file('file_audio')->store('soal_media', 'public');
        }

        Soal::create($data);

        return redirect()->route('guru.soal', ['level_materi_id' => $data['level_materi_id']])->with('sukses', 'Soal kasil disimpen.');
    }

    public function soalUpdate(Request $request, Soal $soal): RedirectResponse
    {
        $this->authorize('update', $soal);

        $data = $this->validatedSoal($request);

        if ($request->hasFile('file_gambar')) {
            $data['media_gambar_url'] = $request->file('file_gambar')->store('soal_media', 'public');
        }
        if ($request->hasFile('file_audio')) {
            $data['media_audio_url'] = $request->file('file_audio')->store('soal_media', 'public');
        }

        $soal->update($data);

        return back()->with('sukses', 'Soal kasil dianyari.');
    }

    public function soalDestroy(Soal $soal): RedirectResponse
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return back()->with('sukses', 'Soal kasil dibusak.');
    }

    public function generateTts(Request $request, TtsService $ttsService)
    {
        $request->validate(['text' => 'required|string']);

        $path = $ttsService->generate($request->text);

        if ($path) {
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'path' => $path,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal generate TTS. Pastikan API Key Azure sudah dikonfigurasi.',
        ], 500);
    }

    private function guru(): Guru
    {
        /** @var Guru $guru */
        $guru = Auth::guard('guru')->user();

        return $guru;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedSoal(Request $request): array
    {
        $data = $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
            'tipe_soal' => ['required', 'in:pilihan_ganda,susun_kalimat,pencocokan_arti,puzzle_pakaian_adat,menulis_aksara,kuis_suara'],
            'pertanyaan' => ['required', 'string'],
            'opsi_jawaban_raw' => ['nullable', 'string'],
            'kunci_jawaban_raw' => ['required', 'string'],
            'media_audio_url' => ['nullable', 'string', 'max:2048'],
            'bobot_exp' => ['required', 'integer', 'min:0', 'max:1000'],
            'file_gambar' => ['nullable', 'image', 'max:5120'], // max 5MB
            'file_audio' => ['nullable', 'mimetypes:audio/*', 'max:10240'], // max 10MB
        ]);

        $data['opsi_jawaban'] = $this->decodeJson($data['opsi_jawaban_raw'] ?? null);
        $data['kunci_jawaban'] = $this->decodeJson($data['kunci_jawaban_raw']) ?? [];
        unset($data['opsi_jawaban_raw'], $data['kunci_jawaban_raw']);

        return $data;
    }

    /**
     * @return array<mixed>|null
     */
    private function decodeJson(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<int, string>
     */
    private function tipeList(): array
    {
        return [
            Soal::TIPE_PILIHAN_GANDA => 'Pilihan Ganda',
            Soal::TIPE_SUSUN_KALIMAT => 'Susun Kalimat',
            Soal::TIPE_PENCOCOKAN_ARTI => 'Pencocokan Arti',
            Soal::TIPE_PUZZLE_PAKAIAN_ADAT => 'Puzzle Pakaian Adat',
            Soal::TIPE_MENULIS_AKSARA => 'Menulis Aksara (Tracing)',
            Soal::TIPE_KUIS_SUARA => 'Kuis Suara (TTS/STT)',
        ];
    }
}
