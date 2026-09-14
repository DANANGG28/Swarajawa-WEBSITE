<?php

namespace App\Http\Resources;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Test
 */
class TestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'level_materi_id' => $this->level_materi_id,
            'nama_test' => $this->nama_test,
            'deskripsi' => $this->deskripsi,
            'guru_id' => $this->guru_id,
            'superadmin_id' => $this->superadmin_id,
            'jumlah_soal' => $this->whenCounted('soal'),
            'level_materi' => new LevelMateriResource($this->whenLoaded('levelMateri')),
            'soal' => SoalResource::collection($this->whenLoaded('soal')),
        ];
    }
}
