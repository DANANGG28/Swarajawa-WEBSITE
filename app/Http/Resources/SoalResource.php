<?php

namespace App\Http\Resources;

use App\Models\Soal;
use App\Services\QuizScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Soal
 */
class SoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Skor per-siswa diisi controller (MateriController) lewat atribut
        // dinamis `skor_tertinggi_siswa`. Null = soal durung tau digarap.
        $skorSiswa = $this->skor_tertinggi_siswa;

        return [
            'id' => $this->id,
            'level_materi_id' => $this->level_materi_id,
            'pembahasan_id' => $this->pembahasan_id,
            'tipe_soal' => $this->tipe_soal,
            'pertanyaan' => $this->pertanyaan,
            'opsi_jawaban' => $this->opsi_jawaban,
            'media_audio_url' => $this->media_audio_url,
            'media_gambar_url' => $this->media_gambar_url,
            'soal_latin' => $this->soal_latin,
            'soal_aksara' => $this->soal_aksara,
            'bobot_exp' => $this->bobot_exp,
            'pembuat' => $this->pembuat,
            'guru_id' => $this->guru_id,
            'superadmin_id' => $this->superadmin_id,
            // Status pengerjaan milik siswa sing login (konsisten karo website).
            'sudah_dijawab' => $skorSiswa !== null,
            'skor_tertinggi' => (int) ($skorSiswa ?? 0),
            'lulus' => $skorSiswa !== null
                && (int) $skorSiswa >= QuizScoringService::PASS_THRESHOLD,
            'kunci_jawaban' => $this->when(
                $request->boolean('with_kunci'),
                fn () => $this->kunci_jawaban,
            ),
            'level_materi' => new LevelMateriResource($this->whenLoaded('levelMateri')),
        ];
    }
}
