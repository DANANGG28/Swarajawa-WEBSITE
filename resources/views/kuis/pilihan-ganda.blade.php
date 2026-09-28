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

                    const kunciDisplay = res.detail?.kunci_teks
                        ? (res.detail.kunci_label ? res.detail.kunci_label + '. ' : '') + res.detail.kunci_teks
                        : (res.detail?.kunci ?? '-');

                    // Tampilkan Popup Feedback Terpusat di Tengah dengan Emoji
                    if (window.showQuizFeedbackModal) {
                        window.showQuizFeedbackModal({
                            benar: res.benar,
                            skor: res.skor,
                            skor_tertinggi: res.skor_tertinggi,
                            exp_didapat: res.exp_didapat,
                            total_exp: res.total_exp,
                            current_streak: res.current_streak,
                            kunciDisplay: kunciDisplay,
                            level_selesai: res.level_selesai,
                            reward_exp: res.reward_exp,
                            level_berikutnya: res.level_berikutnya,
                            next_url: res.next_url,
                            next_level_url: res.next_level_url,
                            title: res.benar ? 'Jawabanmu Bener!' : 'Durung Pas!'
                        });
                    }

                    cards.forEach((c) => {
                        c.dataset.done = '1';
                        c.disabled = true;
                        const mark = c.querySelector('.opsi-mark');
                        const badge = c.querySelector('.opsi-badge');

                        const isSelected = String(c.dataset.label) === String(selected);
                        const isCorrectCard = (res.benar && isSelected) ||
                            (res.detail?.kunci_label && String(c.dataset.label) === String(res.detail.kunci_label)) ||
                            (res.detail?.kunci_teks && c.dataset.teks === res.detail.kunci_teks) ||
                            (res.kunci_jawaban?.jawaban && String(c.dataset.label) === String(res.kunci_jawaban.jawaban)) ||
                            (res.detail?.kunci && String(c.dataset.teks).trim().toLowerCase() === String(res.detail.kunci).trim().toLowerCase());

                        c.classList.remove(...STATE_LIHAT, 'border-green-500', 'bg-green-500/10', 'border-error', 'bg-error-container/40');
                        badge.classList.remove('bg-primary-600', 'text-on-primary', 'bg-green-500', 'bg-error', 'text-white');
                        mark.classList.remove('text-primary-600', 'text-green-500', 'text-error');

                        if (isCorrectCard) {
                            c.classList.add('border-green-500', 'bg-green-500/10');
                            badge.classList.add('bg-green-500', 'text-white');
                            mark.textContent = 'check_circle';
                            mark.classList.add('text-green-500');
                            mark.style.opacity = '1';
                        } else if (isSelected && !res.benar) {
                            c.classList.add('border-error', 'bg-error-container/40');
                            badge.classList.add('bg-error', 'text-white');
                            mark.textContent = 'cancel';
                            mark.classList.add('text-error');
                            mark.style.opacity = '1';
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
