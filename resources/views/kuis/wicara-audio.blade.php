@extends('layouts.kuis')

@section('konten')
    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">mic</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal kuis wicara</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ url('/latihan-soal') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Kembali ke Latihan</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <section class="bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold mb-2">
                    <span class="material-symbols-outlined text-[18px]">graphic_eq</span>
                    Kuis Wicara (STS/STT) • FR-8
                </div>
                <h1 class="font-display text-display font-extrabold text-on-surface leading-snug">{{ $soal['pertanyaan'] }}</h1>
                <div class="mt-4 p-5 rounded-2xl bg-primary-fixed/40 border border-primary-400/30 text-center">
                    <p class="font-heading text-heading font-extrabold text-on-surface">“{{ $soal['teks_referensi'] }}”</p>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-100">
                    <button type="button" id="btn-tts" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">volume_up</span>
                        Dengarkan Contoh (Azure TTS)
                    </button>
                    <span class="font-caption text-caption text-gray-500">Pipeline: Google STT → Modul Pemrosesan → Azure TTS</span>
                </div>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center">
                <div class="w-full flex items-center justify-between mb-2">
                    <span class="inline-flex items-center gap-1.5 font-caption text-caption font-bold text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                        <span id="status-mic">Status Mikrofon: Siap Merekam</span>
                    </span>
                    <span id="timer" class="font-caption text-caption font-bold text-primary-700 bg-primary-fixed/50 px-2.5 py-1 rounded-md">00:00 / 00:10</span>
                </div>

                <div class="my-4 relative flex items-center justify-center">
                    <div class="absolute w-32 h-32 rounded-full bg-primary-400/30 mic-pulse-ring pointer-events-none"></div>
                    <button id="btn-mic" type="button" class="relative z-10 w-24 h-24 rounded-full bg-gradient-to-tr from-primary-700 via-primary-600 to-primary-500 text-on-primary flex flex-col items-center justify-center shadow-md hover:scale-105 active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-[30px]">mic</span>
                        <span id="mic-label" class="font-caption text-caption font-bold uppercase">Rekam</span>
                    </button>
                </div>

                <div id="waveform" class="hidden w-full max-w-sm flex items-end justify-center gap-1.5 h-10 my-1">
                    @for ($i = 0; $i < 12; $i++)
                        <div class="wave-bar w-1.5 bg-primary-400 rounded-full"></div>
                    @endfor
                </div>

                <p class="font-caption text-caption text-gray-500 mt-2">Tekan tombol mic untuk memulai, tekan lagi untuk mengakhiri. Jika mikrofon tidak tersedia, sistem menggunakan mode demo.</p>

                <div id="feedback" class="hidden w-full mt-4 text-left rounded-2xl p-5 border"></div>
            </section>
        </div>
    @endif
@endsection

@section('aksi')
    @include('partials.kuis-xp', ['soal' => $soal])
    <button id="btn-demo" type="button" @disabled(! $soal)
        class="flex items-center gap-2 rounded-full bg-gray-50 hover:bg-surface-container-high border border-gray-200 text-on-surface font-body text-body font-bold px-6 py-3 transition-colors disabled:opacity-50">
        <span class="material-symbols-outlined text-[20px]">play_circle</span>
        <span>Coba Mode Demo</span>
    </button>
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const feedback = document.getElementById('feedback');
            const btnMic = document.getElementById('btn-mic');
            const micLabel = document.getElementById('mic-label');
            const statusMic = document.getElementById('status-mic');
            const timerEl = document.getElementById('timer');
            const waveform = document.getElementById('waveform');

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
                feedback.className = 'w-full mt-4 text-left rounded-2xl p-5 border ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

                let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                if (jawabRes && jawabRes.next_url) {
                    nextButtons += `<a href="${jawabRes.next_url}" class="rounded-full bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body hover:bg-primary-700 transition shadow-sm">Soal Selanjutnya</a>`;
                }
                nextButtons += `<a href="{{ route('siswa.latihan') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Daftar Soal</a>`;
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
                    <div class="flex items-center gap-2 font-heading text-heading font-extrabold ${res.benar ? 'text-green-500' : 'text-error'}">
                        <span class="material-symbols-outlined icon-fill">${res.benar ? 'verified' : 'cancel'}</span>
                        ${res.benar ? 'Pelafalan benar!' : 'Belum tepat'} • Skor ${res.skor ?? 0}/100${expText}
                    </div>
                    <p class="font-body text-body text-on-surface-variant mt-1">Terdeteksi: <strong>${res.transcript ?? '-'}</strong> • Rekor: ${(jawabRes ? jawabRes.skor_tertinggi : res.skor)}/100</p>
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
                micLabel.textContent = 'Rekam';
                btnMic.classList.remove('bg-error');
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
                    micLabel.textContent = 'Berhenti';
                    btnMic.classList.add('bg-error');
                    statusMic.textContent = 'Status Mikrofon: Merekam...';
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

            document.getElementById('btn-tts')?.addEventListener('click', async () => {
                try {
                    const res = await window.postJSON(window.KUIS.ttsUrl, { teks: SOAL.teks_referensi });
                    if (res.audio_base64) {
                        new Audio('data:' + res.mime + ';base64,' + res.audio_base64).play();
                    } else {
                        alert('Mode mock: audio TTS belum tersedia (API Azure belum dikonfigurasi).');
                    }
                } catch (e) { alert(e.message); }
            });
        </script>
    @endpush
@endif
