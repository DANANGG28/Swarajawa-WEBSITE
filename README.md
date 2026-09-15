# SJ Website

Proyek web berbasis [Laravel 12](https://laravel.com) yang berjalan di atas Docker (Laravel Sail) dengan database **PostgreSQL**.

---

## Prasyarat

Sebelum mulai, pastikan sudah terpasang:

| Tool | Versi | Keterangan |
| --- | --- | --- |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | terbaru | Wajib (dipakai Laravel Sail) |
| [Docker Compose](https://docs.docker.com/compose/) | v2 | Biasanya sudah termasuk Docker Desktop |
| [Composer](https://getcomposer.org) | 2.x | Hanya diperlukan jika ingin **tanpa** Docker |
| [PHP](https://www.php.net) | ^8.2 | Hanya diperlukan jika ingin **tanpa** Docker |
| [Node.js](https://nodejs.org) | 20+ | Hanya diperlukan jika ingin **tanpa** Docker |

> **Rekomendasi:** gunakan Docker (Sail). Semua dependensi (PHP, Composer, Node, PostgreSQL)
> ikut terinstal di dalam container sehingga tidak perlu setup PHP/Node di lokal.

---

## 1. Setup Awal (Pertama Kali)

```bash
# Clone repository
git clone <url-repository>.git
cd sjwebsite

# Salin .env dari contoh
cp .env.example .env

# Setup kredensial (bisa dibiarkan, sudah disesuaikan untuk Sail + PostgreSQL)
# Pastikan bagian ini tidak kosong di .env:
#   DB_CONNECTION=pgsql
#   DB_HOST=pgsql
#   DB_PORT=5432
#   DB_DATABASE=laravel
#   DB_USERNAME=laravel
#   DB_PASSWORD=<bebas>

# Generate application key
php artisan key:generate

# Jalankan container (build image + start app & pgsql)
./vendor/bin/sail up -d

# Install dependensi PHP di dalam container
./vendor/bin/sail composer install

# Install dependensi frontend
./vendor/bin/sail npm install

# Build asset (satu kali, atau setiap kali mengubah resource)
./vendor/bin/sail npm run dev

# Jalankan migrasi database
./vendor/bin/sail artisan migrate
```

Buka aplikasi di: **http://localhost**

---

## 2. Setup Tanpa Docker (Opsional)

Jika tidak ingin memakai Docker, jalankan langsung di lokal. Pastikan PostgreSQL
sudah berjalan dan kredensial di `.env` mengarah ke DB tersebut
(`DB_HOST=127.0.0.1`, `DB_PORT=5432`).

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
php artisan migrate
php artisan serve   # http://127.0.0.1:8000
```

---

## 3. Perintah Harian

Semua perintah dijalankan lewat `./vendor/bin/sail` agar berjalan di dalam container.

| Perintah | Fungsi |
| --- | --- |
| `./vendor/bin/sail up -d` | Menjalankan semua container (app + pgsql) |
| `./vendor/bin/sail down` | Menghentikan container |
| `./vendor/bin/sail ps` | Melihat status container |
| `./vendor/bin/sail artisan` | Menjalankan perintah `artisan` di container |
| `./vendor/bin/sail composer` | Menjalankan Composer di container |
| `./vendor/bin/sail npm` | Menjalankan npm di container |
| `./vendor/bin/sail tinker` | REPL / shell interaktif |
| `./vendor/bin/sail logs` | Melihat log aplikasi |
| `./vendor/bin/sail test` | Menjalankan test (PHPUnit) |

### Lint & Formatter

```bash
./vendor/bin/sail artisan pint --test   # cek kode (Laravel Pint)
./vendor/bin/sail artisan pint          # format otomatis
```

---

## 4. Database (PostgreSQL)

- **Host:** `pgsql` (nama service di network Sail, bukan `127.0.0.1`)
- **Port:** `5432` (di-forward ke `FORWARD_DB_PORT`, default `5432`)
- **Database default:** `laravel`
- **User / Password:** sesuai `DB_USERNAME` / `DB_PASSWORD` di `.env`

Masuk ke psql di dalam container:

```bash
./vendor/bin/sail exec pgsql psql -U laravel -d laravel
```

Membuat migrasi baru:

```bash
./vendor/bin/sail artisan make:migration nama_migrasi
./vendor/bin/sail artisan migrate            # jalankan migrasi
./vendor/bin/sail artisan migrate:rollback   # rollback batch terakhir
```

Mengisi data awal:

```bash
./vendor/bin/sail artisan db:seed
```

---

## 5. Frontend (Vite)

Jalankan dev server di dalam container:

```bash
./vendor/bin/sail npm run dev
```

Saat siap produksi, build asset:

```bash
./vendor/bin/sail npm run build
```

---

## 6. Testing

```bash
# Menjalankan seluruh test
./vendor/bin/sail artisan test

# Hanya test tertentu
./vendor/bin/sail artisan test --filter=NamaTest
```

---

## 7. Struktur & Aturan Repo

- **`docker-compose.yml`** — konfigurasi Sail + PostgreSQL (WAJIB di-commit).
- **`.env`** — konfigurasi lokal/pribadi (TIDAK di-commit; sudah masuk `.gitignore`).
- **`.env.example`** — template konfigurasi (WAJIB di-commit).
- Jika menambah komponen eksternal (misal Redis, Mailpit), sesuaikan `docker-compose.yml`
  dan `.env`, lalu pastikan saltin `.env.example`.

---

## 8. Alur Kerja Git (Workflow Project)

1. **Pull / Update branch utama (`main`)**
   ```bash
   git checkout main
   git pull origin main
   ```

2. **Buat & checkout branch baru**
   ```bash
   git checkout -b feature/nama-fitur
   ```

3. **Simpan perubahan (Commit)**
   ```bash
   git add .
   git commit -m "feat: deskripsi perubahan"
   ```

4. **Push branch ke remote**
   ```bash
   git push -u origin feature/nama-fitur
   ```

5. **Buat Pull Request (PR)**
   - Buka halaman repositori di GitHub/GitLab.
   - Buat Pull Request dari branch `feature/nama-fitur` ke `main`.
   - Tunggu review sebelum di-merge.

---

## Troubleshooting

**Port 5432 sudah terpakai?**
Ubah `FORWARD_DB_PORT` di `.env`, lalu restart:
```bash
./vendor/bin/sail down && ./vendor/bin/sail up -d
```

**Ganti password database?**
Ubah `DB_PASSWORD` di `.env`. Untuk database yang sudah terlanjur dibuat, ganti juga
password user di PostgreSQL:
```bash
./vendor/bin/sail exec pgsql psql -U laravel -d postgres -c "ALTER USER laravel PASSWORD 'password-baru';"
```

**Container gagal start karena image belum ter-build?**
```bash
./vendor/bin/sail up -d --build
```

**Saat `npm run dev` gagal / hot reload tidak jalan?**
Pastikan `VITE_PORT` di `.env` sama dengan port yang dipetakan ulang di `docker-compose.yml`,
lalu jalankan lagi `./vendor/bin/sail npm run dev`.
