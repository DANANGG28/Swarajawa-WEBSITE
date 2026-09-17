<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function fakeLlm(string $answer = 'Wangsulan saking AI.'): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => $answer]]],
            ]),
        ]);
    }

    public function test_chat_creates_session_and_persists_messages(): void
    {
        $this->fakeLlm();
        $siswa = Siswa::factory()->create();

        $res = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/chat', ['pertanyaan' => 'Apa krama inggil turu?'])
            ->assertOk()
            ->assertJsonPath('dijawab', true);

        $sessionId = $res->json('session_id');
        $this->assertNotNull($sessionId);

        $session = ChatSession::find($sessionId);
        $this->assertSame($siswa->id, $session->siswa_id);
        $this->assertSame(2, $session->messages()->count());
        $this->assertSame('user', $session->messages->first()->role);
        $this->assertSame('assistant', $session->messages->last()->role);
    }

    public function test_follow_up_question_appends_to_existing_session(): void
    {
        $this->fakeLlm();
        $siswa = Siswa::factory()->create();

        $first = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/chat', ['pertanyaan' => 'Apa krama inggil turu?'])
            ->assertOk();

        $sessionId = $first->json('session_id');

        $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/chat', ['pertanyaan' => 'Tuladha ukara?', 'session_id' => $sessionId])
            ->assertOk()
            ->assertJsonPath('session_id', $sessionId);

        $this->assertSame(1, ChatSession::count());
        $this->assertSame(4, ChatMessage::count());
    }

    public function test_history_endpoint_lists_student_sessions(): void
    {
        $siswa = Siswa::factory()->create();
        $session = ChatSession::create(['siswa_id' => $siswa->id, 'judul' => 'Obrolan A']);

        $this->actingAs($siswa, 'siswa')
            ->getJson('/kuis/chat/histori')
            ->assertOk()
            ->assertJsonFragment(['judul' => 'Obrolan A'])
            ->assertJsonPath('0.id', $session->id);
    }

    public function test_show_endpoint_returns_messages_for_owner(): void
    {
        $siswa = Siswa::factory()->create();
        $session = ChatSession::create(['siswa_id' => $siswa->id, 'judul' => 'Obrolan B']);
        $session->messages()->create(['role' => 'user', 'pesan' => 'pitakon']);
        $session->messages()->create(['role' => 'assistant', 'pesan' => 'wangsulan']);

        $this->actingAs($siswa, 'siswa')
            ->getJson('/kuis/chat/sesi/'.$session->id)
            ->assertOk()
            ->assertJsonPath('judul', 'Obrolan B')
            ->assertJsonCount(2, 'messages');
    }

    public function test_student_cannot_access_another_students_session(): void
    {
        $owner = Siswa::factory()->create();
        $other = Siswa::factory()->create();
        $session = ChatSession::create(['siswa_id' => $owner->id, 'judul' => 'Pribadi']);

        $this->actingAs($other, 'siswa')
            ->getJson('/kuis/chat/sesi/'.$session->id)
            ->assertForbidden();
    }

    public function test_student_can_delete_own_session(): void
    {
        $siswa = Siswa::factory()->create();
        $session = ChatSession::create(['siswa_id' => $siswa->id, 'judul' => 'Busak']);
        $session->messages()->create(['role' => 'user', 'pesan' => 'pitakon']);

        $this->actingAs($siswa, 'siswa')
            ->deleteJson('/kuis/chat/sesi/'.$session->id)
            ->assertOk();

        $this->assertDatabaseMissing('chat_sessions', ['id' => $session->id]);
        $this->assertDatabaseMissing('chat_messages', ['chat_session_id' => $session->id]);
    }
}
