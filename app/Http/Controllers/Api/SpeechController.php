<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\StsService;
use App\Services\Ai\SttService;
use App\Services\Ai\TtsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpeechController extends Controller
{
    public function __construct(
        private readonly TtsService $tts,
        private readonly SttService $stt,
        private readonly StsService $sts,
    ) {}

    /**
     * Text-to-Speech Bahasa Jawa (FR-6).
     */
    public function tts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'teks' => ['required', 'string', 'max:1000'],
            'voice' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json($this->tts->synthesize($data['teks'], $data['voice'] ?? null));
    }

    /**
     * Speech-to-Text Bahasa Jawa (FR-7).
     */
    public function stt(Request $request): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'string', 'max:15000000'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json($this->stt->transcribe(
            $data['audio'],
            mockTranscript: $data['mock_transcript'] ?? null,
        ));
    }

    /**
     * Speech-to-Speech pipeline STT → proses → TTS (FR-8, Opsi A).
     */
    public function sts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'audio' => ['required', 'string', 'max:15000000'],
            'teks_referensi' => ['required', 'string', 'max:1000'],
            'mock_transcript' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json($this->sts->respond(
            $data['audio'],
            $data['teks_referensi'],
            $data['mock_transcript'] ?? null,
        ));
    }
}
