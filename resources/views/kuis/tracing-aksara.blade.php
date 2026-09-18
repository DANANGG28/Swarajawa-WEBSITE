@extends('layouts.kuis')

@section('konten')
    @php
        $paths = $soal['paths'] ?? [];
        $d = '';
        foreach ($paths as $stroke) {
            $first = true;
            foreach ($stroke as $p) {
                $x = (int) round(($p[0] ?? 0) * 1000);
                $y = (int) round(($p[1] ?? 0) * 1000);
                $d .= ($first ? 'M' : 'L')." {$x} {$y} ";
                $first = false;
            }
        }
    @endphp

    @if (! $soal)
        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-gray-500">draw</span>
            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Durung ana soal tracing aksara</h2>
            <p class="font-body text-body text-gray-500 mt-1">Rampungake level sadurunge utawa takon guru kanggo nambah soal.</p>
            <a href="{{ url('/latihan-soal') }}" class="inline-flex items-center gap-2 mt-5 rounded-full bg-primary-600 text-on-primary px-6 py-3 font-body text-body font-bold">Bali menyang Latihan</a>
        </div>
    @else
        <div class="flex flex-col gap-6">
            <section class="bg-surface-container-lowest rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <div class="font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold mb-1">Latihan Nulis Aksara (FR-22 Tracing)</div>
                    <h1 class="font-display text-display font-extrabold text-on-surface leading-snug">Tlusuri wujud aksara kanthi nggaris ing kanvas!</h1>
                    <p class="font-caption text-caption text-gray-500 mt-1">Aksara: <strong class="text-on-surface">{{ $soal['aksara'] ?? '-' }}</strong> • {{ $soal['petunjuk'] ?? 'Tlusuri bayangan aksara.' }}</p>
                </div>
                <button id="btn-tts" type="button" class="self-start md:self-center inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-primary-fixed/50 hover:bg-primary-fixed border border-primary-400/30 text-on-surface font-body text-body font-semibold transition-colors">
                    <span class="material-symbols-outlined text-primary-600">volume_up</span>
                    Pangucapan: <strong class="text-primary-700">/{{ $soal['aksara'] ?? 'aksara' }}/</strong>
                </button>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col gap-5">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700">
                            <span class="material-symbols-outlined">draw</span>
                        </div>
                        <div>
                            <h2 class="font-heading text-heading font-bold text-on-surface">Kanvas Tracing Aksara</h2>
                            <p class="font-caption text-caption text-gray-500">$1 Unistroke Recognizer • skor kemiripan</p>
                        </div>
                    </div>
                    <span id="canvas-status" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-caption text-caption font-bold bg-primary-fixed/60 text-primary-700">
                        <span class="w-2 h-2 rounded-full bg-primary-600 animate-ping"></span>
                        Goresan Aktif
                    </span>
                </div>

                <div id="canvas-wrap" class="relative w-full h-[440px] bg-surface-container-low rounded-2xl border-2 border-primary-400/30 overflow-hidden select-none touch-none">
                    <div id="grid-overlay" class="absolute inset-0 pointer-events-none opacity-40 bg-[linear-gradient(to_right,#e9e6f7_1px,transparent_1px),linear-gradient(to_bottom,#e9e6f7_1px,transparent_1px)] bg-[size:32px_32px]"></div>
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 1000 1000" preserveAspectRatio="none">
                        <line x1="80" x2="920" y1="180" y2="180" stroke="#e9e6f7" stroke-dasharray="6 6" stroke-width="2"></line>
                        <line x1="80" x2="920" y1="820" y2="820" stroke="#e9e6f7" stroke-dasharray="6 6" stroke-width="2"></line>
                        @if ($d)
                            <path d="{{ $d }}" fill="none" stroke="#A79BFF" stroke-opacity="0.3" stroke-width="46" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="{{ $d }}" fill="none" stroke="#6C5CE8" stroke-opacity="0.7" stroke-width="4" stroke-dasharray="10 10" stroke-linecap="round" stroke-linejoin="round"></path>
                        @endif
                    </svg>
                    <canvas id="paint-canvas" class="absolute inset-0 w-full h-full cursor-crosshair touch-none"></canvas>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2">
                        <button id="btn-reset" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-surface-container-high border border-gray-200 text-on-surface font-body text-body font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-primary-600">refresh</span> Busek Goresan
                        </button>
                        <button id="btn-grid" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-surface-container-high border border-gray-200 text-on-surface font-body text-body font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[18px] text-primary-600">grid_4x4</span> Garis Panduan
                        </button>
                    </div>
                    <button id="btn-snap" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary-fixed text-primary-700 hover:bg-primary-fixed-dim font-body text-body font-bold transition-colors">
                        <span class="material-symbols-outlined text-[18px]">magic_button</span> Simulasi Snap
                    </button>
                </div>

                <div class="bg-surface-container-low p-4 rounded-xl border border-gray-100">
                    <div class="flex items-end justify-between mb-1.5">
                        <div>
                            <span class="font-caption text-caption text-gray-500 font-semibold">Tingkat Kemiripan Stroke:</span>
                            <span id="akurasi" class="font-display text-display font-extrabold text-on-surface ml-1">0%</span>
                        </div>
                        <span class="material-symbols-outlined text-[28px] text-primary-600">verified</span>
                    </div>
                    <div class="w-full bg-surface-container-high h-3 rounded-full overflow-hidden">
                        <div id="akurasi-bar" class="bg-primary-600 h-full rounded-full transition-all duration-700" style="width:0%"></div>
                    </div>
                    <p class="font-caption text-caption text-gray-500 mt-2">Skor diitung saka kemiripan asli sadurunge animasi snap (penilaian tetep jujur).</p>
                </div>

                <div id="feedback" class="hidden rounded-2xl p-5 border"></div>
            </section>
        </div>
    @endif
@endsection

@section('aksi')
    @include('partials.kuis-xp', ['soal' => $soal])
    <button id="btn-kirim" type="button" @disabled(! $soal)
        class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-on-primary font-body text-body font-bold px-8 py-3 shadow-sm transition-colors">
        <span>Priksa &amp; Kirim Wangsulan</span>
        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
    </button>
@endsection

@if ($soal)
    @push('skrip')
        <script>
            const SOAL = @json($soal);
            const TEMPLATE = (SOAL.paths || []).map((s) => s.map((p) => ({ x: p[0], y: p[1] })));

            // ---------- $1 Unistroke Recognizer (port ringan) ----------
            const N = 64;
            function pathLength(pts) { let d = 0; for (let i = 1; i < pts.length; i++) d += dist(pts[i - 1], pts[i]); return d; }
            function dist(a, b) { return Math.hypot(a.x - b.x, a.y - b.y); }
            function resample(pts, n) {
                if (pts.length < 2) return pts.slice();
                const I = pathLength(pts) / (n - 1);
                let D = 0, out = [pts[0]], work = pts.slice();
                for (let i = 1; i < work.length; i++) {
                    const d = dist(work[i - 1], work[i]);
                    if (D + d >= I && d > 0) {
                        const t = (I - D) / d;
                        const q = { x: work[i - 1].x + t * (work[i].x - work[i - 1].x), y: work[i - 1].y + t * (work[i].y - work[i - 1].y) };
                        out.push(q); work.splice(i, 0, q); D = 0;
                    } else D += d;
                }
                while (out.length < n) out.push(work[work.length - 1]);
                return out.slice(0, n);
            }
            function centroid(pts) { let x = 0, y = 0; pts.forEach((p) => { x += p.x; y += p.y; }); return { x: x / pts.length, y: y / pts.length }; }
            function rotateBy(pts, th) { const c = centroid(pts), cs = Math.cos(th), sn = Math.sin(th); return pts.map((p) => { const dx = p.x - c.x, dy = p.y - c.y; return { x: dx * cs - dy * sn + c.x, y: dx * sn + dy * cs + c.y }; }); }
            function scaleToSquare(pts, size) {
                let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
                pts.forEach((p) => { minX = Math.min(minX, p.x); maxX = Math.max(maxX, p.x); minY = Math.min(minY, p.y); maxY = Math.max(maxY, p.y); });
                const w = Math.max(0.0001, maxX - minX), h = Math.max(0.0001, maxY - minY);
                return pts.map((p) => ({ x: (p.x - minX) * (size / w), y: (p.y - minY) * (size / h) }));
            }
            function translateToOrigin(pts) { const c = centroid(pts); return pts.map((p) => ({ x: p.x - c.x, y: p.y - c.y })); }
            function prepare(strokes) {
                let pts = []; strokes.forEach((s) => s.forEach((p) => pts.push({ x: p.x, y: p.y })));
                if (pts.length < 2) return pts;
                pts = resample(pts, N);
                const c = centroid(pts);
                pts = rotateBy(pts, -Math.atan2(c.y - pts[0].y, c.x - pts[0].x));
                pts = scaleToSquare(pts, 250);
                return translateToOrigin(pts);
            }
            function pathDistance(a, b) { let d = 0, n = Math.min(a.length, b.length); for (let i = 0; i < n; i++) d += dist(a[i], b[i]); return d / Math.max(1, n); }
            function distanceAtBestAngle(pts, tpl) {
                let a = -45, b = 45; const phi = 0.5 * (Math.sqrt(5) - 1);
                let x1 = pathDistance(rotateBy(pts, a), tpl), x2 = pathDistance(rotateBy(pts, b), tpl);
                while (Math.abs(b - a) > 2) {
                    if (x1 < x2) { b = b - phi * (b - a); x2 = x1; x1 = pathDistance(rotateBy(pts, a + (1 - phi) * (b - a)), tpl); }
                    else { a = a + phi * (b - a); x1 = x2; x2 = pathDistance(rotateBy(pts, a + phi * (b - a)), tpl); }
                }
                return Math.min(x1, x2);
            }
            function similarity(user, tpl) {
                const a = prepare(user), b = prepare(tpl);
                if (a.length < 2 || b.length < 2) return 0;
                const half = 0.5 * Math.sqrt(2 * 250 * 250);
                const d = distanceAtBestAngle(a, b);
                return Math.max(0, Math.min(1, 1 - d / half));
            }

            // ---------- Canvas ----------
            const wrap = document.getElementById('canvas-wrap');
            const canvas = document.getElementById('paint-canvas');
            const ctx = canvas.getContext('2d');
            let dpr = window.devicePixelRatio || 1;
            let strokes = [], current = null;

            function resize() {
                dpr = window.devicePixelRatio || 1;
                canvas.width = wrap.clientWidth * dpr;
                canvas.height = wrap.clientHeight * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                redraw();
            }
            window.addEventListener('resize', resize);

            function toNorm(e) {
                const r = canvas.getBoundingClientRect();
                const cx = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
                const cy = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
                return { x: Math.max(0, Math.min(1, cx / r.width)), y: Math.max(0, Math.min(1, cy / r.height)) };
            }
            function drawStroke(pts) {
                if (!pts || pts.length < 2) return;
                ctx.beginPath();
                ctx.lineWidth = 6; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.strokeStyle = '#6C5CE8';
                pts.forEach((p, i) => { const x = p.x * canvas.clientWidth, y = p.y * canvas.clientHeight; i ? ctx.lineTo(x, y) : ctx.moveTo(x, y); });
                ctx.stroke();
            }
            function redraw() {
                ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);
                strokes.forEach(drawStroke);
                if (current) drawStroke(current);
            }
            function updateAccuracy() {
                const sim = strokes.length ? similarity(strokes, TEMPLATE) : 0;
                const pct = Math.round(sim * 100);
                document.getElementById('akurasi').textContent = pct + '%';
                document.getElementById('akurasi-bar').style.width = pct + '%';
                return pct;
            }
            function snapLast() {
                const i = strokes.length - 1;
                const tpl = TEMPLATE[Math.min(i, TEMPLATE.length - 1)];
                if (!tpl) return;
                const to = resample(tpl.map((p) => ({ x: p.x, y: p.y })), Math.max(strokes[i].length, 2));
                const from = resample(strokes[i], to.length);
                const start = performance.now();
                (function frame(t) {
                    const k = Math.min(1, (t - start) / 450), e = 1 - Math.pow(1 - k, 3);
                    strokes[i] = to.map((p, j) => ({ x: from[j].x + (p.x - from[j].x) * e, y: from[j].y + (p.y - from[j].y) * e }));
                    redraw();
                    if (k < 1) requestAnimationFrame(frame); else updateAccuracy();
                })(start);
            }

            function down(e) { e.preventDefault(); current = [toNorm(e)]; }
            function move(e) { if (!current) return; e.preventDefault(); current.push(toNorm(e)); redraw(); }
            function up(e) {
                if (!current) return;
                strokes.push(current); current = null;
                updateAccuracy(); snapLast();
            }
            canvas.addEventListener('mousedown', down);
            window.addEventListener('mousemove', move);
            window.addEventListener('mouseup', up);
            canvas.addEventListener('touchstart', down, { passive: false });
            canvas.addEventListener('touchmove', move, { passive: false });
            canvas.addEventListener('touchend', up);

            document.getElementById('btn-reset')?.addEventListener('click', () => { strokes = []; current = null; redraw(); updateAccuracy(); });
            document.getElementById('btn-grid')?.addEventListener('click', () => document.getElementById('grid-overlay').classList.toggle('hidden'));
            document.getElementById('btn-snap')?.addEventListener('click', () => { if (strokes.length) snapLast(); });

            setTimeout(resize, 60);
            resize();

            // Submit
            const feedback = document.getElementById('feedback');
            document.getElementById('btn-kirim')?.addEventListener('click', async () => {
                if (!strokes.length) { alert('Goresake aksara dhisik ing kanvas.'); return; }
                const payload = strokes.map((s) => s.map((p) => [Number(p.x.toFixed(4)), Number(p.y.toFixed(4))]));
                try {
                    const res = await window.postJSON(window.KUIS.jawabUrl, { soal_id: SOAL.id, jawaban: { strokes: payload } });
                    feedback.className = 'rounded-2xl p-5 border ' + (res.benar ? 'bg-green-500/10 border-green-500/30' : 'bg-error-container/60 border-error/30');

                    let nextButtons = '<div class="mt-4 flex flex-wrap items-center gap-3">';
                    if (res.next_url) {
                        nextButtons += `<a href="${res.next_url}" class="rounded-full bg-primary-600 text-on-primary px-6 py-2.5 font-bold font-body hover:bg-primary-700 transition shadow-sm">Soal Sabanjure</a>`;
                    }
                    nextButtons += `<a href="{{ route('siswa.latihan') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Daftar Soal</a>`;
                    nextButtons += `<a href="{{ route('siswa.dashboard') }}" class="rounded-full bg-gray-100 text-on-surface px-6 py-2.5 font-bold font-body hover:bg-gray-200 transition">Beranda</a></div>`;

                    let levelSelesaiHtml = '';
                    if (res.level_selesai) {
                        levelSelesaiHtml = `
                            <div class="mt-3 p-4 rounded-xl bg-green-500/20 border border-green-500/40 text-green-700">
                                <div class="font-heading text-heading font-extrabold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined icon-fill">military_tech</span>
                                    Level Rampung! Sampeyan oleh bonus +${res.reward_exp} EXP!
                                </div>
                                <p class="font-body text-body text-green-800 mt-1">Level sabanjure <strong>${res.level_berikutnya ?? ''}</strong> saiki wis kabuka.</p>
                            </div>`;
                    }

                    feedback.innerHTML = `
                        <div class="flex items-center gap-2 font-heading text-heading font-extrabold ${res.benar ? 'text-green-500' : 'text-error'}">
                            <span class="material-symbols-outlined icon-fill">${res.benar ? 'verified' : 'cancel'}</span>
                            Skor kemiripan: ${res.skor}/100 • +${res.exp_didapat} XP
                        </div>
                        <p class="font-body text-body text-on-surface-variant mt-1">Ambang lulus ${res.detail?.ambang_lulus ?? 70} • Rekor: ${res.skor_tertinggi}/100 • Total EXP ${res.total_exp} • Streak ${res.current_streak} dina</p>
                        ${levelSelesaiHtml}
                        ${nextButtons}`;
                    feedback.classList.remove('hidden');
                } catch (e) { alert(e.message); }
            });
        </script>
    @endpush
@endif
