/**
 * Kanvas tracing aksara Jawa (FR-22) — 100% JavaScript.
 *
 * Template aksara (outline, titik panduan, garis tengah untuk snap & skor)
 * dibangun sendiri di sisi klien dari font Noto Sans Javanese:
 *   render glyph → bitmap → thinning (Zhang-Suen) → tracing skeleton.
 * Tidak memerlukan Python / aset yang di-generate offline.
 *
 * Prinsip: outline tampil sebagai garis tunggal berongga (via strokeText),
 * goresan siswa dihaluskan (RDP + Catmull-Rom), di-snap ke garis tengah, lalu
 * ketika skor lulus ambang, aksara terisi penuh sebagai reward.
 */

const TEMPLATE_CACHE_VERSION = 'v3';
const _templateCache = new Map();

export function distance(a, b) {
    return Math.hypot(a.x - b.x, a.y - b.y);
}

export function pathLength(points) {
    let d = 0;
    for (let i = 1; i < points.length; i++) d += distance(points[i - 1], points[i]);
    return d;
}

export function resampleToCount(points, count) {
    if (!points || points.length === 0) return [];
    if (points.length === 1 || count < 2) return points.slice(0, Math.max(1, count));

    const total = pathLength(points);
    if (total === 0) return Array.from({ length: count }, () => ({ ...points[0] }));

    const interval = total / (count - 1);
    const out = [{ ...points[0] }];
    let accumulated = 0;
    const work = points.map((p) => ({ ...p }));

    for (let i = 1; i < work.length; i++) {
        let current = work[i - 1];
        let next = work[i];
        let d = distance(current, next);

        while (d > 0 && accumulated + d >= interval) {
            const t = (interval - accumulated) / d;
            const q = { x: current.x + t * (next.x - current.x), y: current.y + t * (next.y - current.y) };
            out.push(q);
            current = q;
            next = work[i];
            d = distance(current, next);
            accumulated = 0;
        }
        accumulated += d;
    }

    while (out.length < count) out.push({ ...work[work.length - 1] });
    return out.slice(0, count);
}

export function simplifyRDP(points, epsilon = 0.004) {
    if (!points || points.length < 3) return (points || []).map((p) => ({ ...p }));

    const sqEps = epsilon * epsilon;
    const sqSegDist = (p, a, b) => {
        let x = a.x;
        let y = a.y;
        let dx = b.x - x;
        let dy = b.y - y;
        if (dx !== 0 || dy !== 0) {
            const t = ((p.x - x) * dx + (p.y - y) * dy) / (dx * dx + dy * dy);
            if (t > 1) { x = b.x; y = b.y; }
            else if (t > 0) { x += dx * t; y += dy * t; }
        }
        dx = p.x - x;
        dy = p.y - y;
        return dx * dx + dy * dy;
    };

    const pts = points.map((p) => ({ ...p }));
    const keep = new Array(pts.length).fill(false);
    keep[0] = true;
    keep[pts.length - 1] = true;
    const stack = [[0, pts.length - 1]];

    while (stack.length) {
        const [first, end] = stack.pop();
        let maxSq = 0;
        let index = 0;
        for (let i = first + 1; i < end; i++) {
            const sq = sqSegDist(pts[i], pts[first], pts[end]);
            if (sq > maxSq) { maxSq = sq; index = i; }
        }
        if (maxSq > sqEps && index > 0) {
            keep[index] = true;
            stack.push([first, index], [index, end]);
        }
    }

    return pts.filter((_, i) => keep[i]);
}

/**
 * Titik-titik berjarak seragam di sepanjang polyline (utilitas).
 */
export function evenDots(points, spacing) {
    const dots = [];
    if (!points || points.length < 2 || spacing <= 0) return dots;

    let total = 0;
    for (let i = 1; i < points.length; i++) {
        const a = points[i - 1];
        const b = points[i];
        const seg = distance(a, b);
        if (seg <= 0) continue;

        let target = total === 0 ? 0 : Math.ceil(total / spacing) * spacing;
        if (total > 0 && target <= total) target += spacing;
        while (target <= total + seg) {
            const t = (target - total) / seg;
            dots.push({ x: a.x + (b.x - a.x) * t, y: a.y + (b.y - a.y) * t });
            target += spacing;
        }
        total += seg;
    }
    return dots;
}

export function drawSmoothPath(ctx, points) {
    if (!points || points.length < 2) return;
    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    if (points.length === 2) {
        ctx.lineTo(points[1].x, points[1].y);
        ctx.stroke();
        return;
    }

    for (let i = 0; i < points.length - 1; i++) {
        const p0 = points[i - 1] || points[i];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[i + 2] || p2;
        const cp1x = p1.x + (p2.x - p0.x) / 6;
        const cp1y = p1.y + (p2.y - p0.y) / 6;
        const cp2x = p2.x - (p3.x - p1.x) / 6;
        const cp2y = p2.y - (p3.y - p1.y) / 6;
        ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, p2.x, p2.y);
    }
    ctx.stroke();
}

/* ------------------------------------------------------------------ */
/* Template builder (murni JS)                                         */
/* ------------------------------------------------------------------ */

function renderGlyphMask(aksara, size = 512) {
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, size, size);
    ctx.fillStyle = '#000000';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.font = `${Math.floor(size * 0.72)}px "Noto Sans Javanese", sans-serif`;
    ctx.fillText(aksara, size / 2, size / 2);

    const data = ctx.getImageData(0, 0, size, size).data;
    const full = new Uint8Array(size * size);
    let minX = size;
    let minY = size;
    let maxX = -1;
    let maxY = -1;

    for (let i = 0; i < size * size; i++) {
        const on = data[i * 4] < 128 ? 1 : 0;
        full[i] = on;
        if (on) {
            const x = i % size;
            const y = (i / size) | 0;
            if (x < minX) minX = x;
            if (x > maxX) maxX = x;
            if (y < minY) minY = y;
            if (y > maxY) maxY = y;
        }
    }

    if (maxX < 0) return null;

    const m = 2;
    const x0 = Math.max(0, minX - m);
    const y0 = Math.max(0, minY - m);
    const x1 = Math.min(size - 1, maxX + m);
    const y1 = Math.min(size - 1, maxY + m);
    const w = x1 - x0 + 1;
    const h = y1 - y0 + 1;
    const mask = new Uint8Array(w * h);

    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            mask[y * w + x] = full[(y + y0) * size + (x + x0)];
        }
    }

    // Bounding box tinta glyph (koordinat di dalam mask) — dipakai untuk
    // memetakan skeleton 0..1 persis ke kotak glyph di layar.
    const ink = {
        left: minX - x0,
        top: minY - y0,
        w: maxX - minX + 1,
        h: maxY - minY + 1,
    };

    return { mask, w, h, ink };
}

export function thinZhangSuen(mask, w, h) {
    const img = Uint8Array.from(mask);
    const at = (x, y) => img[y * w + x];
    let changed = true;

    while (changed) {
        changed = false;
        for (let step = 0; step < 2; step++) {
            const del = [];
            for (let y = 1; y < h - 1; y++) {
                for (let x = 1; x < w - 1; x++) {
                    const i = y * w + x;
                    if (!img[i]) continue;
                    const p2 = at(x, y - 1); const p3 = at(x + 1, y - 1); const p4 = at(x + 1, y);
                    const p5 = at(x + 1, y + 1); const p6 = at(x, y + 1); const p7 = at(x - 1, y + 1);
                    const p8 = at(x - 1, y); const p9 = at(x - 1, y - 1);
                    const B = p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
                    if (B < 2 || B > 6) continue;
                    const seq = [p2, p3, p4, p5, p6, p7, p8, p9, p2];
                    let A = 0;
                    for (let k = 0; k < 8; k++) if (seq[k] === 0 && seq[k + 1] === 1) A++;
                    if (A !== 1) continue;
                    if (step === 0) {
                        if (p2 * p4 * p6 !== 0) continue;
                        if (p4 * p6 * p8 !== 0) continue;
                    } else {
                        if (p2 * p4 * p8 !== 0) continue;
                        if (p2 * p6 * p8 !== 0) continue;
                    }
                    del.push(i);
                }
            }
            if (del.length) {
                for (const i of del) img[i] = 0;
                changed = true;
            }
        }
    }

    return img;
}

const N8 = [[-1, -1], [-1, 0], [-1, 1], [0, -1], [0, 1], [1, -1], [1, 0], [1, 1]];

function neighborsOf(img, w, h, x, y) {
    const ns = [];
    for (const [dy, dx] of N8) {
        const nx = x + dx;
        const ny = y + dy;
        if (nx >= 0 && ny >= 0 && nx < w && ny < h && img[ny * w + nx]) ns.push([nx, ny]);
    }
    return ns;
}

// Urutan cincin 8-tetangga searah jarum jam mulai dari utara.
const RING = [[0, -1], [1, -1], [1, 0], [1, 1], [0, 1], [-1, 1], [-1, 0], [-1, -1]];

/**
 * Jumlah cabang skeleton di sekitar sebuah piksel = jumlah transisi 0→1
 * pada cincin 8-tetangga (endpoint=1, garis lurus=2, percabangan>=3).
 */
function branchCount(img, w, h, x, y) {
    let transitions = 0;
    for (let i = 0; i < 8; i++) {
        const [dx, dy] = RING[i];
        const nx = x + dx;
        const ny = y + dy;
        const on = nx >= 0 && ny >= 0 && nx < w && ny < h && img[ny * w + nx] ? 1 : 0;
        const [px, py] = RING[(i + 7) % 8];
        const pnx = x + px;
        const pny = y + py;
        const prev = pnx >= 0 && pny >= 0 && pnx < w && pny < h && img[pny * w + pnx] ? 1 : 0;
        if (prev === 0 && on === 1) transitions++;
    }
    return transitions;
}

export function traceSkeleton(img, w, h) {
    const info = new Map();
    const nodeSet = new Set();

    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            if (!img[y * w + x]) continue;
            const key = y * w + x;
            const ns = neighborsOf(img, w, h, x, y);
            info.set(key, ns.map(([nx, ny]) => ny * w + nx));
            const branches = branchCount(img, w, h, x, y);
            if (branches === 1 || branches >= 3) nodeSet.add(key);
        }
    }

    const visited = new Set();
    const edgeKey = (a, b) => (a < b ? `${a}_${b}` : `${b}_${a}`);
    const paths = [];

    const walk = (start, first) => {
        const path = [start, first];
        visited.add(edgeKey(start, first));
        let prev = start;
        let cur = first;
        while (!nodeSet.has(cur)) {
            const nbs = info.get(cur) || [];
            let next = null;
            for (const q of nbs) {
                if (q !== prev && !visited.has(edgeKey(cur, q))) { next = q; break; }
            }
            if (next === null) break;
            visited.add(edgeKey(cur, next));
            path.push(next);
            prev = cur;
            cur = next;
        }
        return path;
    };

    for (const node of nodeSet) {
        for (const q of info.get(node) || []) {
            if (!visited.has(edgeKey(node, q))) paths.push(walk(node, q));
        }
    }
    // Sisa loop murni (semua derajat 2)
    for (let k = 0; k < w * h; k++) {
        if (!img[k]) continue;
        for (const q of info.get(k) || []) {
            if (!visited.has(edgeKey(k, q))) paths.push(walk(k, q));
        }
    }

    return paths;
}

/**
 * Bangun template aksara (garis tengah) dari font, dinormalisasi 0..1.
 * Hasil di-cache (memori + localStorage) agar hanya dihitung sekali.
 *
 * @returns {Promise<Array<Array<{x:number,y:number}>>>}
 */
export async function buildAksaraTemplate(aksara) {
    if (!aksara) return [];
    if (_templateCache.has(aksara)) return _templateCache.get(aksara);

    const localKey = `aksara_tpl_${TEMPLATE_CACHE_VERSION}_${aksara}`;
    try {
        const stored = localStorage.getItem(localKey);
        if (stored) {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed) && parsed.length) {
                _templateCache.set(aksara, parsed);
                return parsed;
            }
        }
    } catch (e) { /* ignore */ }

    if (document.fonts && document.fonts.load) {
        try { await document.fonts.load('400px "Noto Sans Javanese"'); } catch (e) { /* ignore */ }
    }

    const rendered = renderGlyphMask(aksara, 512);
    if (!rendered) return [];

    const { mask, w, h, ink } = rendered;
    const skel = thinZhangSuen(mask, w, h);
    const pixelPaths = traceSkeleton(skel, w, h);
    const minLen = Math.max(10, Math.round(Math.max(w, h) * 0.03));

    let paths = pixelPaths
        .filter((p) => p.length >= minLen)
        .map((p) => p.map((i) => ({ x: i % w, y: (i / w) | 0 })));

    if (!paths.length) {
        // Fallback: pakai seluruh skeleton meski pendek.
        paths = pixelPaths.map((p) => p.map((i) => ({ x: i % w, y: (i / w) | 0 })));
    }

    if (!paths.length) return [];

    // Normalisasi relatif terhadap bounding box TINTA (bukan bbox skeleton),
    // supaya titik-titik template sejajar dengan outline glyph di layar.
    const strokes = paths.map((p) => simplifyRDP(
        p.map((pt) => ({
            x: (pt.x - ink.left) / Math.max(1, ink.w),
            y: (pt.y - ink.top) / Math.max(1, ink.h),
        })),
        0.008
    ));

    _templateCache.set(aksara, strokes);
    try { localStorage.setItem(localKey, JSON.stringify(strokes)); } catch (e) { /* ignore */ }
    return strokes;
}

export function strokesToArrays(strokes) {
    return (strokes || []).map((s) => s.map((p) => [Number(p.x.toFixed(4)), Number(p.y.toFixed(4))]));
}

/* ------------------------------------------------------------------ */
/* Recognizer ($1) — padanan klien                                     */
/* ------------------------------------------------------------------ */

function centroid(points) {
    let x = 0;
    let y = 0;
    points.forEach((p) => { x += p.x; y += p.y; });
    return { x: x / points.length, y: y / points.length };
}

function rotateBy(points, theta) {
    const c = centroid(points);
    const cos = Math.cos(theta);
    const sin = Math.sin(theta);
    return points.map((p) => {
        const dx = p.x - c.x;
        const dy = p.y - c.y;
        return { x: dx * cos - dy * sin + c.x, y: dx * sin + dy * cos + c.y };
    });
}

function scaleToSquare(points, size) {
    let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
    points.forEach((p) => {
        minX = Math.min(minX, p.x); maxX = Math.max(maxX, p.x);
        minY = Math.min(minY, p.y); maxY = Math.max(maxY, p.y);
    });
    const w = Math.max(0.0001, maxX - minX);
    const h = Math.max(0.0001, maxY - minY);
    return points.map((p) => ({ x: (p.x - minX) * (size / w), y: (p.y - minY) * (size / h) }));
}

function translateToOrigin(points) {
    const c = centroid(points);
    return points.map((p) => ({ x: p.x - c.x, y: p.y - c.y }));
}

function prepareForRecognition(strokes) {
    let points = [];
    strokes.forEach((stroke) => stroke.forEach((p) => points.push({ x: p.x, y: p.y })));
    if (points.length < 2) return points;

    points = resampleToCount(points, 64);
    const c = centroid(points);
    points = rotateBy(points, -Math.atan2(c.y - points[0].y, c.x - points[0].x));
    points = scaleToSquare(points, 250);
    return translateToOrigin(points);
}

function pathDistance(a, b) {
    let d = 0;
    const n = Math.min(a.length, b.length);
    for (let i = 0; i < n; i++) d += distance(a[i], b[i]);
    return d / Math.max(1, n);
}

function distanceAtBestAngle(points, template) {
    // $1 Unistroke: golden-section mencari sudut terbaik dalam ±45° (radian).
    const phi = 0.5 * (Math.sqrt(5) - 1);
    const delta = (2 * Math.PI) / 180;
    let start = -Math.PI / 4;
    let end = Math.PI / 4;
    let a = start + (1 - phi) * (end - start);
    let b = start + phi * (end - start);
    let x1 = pathDistance(rotateBy(points, a), template);
    let x2 = pathDistance(rotateBy(points, b), template);

    while (Math.abs(end - start) > delta) {
        if (x1 < x2) {
            end = b;
            b = a;
            x2 = x1;
            a = start + (1 - phi) * (end - start);
            x1 = pathDistance(rotateBy(points, a), template);
        } else {
            start = a;
            a = b;
            x1 = x2;
            b = start + phi * (end - start);
            x2 = pathDistance(rotateBy(points, b), template);
        }
    }

    return Math.min(x1, x2);
}

function flattenPoints(strokes) {
    const out = [];
    (strokes || []).forEach((s) => (s || []).forEach((p) => out.push(p)));
    return out;
}

function meanNearestDistance(source, target) {
    if (!source.length || !target.length) return 0;
    let sum = 0;
    for (const p of source) {
        let best = Infinity;
        for (const q of target) {
            const dx = p.x - q.x;
            const dy = p.y - q.y;
            const d = dx * dx + dy * dy;
            if (d < best) best = d;
        }
        sum += Math.sqrt(best);
    }
    return sum / source.length;
}

/**
 * Skor kecocokan berbasis cakupan bentuk (order-independent).
 * Rata-rata jarak tiap titik goresan ke titik template terdekat (dua arah),
 * dinormalisasi dengan toleransi. Cocok untuk aksara multi-goresan.
 */
export function accuracyScore(userStrokes, templateStrokes, tolerance = 0.3) {
    const user = flattenPoints(userStrokes);
    const template = flattenPoints(templateStrokes);
    if (user.length < 2 || template.length < 2) return 0;

    const avg = (meanNearestDistance(user, template) + meanNearestDistance(template, user)) / 2;

    return Math.max(0, Math.min(1, 1 - avg / tolerance));
}

function easeOutBack(t, overshoot = 1.70158) {
    const c3 = overshoot + 1;
    return 1 + c3 * Math.pow(t - 1, 3) + overshoot * Math.pow(t - 1, 2);
}

/* ------------------------------------------------------------------ */
/* TracingCanvas                                                       */
/* ------------------------------------------------------------------ */

export class TracingCanvas {
    constructor(canvas, fallbackTemplate = [], options = {}) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.aksara = options.aksara || null;

        this.options = Object.assign({
            boardColor: '#14352a',
            outlineColor: 'rgba(234, 251, 241, 0.95)',
            outlineWidth: 2.5,
            fillColor: '#6C5CE8',
            strokeColor: '#6C5CE8',
            lineWidth: 7,
            snapDuration: 450,
            fillDuration: 520,
            epsilon: 0.004,
            passThreshold: 0.7,
            retryDelay: 750,
            fontFamily: '"Noto Sans Javanese", sans-serif',
            onStrokeCompleted: null,
            onSuccess: null,
            onFail: null,
        }, options);

        if (!this.aksara) {
            this.template = (fallbackTemplate || []).map((s) => s.map((p) => ({ x: p.x, y: p.y })));
            this._ready = true;
        } else {
            this.template = (fallbackTemplate || []).map((s) => s.map((p) => ({ x: p.x, y: p.y })));
            this._ready = false;
            this._initTemplate();
        }

        this.strokes = [];
        this.rawStrokes = [];
        this.current = null;
        this.completed = false;
        this.fillAlpha = 0;
        this._raf = null;
        this._fillRaf = null;
        this._retryTimer = null;
        this._busy = false;

        this._down = this._down.bind(this);
        this._move = this._move.bind(this);
        this._up = this._up.bind(this);

        this.canvas.addEventListener('pointerdown', this._down);
        this.canvas.addEventListener('pointermove', this._move);
        this.canvas.addEventListener('pointerup', this._up);
        this.canvas.addEventListener('pointercancel', this._up);

        this._onResize = () => this.resize();
        window.addEventListener('resize', this._onResize);
        if (window.ResizeObserver) {
            this._observer = new ResizeObserver(() => this.resize());
            this._observer.observe(this.canvas);
        }

        this.resize();
    }

    async _initTemplate() {
        try {
            const strokes = await buildAksaraTemplate(this.aksara);
            if (strokes && strokes.length) this.template = strokes;
        } catch (e) { /* fallback ke template yang diberikan */ }
        this._ready = true;
        this.redraw();
    }

    resize() {
        this._dpr = window.devicePixelRatio || 1;
        const w = this.canvas.clientWidth || this.canvas.width;
        const h = this.canvas.clientHeight || this.canvas.height;
        if (w === 0 || h === 0) return;
        this.canvas.width = w * this._dpr;
        this.canvas.height = h * this._dpr;
        this.ctx.setTransform(this._dpr, 0, 0, this._dpr, 0, 0);
        this.redraw();
    }

    _glyphLayout() {
        const w = this.canvas.clientWidth || this.canvas.width;
        const h = this.canvas.clientHeight || this.canvas.height;
        const pad = 0.14;
        const targetW = w * (1 - 2 * pad);
        const targetH = h * (1 - 2 * pad);

        const measure = (fontSize) => {
            this.ctx.font = `${fontSize}px ${this.options.fontFamily}`;
            const m = this.ctx.measureText(this.aksara);
            const left = m.actualBoundingBoxLeft || 0;
            const right = m.actualBoundingBoxRight || 0;
            const ascent = m.actualBoundingBoxAscent || 0;
            const descent = m.actualBoundingBoxDescent || 0;
            const gw = (left + right) || m.width || fontSize;
            const gh = (ascent + descent) || fontSize;
            return { left, right, ascent, descent, gw, gh };
        };

        let fontSize = Math.min(w, h);
        let mt = measure(fontSize);
        fontSize = fontSize * Math.min(targetW / Math.max(1, mt.gw), targetH / Math.max(1, mt.gh));
        mt = measure(fontSize);

        const cx = w / 2;
        const cy = h / 2;
        // Baseline agar pusat ink glyph tepat di tengah kanvas.
        const baselineY = cy + (mt.ascent - mt.descent) / 2;

        return {
            fontSize,
            cx,
            baselineY,
            box: {
                x: cx - mt.gw / 2,
                y: baselineY - mt.ascent,
                w: mt.gw,
                h: mt.ascent + mt.descent,
            },
        };
    }

    _normPoint(p, box) {
        return { x: box.x + p.x * box.w, y: box.y + p.y * box.h };
    }

    /** Konversi koordinat klien → kerangka internal (glyph-frame bila ada aksara). */
    _pointToInternal(clientX, clientY) {
        const rect = this.canvas.getBoundingClientRect();
        const nx = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        const ny = Math.max(0, Math.min(1, (clientY - rect.top) / rect.height));

        if (this.aksara) {
            if (!this._layout) this._layout = this._glyphLayout();
            const box = this._layout.box;
            return { x: (nx * rect.width - box.x) / box.w, y: (ny * rect.height - box.y) / box.h };
        }

        return { x: nx, y: ny };
    }

    /** Konversi titik kerangka internal → piksel kanvas. */
    _internalToPixels(p) {
        if (this.aksara) {
            if (!this._layout) this._layout = this._glyphLayout();
            return this._normPoint(p, this._layout.box);
        }
        const w = this.canvas.clientWidth || this.canvas.width;
        const h = this.canvas.clientHeight || this.canvas.height;
        return { x: p.x * w, y: p.y * h };
    }

    _pointsToPixels(points) {
        return points.map((p) => this._internalToPixels(p));
    }

    _down(e) {
        if (this._busy || !this._ready) return;
        if (e.pointerId !== undefined) this.canvas.setPointerCapture?.(e.pointerId);
        e.preventDefault();
        this.current = [this._pointToInternal(e.clientX, e.clientY)];
    }

    _move(e) {
        if (!this.current) return;
        e.preventDefault();
        this.current.push(this._pointToInternal(e.clientX, e.clientY));
        this.redraw();
    }

    _up(e) {
        if (!this.current) return;
        e.preventDefault();
        const simplified = simplifyRDP(this.current, this.options.epsilon);
        this.current = null;

        if (simplified.length < 2) { this.redraw(); return; }

        this.rawStrokes.push(simplified);
        this.strokes.push(simplified.map((p) => ({ ...p })));

        const score = accuracyScore(this.rawStrokes, this.template);
        if (typeof this.options.onStrokeCompleted === 'function') {
            this.options.onStrokeCompleted(score);
        }

        if (score >= this.options.passThreshold) {
            this.redraw();
            if (!this.completed) {
                this.completed = true;
                this._animateFill();
                if (typeof this.options.onSuccess === 'function') this.options.onSuccess(score);
            }
        } else {
            // Belum mirip → rapikan sekilas lalu ulangi (bersihkan kanvas).
            this.redraw();
            if (typeof this.options.onFail === 'function') this.options.onFail(score);
            if (this._retryTimer) clearTimeout(this._retryTimer);
            this._retryTimer = setTimeout(() => {
                this._retryTimer = null;
                this.reset();
            }, this.options.retryDelay);
        }
    }

    _animateSnap(index) {
        const stroke = this.strokes[index];
        if (!stroke || stroke.length < 2) return;

        const templatePoints = flattenPoints(this.template);
        if (templatePoints.length < 2) return;

        // Tiap titik goresan ditarik ke titik terdekat pada SELURUH template,
        // sehingga goresan panjang tidak ambruk ke satu segmen pendek.
        const count = Math.max(stroke.length, 24);
        const from = resampleToCount(stroke, count);
        let to = from.map((p) => {
            let best = templatePoints[0];
            let bestD = Infinity;
            for (const q of templatePoints) {
                const dx = p.x - q.x;
                const dy = p.y - q.y;
                const d = dx * dx + dy * dy;
                if (d < bestD) { bestD = d; best = q; }
            }
            return { x: best.x, y: best.y };
        });

        // Haluskan hasil agar tidak bergerigi.
        to = to.map((p, i) => {
            const a = to[Math.max(0, i - 1)];
            const b = to[Math.min(to.length - 1, i + 1)];
            return { x: (a.x + p.x + b.x) / 3, y: (a.y + p.y + b.y) / 3 };
        });

        const start = performance.now();
        const duration = this.options.snapDuration;

        if (this._raf) cancelAnimationFrame(this._raf);
        const frame = (now) => {
            const k = Math.min(1, (now - start) / duration);
            const e = easeOutBack(k);
            this.strokes[index] = from.map((p, j) => ({ x: p.x + (to[j].x - p.x) * e, y: p.y + (to[j].y - p.y) * e }));
            this.redraw();
            if (k < 1) this._raf = requestAnimationFrame(frame);
            else { this.strokes[index] = to; this._raf = null; this.redraw(); }
        };
        this._raf = requestAnimationFrame(frame);
    }

    _animateFill() {
        this._busy = true;
        const start = performance.now();
        const duration = this.options.fillDuration;
        if (this._fillRaf) cancelAnimationFrame(this._fillRaf);

        const frame = (now) => {
            const k = Math.min(1, (now - start) / duration);
            this.fillAlpha = 1 - Math.pow(1 - k, 3);
            this.redraw();
            if (k < 1) this._fillRaf = requestAnimationFrame(frame);
            else { this.fillAlpha = 1; this._fillRaf = null; this._busy = false; this.redraw(); }
        };
        this._fillRaf = requestAnimationFrame(frame);
    }

    redraw() {
        const ctx = this.ctx;
        const w = this.canvas.clientWidth || this.canvas.width;
        const h = this.canvas.clientHeight || this.canvas.height;
        ctx.clearRect(0, 0, w, h);

        // Latar papan
        ctx.save();
        ctx.fillStyle = this.options.boardColor;
        ctx.fillRect(0, 0, w, h);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
        ctx.lineWidth = 1;
        const step = 32;
        ctx.beginPath();
        for (let x = step; x < w; x += step) { ctx.moveTo(x, 0); ctx.lineTo(x, h); }
        for (let y = step; y < h; y += step) { ctx.moveTo(0, y); ctx.lineTo(w, y); }
        ctx.stroke();
        ctx.restore();

        if (this.aksara) {
            const layout = this._glyphLayout();
            this._layout = layout;
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'alphabetic';
            ctx.font = `${layout.fontSize}px ${this.options.fontFamily}`;

            if (this.completed && this.fillAlpha > 0) {
                ctx.globalAlpha = this.fillAlpha;
                ctx.fillStyle = this.options.fillColor;
                ctx.fillText(this.aksara, layout.cx, layout.baselineY);
                ctx.globalAlpha = 1;
            }

            ctx.lineWidth = this.options.outlineWidth;
            ctx.lineJoin = 'round';
            ctx.strokeStyle = this.options.outlineColor;
            ctx.strokeText(this.aksara, layout.cx, layout.baselineY);
            ctx.restore();
        } else if (this.template.length) {
            ctx.save();
            ctx.strokeStyle = 'rgba(255,255,255,0.35)';
            ctx.lineWidth = 10;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            this.template.forEach((stroke) => drawSmoothPath(ctx, this._pointsToPixels(stroke)));
            ctx.restore();
        }

        ctx.save();
        ctx.strokeStyle = this.options.strokeColor;
        ctx.lineWidth = this.options.lineWidth;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        this.strokes.forEach((stroke) => drawSmoothPath(ctx, this._pointsToPixels(stroke)));
        if (this.current) drawSmoothPath(ctx, this._pointsToPixels(this.current));
        ctx.restore();
    }

    getStrokes() {
        return this.rawStrokes.map((stroke) => stroke.map((p) => [Number(p.x.toFixed(4)), Number(p.y.toFixed(4))]));
    }

    getTemplate() {
        return this.template.map((stroke) => stroke.map((p) => [Number(p.x.toFixed(4)), Number(p.y.toFixed(4))]));
    }

    getScore() {
        return accuracyScore(this.rawStrokes, this.template);
    }

    snapLast() {
        if (this.strokes.length && !this.completed) this._animateSnap(this.strokes.length - 1);
    }

    reset() {
        if (this._raf) cancelAnimationFrame(this._raf);
        if (this._fillRaf) cancelAnimationFrame(this._fillRaf);
        if (this._retryTimer) clearTimeout(this._retryTimer);
        this._raf = null;
        this._fillRaf = null;
        this._retryTimer = null;
        this.strokes = [];
        this.rawStrokes = [];
        this.current = null;
        this.completed = false;
        this.fillAlpha = 0;
        this._busy = false;
        this.redraw();
    }

    destroy() {
        if (this._raf) cancelAnimationFrame(this._raf);
        if (this._fillRaf) cancelAnimationFrame(this._fillRaf);
        if (this._retryTimer) clearTimeout(this._retryTimer);
        this.canvas.removeEventListener('pointerdown', this._down);
        this.canvas.removeEventListener('pointermove', this._move);
        this.canvas.removeEventListener('pointerup', this._up);
        this.canvas.removeEventListener('pointercancel', this._up);
        window.removeEventListener('resize', this._onResize);
        this._observer?.disconnect();
    }
}

if (typeof window !== 'undefined') {
    window.TracingCanvas = TracingCanvas;
    window.AksaraTracing = {
        simplifyRDP,
        drawSmoothPath,
        evenDots,
        resampleToCount,
        accuracyScore,
        buildAksaraTemplate,
        strokesToArrays,
        TracingCanvas,
    };
}

export default TracingCanvas;
