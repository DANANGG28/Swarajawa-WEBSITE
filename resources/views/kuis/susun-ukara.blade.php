@extends('layouts.kuis')

@section('konten')
    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">segment</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Belum ada soal susun kalimat</h2>
            <p class="font-body text-body text-gray-500 mt-1">Selesaikan level sebelumnya atau hubungi guru untuk menambah soal.</p>
            <a href="{{ url('/latihan-soal') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Kembali ke Latihan</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <section class="flex flex-col gap-2">
                <div class="font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold">Latihan Susun Kalimat (FR-4)</div>
                <h1 class="font-display text-display font-extrabold text-on-surface leading-snug">{{ $soal['pertanyaan'] }}</h1>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 border-2 border-dashed border-primary-400/60 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-dashed border-gray-200">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Kalimat yang Disusun</span>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btn-undo" class="font-caption text-caption font-bold text-gray-500 hover:text-error">Hapus Terakhir</button>
                        <button type="button" id="btn-reset" class="font-caption text-caption font-bold text-gray-500 hover:text-error">Ulangi Semua</button>
                    </div>
                </div>
                <div id="zona-ukara" class="min-h-[110px] flex flex-wrap items-center gap-2.5 p-3 bg-primary-fixed/30 rounded-2xl border border-gray-100">
                    <span id="placeholder" class="font-body text-body text-gray-500">Klik kata di bawah untuk menyusun kalimat...</span>
                </div>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary-600">dataset</span>
                    <h3 class="font-heading text-heading font-bold text-on-surface">Bank Kata Pilihan</h3>
                </div>
                <div id="bank-tembung" class="flex flex-wrap gap-2.5"></div>
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
            const bank = document.getElementById('bank-tembung');
            const zona = document.getElementById('zona-ukara');
            const placeholder = document.getElementById('placeholder');

            const words = SOAL.opsi.map((w, i) => ({ word: typeof w === 'string' ? w : (w.teks ?? w), uid: i }));

            function chipButton(item, where) {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'tactile-chip inline-flex items-center gap-1.5 px-3.5 py-2 bg-surface-container-lowest border border-gray-200 text-on-surface rounded-xl font-body text-body font-semibold shadow-sm hover:border-primary-400 transition-all';
                b.innerHTML = `<span>${item.word}</span><span class="material-symbols-outlined text-[16px] text-gray-500">${where === 'zona' ? 'close' : 'add'}</span>`;
                return b;
            }

            function render() {
                bank.innerHTML = '';
                words.filter((w) => !w.placed).forEach((item) => {
                    const b = chipButton(item, 'bank');
                    b.addEventListener('click', () => { item.placed = true; render(); });
                    bank.appendChild(b);
                });
                zona.querySelectorAll('[data-chip]').forEach((n) => n.remove());
                words.filter((w) => w.placed).forEach((item) => {
                    const b = chipButton(item, 'zona');
                    b.dataset.chip = item.uid;
                    b.addEventListener('click', () => { item.placed = false; render(); });
                    zona.appendChild(b);
                });
                placeholder.style.display = words.some((w) => w.placed) ? 'none' : 'block';
            }

            document.getElementById('btn-undo')?.addEventListener('click', () => {
                const placed = words.filter((w) => w.placed);
                if (placed.length) { placed[placed.length - 1].placed = false; render(); }
            });
            document.getElementById('btn-reset')?.addEventListener('click', () => {
                words.forEach((w) => { w.placed = false; }); render();
            });

            render();

            const feedback = document.getElementById('feedback');
            document.getElementById('btn-kirim')?.addEventListener('click', async () => {
                const answer = words.filter((w) => w.placed).map((w) => w.word);
                if (!answer.length) { alert('Susun kata terlebih dahulu.'); return; }
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: answer });
                    feedback.className = 'rounded-2xl p-5 border ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

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
                            ${res.benar ? 'Kalimat benar!' : 'Belum tepat'} • Skor ${res.skor}/100 • +${res.exp_didapat} XP
                        </div>
                        <p class="font-body text-body text-on-surface-variant mt-1">Kunci: <strong>${(res.detail?.kunci ?? []).join(' ')}</strong> • Rekor Skor: ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} hari</p>
                        ${levelSelesaiHtml}
                        ${nextButtons}`;
                    feedback.classList.remove('hidden');
                } catch (e) { alert(e.message); }
            });
                    feedback.classList.remove('hidden');
                } catch (e) { alert(e.message); }
            });
        </script>
    @endpush
@endif
