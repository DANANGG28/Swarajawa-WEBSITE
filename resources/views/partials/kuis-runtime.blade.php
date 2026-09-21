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
</script>
