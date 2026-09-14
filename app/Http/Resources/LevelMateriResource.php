<?php

namespace App\Http\Resources;

use App\Models\LevelMateri;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LevelMateri
 */
class LevelMateriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_materi' => $this->nama_materi,
            'deskripsi' => $this->deskripsi,
            'reward_exp' => (int) $this->reward_exp,
            'urutan' => (int) $this->urutan,
            'jumlah_soal' => $this->whenCounted('soal'),
            'progres' => $this->whenPivotLoaded('progres_siswa', fn () => [
                'status' => $this->pivot->status,
                'tanggal_selesai' => $this->pivot->tanggal_selesai,
            ]),
        ];
    }
}
