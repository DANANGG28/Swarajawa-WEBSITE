<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Soal;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * FR-12: Daftar soal milik guru.
     */
    public function soal(Request $request): View
    {
        $guru = $this->guru();

        $query = Soal::query()->with('levelMateri')->where('guru_id', $guru->id);

        if ($request->filled('level_materi_id')) {
            $query->where('level_materi_id', $request->integer('level_materi_id'));
        }

        if ($request->filled('tipe_soal')) {
            $query->where('tipe_soal', (string) $request->query('tipe_soal'));
        }

        return view('guru.soal', [
            'judul' => 'Manajemen Soal',
            'subjudul' => 'Buat dan kelola soal milik Anda sendiri',
            'role' => 'guru',
            'active' => 'soal',
            'soalList' => $query->latest()->paginate(12)->withQueryString(),
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

        Soal::create($data);

        return back()->with('sukses', 'Soal kasil disimpen.');
    }

    public function soalUpdate(Request $request, Soal $soal): RedirectResponse
    {
        $this->authorize('update', $soal);

        $soal->update($this->validatedSoal($request));

        return back()->with('sukses', 'Soal kasil dianyari.');
    }

    public function soalDestroy(Soal $soal): RedirectResponse
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return back()->with('sukses', 'Soal kasil dibusak.');
    }

    /**
     * FR-23: Daftar paket test milik guru.
     */
    public function test(): View
    {
        $guru = $this->guru();

        return view('guru.test', [
            'judul' => 'Paket Test',
            'subjudul' => 'Susun paket latihan dari bank soal Anda (boleh campur tipe)',
            'role' => 'guru',
            'active' => 'test',
            'testList' => Test::query()->with('levelMateri')->withCount('soal')->where('guru_id', $guru->id)->latest()->get(),
            'levels' => LevelMateri::orderBy('urutan')->get(),
            'soalList' => Soal::query()->with('levelMateri')->where('guru_id', $guru->id)->orderBy('level_materi_id')->get(),
            'tipeList' => $this->tipeList(),
        ]);
    }

    public function testStore(Request $request): RedirectResponse
    {
        $guru = $this->guru();

        $data = $request->validate([
            'level_materi_id' => ['required', 'integer', 'exists:level_materi,id'],
            'nama_test' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'soal_ids' => ['required', 'array', 'min:1'],
            'soal_ids.*' => ['integer', 'exists:soal,id'],
        ]);

        $test = DB::transaction(function () use ($data, $guru) {
            $test = Test::create([
                'level_materi_id' => $data['level_materi_id'],
                'nama_test' => $data['nama_test'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'guru_id' => $guru->id,
            ]);

            $pivot = [];
            foreach (array_values($data['soal_ids']) as $index => $soalId) {
                $pivot[$soalId] = ['urutan' => $index + 1];
            }
            $test->soal()->sync($pivot);

            return $test;
        });

        return back()->with('sukses', 'Paket test "'.$test->nama_test.'" kasil disimpen.');
    }

    public function testDestroy(Test $test): RedirectResponse
    {
        $guru = $this->guru();
        abort_unless($test->guru_id === $guru->id, 403);

        $test->delete();

        return back()->with('sukses', 'Paket test kasil dibusak.');
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
