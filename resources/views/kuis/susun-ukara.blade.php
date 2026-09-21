@extends('layouts.kuis-fokus')

@section('konten')
    @if (! $soal)
        <div class="rounded-3xl p-10 text-center border-2 border-gray-200">
            <span class="material-symbols-outlined text-[48px] text-gray-500">segment</span>
            <h2 class="font-heading text-2xl font-extrabold text-on-surface mt-3">Belum ana soal susun ukara</h2>
            <p class="font-body text-base text-gray-500 mt-1">Rampungna level sadurunge utawa hubungi guru kanggo nambah soal.</p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 mt-6 rounded-2xl bg-primary-600 text-on-primary px-7 py-3 font-body text-base font-bold shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">Bali menyang Beranda</a>
        </div>
    @else
        <div class="flex flex-col gap-8 md:gap-10">
            <section class="flex flex-col gap-4">
                <div class="inline-flex items-center gap-2 self-start rounded-full bg-primary-fixed/60 px-3.5 py-1.5 text-primary-700">
                    <span class="material-symbols-outlined icon-fill text-[18px]">segment</span>
                    <span class="font-label-upper text-sm uppercase tracking-widest font-bold">Susun Ukara</span>
                </div>

                <h1 class="font-display text-2xl md:text-3xl font-extrabold text-on-surface leading-snug">
                    {{ $soal['pertanyaan'] }}
                </h1>

                <button id="btn-tts" type="button" aria-label="Rungokna ukara"
                    class="flex items-center gap-3 rounded-2xl border-2 border-gray-200 bg-surface-container-lowest px-5 py-4 shadow-sm hover:border-primary-400 transition-colors max-w-lg text-left">
                    <span class="material-symbols-outlined icon-fill text-[28px] text-primary-600 shrink-0">volume_up</span>
                    <span class="flex flex-col items-stretch min-w-0">
                        <span id="tts-teks" class="font-display text-lg font-extrabold text-primary-600 leading-snug">{{ $soal['pertanyaan'] }}</span>
                        <span class="mt-1.5 w-full border-b-[3px] border-dotted border-primary-400"></span>
                    </span>
                </button>
            </section>

            <section class="flex flex-col gap-6">
                <div class="flex flex-col">
                    <span id="placeholder" class="font-body text-base text-gray-400 pb-3">Pencet tembung ing ngisor iki kanggo nyusun ukara...</span>
                    <div class="border-t-2 border-gray-200"></div>
                    <div id="zona-ukara" data-dropzone
                        class="flex flex-wrap items-center justify-center gap-3 min-h-[72px] py-1 transition-colors"></div>
                    <div class="border-t-2 border-gray-200"></div>
                </div>

                <div id="bank-tembung" data-dropzone class="flex flex-wrap items-center justify-center gap-3 min-h-[44px]"></div>
            </section>

            <section id="feedback" class="hidden rounded-2xl p-5 border-2"></section>
        </div>
    @endif
@endsection

@section('aksi')
    @if ($soal)
        <a href="{{ route('siswa.dashboard') }}" id="btn-lompati"
            class="rounded-2xl bg-gray-100 text-gray-500 hover:bg-gray-200 font-heading text-base font-extrabold uppercase tracking-wide px-6 py-3.5 transition-colors">
            Lompati
        </a>
        <button id="btn-kirim" type="button" disabled
            class="rounded-2xl bg-primary-600 hover:bg-primary-700 disabled:opacity-40 disabled:shadow-none disabled:translate-y-0 text-on-primary font-heading text-base font-extrabold uppercase tracking-wide px-8 py-3.5 shadow-[0_4px_0_#5443C9] active:translate-y-[3px] active:shadow-none transition-all">
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
            const bank = document.getElementById('bank-tembung');
            const zona = document.getElementById('zona-ukara');
            const placeholder = document.getElementById('placeholder');
            const feedback = document.getElementById('feedback');
            const btn = document.getElementById('btn-kirim');

            const words = SOAL.opsi.map((w, i) => ({ word: typeof w === 'string' ? w : (w.teks ?? w), uid: i }));
            const byUid = new Map(words.map((w) => [w.uid, w]));
            let placed = [];

            function chip(item, inZona) {
                const b = document.createElement('button');
                b.type = 'button';
                b.textContent = item.word;
                b.dataset.uid = item.uid;
                b.draggable = true;
                b.className = 'cursor-grab select-none rounded-xl border-2 px-4 py-2.5 font-body text-base font-semibold text-on-surface transition-all active:cursor-grabbing ' +
                    (inZona
                        ? 'border-primary-600 bg-primary-fixed/40 shadow-[0_2px_0_#5443C9]'
                        : 'border-gray-200 bg-surface-container-lowest shadow-[0_2px_0_#E4E4EC] hover:border-primary-400');

                b.addEventListener('click', () => {
                    if (inZona) {
                        placed = placed.filter((u) => u !== item.uid);
                    } else {
                        placed.push(item.uid);
                    }
                    window.KuisFx.tap();
                    render();
                });

                b.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', String(item.uid));
                    e.dataTransfer.effectAllowed = 'move';
                    b.classList.add('opacity-50');
                });
                b.addEventListener('dragend', () => b.classList.remove('opacity-50'));

                return b;
            }

            function render() {
                bank.innerHTML = '';
                zona.querySelectorAll('[data-chip]').forEach((n) => n.remove());
                words.forEach((item) => {
                    if (! placed.includes(item.uid)) bank.appendChild(chip(item, false));
                });
                placed.forEach((uid) => {
                    const b = chip(byUid.get(uid), true);
                    b.dataset.chip = uid;
                    zona.appendChild(b);
                });
                placeholder.classList.toggle('invisible', placed.length > 0);
                btn.disabled = placed.length === 0;
            }

            function uidFromEvent(e) {
                const raw = e.dataTransfer.getData('text/plain');
                return raw === '' ? null : Number(raw);
            }

            function dropIndex(uid, clientX) {
                const chips = Array.from(zona.querySelectorAll('[data-chip]')).filter((c) => Number(c.dataset.chip) !== uid);
                for (let i = 0; i < chips.length; i++) {
                    const r = chips[i].getBoundingClientRect();
                    if (clientX < r.left + r.width / 2) return i;
                }
                return chips.length;
            }

            zona.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                zona.classList.add('bg-primary-fixed/20');
            });
            zona.addEventListener('dragleave', () => zona.classList.remove('bg-primary-fixed/20'));
            zona.addEventListener('drop', (e) => {
                e.preventDefault();
                zona.classList.remove('bg-primary-fixed/20');
                const uid = uidFromEvent(e);
                if (uid === null || ! byUid.has(uid)) return;
                const index = dropIndex(uid, e.clientX);
                placed = placed.filter((u) => u !== uid);
                placed.splice(Math.max(0, Math.min(index, placed.length)), 0, uid);
                window.KuisFx.tap();
                render();
            });

            bank.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });
            bank.addEventListener('drop', (e) => {
                e.preventDefault();
                const uid = uidFromEvent(e);
                if (uid === null || ! placed.includes(uid)) return;
                placed = placed.filter((u) => u !== uid);
                window.KuisFx.tap();
                render();
            });

            render();

            const TTS_TEKS = SOAL.pertanyaan;

            document.getElementById('btn-tts')?.addEventListener('click', async () => {
                const btnTts = document.getElementById('btn-tts');
                if (btnTts.dataset.loading === '1') return;
                btnTts.dataset.loading = '1';
                btnTts.classList.add('opacity-60');
                window.KuisFx.tap();
                try {
                    const res = await window.postJSON(window.KUIS.ttsUrl, { teks: TTS_TEKS });
                    const src = res.audio_url || (res.audio_base64 ? 'data:' + res.mime + ';base64,' + res.audio_base64 : null);
                    if (src) new Audio(src).play().catch(() => {});
                } catch (e) {
                    // abaikan — audio mung pambantu
                } finally {
                    btnTts.dataset.loading = '0';
                    btnTts.classList.remove('opacity-60');
                }
            });

            document.getElementById('btn-lompati')?.addEventListener('click', () => window.KuisFx.tap());

            btn?.addEventListener('click', async () => {
                const answer = placed.map((uid) => byUid.get(uid).word);
                if (! answer.length) return;
                btn.disabled = true;
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: answer });

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
                            ${res.benar ? 'Ukara bener!' : 'Durung pas'} • Skor ${res.skor}/100 • +${res.exp_didapat} XP
                        </div>
                        <p class="font-body text-base text-on-surface-variant mt-1">Kunci: <strong>${(res.detail?.kunci ?? []).join(' ')}</strong> • Rekor ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} dina</p>
                        ${levelSelesaiHtml}
                        ${nextButtons}`;
                    feedback.classList.remove('hidden');
                } catch (e) {
                    alert(e.message);
                    btn.disabled = false;
                }
            });
        </script>
    @endpush
@endif
