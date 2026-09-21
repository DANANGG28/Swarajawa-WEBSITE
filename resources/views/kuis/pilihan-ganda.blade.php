@extends('layouts.kuis-fokus')

@section('konten')
    @if (! $soal)
        <div class="rounded-3xl p-10 text-center border-2 border-gray-200">
            <span class="material-symbols-outlined text-[48px] text-gray-500">quiz</span>
            <h2 class="font-heading text-2xl font-extrabold text-on-surface mt-3">Belum ana soal pilihan ganda</h2>
            <p class="font-body text-base text-gray-500 mt-1">Rampungna level sadurunge utawa hubungi guru kanggo nambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-6 rounded-2xl bg-primary-600 text-on-primary px-7 py-3 font-body text-base font-bold shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">Bali menyang Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-8 md:gap-10">
            <section class="flex flex-col gap-3">
                <div class="font-label-upper text-sm uppercase tracking-widest text-primary-600 font-bold">
                    Latihan {{ $progress['nomor'] ?? 0 }} dari {{ $progress['total'] ?? 0 }}
                </div>
                <h1 class="font-display text-2xl md:text-3xl font-extrabold text-on-surface leading-snug">
                    {{ $soal['pertanyaan'] }}
                </h1>
            </section>

            <section>
                <div id="opsi-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($soal['opsi'] as $opsi)
                        <button type="button"
                            data-label="{{ $opsi['label'] ?? $loop->index }}"
                            data-teks="{{ $opsi['teks'] ?? $opsi }}"
                            class="opsi-card group flex items-center gap-4 p-4 md:p-5 rounded-2xl border-2 border-gray-200 bg-surface-container-lowest hover:border-primary-400 hover:bg-primary-fixed/20 transition-all text-left active:translate-y-[2px]">
                            <span class="opsi-badge w-11 h-11 rounded-xl bg-surface-container-high flex items-center justify-center font-heading text-lg font-extrabold text-on-surface-variant shrink-0 transition-colors">
                                {{ $opsi['label'] ?? chr(65 + $loop->index) }}
                            </span>
                            <span class="opsi-teks font-heading text-base md:text-lg font-bold text-on-surface leading-snug">{{ $opsi['teks'] ?? $opsi }}</span>
                            <span class="opsi-mark material-symbols-outlined text-[24px] opacity-0 shrink-0 ml-auto transition-opacity">radio_button_checked</span>
                        </button>
                    @endforeach
                </div>
            </section>

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
            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-2xl bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:shadow-none disabled:translate-y-0 text-on-primary font-heading text-base font-extrabold uppercase tracking-wide px-8 py-3.5 shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">
            <span>Periksa Jawaban</span>
            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
        </button>
    @else
        <span></span>
    @endif
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const cards = Array.from(document.querySelectorAll('.opsi-card'));
            const feedback = document.getElementById('feedback');
            const btn = document.getElementById('btn-kirim');
            let selected = null;

            const STATE_DEFAULT = ['border-gray-200'];
            const STATE_LIHAT = ['border-primary-600', 'bg-primary-fixed/40'];

            function bersihkan() {
                cards.forEach((c) => {
                    c.classList.remove(...STATE_LIHAT, 'border-green-500', 'bg-green-500/10', 'border-error', 'bg-error-container/40');
                    c.classList.add(...STATE_DEFAULT);
                    c.querySelector('.opsi-mark').style.opacity = '0';
                    c.querySelector('.opsi-mark').textContent = 'radio_button_checked';
                    c.querySelector('.opsi-mark').classList.remove('text-primary-600', 'text-green-500', 'text-error');
                    c.querySelector('.opsi-badge').classList.remove('bg-primary-600', 'text-on-primary');
                });
            }

            cards.forEach((card) => {
                card.addEventListener('click', () => {
                    if (card.dataset.done === '1') return;
                    bersihkan();
                    card.classList.remove(...STATE_DEFAULT);
                    card.classList.add(...STATE_LIHAT);
                    card.querySelector('.opsi-badge').classList.add('bg-primary-600', 'text-on-primary');
                    const mark = card.querySelector('.opsi-mark');
                    mark.style.opacity = '1';
                    mark.classList.add('text-primary-600');
                    selected = card.dataset.label;
                    btn.disabled = false;
                    window.KuisFx.pilih();
                });
            });

            btn?.addEventListener('click', async () => {
                if (!selected) return;
                btn.disabled = true;
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: selected });

                    if (res.benar && res.level_selesai) {
                        window.KuisFx.menang();
                    } else if (res.benar) {
                        window.KuisFx.benar();
                    } else {
                        window.KuisFx.salah();
                    }

                    feedback.className = 'rounded-2xl p-5 border-2 ' + (res.benar ? 'bg-green-500/10 border-green-500/40' : 'bg-error-container/50 border-error/40');

                    let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                    if (res.next_url) {
                        nextButtons += `<a href="${res.next_url}" class="rounded-2xl bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body shadow-[0_3px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">Soal Selanjutnya</a>`;
                    }
                    nextButtons += `<a href="{{ route('siswa.dashboard') }}" class="rounded-2xl bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition-colors">Beranda</a></div>`;

                    let levelSelesaiHtml = '';
                    if (res.level_selesai) {
                        levelSelesaiHtml = `
                            <div class="mt-3 p-4 rounded-xl bg-green-500/20 border border-green-500/40 text-green-700">
                                <div class="font-heading text-lg font-extrabold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined icon-fill">military_tech</span>
                                    Level Selesai! Bonus +${res.reward_exp} EXP!
                                </div>
                                <p class="font-body text-base text-green-800 mt-1">Level sabanjure <strong>${res.level_berikutnya ?? ''}</strong> saiki wis kabukak.</p>
                            </div>`;
                    }

                    feedback.innerHTML = `
                        <div class="flex items-center gap-2 font-heading text-lg font-extrabold ${res.benar ? 'text-green-600' : 'text-error'}">
                            <span class="material-symbols-outlined icon-fill">${res.benar ? 'verified' : 'cancel'}</span>
                            ${res.benar ? 'Bener!' : 'Durung pas'} • Skor ${res.skor}/100 • +${res.exp_didapat} XP
                        </div>
                        <p class="font-body text-base text-on-surface-variant mt-1">Kunci: <strong>${res.detail?.kunci ?? '-'}</strong> • Rekor ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} dina</p>
                        ${levelSelesaiHtml}
                        ${nextButtons}`;
                    feedback.classList.remove('hidden');

                    cards.forEach((c) => {
                        c.dataset.done = '1';
                        c.disabled = true;
                        const mark = c.querySelector('.opsi-mark');
                        mark.style.opacity = '1';
                        if (c.dataset.teks === res.detail?.kunci) {
                            c.classList.remove(...STATE_LIHAT);
                            c.classList.add('border-green-500', 'bg-green-500/10');
                            mark.textContent = 'check_circle';
                            mark.classList.add('text-green-500');
                        } else if (c.dataset.label === selected) {
                            c.classList.remove(...STATE_LIHAT);
                            c.classList.add('border-error', 'bg-error-container/40');
                            mark.textContent = 'cancel';
                            mark.classList.add('text-error');
                        } else {
                            mark.style.opacity = '0';
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
