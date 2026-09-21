@extends('layouts.kuis')

@section('konten')
    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">draw</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal tracing aksara</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Kembali ke Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <section class="bg-surface-container-lowest rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <div class="font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold mb-1">Latihan Menulis Aksara (FR-22 Tracing)</div>
                    <h1 class="font-display text-display font-extrabold text-on-surface leading-snug">Telusuri bentuk aksara dengan menggambar pada kanvas!</h1>
                    <p class="font-caption text-caption text-gray-500 mt-1">
                        Aksara: <strong class="font-javanese text-on-surface text-lg">{{ $soal['aksara'] ?? '-' }}</strong>
                        @if (! empty($soal['soal_latin'])) <span class="mx-1">•</span> Latin: <strong class="text-on-surface">{{ $soal['soal_latin'] }}</strong> @endif
                        • {{ $soal['petunjuk'] ?? 'Telusuri bayangan aksara.' }}
                    </p>
                </div>
                @if (! empty($soal['aksara']))
                    <button id="btn-tts" type="button" class="self-start md:self-center inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-primary-fixed/50 hover:bg-primary-fixed border border-primary-400/30 text-on-surface font-body text-body font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary-600">volume_up</span>
                        Pengucapan: <strong class="font-javanese text-primary-700">{{ $soal['aksara'] }}</strong>
                    </button>
                @endif
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col gap-5">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700">
                            <span class="material-symbols-outlined">draw</span>
                        </div>
                        <div>
                            <h2 class="font-heading text-heading font-bold text-on-surface">Kanvas Tracing Aksara</h2>
                            <p class="font-caption text-caption text-gray-500">Telusuri garis putus-putus mengikuti bentuk aksara</p>
                        </div>
                    </div>
                    <span id="canvas-status" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-caption text-caption font-bold bg-primary-fixed/60 text-primary-700">
                        <span class="w-2 h-2 rounded-full bg-primary-600 animate-ping"></span>
                        Goresan Aktif
                    </span>
                </div>

                <div id="canvas-wrap" class="relative w-full h-[440px] rounded-2xl border-2 border-primary-400/30 overflow-hidden select-none touch-none bg-[#14352a]">
                    <canvas id="tracing-canvas" class="absolute inset-0 w-full h-full cursor-crosshair touch-none"></canvas>
                    <div id="canvas-loading" class="absolute inset-0 flex items-center justify-center text-white/80 font-body text-body gap-2">
                        <span class="material-symbols-outlined animate-spin">progress_activity</span> Nyiapake kanvas…
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2">
                        <button id="btn-reset" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-surface-container-high border border-gray-200 text-on-surface font-body text-body font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-primary-600">refresh</span> Hapus Goresan
                        </button>
                    </div>
                    <button id="btn-snap" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-fixed text-primary-700 hover:bg-primary-fixed-dim font-body text-body font-bold transition-colors">
                        <span class="material-symbols-outlined text-[18px]">magic_button</span> Rapikan Goresan
                    </button>
                </div>

                <div id="canvas-hint-bar" class="rounded-xl border border-gray-100 bg-surface-container-low p-3 text-center font-body text-body text-gray-500">
                    Telusuri bentuk aksara nganti ketemu. Yen kurang mirip, kanvas bakal mbaleni otomatis.
                </div>
                <div id="canvas-popup" class="hidden rounded-2xl p-4 border text-center font-body text-body font-bold"></div>

                <div id="feedback" class="hidden rounded-2xl p-5 border"></div>
            </section>
        </div>
    @endif
@endsection

@section('aksi')
    @include('partials.kuis-xp', ['soal' => $soal])
    <button id="btn-kirim" type="button" @disabled(! $soal)
        class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-on-primary font-body text-body font-bold px-8 py-3 shadow-sm transition-colors">
        <span>Periksa &amp; Kirim Jawaban</span>
        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
    </button>
@endsection

@if ($soal)
    @push('skrip')
        <script>
            (function () {
                const SOAL = @json($soal);
                const FALLBACK = (SOAL.paths || []).map((s) => s.map((p) => ({ x: p[0], y: p[1] })));

                const feedback = document.getElementById('feedback');
                const statusChip = document.getElementById('canvas-status');
                const popup = document.getElementById('canvas-popup');
                const loading = document.getElementById('canvas-loading');
                let tracer = null;
                let popupTimer = null;

                function showPopup(message, type) {
                    if (!popup) return;
                    const green = type === 'success';
                    popup.className = 'rounded-2xl p-4 border text-center font-body text-body font-bold ' + (green ? 'bg-green-500/10 border-green-500/30 text-green-600' : 'bg-error-container/60 border-error/30 text-error');
                    popup.textContent = message;
                    popup.classList.remove('hidden');
                    if (popupTimer) clearTimeout(popupTimer);
                    popupTimer = setTimeout(() => popup.classList.add('hidden'), 1600);
                }

                function markSuccess() {
                    statusChip.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-caption text-caption font-bold bg-green-500/15 text-green-600';
                    statusChip.innerHTML = '<span class="w-2 h-2 rounded-full bg-green-500"></span> Berhasil!';
                    showPopup('Berhasil! Aksara wis bener.', 'success');
                }

                function markActive() {
                    statusChip.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-caption text-caption font-bold bg-primary-fixed/60 text-primary-700';
                    statusChip.innerHTML = '<span class="w-2 h-2 rounded-full bg-primary-600 animate-ping"></span> Goresan Aktif';
                }

                function start() {
                    if (!window.TracingCanvas) { setTimeout(start, 50); return; }

                    const canvas = document.getElementById('tracing-canvas');
                    tracer = new window.TracingCanvas(canvas, FALLBACK, {
                        aksara: SOAL.aksara || null,
                        passThreshold: 0.7,
                        onSuccess: () => markSuccess(),
                        onFail: () => showPopup('Kurang mirip, ayo dicoba maneh.', 'fail'),
                    });

                    if (loading) loading.remove();

                    document.getElementById('btn-reset')?.addEventListener('click', () => {
                        tracer.reset();
                        markActive();
                    });

                    document.getElementById('btn-snap')?.addEventListener('click', () => tracer.snapLast());

                    const ttsBtn = document.getElementById('btn-tts');
                    ttsBtn?.addEventListener('click', async () => {
                        if (!SOAL.aksara) return;
                        try {
                            const res = await window.postJSON(window.KUIS.ttsUrl, { teks: SOAL.aksara });
                            if (res.audio_url) { new Audio(res.audio_url).play(); }
                        } catch (e) {
                            alert('Gagal membunyikan aksara: ' + e.message);
                        }
                    });

                    document.getElementById('btn-kirim')?.addEventListener('click', async () => {
                        const strokes = tracer.getStrokes();
                        if (!strokes.length) { alert('Goreskan aksara terlebih dahulu pada kanvas.'); return; }

                        try {
                            const res = await window.postJSON(window.KUIS.jawabUrl, {
                                soal_id: SOAL.id,
                                jawaban: { strokes, template: tracer.getTemplate() },
                            });
                            feedback.className = 'rounded-2xl p-5 border ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

                            let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                            if (res.next_url) {
                                nextButtons += `<a href="${res.next_url}" class="rounded-full bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body hover:bg-primary-700 transition shadow-sm">Soal Selanjutnya</a>`;
                            }
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
                                    ${res.benar ? 'Berhasil! Tulisanmu wis mirip.' : 'Durung mirip, ayo dicoba maneh.'}
                                </div>
                                <p class="font-body text-body text-on-surface-variant mt-1">+${res.exp_didapat} XP • Total EXP ${res.total_exp} • Streak ${res.current_streak} hari</p>
                                ${levelSelesaiHtml}
                                ${nextButtons}`;
                            feedback.classList.remove('hidden');
                        } catch (e) {
                            alert(e.message);
                        }
                    });
                }

                start();
            })();
        </script>
    @endpush
@endif
