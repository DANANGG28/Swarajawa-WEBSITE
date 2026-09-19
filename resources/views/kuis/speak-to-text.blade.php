@extends('layouts.kuis')

@section('konten')
    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">keyboard_voice</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal speak-to-text</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ url('/latihan-soal') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Kembali ke Latihan</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <section class="lg:col-span-5 bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2.5 pb-3.5 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700">
                        <span class="material-symbols-outlined">graphic_eq</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Pemutar Suara Contoh</h3>
                        <p class="font-caption text-caption text-gray-500">edge-tts • jv-ID-DimasNeural</p>
                    </div>
                </div>
                <div class="mt-4 p-6 rounded-xl bg-primary-fixed/40 border border-primary-400/30 text-center">
                    <p class="font-body text-body text-on-surface-variant">Tekan tombol di bawah, lalu tulis apa yang Anda dengarkan.</p>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button id="btn-tts" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                        Putar Suara
                    </button>
                    <span class="font-caption text-caption text-gray-500">FR-7 Speech-to-Text</span>
                </div>
            </section>

            <section class="lg:col-span-7 bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2.5 pb-3.5 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700">
                        <span class="material-symbols-outlined">edit_note</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Zona Mengetik Jawaban</h3>
                        <p class="font-caption text-caption text-gray-500">Tulis kalimat yang sesuai dengan suara yang didengarkan</p>
                    </div>
                </div>

                <label class="flex flex-col gap-1.5 mt-4">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Jawaban</span>
                    <textarea id="jawaban" rows="3" placeholder="Tulis di sini..."
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 transition-colors"></textarea>
                </label>

                <div class="flex items-center gap-3 mt-3">
                    <button id="btn-mic" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-50 hover:bg-surface-container-high border border-gray-200 text-on-surface font-body text-body font-semibold">
                        <span class="material-symbols-outlined text-[18px] text-primary-600">mic</span>
                        Gunakan Suara (STT)
                    </button>
                    <span id="stt-status" class="font-caption text-caption text-gray-500"></span>
                </div>

                <div id="feedback" class="hidden mt-4 rounded-2xl p-5 border"></div>
            </section>
        </div>
    @endif
@endsection

@section('aksi')
    @include('partials.kuis-xp', ['soal' => $soal])
    <button id="btn-kirim" type="button" @disabled(! $soal)
        class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-on-primary font-body text-body font-bold px-8 py-3 shadow-sm transition-colors">
        <span>Kirim Jawaban</span>
        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
    </button>
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const input = document.getElementById('jawaban');
            const feedback = document.getElementById('feedback');

            function showResult(res) {
                feedback.className = 'mt-4 rounded-2xl p-5 border ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

                let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                if (res.next_url) {
                    nextButtons += `<a href="${res.next_url}" class="rounded-full bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body hover:bg-primary-700 transition shadow-sm">Soal Selanjutnya</a>`;
                }
                nextButtons += `<a href="{{ route('siswa.latihan') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Daftar Soal</a>`;
                nextButtons += `<a href="{{ route('siswa.dashboard') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Beranda</a></div>`;

                let levelSelesaiHtml = '';
                if (res.level_selesai) {
                    levelSelesaiHtml = `
                        <div class="mt-3 p-4 rounded-xl bg-green-500/20 border border-green-500/40 text-green-700">
                            <div class="font-heading text-heading font-extrabold flex items-center gap-1.5">
                                <span class="material-symbols-outlined icon-fill">military_tech</span>
                                Level Selesai! Anda mendapatkan bonus +${res.reward_exp} EXP!
                            </div>
                            <p class="font-body text-body text-green-800 mt-1">Level selanjutnya <strong>${res.level_berikutnya ?? ''}</strong> sekarang sudah terbuka.</p>
                        </div>`;
                }

                feedback.innerHTML = `
                    <div class="flex items-center gap-2 font-heading text-heading font-extrabold ${res.benar ? 'text-green-500' : 'text-error'}">
                        <span class="material-symbols-outlined icon-fill">${res.benar ? 'verified' : 'cancel'}</span>
                        ${res.benar ? 'Benar!' : 'Belum tepat'} • Skor ${res.skor}/100 • +${res.exp_didapat ?? 0} XP
                    </div>
                    <p class="font-body text-body text-on-surface-variant mt-1">Kunci: <strong>${res.detail?.kunci ?? ''}</strong> • Rekor: ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} hari</p>
                    ${levelSelesaiHtml}
                    ${nextButtons}`;
                feedback.classList.remove('hidden');
            }

            document.getElementById('btn-kirim')?.addEventListener('click', async () => {
                const text = input.value.trim();
                if (!text) { alert('Tulis jawaban terlebih dahulu.'); return; }
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: text });
                    showResult(res);
                } catch (e) { alert(e.message); }
            });

            document.getElementById('btn-tts')?.addEventListener('click', async () => {
                try {
                    const res = await window.postJSON(window.KUIS.ttsUrl, { teks: SOAL.teks_referensi });
                    if (res.audio_base64) {
                        new Audio('data:' + res.mime + ';base64,' + res.audio_base64).play();
                    } else {
                        alert('Mode mock: audio TTS belum tersedia. Tuliskan contoh: ' + SOAL.teks_referensi);
                    }
                } catch (e) { alert(e.message); }
            });

            let recorder = null, chunks = [];
            document.getElementById('btn-mic')?.addEventListener('click', async () => {
                const status = document.getElementById('stt-status');
                try {
                    if (!recorder) {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        recorder = new MediaRecorder(stream);
                        recorder.ondataavailable = (e) => chunks.push(e.data);
                        recorder.onstop = () => {
                            const blob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });
                            const reader = new FileReader();
                            reader.onloadend = async () => {
                                try {
                                    const res = await window.postJSON(window.KUIS.sttUrl, { audio: reader.result.split(',')[1] || '', mock_transcript: SOAL.teks_referensi });
                                    if (res.transcript) { input.value = res.transcript; }
                                    status.textContent = res.mock ? 'Mode mock — transcript disimulasikan.' : 'Transkripsi selesai.';
                                } catch (e) { alert(e.message); }
                            };
                            reader.readAsDataURL(blob);
                            stream.getTracks().forEach((t) => t.stop());
                            chunks = [];
                        };
                    }
                    if (recorder.state === 'recording') {
                        recorder.stop();
                        status.textContent = 'Memproses suara...';
                    } else {
                        recorder.start();
                        status.textContent = 'Merekam... tekan lagi untuk menghentikan.';
                    }
                } catch (e) {
                    alert('Mikrofon tidak tersedia. Silakan ketik secara manual.');
                }
            });
        </script>
    @endpush
@endif
