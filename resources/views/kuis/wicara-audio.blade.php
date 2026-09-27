@extends('layouts.kuis-wicara')

@section('konten')
    @if (! $soal)
        <div class="w-full bg-white rounded-2xl p-10 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center">
            <span class="material-symbols-outlined text-[48px] text-gray-400">mic</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal kuis wicara</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary px-6 py-3 font-body text-body font-bold transition shadow-sm">Kembali ke Beranda</a>
        </div>
    @else
        <div class="w-full max-w-2xl mx-auto flex flex-col items-center text-center">
            <h1 class="font-heading text-heading md:text-xl font-bold text-on-surface mb-6">
                {{ $soal['pertanyaan'] }}
            </h1>

            <div class="w-full bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center justify-center text-center relative overflow-hidden mb-8 border border-gray-100">
                <div class="absolute top-0 left-0 w-full h-1 bg-primary-500"></div>
                <p id="targetSentence" class="font-stat-number text-stat-number text-primary-700 font-extrabold tracking-tight mb-4">
                    “{{ $soal['teks_referensi'] }}”
                </p>

                <button id="btn-tts" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container hover:bg-surface-container-high text-primary-700 font-body text-body font-semibold rounded-xl shadow-sm transition">
                    <span id="tts-icon" class="material-symbols-outlined text-[18px]">volume_up</span>
                    <span id="tts-label">Dengarkan Contoh (edge-tts)</span>
                </button>
            </div>

            <div class="flex flex-col items-center justify-center mb-6 relative w-full">
                <div id="pulseRing" class="absolute w-28 h-28 rounded-full bg-primary-400/20 animate-ping opacity-0 pointer-events-none transition-opacity"></div>
                <div id="outerGlow" class="absolute w-24 h-24 rounded-full bg-primary-500/10 pointer-events-none"></div>
                <button id="btn-mic" type="button" aria-label="Mulai Merekam Suara" class="relative z-10 w-20 h-20 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary shadow-md flex flex-col items-center justify-center transition-all duration-300 transform active:scale-95">
                    <span id="mic-icon" class="material-symbols-outlined text-[36px]">mic</span>
                    <span id="mic-label" class="sr-only">Rekam</span>
                </button>

                <div class="mt-4 flex items-center gap-2 text-on-surface-variant font-caption text-caption">
                    <span id="status-mic">Ketuk untuk Mulai Rekam Ucapan</span>
                    <span id="timer" class="font-caption text-caption font-bold text-primary-700 bg-primary-fixed/50 px-2.5 py-0.5 rounded-md hidden">00:00 / 00:10</span>
                    <div id="waveform" class="hidden flex items-center gap-1">
                        <span class="w-1 h-3 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-1 h-5 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-1 h-2 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        <span class="w-1 h-4 bg-primary-600 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                    </div>
                </div>
            </div>

            <div id="feedback" class="hidden w-full bg-white rounded-2xl shadow-sm p-5 border text-left transition-all duration-300 mb-6"></div>

            <div class="w-full flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-200">
                <button id="btn-demo" type="button" @disabled(! $soal) class="inline-flex items-center gap-2 rounded-full bg-gray-100 hover:bg-gray-200 text-on-surface font-body text-body font-bold px-5 py-2.5 transition-colors disabled:opacity-50">
                    <span class="material-symbols-outlined text-[20px]">play_circle</span>
                    <span>Coba Mode Demo</span>
                </button>
                <span class="font-caption text-caption text-gray-400">Pipeline: ElevenLabs STT → Fuzzy Matching → edge-tts</span>
            </div>
        </div>
    @endif
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const feedback = document.getElementById('feedback');
            const btnMic = document.getElementById('btn-mic');
            const micIcon = document.getElementById('mic-icon');
            const pulseRing = document.getElementById('pulseRing');
            const statusMic = document.getElementById('status-mic');
            const timerEl = document.getElementById('timer');
            const waveform = document.getElementById('waveform');
            const btnTts = document.getElementById('btn-tts');
            const ttsIcon = document.getElementById('tts-icon');
            const ttsLabel = document.getElementById('tts-label');

            let recorder = null, chunks = [], recording = false, timer = null, seconds = 0;

            async function showResult(res) {
                let jawabRes = null;
                try {
                    jawabRes = await window.postJSON(window.KUIS.jawabUrl, {
                        soal_id: SOAL.id,
                        jawaban: { transcript: res.transcript ?? '' }
                    });
                } catch(e) { console.error(e); }

                const expText = jawabRes ? ` • +${jawabRes.exp_didapat} XP` : '';
                feedback.className = 'w-full mb-6 text-left rounded-2xl p-5 border shadow-sm ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

                let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                if (jawabRes && jawabRes.next_url) {
                    nextButtons += `<a href="${jawabRes.next_url}" class="rounded-full bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body hover:bg-primary-700 transition shadow-sm">Soal Selanjutnya</a>`;
                }
                nextButtons += `<a href="{{ route('siswa.dashboard') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Beranda</a></div>`;

                let levelSelesaiHtml = '';
                if (jawabRes && jawabRes.level_selesai) {
                    levelSelesaiHtml = `
                        <div class="mt-3 p-4 rounded-xl bg-green-500/20 border border-green-500/40 text-green-700">
                            <div class="font-heading text-heading font-extrabold flex items-center gap-1.5">
                                <span class="material-symbols-outlined icon-fill">military_tech</span>
                                Level Selesai! Anda mendapatkan bonus +${jawabRes.reward_exp} EXP!
                            </div>
                            <p class="font-body text-body text-green-800 mt-1">Level selanjutnya <strong>${jawabRes.level_berikutnya ?? ''}</strong> sekarang sudah terbuka.</p>
                        </div>`;
                }

                feedback.innerHTML = `
                    <div class="flex items-center gap-2 font-heading text-heading font-extrabold ${res.benar ? 'text-green-600' : 'text-error'}">
                        <span class="material-symbols-outlined icon-fill">${res.benar ? 'verified' : 'cancel'}</span>
                        ${res.benar ? 'LANCAR & BENER!' : 'Belum tepat'} • Skor ${res.skor ?? 0}/100${expText}
                    </div>
                    <p class="font-body text-body text-on-surface-variant mt-1">Terdeteksi: <strong>“${res.transcript ?? '-'}”</strong> • Rekor: ${(jawabRes ? jawabRes.skor_tertinggi : res.skor)}/100</p>
                    <p class="font-body text-body text-on-surface mt-1">${res.balasan_teks ?? ''}</p>
                    ${levelSelesaiHtml}
                    ${nextButtons}
                    ${res.mock ? '<p class="font-caption text-caption text-gray-500 mt-2">(Mode mock — API vendor belum dikonfigurasi)</p>' : ''}`;
                feedback.classList.remove('hidden');
            }

            async function kirimAudio(base64) {
                statusMic.textContent = 'Memproses suara...';
                try {
                    const res = await window.postJSON(window.KUIS.stsUrl, {
                        audio: base64,
                        teks_referensi: SOAL.teks_referensi,
                    });
                    showResult(res);
                } catch (e) {
                    alert(e.message);
                } finally {
                    statusMic.textContent = 'Status Mikrofon: Siap Merekam';
                }
            }

            function stopRecording() {
                recording = false;
                clearInterval(timer);
                waveform.classList.add('hidden');
                timerEl.classList.add('hidden');
                pulseRing.classList.add('opacity-0');
                if (micIcon) micIcon.textContent = 'mic';
                btnMic.classList.remove('bg-error');
                btnMic.classList.add('bg-primary-600');
                if (recorder && recorder.state !== 'inactive') recorder.stop();
            }

            async function startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    recorder = new MediaRecorder(stream);
                    chunks = [];
                    recorder.ondataavailable = (e) => chunks.push(e.data);
                    recorder.onstop = () => {
                        const blob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });
                        const reader = new FileReader();
                        reader.onloadend = () => kirimAudio(reader.result.split(',')[1] || '');
                        reader.readAsDataURL(blob);
                        stream.getTracks().forEach((t) => t.stop());
                    };
                    recorder.start();
                    recording = true;
                    seconds = 0;
                    waveform.classList.remove('hidden');
                    timerEl.classList.remove('hidden');
                    pulseRing.classList.remove('opacity-0');
                    if (micIcon) micIcon.textContent = 'stop';
                    btnMic.classList.remove('bg-primary-600');
                    btnMic.classList.add('bg-error');
                    statusMic.textContent = 'Nyemak swara panjenengan...';
                    timer = setInterval(() => {
                        seconds = Math.min(seconds + 1, 10);
                        timerEl.textContent = `00:0${seconds} / 00:10`;
                        if (seconds >= 10) stopRecording();
                    }, 1000);
                } catch (e) {
                    alert('Mikrofon tidak tersedia. Gunakan "Coba Mode Demo".');
                }
            }

            btnMic?.addEventListener('click', () => recording ? stopRecording() : startRecording());

            document.getElementById('btn-demo')?.addEventListener('click', async () => {
                try {
                    const res = await window.postJSON(window.KUIS.stsUrl, {
                        audio: 'demo',
                        teks_referensi: SOAL.teks_referensi,
                        mock_transcript: SOAL.teks_referensi,
                    });
                    showResult(res);
                } catch (e) { alert(e.message); }
            });

            btnTts?.addEventListener('click', async () => {
                if (ttsIcon) {
                    ttsIcon.textContent = 'progress_activity';
                    ttsIcon.classList.add('animate-spin');
                }
                if (ttsLabel) ttsLabel.textContent = 'Nyetel Swara...';

                try {
                    const res = await window.postJSON(window.KUIS.ttsUrl, { teks: SOAL.teks_referensi });
                    if (res.audio_base64) {
                        const audio = new Audio('data:' + res.mime + ';base64,' + res.audio_base64);
                        audio.play();
                    } else if (res.audio_url) {
                        new Audio(res.audio_url).play();
                    } else {
                        alert('Mode mock: audio TTS belum tersedia.');
                    }
                } catch (e) {
                    alert(e.message);
                } finally {
                    if (ttsIcon) {
                        ttsIcon.textContent = 'volume_up';
                        ttsIcon.classList.remove('animate-spin');
                    }
                    if (ttsLabel) ttsLabel.textContent = 'Dengarkan Contoh (edge-tts)';
                }
            });
        </script>
    @endpush
@endif
