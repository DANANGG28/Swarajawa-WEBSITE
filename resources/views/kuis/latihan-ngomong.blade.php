@extends('layouts.kuis-wicara')

@section('konten')
    @if (! $soal)
        <div class="w-full max-w-md bg-surface-container-lowest rounded-3xl p-8 sm:p-10 text-center border border-gray-200 shadow-md flex flex-col items-center">
            <div class="w-16 h-16 rounded-2xl bg-surface-container-low flex items-center justify-center text-primary-600 mb-4">
                <span class="material-symbols-outlined text-[36px]">record_voice_over</span>
            </div>
            <h2 class="font-heading text-xl sm:text-2xl font-extrabold text-black-900">Belum ada soal latihan</h2>
            <p class="font-body text-xs sm:text-sm text-gray-500 mt-1.5">Rampungna level sadurunge utawa hubungi guru kanggo nambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-6 rounded-2xl bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 font-body text-xs sm:text-sm font-bold shadow-md shadow-primary-600/25 transition-all">Bali menyang Beranda</a>
        </div>
    @else
        <div class="w-full max-w-2xl mx-auto bg-surface-container-lowest rounded-2xl sm:rounded-3xl p-4 sm:p-7 md:p-8 shadow-md border border-gray-200 flex flex-col gap-4 sm:gap-6 relative mt-1 sm:mt-0">
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

            <!-- Header Badge & Instruction -->
            <div class="flex flex-col items-center text-center gap-1 border-b border-gray-100 pb-3 sm:pb-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-primary-50 text-primary-700 font-label-upper text-[10px] sm:text-[11px] font-bold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[15px] sm:text-[16px]">record_voice_over</span>
                    Latihan Wicara Basa Jawa
                </span>
                <h1 class="font-heading text-base sm:text-xl md:text-2xl font-bold text-black-900 tracking-tight mt-0.5 sm:mt-1">
                    Ucapkan Kalimat di Bawah Ini:
                </h1>
            </div>

            <!-- Sentence / TTS Card -->
            <section id="tts-card"
                class="rounded-xl sm:rounded-2xl bg-surface-container-low p-4 sm:p-6 border border-primary-100/60 flex flex-col items-center text-center gap-3 sm:gap-4 relative overflow-hidden shadow-sm">
                <div class="w-full">
                    <p id="tts-sentence" class="font-display text-lg sm:text-2xl md:text-3xl font-extrabold text-primary-700 leading-snug tracking-tight">
                        &ldquo;{{ $soal['teks_referensi'] }}&rdquo;
                    </p>
                </div>
                <button id="btn-tts" type="button"
                    class="inline-flex items-center gap-1.5 sm:gap-2 rounded-xl bg-white px-3.5 py-1.5 sm:px-4 sm:py-2 text-primary-700 hover:bg-primary-50 hover:text-primary-800 border border-primary-200 shadow-sm disabled:opacity-70 disabled:cursor-wait transition-all active:scale-95">
                    <span id="tts-icon" class="material-symbols-outlined text-[16px] sm:text-[18px]">volume_up</span>
                    <span id="tts-label" class="font-body text-xs sm:text-sm font-bold">Dengarkan Audio</span>
                </button>
            </section>

            <!-- Recording Control Area -->
            <section class="flex flex-col items-center justify-center gap-2 sm:gap-3 py-1 sm:py-3">
                <div class="relative flex items-center justify-center">
                    <div id="record-ping" class="hidden absolute w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-primary-400/30 animate-ping"></div>
                    <button id="btn-mic" type="button" aria-label="Miwiti rekam"
                        class="relative z-10 w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary flex items-center justify-center shadow-lg shadow-primary-600/30 active:scale-95 transition-all">
                        <span id="mic-icon" class="material-symbols-outlined text-[28px] sm:text-[36px]">mic</span>
                    </button>
                </div>
                <p id="mic-label" class="font-heading text-xs sm:text-base font-bold text-black-900 text-center">Ketuk untuk Mulai Rekam Ucapan</p>

                <div id="waveform" aria-hidden="true" class="flex items-center justify-center gap-1 sm:gap-1.5 h-6 sm:h-7">
                    <span class="bar-v w-1 sm:w-1.5 h-2 bg-primary-400 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-3 bg-primary-500 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-5 bg-primary-600 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-6 sm:h-7 bg-primary-700 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-5 bg-primary-600 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-4 bg-primary-500 rounded-full"></span>
                    <span class="bar-v w-1 sm:w-1.5 h-2 bg-primary-400 rounded-full"></span>
                </div>

                <button id="btn-demo" type="button" class="font-caption text-[11px] sm:text-xs font-semibold text-gray-500 hover:text-primary-700 hover:underline transition-colors mt-0.5">
                    Mic ora kasedhiya? Coba mode demo
                </button>
            </section>

            <!-- Main Page Footer Action -->
            <div class="flex items-center justify-end gap-3 pt-2.5 sm:pt-3 border-t border-gray-100">
                <a href="{{ route('siswa.dashboard') }}"
                    class="inline-flex items-center gap-1.5 sm:gap-2 rounded-xl sm:rounded-2xl bg-surface-container-low hover:bg-surface-container border border-gray-200 px-4 py-2 sm:px-5 sm:py-2.5 text-black-900 font-heading text-xs sm:text-sm font-bold transition-all active:scale-95">
                    <span>Kembali ke Beranda</span>
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- POPUP / MODAL HASIL EVALUASI GURU AI -->
        <div id="modal-hasil" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200">
            <div class="modal-content w-full max-w-lg bg-surface-container-lowest rounded-3xl p-6 sm:p-7 shadow-2xl border border-gray-100 flex flex-col gap-5 max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-[22px]">smart_toy</span>
                        </div>
                        <div>
                            <h2 class="font-heading text-base sm:text-lg font-bold text-black-900 leading-tight">Umpan Balik Guru AI</h2>
                            <span class="font-caption text-xs text-primary-600 font-semibold">Hasil Evaluasi Pengucapan</span>
                        </div>
                    </div>
                    <button type="button" id="btn-close-modal" class="w-8 h-8 rounded-full bg-surface-container-low hover:bg-surface-container flex items-center justify-center text-gray-500 hover:text-black-900 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <!-- Hasil Suaramu -->
                <div class="rounded-2xl bg-surface-container-low p-4 border border-gray-100 flex flex-col gap-1.5">
                    <div class="flex items-center gap-1.5 text-gray-500">
                        <span class="material-symbols-outlined text-[16px]">hearing</span>
                        <span class="font-label-upper text-[11px] uppercase tracking-wider font-bold">Hasil Suaramu</span>
                    </div>
                    <p id="transkripsi" class="font-body text-sm sm:text-base font-semibold text-black-900">Durung ana rekaman.</p>
                </div>

                <!-- Audio Koreksi Guru AI & Feedback -->
                <article id="guru-ai" class="flex flex-col gap-3.5">
                    <!-- Audio Player -->
                    <div class="rounded-xl bg-surface-container-low p-3 flex items-center gap-3 border border-gray-100">
                        <button id="btn-play-ai" type="button" aria-label="Putar koreksi Guru AI"
                            class="w-10 h-10 rounded-full bg-primary-600 text-on-primary flex items-center justify-center hover:bg-primary-700 transition-colors shadow-sm shrink-0">
                            <span id="ai-play-icon" class="material-symbols-outlined text-[20px]">play_arrow</span>
                        </button>
                        <div class="flex-1 min-w-0">
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div id="ai-progress" class="h-full w-0 bg-primary-600 rounded-full transition-all duration-200"></div>
                            </div>
                            <div class="flex items-center justify-between text-gray-500 font-caption text-xs mt-1.5">
                                <span>Dengarkan Koreksi Guru AI</span>
                                <span id="ai-duration" class="font-semibold text-primary-700">00:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Text -->
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                        <span class="block font-label-upper text-[10px] uppercase tracking-wider text-gray-500 mb-1 font-bold">Catatan Evaluasi:</span>
                        <p id="feedback-text" class="font-body text-xs sm:text-sm text-black-900 leading-relaxed"></p>
                    </div>
                </article>

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                    <button id="btn-ulang" type="button"
                        class="inline-flex items-center gap-2 rounded-2xl bg-surface-container-low hover:bg-surface-container border border-gray-200 px-4 py-2.5 text-black-900 font-heading text-xs sm:text-sm font-bold transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px] text-gray-600">replay</span>
                        <span>Coba Ulangi</span>
                    </button>
                    <a href="{{ route('siswa.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-2xl bg-primary-600 hover:bg-primary-700 px-5 py-2.5 text-white font-heading text-xs sm:text-sm font-bold shadow-md shadow-primary-600/25 transition-all active:scale-95">
                        <span>Rampung</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
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
            const playBtn = document.getElementById('btn-play-ai');
            const playIcon = document.getElementById('ai-play-icon');
            const progress = document.getElementById('ai-progress');
            const durationEl = document.getElementById('ai-duration');
            const modalHasil = document.getElementById('modal-hasil');
            const btnCloseModal = document.getElementById('btn-close-modal');

            let recorder = null;
            let chunks = [];
            let recording = false;
            let autoStop = null;
            let aiAudio = null;

            function openModal() {
                if (!modalHasil) return;
                modalHasil.classList.remove('hidden');
                requestAnimationFrame(() => {
                    modalHasil.classList.remove('opacity-0');
                    modalHasil.querySelector('.modal-content')?.classList.remove('scale-95');
                });
            }

            function closeModal() {
                if (!modalHasil) return;
                modalHasil.classList.add('opacity-0');
                modalHasil.querySelector('.modal-content')?.classList.add('scale-95');
                setTimeout(() => {
                    modalHasil.classList.add('hidden');
                    resetPlayback();
                }, 200);
            }

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

                openModal();
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
                closeModal();
            });

            btnCloseModal?.addEventListener('click', () => {
                closeModal();
            });

            modalHasil?.addEventListener('click', (e) => {
                if (e.target === modalHasil) {
                    closeModal();
                }
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
