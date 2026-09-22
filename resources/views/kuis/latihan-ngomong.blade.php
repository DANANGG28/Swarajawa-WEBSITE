@extends('layouts.kuis-wicara')

@section('konten')
    @if (! $soal)
        <div class="rounded-3xl p-10 text-center border-2 border-gray-200">
            <span class="material-symbols-outlined text-[48px] text-gray-500">record_voice_over</span>
            <h2 class="font-heading text-2xl font-extrabold text-on-surface mt-3">Belum ana soal latihan ngomong</h2>
            <p class="font-body text-base text-gray-500 mt-1">Rampungna level sadurunge utawa hubungi guru kanggo nambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-6 rounded-2xl bg-primary-600 text-on-primary px-7 py-3 font-body text-base font-bold shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">Bali menyang Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <style>
                /* Animasi mlebu: tembung-tembung katon siji-siji. Tanpa JS teks tetep katon. */
                html.js-anim #tts-card .tts-word { opacity: 0; transform: translateY(10px); }
                html.js-anim #tts-card.is-ready .tts-word {
                    opacity: 1; transform: none;
                    transition: opacity .4s cubic-bezier(.16,1,.3,1), transform .4s cubic-bezier(.16,1,.3,1);
                    transition-delay: calc(var(--i, 0) * 42ms);
                }
                @media (prefers-reduced-motion: reduce) {
                    html.js-anim #tts-card .tts-word { opacity: 1 !important; transform: none !important; transition: none !important; }
                }
            </style>
            <script>document.documentElement.classList.add('js-anim');</script>

            <section id="tts-card"
                class="rounded-3xl bg-surface-container-lowest p-6 md:p-7 border border-gray-100 shadow-[0_8px_20px_rgba(30,30,42,0.06)] flex flex-col gap-4">
                <button id="btn-tts" type="button"
                    class="self-start inline-flex items-center gap-1.5 rounded-xl bg-surface-container px-3 py-1.5 text-primary-700 hover:bg-surface-container-high disabled:opacity-70 disabled:cursor-wait transition-colors">
                    <span id="tts-icon" class="material-symbols-outlined text-[18px]">volume_up</span>
                    <span id="tts-label" class="font-body text-sm font-bold">Dengarkan Audio</span>
                </button>
                <p id="tts-sentence" class="font-display text-[22px] md:text-2xl font-bold text-on-surface leading-snug tracking-tight">
                    &ldquo;{{ $soal['teks_referensi'] }}&rdquo;
                </p>
            </section>

            <section class="flex flex-col items-center justify-center gap-3 py-2">
                <div class="relative flex items-center justify-center">
                    <div id="record-ping" class="hidden absolute w-28 h-28 rounded-full bg-primary-400/30 animate-ping"></div>
                    <button id="btn-mic" type="button" aria-label="Miwiti rekam"
                        class="relative z-10 w-20 h-20 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary flex items-center justify-center shadow-[0_14px_28px_rgba(84,67,201,0.24)] active:scale-95 transition-all">
                        <span id="mic-icon" class="material-symbols-outlined text-[36px]">mic</span>
                    </button>
                </div>
                <p id="mic-label" class="font-heading text-base font-bold text-on-surface text-center">Ketuk untuk Mulai Rekam Ucapan</p>

                <div id="waveform" aria-hidden="true" class="flex items-center justify-center gap-1.5 h-7">
                    <span class="bar-v w-1 h-2 bg-primary-400 rounded-full"></span>
                    <span class="bar-v w-1 h-3 bg-primary-500 rounded-full"></span>
                    <span class="bar-v w-1 h-5 bg-primary-600 rounded-full"></span>
                    <span class="bar-v w-1 h-7 bg-primary-700 rounded-full"></span>
                    <span class="bar-v w-1 h-5 bg-primary-600 rounded-full"></span>
                    <span class="bar-v w-1 h-4 bg-primary-500 rounded-full"></span>
                    <span class="bar-v w-1 h-2 bg-primary-400 rounded-full"></span>
                </div>

                <button id="btn-demo" type="button" class="font-caption text-xs font-semibold text-gray-500 hover:text-primary-700 transition-colors">
                    Mic ora kasedhiya? Coba mode demo
                </button>
            </section>

            <section class="rounded-2xl bg-surface-container-low p-4 flex flex-col gap-1">
                <span class="font-label-upper text-[11px] uppercase tracking-wider font-semibold text-gray-500">Hasil Suaramu</span>
                <p id="transkripsi" class="font-body text-base font-semibold text-on-surface">Durung ana rekaman.</p>
            </section>

            <article id="guru-ai" class="hidden rounded-3xl bg-surface-container-lowest p-6 border border-gray-100 shadow-[0_8px_20px_rgba(30,30,42,0.06)] flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-primary-700 to-primary-500 p-0.5 shadow-[0_4px_12px_rgba(84,67,201,0.18)]">
                        <div class="w-full h-full rounded-full bg-surface-container-lowest flex items-center justify-center text-primary-600">
                            <span class="material-symbols-outlined icon-fill text-[24px]">smart_toy</span>
                        </div>
                    </div>
                    <h2 class="font-heading text-lg font-bold text-on-surface">Guru AI</h2>
                </div>

                <div class="rounded-xl bg-surface-container-low p-2.5 flex items-center gap-3">
                    <button id="btn-play-ai" type="button" aria-label="Putar koreksi Guru AI"
                        class="w-10 h-10 rounded-full bg-primary-600 text-on-primary flex items-center justify-center hover:bg-primary-700 transition-colors shrink-0">
                        <span id="ai-play-icon" class="material-symbols-outlined text-[20px]">play_arrow</span>
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
                            <div id="ai-progress" class="h-full w-0 bg-primary-500 rounded-full transition-all duration-200"></div>
                        </div>
                        <div class="flex items-center justify-between text-gray-500 font-caption text-xs mt-1">
                            <span>Dengarkan Koreksi Guru AI</span>
                            <span id="ai-duration">00:00</span>
                        </div>
                    </div>
                </div>

                <p id="feedback-text" class="font-body text-base text-on-surface leading-relaxed"></p>
                <p id="mock-note" class="hidden font-caption text-xs text-gray-500">(Mode mock — layanan AI belum dikonfigurasi)</p>
            </article>

            <div class="flex items-center justify-between gap-3 pt-1 pb-4">
                <button id="btn-ulang" type="button"
                    class="inline-flex items-center gap-2 rounded-2xl bg-surface-container px-5 py-3 text-on-surface font-heading text-base font-bold hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[18px]">replay</span>
                    <span>Coba Ulangi</span>
                </button>
                <a href="{{ route('siswa.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 px-6 py-3 text-on-primary font-heading text-base font-bold shadow-[0_8px_20px_rgba(84,67,201,0.22)] hover:bg-primary-700 transition-all">
                    <span>Rampung</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>
        </div>
    @endif
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const LATIHAN_URL = @json(route('kuis.latihan-ngomong.jawab'));
            const btnMic = document.getElementById('btn-mic');
            const micIcon = document.getElementById('mic-icon');
            const micLabel = document.getElementById('mic-label');
            const micPing = document.getElementById('record-ping');
            const bars = document.querySelectorAll('.bar-v');
            const transkripsiEl = document.getElementById('transkripsi');
            const guruAi = document.getElementById('guru-ai');
            const feedbackText = document.getElementById('feedback-text');
            const mockNote = document.getElementById('mock-note');
            const playBtn = document.getElementById('btn-play-ai');
            const playIcon = document.getElementById('ai-play-icon');
            const progress = document.getElementById('ai-progress');
            const durationEl = document.getElementById('ai-duration');

            let recorder = null;
            let chunks = [];
            let recording = false;
            let autoStop = null;
            let aiAudio = null;

            function setVisual(active) {
                micPing.classList.toggle('hidden', ! active);
                bars.forEach((b) => b.classList.toggle('wave-bar', active));
            }

            function fmt(sec) {
                if (! isFinite(sec)) return '00:00';
                const m = String(Math.floor(sec / 60)).padStart(2, '0');
                const s = String(Math.floor(sec % 60)).padStart(2, '0');
                return m + ':' + s;
            }

            function resetPlayback() {
                if (aiAudio) { aiAudio.pause(); }
                aiAudio = null;
                progress.style.width = '0%';
                playIcon.textContent = 'play_arrow';
                durationEl.textContent = '00:00';
            }

            function showFeedback(res) {
                transkripsiEl.textContent = res.transkripsi ? '“' + res.transkripsi + '”' : 'Durung ana rekaman.';
                feedbackText.textContent = res.feedback_text ?? '';
                mockNote.classList.toggle('hidden', ! res.mock);
                guruAi.classList.remove('hidden');
                window.KuisFx.benar();

                resetPlayback();
                if (res.audio_url) {
                    aiAudio = new Audio(res.audio_url);
                    aiAudio.addEventListener('timeupdate', () => {
                        durationEl.textContent = fmt(aiAudio.currentTime);
                        progress.style.width = (aiAudio.duration ? (aiAudio.currentTime / aiAudio.duration) * 100 : 0) + '%';
                    });
                    aiAudio.addEventListener('ended', () => {
                        playIcon.textContent = 'play_arrow';
                        progress.style.width = '0%';
                        durationEl.textContent = '00:00';
                    });
                    aiAudio.play().then(() => { playIcon.textContent = 'pause'; }).catch(() => {});
                }
            }

            async function kirimAudio(base64) {
                micLabel.textContent = 'Ngolah swara panjenengan...';
                try {
                    const res = await window.postJSON(LATIHAN_URL, { soal_id: SOAL.id, audio: base64 });
                    showFeedback(res);
                } catch (e) {
                    alert(e.message);
                } finally {
                    micLabel.textContent = 'Ketuk untuk Mulai Rekam Ucapan';
                }
            }

            async function startRecording() {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recorder = new MediaRecorder(stream);
                chunks = [];
                recorder.ondataavailable = (e) => chunks.push(e.data);
                recorder.onstop = () => {
                    stream.getTracks().forEach((t) => t.stop());
                    clearTimeout(autoStop);
                    recording = false;
                    setVisual(false);
                    micIcon.textContent = 'mic';
                    micLabel.textContent = 'Ngolah swara panjenengan...';

                    const blob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });
                    const reader = new FileReader();
                    reader.onloadend = () => kirimAudio(reader.result.split(',')[1] || '');
                    reader.readAsDataURL(blob);
                };
                recorder.start();
                recording = true;
                window.KuisFx.tap();
                setVisual(true);
                micIcon.textContent = 'stop';
                micLabel.textContent = 'Nyimak pengucapanmu... ketuk maneh kanggo mungkasi';
                autoStop = setTimeout(() => { if (recording) recorder.stop(); }, 15000);
            }

            function stopRecording() {
                if (recorder && recorder.state !== 'inactive') recorder.stop();
            }

            btnMic?.addEventListener('click', async () => {
                if (recording) { stopRecording(); return; }
                try {
                    await startRecording();
                } catch (e) {
                    alert('Mikrofon tidak tersedia. Gunakan "Coba mode demo".');
                }
            });

            document.getElementById('btn-demo')?.addEventListener('click', async () => {
                window.KuisFx.tap();
                try {
                    const res = await window.postJSON(LATIHAN_URL, {
                        soal_id: SOAL.id,
                        audio: 'demo',
                        mock_transcript: SOAL.teks_referensi,
                    });
                    showFeedback(res);
                } catch (e) { alert(e.message); }
            });

            playBtn?.addEventListener('click', () => {
                if (! aiAudio) return;
                if (aiAudio.paused) {
                    aiAudio.play().then(() => { playIcon.textContent = 'pause'; }).catch(() => {});
                } else {
                    aiAudio.pause();
                    playIcon.textContent = 'play_arrow';
                }
            });

            document.getElementById('btn-ulang')?.addEventListener('click', () => {
                window.KuisFx.tap();
                guruAi.classList.add('hidden');
                transkripsiEl.textContent = 'Durung ana rekaman.';
                resetPlayback();
            });

            (function initContohAudio() {
                const card = document.getElementById('tts-card');
                const sentence = document.getElementById('tts-sentence');
                const btn = document.getElementById('btn-tts');
                const icon = document.getElementById('tts-icon');
                const label = document.getElementById('tts-label');
                const teks = SOAL.teks_referensi;

                if (! card || ! sentence) return;

                let audio = null;

                // Animasi mlebu: tembung-tembung katon siji-siji.
                function reveal() {
                    const words = sentence.textContent.trim().split(/\s+/).filter(Boolean);
                    if (words.length) {
                        sentence.textContent = '';
                        const frag = document.createDocumentFragment();
                        words.forEach((w, i) => {
                            const span = document.createElement('span');
                            span.className = 'tts-word';
                            span.style.setProperty('--i', i);
                            span.textContent = w;
                            frag.appendChild(span);
                            if (i < words.length - 1) frag.appendChild(document.createTextNode(' '));
                        });
                        sentence.appendChild(frag);
                    }
                    requestAnimationFrame(() => requestAnimationFrame(() => card.classList.add('is-ready')));
                }

                reveal();

                function setLoading(on) {
                    btn.disabled = on;
                    btn.dataset.loading = on ? '1' : '0';
                    if (icon) {
                        icon.textContent = on ? 'progress_activity' : 'volume_up';
                        icon.classList.toggle('animate-spin', on);
                    }
                    if (label) label.textContent = on ? 'Nyetel swara…' : 'Dengarkan Audio';
                }

                btn?.addEventListener('click', async () => {
                    if (btn.dataset.loading === '1') return;
                    if (! teks || ! window.postJSON) return;

                    window.KuisFx?.tap();
                    setLoading(true);
                    try {
                        // On-demand: saben play, TTS dienggo maneh (ora di-prefetch).
                        const res = await window.postJSON(window.KUIS.ttsUrl, { teks: teks });
                        const src = res.audio_url || (res.audio_base64 ? 'data:' + res.mime + ';base64,' + res.audio_base64 : null);
                        if (! src) throw new Error('Audio kosong');

                        if (audio) audio.pause();
                        audio = new Audio(src);
                        audio.play().catch(() => {});
                    } catch (e) {
                        alert(e.message);
                    } finally {
                        setLoading(false);
                    }
                });
            })();
        </script>
    @endpush
@endif
