<script>
    window.KUIS = {
        csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? null,
        jawabUrl: @json(route('kuis.jawab')),
        ttsUrl: @json(route('kuis.tts')),
        sttUrl: @json(route('kuis.stt')),
        stsUrl: @json(route('kuis.sts')),
    };
    window.postJSON = async function (url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.KUIS.csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            throw Object.assign(new Error(data.message || 'Gagal mengirim data.'), { data, status: res.status });
        }
        return data;
    };

    window.KuisFx = (function () {
        const KEY = 'kuis-suara';
        let ctx = null;
        let muted = localStorage.getItem(KEY) === 'mati';

        function ac() {
            const Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return null;
            if (!ctx) ctx = new Ctx();
            if (ctx.state === 'suspended') ctx.resume();
            return ctx;
        }

        function note(freq, start, dur, type, vol) {
            if (muted) return;
            const c = ac();
            if (!c) return;
            const t = c.currentTime + start;
            const o = c.createOscillator();
            const g = c.createGain();
            o.type = type || 'sine';
            o.frequency.setValueAtTime(freq, t);
            g.gain.setValueAtTime(0.0001, t);
            g.gain.exponentialRampToValueAtTime(vol || 0.12, t + 0.012);
            g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
            o.connect(g);
            g.connect(c.destination);
            o.start(t);
            o.stop(t + dur + 0.03);
        }

        return {
            get muted() { return muted; },
            toggle() {
                muted = !muted;
                localStorage.setItem(KEY, muted ? 'mati' : 'urip');
                if (!muted) note(880, 0, 0.14, 'sine', 0.1);
                return muted;
            },
            tap() { note(760, 0, 0.05, 'triangle', 0.06); },
            pilih() { note(523.25, 0, 0.08, 'sine', 0.1); note(659.25, 0.06, 0.1, 'sine', 0.09); },
            benar() { [659.25, 830.61, 1046.5].forEach((f, i) => note(f, i * 0.09, 0.2, 'sine', 0.13)); },
            salah() { note(311.13, 0, 0.16, 'sawtooth', 0.07); note(207.65, 0.13, 0.3, 'sawtooth', 0.07); },
            menang() { [523.25, 659.25, 783.99, 1046.5].forEach((f, i) => note(f, i * 0.12, 0.32, 'triangle', 0.13)); },
        };
    })();

    window.showQuizFeedbackModal = function (opts) {
        const modal = document.getElementById('quiz-feedback-modal');
        const card = document.getElementById('quiz-feedback-card');
        if (!modal || !card) return;

        const isBenar = Boolean(opts.benar);

        // Emoji Reaksi & 3D Badge
        const emojiEl = document.getElementById('modal-emoji');
        const emojiBadge = document.getElementById('modal-emoji-badge');
        if (emojiEl) {
            emojiEl.textContent = isBenar ? '🥳' : '😔';
        }
        if (emojiBadge) {
            emojiBadge.className = 'w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl flex items-center justify-center text-3xl sm:text-4xl border-2 border-b-4 mb-2.5 shadow-sm transform transition-transform hover:scale-105 select-none ' +
                (isBenar ? 'bg-emerald-100 border-emerald-300 border-b-emerald-500' : 'bg-rose-100 border-rose-300 border-b-rose-500');
        }

        // Status Badge & Title
        const badgeEl = document.getElementById('modal-status-badge');
        const iconEl = document.getElementById('modal-status-icon');
        const statusTextEl = document.getElementById('modal-status-text');
        const titleEl = document.getElementById('modal-title');

        if (badgeEl) {
            badgeEl.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-heading font-black text-[11px] tracking-wider uppercase mb-1.5 shadow-sm ' +
                (isBenar ? 'bg-emerald-500 text-white border-b-2 border-emerald-700' : 'bg-rose-500 text-white border-b-2 border-rose-700');
        }
        if (iconEl) {
            iconEl.textContent = isBenar ? 'verified' : 'cancel';
        }
        if (statusTextEl) {
            statusTextEl.textContent = isBenar ? 'Leres Sanget!' : 'Durung Pas!';
        }
        if (titleEl) {
            titleEl.textContent = isBenar ? (opts.title || 'Jawabanmu Bener!') : (opts.title || 'Jawaban Kurang Tepat');
        }

        // Statistik Skor, EXP, Streak
        const skorEl = document.getElementById('modal-stat-skor');
        const expEl = document.getElementById('modal-stat-exp');
        const streakEl = document.getElementById('modal-stat-streak');

        if (skorEl) skorEl.textContent = (opts.skor ?? 0) + '/100';
        if (expEl) expEl.textContent = '+' + (opts.exp_didapat ?? 0) + ' XP';
        if (streakEl) streakEl.textContent = (opts.current_streak ?? 0) + ' Dina';

        // Kunci Jawaban / Penjelasan Box
        const kunciBox = document.getElementById('modal-kunci-box');
        const kunciText = document.getElementById('modal-kunci-text');
        if (kunciBox && kunciText) {
            if (opts.kunciDisplay && (!isBenar || opts.showKunci)) {
                kunciText.innerHTML = opts.kunciDisplay;
                kunciBox.classList.remove('hidden');
            } else if (opts.keterangan) {
                kunciText.innerHTML = opts.keterangan;
                kunciBox.classList.remove('hidden');
            } else {
                kunciBox.classList.add('hidden');
            }
        }

        // Level Selesai Banner
        const levelBanner = document.getElementById('modal-level-selesai-banner');
        const bonusExpEl = document.getElementById('modal-bonus-exp');
        const nextLevelTextEl = document.getElementById('modal-level-next-text');
        if (levelBanner) {
            if (opts.level_selesai) {
                if (bonusExpEl) bonusExpEl.textContent = opts.reward_exp ?? 0;
                if (nextLevelTextEl) {
                    nextLevelTextEl.innerHTML = opts.level_berikutnya
                        ? `Level sabanjure <strong>${opts.level_berikutnya}</strong> saiki wis kabukak.`
                        : 'Kabeh materi ing level iki wis rampung 100%!';
                }
                levelBanner.classList.remove('hidden');
            } else {
                levelBanner.classList.add('hidden');
            }
        }

        // Tombol Navigasi
        const btnNext = document.getElementById('modal-btn-next');
        const btnNextLevel = document.getElementById('modal-btn-next-level');

        if (btnNext) {
            if (opts.next_url) {
                btnNext.href = opts.next_url;
                btnNext.classList.remove('hidden');
            } else {
                btnNext.classList.add('hidden');
            }
        }

        if (btnNextLevel) {
            if (opts.next_level_url && !opts.next_url) {
                btnNextLevel.href = opts.next_level_url;
                btnNextLevel.classList.remove('hidden');
            } else {
                btnNextLevel.classList.add('hidden');
            }
        }

        // Tampilkan Modal dengan Animasi
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    };
</script>
