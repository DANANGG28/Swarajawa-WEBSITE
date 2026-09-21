@extends('layouts.kuis')

@section('konten')
    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">quiz</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal pilihan ganda</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Kembali ke Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <section class="flex flex-col gap-2">
                <div class="font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold">Latihan Pilihan Ganda (FR-3)</div>
                <h1 class="font-display text-display font-extrabold text-on-surface leading-snug">{{ $soal['pertanyaan'] }}</h1>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-heading text-heading font-bold text-on-surface">Pilihan Jawaban</span>
                    <span class="font-caption text-caption text-gray-500">Pilih 1 jawaban yang paling benar</span>
                </div>
                <div id="opsi-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($soal['opsi'] as $opsi)
                        <button type="button"
                            data-label="{{ $opsi['label'] ?? $loop->index }}"
                            data-teks="{{ $opsi['teks'] ?? $opsi }}"
                            class="opsi-card group flex items-start gap-4 p-4 rounded-xl border-2 border-gray-200 bg-surface-container-lowest hover:border-primary-400 transition-all text-left">
                            <span class="opsi-badge w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center font-heading text-heading font-bold text-on-surface shrink-0">
                                {{ $opsi['label'] ?? chr(65 + $loop->index) }}
                            </span>
                            <span class="font-heading text-heading font-bold text-on-surface mt-1.5">{{ $opsi['teks'] ?? $opsi }}</span>
                            <span class="opsi-check ml-auto w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 mt-2">
                                <span class="material-symbols-outlined text-[16px] text-on-primary opacity-0">check</span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>

            <section id="feedback" class="hidden rounded-2xl p-5 border"></section>
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
            const SOAL = @json($soal);
            let selected = null;

            document.querySelectorAll('.opsi-card').forEach((card) => {
                card.addEventListener('click', () => {
                    if (card.dataset.done === '1') return;
                    document.querySelectorAll('.opsi-card').forEach((c) => {
                        c.classList.remove('border-primary-600', 'bg-primary-fixed/60');
                        c.classList.add('border-gray-200');
                        c.querySelector('.opsi-badge').classList.remove('bg-primary-600', 'text-on-primary');
                        c.querySelector('.opsi-badge').classList.add('bg-surface-container-high', 'text-on-surface');
                        c.querySelector('.opsi-check').classList.remove('bg-primary-600', 'border-primary-600');
                        c.querySelector('.opsi-check .material-symbols-outlined').style.opacity = '0';
                    });
                    card.classList.remove('border-gray-200');
                    card.classList.add('border-primary-600', 'bg-primary-fixed/60');
                    card.querySelector('.opsi-badge').classList.add('bg-primary-600', 'text-on-primary');
                    card.querySelector('.opsi-check').classList.add('bg-primary-600', 'border-primary-600');
                    card.querySelector('.opsi-check .material-symbols-outlined').style.opacity = '1';
                    selected = card.dataset.label;
                });
            });

            const feedback = document.getElementById('feedback');
            const btn = document.getElementById('btn-kirim');

            btn?.addEventListener('click', async () => {
                if (!selected) {
                    alert('Pilih jawaban terlebih dahulu.');
                    return;
                }
                btn.disabled = true;
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: selected });
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
                            ${res.benar ? 'Jawaban benar!' : 'Belum tepat'} • Skor ${res.skor}/100 • +${res.exp_didapat} XP
                        </div>
                        <p class="font-body text-body text-on-surface-variant mt-1">Kunci: <strong>${res.detail?.kunci ?? '-'}</strong> • Rekor Skor: ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} hari</p>
                        ${levelSelesaiHtml}
                        ${nextButtons}`;
                    feedback.classList.remove('hidden');
                    document.querySelectorAll('.opsi-card').forEach((c) => {
                        c.dataset.done = '1';
                        if (c.dataset.teks === res.detail?.kunci) {
                            c.classList.add('border-green-500', 'bg-green-500/10');
                        } else if (c.dataset.label === selected) {
                            c.classList.add('border-error', 'bg-error-container/40');
                        }
                    });
                } catch (e) {
                    alert(e.message);
                    btn.disabled = false;
                }
            });
        </script>
    @endpush
@endif
