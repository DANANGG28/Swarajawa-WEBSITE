<?php

namespace App\Http\Resources;

use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Soal
 */
class SoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'level_materi_id' => $this->level_materi_id,
            'tipe_soal' => $this->tipe_soal,
            'pertanyaan' => $this->pertanyaan,
            'opsi_jawaban' => $this->opsi_jawaban,
            'media_audio_url' => $this->media_audio_url,
            'bobot_exp' => $this->bobot_exp,
            'pembuat' => $this->pembuat,
            'guru_id' => $this->guru_id,
            'superadmin_id' => $this->superadmin_id,
            'kunci_jawaban' => $this->when(
                $request->boolean('with_kunci'),
                fn () => $this->kunci_jawaban,
            ),
            'level_materi' => new LevelMateriResource($this->whenLoaded('levelMateri')),
        ];
    }
}
