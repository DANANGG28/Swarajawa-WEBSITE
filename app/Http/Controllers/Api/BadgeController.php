<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Katalog & status lencana milik siswa yang sedang login (FR-10).
 *
 * Memakai BadgeService yang sama dengan website agar data konsisten
 * (satu sumber kebenaran).
 */
class BadgeController extends Controller
{
    public function __construct(private readonly BadgeService $badges) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->badges->getBadgesForSiswa($request->user()));
    }
}
