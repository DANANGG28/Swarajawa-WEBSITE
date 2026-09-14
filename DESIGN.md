# Design System — Quiz/Learning App UI
Referensi: Capi Creative mobile app mockup (Home, Leaderboard, Profile)
Versi: 1.0
Status: Ketat — semua nilai di bawah ini WAJIB diikuti kecuali ada perubahan resmi dari tim desain.

---

## 1. Prinsip Desain

1. **Playful but structured** — ilustrasi karakter kartun + bentuk geometris solid, bukan skeuomorphic.
2. **Purple-first branding** — ungu adalah warna identitas utama, dipakai di navbar, tombol utama, dan elemen aktif.
3. **Card-based layout** — semua konten dikelompokkan dalam card dengan rounded corner besar, tidak ada elemen mengambang tanpa container.
4. **Konsistensi rounding** — semua sudut membulat (16–28px), tidak ada sudut tajam di komponen interaktif.
5. **Kontras tinggi untuk status/skor** — angka poin, ranking, dan progres selalu ditonjolkan dengan warna solid atau badge.

---

## 2. Color Palette

### 2.1 Primary
| Token | Hex (approx) | Penggunaan |
|---|---|---|
| `color-primary-600` | `#6C5CE8` | Background navbar/header, tombol utama, ikon aktif |
| `color-primary-500` | `#7B6CF0` | Card "Featured", elemen ungu sekunder |
| `color-primary-400` | `#A79BFF` | Chip inactive (mis. tab "All Time" belum dipilih), aksen ringan |
| `color-primary-700` | `#5443C9` | Podium rank #1 (bar tengah), shadow/pressed state |

### 2.2 Secondary / Accent
| Token | Hex (approx) | Penggunaan |
|---|---|---|
| `color-pink-100` | `#FBD9DE` | Card "Recent Quiz" background |
| `color-pink-500` | `#F08CA0` | Progress ring / icon di card pink |
| `color-orange-300` | `#F7B98A` | Card "You are doing better than 60%", podium rank #2 |
| `color-orange-500` | `#F0955A` | Highlight angka rank, ikon topi ulang tahun |
| `color-yellow-300` | `#F6D98B` | Podium rank #3, badge emas |
| `color-green-500` | `#4CAF6D` | Badge hexagon hijau (achievement earned) |

### 2.3 Neutral
| Token | Hex (approx) | Penggunaan |
|---|---|---|
| `color-white` | `#FFFFFF` | Card background, teks di atas ungu |
| `color-gray-50` | `#F7F7FA` | Background utama layar (di luar header ungu) |
| `color-gray-200` | `#E4E4EC` | Divider, badge locked (abu-abu) |
| `color-gray-500` | `#8A8A9A` | Teks sekunder (subtitle, label kecil) |
| `color-black-900` | `#1E1E2A` | Teks utama/heading |

### 2.4 Status
| Token | Penggunaan |
|---|---|
| `color-success` | Hijau — badge selesai/earned, indikator naik ranking |
| `color-locked` | Abu-abu dengan ikon gembok — badge belum terbuka |

**Aturan:** Tidak boleh menambah warna baru di luar palet ini tanpa approval. Semua warna gradient (card featured, podium) harus dua-stop maksimal, arah diagonal 135°.

---

## 3. Typography

Font family: **Sans-serif rounded/geometris** (mis. Poppins / Plus Jakarta Sans / SF Pro Rounded).

| Token | Size | Weight | Penggunaan |
|---|---|---|---|
| `text-display` | 22–24px | Bold (700) | Judul halaman ("Leaderboard") |
| `text-heading` | 18px | Bold (700) | Nama user, judul card ("Statistics Math Quiz") |
| `text-body` | 14px | Medium (500) | Teks konten umum |
| `text-caption` | 12px | Regular (400) | Label kecil ("RECENT QUIZ", "Math • 12 Quizzes") |
| `text-label-upper` | 11px | SemiBold (600), letter-spacing 0.5px, UPPERCASE | Label section kecil ("NEW DEVICE CONNECTED", "FEATURED") |
| `text-stat-number` | 20–28px | Bold/Black (700–800) | Angka poin, ranking (#1438, 590) |

**Aturan:**
- Heading selalu warna `color-black-900` di atas background putih, `color-white` di atas ungu.
- Tidak ada italic. Tidak ada underline kecuali link teks eksplisit.
- Line-height: 1.3× untuk heading, 1.5× untuk body.

---

## 4. Spacing & Grid

- Base unit: **4px**
- Padding layar (horizontal): **20px**
- Gap antar card dalam satu section: **12px**
- Padding dalam card: **16–20px**
- Border radius:
  - Card besar (hero/featured): **24–28px**
  - Card kecil (list item, chip): **16px**
  - Button / pill: **full-round (999px)**
  - Avatar: **full-round**
- Status bar/header selalu menempel di top tanpa margin, konten mulai setelah header dengan gap **16px**.

---

## 5. Komponen

### 5.1 Header / Navbar
- Background: `color-primary-600`, full-width, rounded bottom **28px** menyatu ke konten putih.
- Elemen kiri: ikon back (panah putih) — kecuali di Home (diganti status koneksi).
- Elemen kanan: ikon settings (putih) atau kosong.
- Judul: `text-display`, `color-white`, center atau left align tergantung halaman.

### 5.2 Card — Info/Status (mis. "Recent Quiz")
- Background solid pastel (`color-pink-100`).
- Radius **20px**, padding **16px**.
- Struktur: label kecil (`text-label-upper`) → judul (`text-heading`) → elemen kanan (progress ring/icon).
- Progress ring: stroke tebal, warna aksen kontras (`color-pink-500`), angka % di tengah.

### 5.3 Card — Featured/CTA
- Background `color-primary-500`, radius **24px**, padding **20px**, teks putih center-align atau left-align tergantung konten.
- Label kategori (mis. "Tantangan Dina Iki") ditulis sebagai `text-label-upper`, warna putih dengan opacity **80–90%**, TANPA background, TANPA border/outline pill, TANPA dot/ikon bullet di depannya. Cukup teks polos uppercase dengan letter-spacing.
- Tombol pill putih di bawah teks, teks ungu, dengan ikon SVG kecil di kiri teks (lihat §6 — bukan emoji, bukan ikon dot generik).
- Info sekunder (mis. "14j 20m maneh", "+100 EXP") ditulis sebagai teks kecil putih/opacity rendah dengan ikon SVG outline di depannya (jam, dsb), TANPA container/pill.

### 5.3b Meta Info Row — Level & Status (bukan badge/pill)
Dipakai di atas judul materi/lesson untuk menunjukkan level dan status pengerjaan. **Tidak boleh** dibungkus kapsul/pill dengan border/outline.
- Format: `text-label-upper`, warna `color-gray-500`, tanpa background, tanpa border.
- Struktur horizontal rata kiri, dipisah separator titik tipis (`•`), contoh: `LEVEL 1  •  RAMPUNG`.
- Status "Rampung/selesai" ditulis dengan warna `color-success` HANYA pada teksnya (bukan pada container), tanpa ikon centang di sebelahnya kecuali ikon tsb diletakkan terpisah di pojok card (lihat 5.3c).
- Info EXP/poin (mis. "+200 EXP") tetap terpisah di ujung kanan row yang sama, boleh pakai warna aksen (ungu/kuning) pada teks saja, tanpa background.

### 5.3c Indikator Status Selesai (alternatif ikon-only)
- Jika ingin penanda visual "selesai" tanpa menambah teks, gunakan ikon centang solid kecil (16–18px) di pojok kanan atas card, warna `color-success`, tanpa lingkaran/border di sekelilingnya kecuali fill solid penuh (bukan outline).

### 5.4 List Item (Live Quizzes)
- Card putih, radius **16px**, border tipis atau shadow halus.
- Struktur horizontal: ikon-box kiri (rounded square, background pastel) → judul + subtitle (2 baris) → chevron kanan.
- Ikon-box: **44×44px**, radius **12px**.

### 5.5 Bottom Navigation Bar
- 4–5 ikon, background putih/gray-50, item aktif = ikon solid hitam/ungu, item lain outline abu-abu.
- Floating Action Button (+): lingkaran ungu solid, menonjol di atas bar (overlap), shadow jelas, posisi center atau sesuai konteks.

### 5.6 Leaderboard — Tab Switch
- Pill container abu-abu/ungu muda, dua opsi ("Weekly"/"All Time"), opsi aktif = background putih dengan teks hitam bold, opsi nonaktif = teks putih/ungu pudar transparan.

### 5.7 Leaderboard — Banner Ranking
- Card gradient oranye, radius **20px**.
- Isi: angka rank besar dalam lingkaran/badge kiri + teks status di kanan.

### 5.8 Leaderboard — Podium
- 3 kolom balok (bar chart style), tinggi berbeda: rank 1 (tengah, tertinggi, `color-primary-700`), rank 2 (kiri, `color-orange-300`), rank 3 (kanan, `color-yellow-300`).
- Avatar + crown icon (khusus rank 1) mengambang di atas balok.
- Nama + poin di bawah avatar, sebelum masuk balok.
- Angka rank besar (1/2/3) ditampilkan di tengah balok, warna putih transparan/outline.

### 5.9 Leaderboard — List Item (rank 4 ke bawah)
- Card putih rounded, avatar kiri, nomor rank kecil di pojok avatar, nama + poin di kanan.

### 5.10 Profile — Header Card
- Avatar besar overlap antara background ungu dan card putih (avatar setengah di ungu, setengah di card).
- Nama di bawah avatar, bendera negara kecil di samping avatar.
- Card stat 3-kolom (Points / World Rank / Local Rank) dengan divider vertikal tipis, background ungu muda/transparan di atas card putih.

### 5.11 Profile — Tab (Badge / Stats / Details)
- Tab teks sederhana, aktif = bold + underline warna ungu, nonaktif = abu-abu.

### 5.12 Badge Grid
- Grid 3 kolom, tiap badge = bentuk geometris berbeda (pentagon, hexagon) sebagai container ikon.
- Badge earned: warna solid cerah (emas, ungu, oranye, hijau) + ikon putih di tengah.
- Badge locked: `color-gray-200`, ikon gembok, tanpa warna solid.

---

## 6. Ikonografi & Ilustrasi

- **Semua ikon WAJIB berupa SVG (vector icon set), TIDAK BOLEH menggunakan emoji** (🔥, ⏰, ✅, 💜, dsb) di manapun dalam UI — termasuk di label kategori, tombol, badge, notifikasi, maupun copywriting di dalam komponen. Emoji tidak konsisten renderingnya lintas platform (iOS/Android/web) dan langsung terlihat tidak profesional/"AI slop".
- Ikon: line-icon minimalis (outline, stroke ~1.5–2px), warna sesuai konteks (putih di atas ungu, hitam/abu di atas putih). Untuk status "aktif/selesai" boleh pakai versi solid/filled dari icon set yang sama, bukan icon berbeda gaya.
- Sumber ikon harus dari satu icon set konsisten (mis. Tabler Icons, Phosphor, atau Material Symbols) — dipilih tim desain dan tidak boleh dicampur antar-set dalam satu produk.
- Ilustrasi karakter: gaya kartun flat dengan outline halus, digunakan untuk avatar user & elemen dekoratif (bukan foto asli, bukan emoji wajah).
- Bendera negara: digunakan sebagai indikator lokal, ukuran kecil (16–20px), circular atau rounded-square crop, sebagai gambar/SVG asset — bukan emoji bendera.

---

## 7. Elevasi & Shadow

| Token | Penggunaan |
|---|---|
| `shadow-sm` | List item, chip |
| `shadow-md` | Card putih di atas background ungu (Home), FAB |
| `shadow-lg` | FAB saat pressed/hover, modal |

Shadow selalu soft (blur besar, opacity rendah 8–15%), tidak ada hard shadow.

---

## 8. Aturan Penggunaan (Do & Don't)

**Do:**
- Selalu gunakan rounded corner konsisten sesuai tabel §4.
- Gunakan satu warna aksen dominan per card (jangan campur >2 warna aksen dalam satu card).
- Gunakan `text-label-upper` untuk semua label kategori/section kecil.

**Don't:**
- Jangan gunakan sudut tajam (radius 0) di komponen manapun.
- Jangan gunakan lebih dari 1 CTA pill button dalam satu card.
- Jangan campur font weight lebih dari 3 level dalam satu card.
- Jangan gunakan warna di luar palet §2 tanpa approval tim desain.
- **Jangan gunakan outline pill/kapsul berbentuk stroke tipis untuk menampilkan info level, status, atau metadata kecil** (mis. "LEVEL 1 • RAMPUNG" dibungkus border hijau). Pola ini terlihat generic/"AI slop" dan tidak konsisten dengan gaya fill solid di sistem desain ini. Gunakan format teks polos sesuai §5.3b, atau ikon solid tanpa container sesuai §5.3c.
- Jangan menggabungkan dua informasi berbeda jenis (mis. nomor level + status penyelesaian) ke dalam satu badge/kapsul tunggal.
- **Jangan pakai emoji sebagai ikon di UI mana pun** (tombol, badge, label kategori, notifikasi, dsb). Semua ikon harus SVG dari satu icon set konsisten (§6).
- Aturan outline-pill di atas berlaku untuk SEMUA label kategori/status kecil, termasuk di atas card featured/CTA (mis. "Tantangan Dina Iki") — bukan cuma di list item. Kalau ragu suatu elemen teks kecil butuh "dibungkus" kapsul atau tidak, defaultnya: **tidak usah dibungkus**.

---

## 9. Catatan Adaptasi untuk Proyek "Sinau"

Jika sistem desain ini dipakai sebagai basis UI Sinau (app belajar Bahasa & budaya Jawa):
- Palet ungu tetap bisa dipertahankan sebagai warna brand, atau diganti ke palet yang merefleksikan budaya Jawa (mis. earth-tone/batik-inspired) — perlu keputusan tim.
- Struktur card (Home: recent activity + featured challenge + live list), Leaderboard (podium + list), dan Profile (stat card + badge grid) dapat digunakan langsung untuk fitur: progres belajar, papan peringkat siswa, dan koleksi badge/aksara.
- Badge grid (§5.12) sangat relevan untuk merepresentasikan pencapaian Aksara Jawa yang sudah dikuasai (earned vs locked).
