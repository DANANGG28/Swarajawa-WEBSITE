<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Siswa;
use App\Services\Ai\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(private readonly RagService $rag) {}

    /**
     * Tanya jawab chatbot RAG berguardrail (FR-9) + simpan histori.
     */
    public function ask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pertanyaan' => ['required', 'string', 'max:1000'],
            'session_id' => ['nullable', 'integer'],
        ]);

        $siswa = $this->siswa($request);
        $session = null;

        if (! empty($data['session_id'])) {
            $session = $siswa->chatSessions()->find($data['session_id']);
        }

        if (! $session) {
            $session = $siswa->chatSessions()->create([
                'judul' => Str::limit($data['pertanyaan'], 45),
            ]);
        }

        $session->messages()->create([
            'role' => 'user',
            'pesan' => $data['pertanyaan'],
        ]);

        $result = $this->rag->ask($data['pertanyaan']);

        $session->messages()->create([
            'role' => 'assistant',
            'pesan' => $result['jawaban'],
            'sumber' => $result['sumber'] ?? [],
        ]);

        $session->touch();

        return response()->json(array_merge($result, [
            'session_id' => $session->id,
            'session_title' => $session->judul,
        ]));
    }

    public function histori(Request $request): JsonResponse
    {
        $sessions = $this->siswa($request)->chatSessions()
            ->latest('updated_at')
            ->take(20)
            ->get(['id', 'judul', 'updated_at']);

        return response()->json($sessions);
    }

    public function show(Request $request, ChatSession $chatSession): JsonResponse
    {
        if ($chatSession->siswa_id !== $this->siswa($request)->id) {
            abort(403, 'Akses ditolak.');
        }

        $chatSession->load('messages');

        return response()->json([
            'id' => $chatSession->id,
            'judul' => $chatSession->judul,
            'messages' => $chatSession->messages->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'role' => $m->role,
                'pesan' => $m->pesan,
                'sumber' => $m->sumber,
                'waktu' => $m->created_at->format('H.i').' WIB',
            ]),
        ]);
    }

    public function destroy(Request $request, ChatSession $chatSession): JsonResponse
    {
        if ($chatSession->siswa_id !== $this->siswa($request)->id) {
            abort(403, 'Akses ditolak.');
        }

        $chatSession->delete();

        return response()->json(['status' => 'ok']);
    }

    private function siswa(Request $request): Siswa
    {
        /** @var Siswa $siswa */
        $siswa = $request->user();

        return $siswa;
    }
}
