@extends('layouts.kuis-fokus')

@section('konten')
    @if (! $soal)
        <div class="rounded-3xl p-10 text-center border-2 border-gray-200">
            <span class="material-symbols-outlined text-[48px] text-gray-500">keyboard_voice</span>
            <h2 class="font-heading text-2xl font-extrabold text-on-surface mt-3">Belum ana soal wicara</h2>
            <p class="font-body text-base text-gray-500 mt-1">Rampungna level sadurunge utawa hubungi guru kanggo nambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-6 rounded-2xl bg-primary-600 text-on-primary px-7 py-3 font-body text-base font-bold shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">Bali menyang Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-8 md:gap-10">
            <h1 class="text-center font-heading text-lg md:text-xl font-bold text-on-surface leading-snug">
                {{ $soal['pertanyaan'] }}
            </h1>

            <div class="relative overflow-hidden rounded-3xl border-2 border-gray-200 bg-surface-container-lowest p-6 md:p-8 text-center shadow-sm">
                <div class="absolute top-0 left-0 h-1.5 w-full bg-primary-500"></div>
                <p class="font-display text-2xl md:text-3xl font-extrabold text-primary-700 leading-snug tracking-tight">
                    &ldquo;{{ $soal['teks_referensi'] }}&rdquo;
                </p>
                <button id="btn-tts" type="button"
                    class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-surface-container px-4 py-2.5 text-primary-700 hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[20px]">volume_up</span>
                    <span id="tts-label" class="font-body text-sm font-semibold">Rungokna tuladha</span>
                </button>
            </div>

            <div class="flex flex-col items-center gap-3">
                <div class="relative flex items-center justify-center">
                    <div id="pulse-ring" class="absolute w-32 h-32 rounded-full bg-primary-400/25 opacity-0 pointer-events-none transition-opacity"></div>
                    <div class="absolute w-24 h-24 rounded-full bg-primary-500/10 pointer-events-none"></div>
                    <button id="btn-mic" type="button" aria-label="Miwiti ngrekam swara"
                        class="relative z-10 w-20 h-20 rounded-full bg-primary-600 text-on-primary shadow-[0_6px_0_#5443C9] flex items-center justify-center transition-all active:translate-y-[3px] active:shadow-none">
                        <span class="material-symbols-outlined text-[38px]">mic</span>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span id="stt-status" class="font-body text-sm">Ketuk kanggo miwiti ngrekam ucapan</span>
                    <span id="wave" class="hidden items-end gap-1">
                        <span class="wave-bar w-1 h-3 bg-primary-600 rounded-full"></span>
                        <span class="wave-bar w-1 h-5 bg-primary-600 rounded-full"></span>
                        <span class="wave-bar w-1 h-2 bg-primary-600 rounded-full"></span>
                        <span class="wave-bar w-1 h-4 bg-primary-600 rounded-full"></span>
                        <span class="wave-bar w-1 h-3 bg-primary-600 rounded-full"></span>
                    </span>
                </div>
            </div>

            <div class="rounded-3xl border-2 border-gray-200 bg-surface-container-lowest p-5 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-label-upper text-xs uppercase tracking-widest font-bold text-gray-500">Asil Transkripsi</span>
                    <span id="stt-badge" class="hidden rounded-full bg-yellow-300/50 px-2.5 py-1 text-[11px] font-bold text-tertiary">Mode mock</span>
                </div>
                <textarea id="jawaban" rows="2" placeholder="Asil transkripsi bakal katon ing kene, utawa tulisen manual..."
                    class="w-full resize-none bg-transparent font-body text-lg font-semibold text-on-surface outline-none placeholder:text-gray-400"></textarea>
            </div>

            <section id="feedback" class="hidden rounded-2xl p-5 border-2"></section>
        </div>
    @endif
@endsection

@section('aksi')
    @if ($soal)
        <div class="hidden sm:flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-yellow-300/40 text-tertiary">
            <span class="material-symbols-outlined icon-fill text-[20px] text-orange-500">star</span>
            <span class="font-body text-base font-extrabold">Hadiah: +{{ $soal['bobot_exp'] ?? 0 }} XP</span>
        </div>
        <button id="btn-kirim" type="button" disabled
            class="w-full sm:w-auto rounded-2xl bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:shadow-none disabled:translate-y-0 text-on-primary font-heading text-base font-extrabold uppercase tracking-wide px-8 py-3.5 shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">
            Periksa
        </button>
    @else
        <span></span>
    @endif
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const input = document.getElementById('jawaban');
            const feedback = document.getElementById('feedback');
            const btn = document.getElementById('btn-kirim');
            const status = document.getElementById('stt-status');
            const wave = document.getElementById('wave');
            const pulse = document.getElementById('pulse-ring');
            const badge = document.getElementById('stt-badge');
            const micBtn = document.getElementById('btn-mic');

            function sync() {
                btn.disabled = input.value.trim() === '';
            }
            input.addEventListener('input', sync);
            sync();

            function showResult(res) {
                if (res.benar && res.level_selesai) {
                    window.KuisFx.menang();
                } else if (res.benar) {
                    window.KuisFx.benar();
                } else {
                    window.KuisFx.salah();
                }

                if (window.showQuizFeedbackModal) {
                    window.showQuizFeedbackModal({
                        benar: res.benar,
                        skor: res.skor,
                        skor_tertinggi: res.skor_tertinggi,
                        exp_didapat: res.exp_didapat,
                        total_exp: res.total_exp,
                        current_streak: res.current_streak,
                        kunciDisplay: res.detail?.kunci ?? '',
                        level_selesai: res.level_selesai,
                        reward_exp: res.reward_exp,
                        level_berikutnya: res.level_berikutnya,
                        next_url: res.next_url,
                        next_level_url: res.next_level_url,
                        title: res.benar ? 'Lancar & Bener!' : 'Durung Pas!'
                    });
                }
            }

            btn?.addEventListener('click', async () => {
                const text = input.value.trim();
                if (! text) return;
                btn.disabled = true;
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: text });
                    showResult(res);
                } catch (e) {
                    alert(e.message);
                    btn.disabled = false;
                }
            });

            const ttsBtn = document.getElementById('btn-tts');
            const ttsLabel = document.getElementById('tts-label');
            ttsBtn?.addEventListener('click', async () => {
                if (ttsBtn.dataset.loading === '1') return;
                ttsBtn.dataset.loading = '1';
                ttsLabel.textContent = 'Nyetel swara...';
                window.KuisFx.tap();
                try {
                    const res = await window.postJSON(window.KUIS.ttsUrl, { teks: SOAL.teks_referensi });
                    const src = res.audio_url || (res.audio_base64 ? 'data:' + res.mime + ';base64,' + res.audio_base64 : null);
                    if (src) new Audio(src).play().catch(() => {});
                } catch (e) {
                    // abaikan — swara mung pambantu
                } finally {
                    ttsBtn.dataset.loading = '0';
                    ttsLabel.textContent = 'Rungokna tuladha';
                }
            });

            let recorder = null;
            let chunks = [];

            function setRecordingUi(active) {
                pulse.classList.toggle('opacity-0', ! active);
                wave.classList.toggle('hidden', ! active);
                wave.classList.toggle('flex', active);
                micBtn.classList.toggle('bg-secondary', active);
                micBtn.classList.toggle('bg-primary-600', ! active);
                micBtn.classList.toggle('shadow-[0_6px_0_#5443C9]', ! active);
                if (active) status.textContent = 'Nyemak swara panjenengan...';
            }

            async function startRecording() {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                chunks = [];
                recorder = new MediaRecorder(stream);
                recorder.ondataavailable = (e) => chunks.push(e.data);
                recorder.onstop = () => {
                    stream.getTracks().forEach((t) => t.stop());
                    setRecordingUi(false);
                    status.textContent = 'Ngolah swara...';

                    const blob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });
                    const reader = new FileReader();
                    reader.onloadend = async () => {
                        try {
                            const res = await window.postJSON(window.KUIS.sttUrl, {
                                audio: reader.result.split(',')[1] || '',
                                mock_transcript: SOAL.teks_referensi,
                            });
                            if (res.transcript) {
                                input.value = res.transcript;
                                sync();
                            }
                            status.textContent = res.mock ? 'Mode mock — transkripsi disimulasikan.' : 'Transkripsi rampung.';
                            badge.classList.toggle('hidden', ! res.mock);
                            window.KuisFx.benar();
                        } catch (e) {
                            status.textContent = 'Gagal ngolah swara.';
                            alert(e.message);
                        }
                    };
                    reader.readAsDataURL(blob);
                };
                recorder.start();
                window.KuisFx.tap();
                setRecordingUi(true);
            }

            micBtn?.addEventListener('click', async () => {
                try {
                    if (recorder && recorder.state === 'recording') {
                        recorder.stop();
                    } else {
                        await startRecording();
                    }
                } catch (e) {
                    alert('Mikrofon ora kasedhiya. Tulisen manual wae.');
                }
            });
        </script>
    @endpush
@endif
