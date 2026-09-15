<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Http\JsonResponse;

class SuperadminDashboardController extends Controller
{
    /**
     * Dashboard ringkas superadmin (FR-16).
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'total_guru' => Guru::count(),
            'total_siswa' => Siswa::count(),
            'total_superadmin' => Superadmin::count(),
            'total_level_materi' => LevelMateri::count(),
            'total_soal' => Soal::count(),
        ]);
    }
}
