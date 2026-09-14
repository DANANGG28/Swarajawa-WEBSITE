<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ai\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private readonly RagService $rag) {}

    /**
     * Tanya jawab chatbot RAG berguardrail (FR-9).
     */
    public function ask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:1000'],
        ]);

        return response()->json($this->rag->ask($data['pertanyaan']));
    }
}
