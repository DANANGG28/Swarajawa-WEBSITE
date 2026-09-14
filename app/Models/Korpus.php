<?php

namespace App\Models;

use Database\Factories\KorpusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Korpus extends Model
{
    /** @use HasFactory<KorpusFactory> */
    use HasFactory;

    protected $table = 'korpus';

    protected $fillable = [
        'judul',
        'kategori',
        'konten',
        'sumber',
    ];
}
