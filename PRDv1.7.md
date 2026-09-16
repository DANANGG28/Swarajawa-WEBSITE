# Product Requirements Document (PRD)
## Sinau Jowo
**Platform Pembelajaran Interaktif Bahasa dan Budaya Jawa Berbasis Mobile & Web**

Versi 1.7 — Politeknik Negeri Jember (Polije), Tugas Kelompok Semester 3, 2026

> **Catatan revisi v1.1:** Menyempurnakan v1.0 berdasarkan flowchart alur sistem dan ERD yang sudah dibuat tim. Perubahan utama: (1) peran pengguna dipecah menjadi tiga — **Siswa, Guru, Superadmin** (sebelumnya "guru/admin" digabung), (2) penambahan Bagian 6A Model Data (ERD) dan Bagian 6B Alur Sistem (Flowchart), (3) penyesuaian Functional Requirements & User Stories agar konsisten dengan hak akses tiap peran di flowchart, (4) penambahan Open Questions baru dari temuan desain ERD; juga ditambahkan bagian Tech Stack (Laravel + PostgreSQL untuk web, Flutter untuk mobile).
>
> **Catatan revisi v1.2:** Menjawab sebagian Open Questions v1.1 — lihat Bagian 10.1 (Keputusan): metodologi uji hanya simulasi internal, struktur JSON soal diserahkan ke tim dev, kebutuhan tabel pivot progres siswa dikonfirmasi, pembatasan pemantauan guru per kelas, dan hak edit soal guru dibatasi ke miliknya sendiri. Untuk 2 pertanyaan yang belum diputuskan (sumber korpus & API TTS/STT), ditambahkan rekomendasi awal di Bagian 10.2.
>
> **Catatan revisi v1.3:** Menambahkan Bagian 6C — arsitektur Speech-to-Speech (STS), yang sebelumnya hanya disebut satu baris di FR-8 tanpa penjelasan. Pipeline STT (Google) → Modul Pemrosesan Respons → TTS (Azure) ditambahkan sebagai Opsi A, dengan risiko latensi gabungan ditambahkan ke Bagian 9.
>
> **Catatan revisi v1.4 (koreksi):** v1.3 sempat menyatakan tidak ada vendor dengan model STS native untuk Bahasa Jawa — **ini tidak akurat**. Ditemukan bahwa mode *Live Translation* pada Gemini Live API (`gemini-3.5-live-translate-preview`) resmi mendukung Bahasa Jawa (`jv`) sebagai model satu-panggilan (Opsi B di Bagian 6C), meski sifatnya murni penerjemah (bukan pengganti logika penilaian jawaban di Opsi A).
>
> **Catatan revisi v1.5 (sinkronisasi dengan Laporan Resmi Kelompok 1):** Menyesuaikan PRD dengan dokumen `LAPORAN_SISTEM_PEMBELAJARAN_BAHASA_BUDAYA_JAWA.pdf`. Perubahan: (1) Tujuan Produk (3.1) ditulis ulang mengikuti 5 Tujuan Khusus di laporan; (2) ditambahkan FR-21 Latihan Puzzle Pakaian Adat dan cakupan materi budaya (busana adat, rumah adat, kesenian tradisional) yang sebelumnya tidak tercakup; (3) NFR ditambah poin Kemudahan Penggunaan (Usability) terpisah dari Kompatibilitas; (4) relasi "Memantau" guru–siswa diperjelas mengikuti kalimat asli laporan ("sesuai kelas **atau mata pelajaran** terkait"), bukan hanya dibatasi per kelas; (5) ditambahkan catatan bahwa kelompok data "basis pengetahuan RAG" yang disebut di laporan belum benar-benar dimodelkan sebagai entitas ERD; (6) nama kolom ERD (6A) **sengaja distandarkan** (`jenis_kelamin`, `status_pegawaian`, `created_at`/`updated_at` konsisten di semua tabel) alih-alih menyalin penulisan tidak konsisten dari laporan asli (`jenis_klamin`, `st_pegawaian`, `update_at`, `create_at`).
>
> **Catatan revisi v1.6:** Menambahkan **FR-22 Latihan Menulis Aksara Jawa (Tracing)** dan Bagian 6D — kanvas dengan template bayangan yang ditelusuri siswa, skor kemiripan dihitung dengan algoritma geometris ringan ($1/$N Recognizer) sebelum goresan kasar siswa dianimasikan ("snap") menjadi bentuk baku. Risiko terkait toleransi algoritma pengenalan goresan ditambahkan ke Bagian 9.
>
> **Catatan revisi v1.7:** Menambahkan entitas **`test`** dan pivot **`test_soal`** ke Bagian 6A, serta **FR-23**. Keputusan: komposisi latihan diatur berdasarkan `tipe_soal` pada tiap butir soal (bukan "jenis test" terpisah yang mengunci satu tipe) — guru/superadmin bebas menyusun satu `test` berisi campuran beberapa tipe soal, atau hanya satu tipe soal yang sama, sesuai kebutuhan (variatif vs. remedial/drilling). Sistem tidak membatasi ini di level skema database.
>
> **Catatan revisi v1.7.1 (Sinkronisasi Fitur CRUD Akun & UI Detail Superadmin):** Menyempurnakan spesifikasi FR-17 & FR-18 terkait manajemen akun Guru dan Siswa oleh Superadmin. Penyesuaian mencakup: (1) pemisahan form pendaftaran (`create`) dan form sunting (`edit`) ke halaman antarmuka terpisah (`/superadmin/guru/tambah`, `/superadmin/guru/{id}/edit`, `/superadmin/siswa/tambah`, `/superadmin/siswa/{id}/edit`), (2) penambahan atribut `foto_url` pada entitas `guru` beserta dukungan upload berkas gambar profil ke `resources/image/guru/` dan fitur modal zoom foto interaktif, (3) penyediaan halaman rincian akun (`detail/show`) untuk melihat profil lengkap, kontak, status kepegawaian guru, serta capaian EXP & streak belajar siswa, (4) standardisasi antarmuka tabel dengan toolbar terpadu (pencarian real-time + filter kelas), 3 tombol aksi cepat berikon (Edit, Detail, Hapus), pagination 10 data per halaman, dan notifikasi animasi toast pop-up sukses/gagal di sudut kanan bawah.

---

## 1. Overview

**Produk:** Sinau Jowo — aplikasi mobile Android dan website terintegrasi untuk pembelajaran Bahasa dan Budaya Jawa, menggunakan pendekatan gamifikasi (mirip Duolingo) yang diperkuat teknologi AI suara (TTS, STT, STS) dan chatbot RAG.

**Target Pengguna:** Tiga peran dengan hak akses berbeda (role-based access):
- **Siswa** (pengguna utama) — SMP/SMA, belajar via mobile/web.
- **Guru** — membuat & mengelola soal, memantau progres siswa.
- **Superadmin** — mengelola seluruh akun (guru & siswa), struktur level materi, dan seluruh bank soal di tingkat sistem.

**Selaras SDG:** SDG 10 (Berkurangnya Kesenjangan) target 10.2 — pemberdayaan akses belajar bahasa & budaya sendiri bagi kelompok yang tersisih darinya; didukung SDG 4 target 4.7 dan SDG 11 target 11.4.

**Tech Stack:**
- **Backend/Web:** Laravel (REST API) dengan database **PostgreSQL**.
- **Mobile:** **Flutter**, mengonsumsi REST API yang sama dengan website (satu backend terintegrasi untuk web & mobile).

---

## 2. Problem Statement

Penggunaan aktif Bahasa Jawa di kalangan generasi muda terus menurun akibat pergeseran bahasa (*language shift*), sementara media pembelajaran bahasa daerah di sekolah masih konvensional dan kurang menarik dibandingkan aplikasi belajar bahasa modern. Siswa yang ingin mendalami bahasa dan budaya leluhurnya sendiri tidak memiliki akses ke sarana belajar yang setara kualitasnya dengan sarana belajar bahasa asing.

---

## 3. Goals & Success Metrics

### 3.1 Tujuan Produk

Mengikuti rumusan Tujuan Khusus pada laporan resmi tim:

1. **Meningkatkan Keterlibatan Siswa melalui Gamifikasi** — mengimplementasikan EXP, streak, dan leaderboard untuk mendorong kebiasaan belajar yang konsisten dan kompetitif secara sehat.
2. **Menyediakan Asistensi Belajar Cerdas** — mengintegrasikan Chatbot RAG untuk memberikan respons dan bantuan belajar yang relevan secara mandiri bagi siswa.
3. **Menghadirkan Model Evaluasi Pembelajaran yang Variatif** — instrumen kuis multi-format mencakup pilihan ganda, susun kata/puzzle (termasuk puzzle pakaian adat), serta evaluasi berbasis suara (TTS/STT).
4. **Mempermudah Pengawasan Akademik oleh Pengajar** — memfasilitasi guru melacak riwayat progres dan capaian belajar siswa secara terstruktur berdasarkan kelas maupun NIS.
5. **Mewujudkan Pengelolaan Data Terpusat dan Multi-Peran** — membangun hak akses bertingkat (Siswa, Guru, Superadmin) untuk tata kelola pembuatan soal, manajemen level materi, dan pengelolaan akun pengguna secara efisien melalui satu basis data terintegrasi.

### 3.2 Metrik Keberhasilan (Success Metrics)

| Metrik | Target (MVP/Demo) | Cara Ukur |
|---|---|---|
| Jumlah pengguna aktif | ≥ 30 siswa uji coba | Jumlah akun terdaftar & aktif login ≥ 1x/minggu |
| Retensi (streak) | ≥ 40% siswa uji coba memiliki streak ≥ 3 hari | Data streak di database |
| Akurasi skor pelafalan (STT) | Skor konsisten dengan penilaian manual pada sampel uji | Bandingkan hasil STT vs penilaian guru pada 20 sampel ucapan |
| Guardrail chatbot | 0% jawaban di luar korpus tanpa penolakan | Uji dengan ≥ 20 pertanyaan di luar cakupan materi |
| Penyelesaian level | ≥ 50% siswa uji coba menyelesaikan Level 1 | Data progres di database |
| Adopsi panel admin | Guru & superadmin dapat menambah 1 level materi + minimal 10 soal tanpa bantuan developer | Uji coba mandiri panel web oleh tim non-teknis |

> **Catatan metodologi pengujian:** untuk fase tugas ini, seluruh uji coba pada tabel di atas (termasuk "≥ 30 siswa uji coba") dilakukan sebagai **simulasi internal tim** (bukan uji coba dengan siswa sekolah nyata), untuk keperluan presentasi/demo di depan dosen penguji. Skala dan skenario data dapat disesuaikan agar merepresentasikan kondisi target secara meyakinkan meski sumber datanya simulasi.

---

## 4. Target Pengguna & Persona

**Persona 1 — Siswa (Andi, 14 tahun, SMP):** Tumbuh di lingkungan yang jarang memakai Bahasa Jawa sehari-hari, ingin bisa berbicara krama dengan sopan ke orang tua/guru, lebih suka belajar lewat HP dibanding buku.

**Persona 2 — Guru Bahasa Jawa (Bu Sri, 35 tahun):** Mengajar dengan jumlah siswa banyak dan waktu terbatas, butuh cara memantau siapa siswa yang tertinggal tanpa harus mengoreksi manual satu per satu, serta ingin bisa menambah soal latihan sendiri untuk kelasnya.

**Persona 3 — Superadmin (Pak Dedi, 40 tahun, operator sistem sekolah/tim pengembang):** Bertanggung jawab menjaga integritas data di seluruh sekolah — mendaftarkan akun guru baru, memantau jumlah pengguna terdaftar, menyusun struktur level materi, dan menjadi otoritas tertinggi atas bank soal jika guru tidak dapat mengelolanya sendiri.

---

## 5. User Stories & Acceptance Criteria

| Peran | User Story | Acceptance Criteria |
|---|---|---|
| Siswa | Sebagai siswa, saya ingin mendaftar akun sendiri atau masuk dengan akun yang ada, agar saya bisa langsung mulai belajar. | Sistem mengecek status akun; jika belum punya akun, siswa diarahkan ke form Daftar; jika sudah, langsung ke Masuk lalu beranda EXP/Streak. |
| Siswa | Sebagai siswa, saya ingin berlatih pelafalan Bahasa Jawa lewat suara, agar saya tahu apakah ucapan saya sudah benar. | STT membandingkan ucapan siswa dengan referensi dan memberi skor akurasi ≥ 1 kali percobaan; feedback ditampilkan < 3 detik. |
| Siswa | Sebagai siswa, saya ingin bertanya ke chatbot soal kapan pakai krama vs ngoko, agar saya tidak salah unggah-ungguh. | Chatbot menjawab dari korpus resmi; jika pertanyaan di luar cakupan, chatbot menyatakan tidak tahu, bukan mengarang jawaban. |
| Siswa | Sebagai siswa, saya ingin melihat streak dan XP saya serta leaderboard, agar saya termotivasi belajar tiap hari. | Streak bertambah otomatis saat siswa menyelesaikan ≥ 1 sesi latihan per hari; reset jika lewat 24 jam tanpa aktivitas; leaderboard menampilkan peringkat EXP tertinggi antar siswa. |
| Siswa | Sebagai siswa, saya ingin diberi tahu jika saya belum memenuhi syarat suatu materi, agar saya tahu harus menyelesaikan materi sebelumnya dulu. | Jika materi prasyarat belum tercapai, sistem menampilkan peringatan "Materi belum tercapai" dan tidak mengizinkan siswa memulai sesi quiz. |
| Guru | Sebagai guru, saya ingin menyusun satu test dari beberapa soal di bank soal saya, agar saya bisa membuat latihan campuran atau latihan yang fokus ke satu skill saja (mis. remedial susun kalimat). | Guru dapat membuat `test` baru, memilih soal-soal miliknya dari bank soal (boleh beda `tipe_soal` atau sama semua), dan menentukan urutannya. |
| Guru | Sebagai guru, saya ingin login langsung dengan akun yang sudah didaftarkan superadmin, tanpa perlu mendaftar sendiri, agar identitas guru terverifikasi oleh sekolah. | Guru hanya dapat login (tidak ada opsi daftar mandiri); akun guru dibuat oleh superadmin terlebih dahulu. |
| Guru | Sebagai guru, saya ingin menambah atau mengelola soal untuk materi tertentu, agar konten latihan tetap relevan. | Guru dapat memilih materi, membuat soal baru (pilihan ganda/susun kalimat/pencocokan/audio), dan melakukan CRUD/edit/hapus **hanya atas soal buatannya sendiri** (soal buatan guru lain tidak dapat diubah/dihapus oleh guru tersebut). |
| Guru | Sebagai guru, saya ingin melihat progres siswa berdasarkan kelas atau NIS, agar saya tahu siapa yang tertinggal. | Guru dapat mencari siswa berdasarkan kelas/NIS, **tetapi hanya untuk kelas/mata pelajaran yang ia ampu sendiri**; guru tidak dapat melihat progres siswa di luar kelas/mapel yang diampunya (siswa yang sama boleh muncul di lebih dari satu guru bila diampu beberapa mapel). Menampilkan persentase penyelesaian level serta aktivitas terakhir. |
| Superadmin | Sebagai superadmin, saya ingin melihat dashboard ringkas jumlah guru dan siswa terdaftar, agar saya punya gambaran skala penggunaan sistem. | Dashboard superadmin menampilkan total akun guru dan total akun siswa terdaftar saat login. |
| Superadmin | Sebagai superadmin, saya ingin mendaftarkan, melihat detail profil, menyunting, dan menghapus (CRUD) akun guru, agar pengelolaan pengajar teratur dan terverifikasi. | Superadmin dapat mendaftarkan guru baru beserta upload foto profil, melihat detail profil & zoom foto, menyunting data profil/kontak/password, serta menghapus akun guru dengan konfirmasi. |
| Superadmin | Sebagai superadmin, saya ingin mendaftarkan, melihat rincian capaian, menyunting, dan menghapus (CRUD) akun siswa secara terpusat, agar data siswa tetap konsisten dan terpantau. | Superadmin dapat mendaftarkan siswa baru via form terpisah, melihat rincian capaian EXP/streak/kontak di halaman detail, menyunting data akademik/sandi siswa, memfilter daftar per kelas/NIS, serta menghapus akun siswa dengan konfirmasi. |
| Superadmin | Sebagai superadmin, saya ingin membuat dan mengelola level/materi quiz baru, agar struktur pembelajaran bisa berkembang tanpa mengubah kode aplikasi. | Superadmin dapat menambah nama materi quiz baru atau melihat daftar materi yang sudah ada dan melakukan CRUD/edit/hapus. |
| Superadmin | Sebagai superadmin, saya ingin menambah dan mengelola soal di seluruh materi (tidak terbatas pada satu guru), agar kualitas bank soal tetap terjaga. | Superadmin dapat membuat soal baru untuk materi manapun atau mengelola (CRUD/edit/hapus) seluruh soal yang ada di sistem. |

---

## 6. Functional Requirements

Prioritas menggunakan kerangka MoSCoW (Must / Should / Could / Won't untuk fase ini).

| ID | Fitur | Deskripsi | Peran Terkait | Prioritas |
|---|---|---|---|---|
| FR-1 | Autentikasi & Profil | Sistem HARUS menyediakan pemilihan peran (Siswa/Guru/Superadmin) di titik masuk, lalu registrasi mandiri **khusus siswa** (jika belum punya akun) atau login langsung untuk guru dan superadmin (akun mereka dibuat oleh superadmin, bukan self-registrasi). | Semua | Must |
| FR-2 | Materi Berjenjang | Sistem HARUS menampilkan materi dalam struktur level (Dasar, Unggah-ungguh, Aksara Jawa, Peribahasa, Cerita Rakyat) yang terkunci berurutan sesuai progres; jika prasyarat belum terpenuhi, tampilkan peringatan "materi belum tercapai" dan blokir akses quiz. Materi HARUS memadukan unsur bahasa dengan artefak budaya (busana adat, rumah adat, kesenian tradisional), bukan bahasa saja. | Siswa | Must |
| FR-3 | Latihan Pilihan Ganda | Sistem HARUS menyediakan soal pilihan ganda dengan validasi jawaban otomatis dan feedback langsung. | Siswa | Must |
| FR-4 | Latihan Susun Kalimat | Sistem HARUS menyediakan latihan drag-and-drop menyusun kata acak menjadi kalimat yang benar. | Siswa | Must |
| FR-5 | Latihan Pencocokan Arti | Sistem HARUS menyediakan latihan menjodohkan kata Bahasa Jawa dengan artinya. | Siswa | Must |
| FR-6 | Text-to-Speech (TTS) | Sistem HARUS dapat membacakan teks materi/kosakata dalam Bahasa Jawa dengan pelafalan yang jelas; soal yang memerlukan audio referensi menyimpan tautannya pada field `media_audio_url`. | Siswa | Must |
| FR-7 | Speech-to-Text (STT) | Sistem HARUS dapat menangkap ucapan siswa dan mengubahnya menjadi teks untuk dicocokkan dengan jawaban referensi (`kunci_jawaban`/`media_audio_url` pada soal tipe audio). | Siswa | Must |
| FR-8 | Speech-to-Speech (STS) | Sistem HARUS dapat memproses ucapan siswa dan merespons secara lisan dalam Bahasa Jawa. Untuk kebutuhan penilaian jawaban (quiz suara), dibangun sebagai pipeline: **STT (Google Cloud, `jv-ID`) → Modul Pemrosesan Respons → TTS (Azure AI Speech, `jv-ID-SitiNeural`/`jv-ID-DimasNeural`)**. Untuk kemungkinan fitur tambahan latihan terjemahan lisan, dapat dipertimbangkan **Gemini Live Translation** (`gemini-3.5-live-translate-preview`, resmi mendukung `jv`) sebagai model satu-panggilan. Lihat Bagian 6C untuk detail & trade-off kedua opsi. | Siswa | Must |
| FR-9 | Chatbot RAG | Sistem HARUS menyediakan chatbot yang menjawab pertanyaan tata bahasa/unggah-ungguh hanya berdasarkan korpus yang di-embed, dan menolak menjawab di luar cakupan. Dapat diakses langsung dari beranda siswa. | Siswa | Must |
| FR-10 | Gamifikasi (XP/Streak/Leaderboard) | Sistem HARUS mencatat XP (akumulasi `bobot_exp` per soal + `reward_exp` per level selesai) pada tabel `exp`, streak harian pada tabel `strek` (current & highest streak, reset otomatis lewat 24 jam), dan menampilkan leaderboard EXP tertinggi antar siswa. | Siswa | Must |
| FR-11 | Dashboard Progres Siswa (Web/Guru) | Website HARUS menampilkan dashboard progres belajar siswa (per kelas/NIS) untuk dipantau oleh guru, **dibatasi hanya pada kelas/mata pelajaran yang diampu oleh guru tersebut** (satu siswa dapat tampil ke lebih dari satu guru jika diampu beberapa mapel berbeda di kelas yang sama). | Guru | Must |
| FR-12 | Manajemen Soal oleh Guru | Website HARUS menyediakan panel bagi guru untuk mengelola soal dengan alur bertahap: (1) tampilkan card untuk setiap Level Materi (grid responsive dengan jumlah soal), (2) guru klik salah satu card untuk memilih level, (3) tampil daftar soal khusus level tersebut, (4) guru dapat membuat soal baru (inline form) atau CRUD/edit/hapus **soal miliknya sendiri** (guru tidak dapat mengubah/menghapus soal buatan guru lain atau superadmin). Dari halaman daftar soal, tampilkan tombol "Kembali ke Pilihan Level" untuk kembali ke card selection. | Guru | Should |
| FR-13 | Notifikasi Pengingat Belajar | Sistem SEBAIKNYA mengirim notifikasi pengingat harian untuk menjaga streak siswa. | Siswa | Should |
| FR-14 | Mode Offline Sebagian | Sistem DAPAT menyimpan materi teks dasar untuk diakses tanpa koneksi internet (fitur suara tetap butuh koneksi). | Siswa | Could |
| FR-15 | Ekspansi Bahasa Daerah Lain | Sistem DAPAT dirancang agar arsitektur mendukung penambahan bahasa daerah lain di masa depan. | — | Won't (fase ini) |
| FR-16 | Dashboard & Ringkasan Superadmin | Website HARUS menampilkan dashboard superadmin berisi jumlah total akun guru dan akun siswa terdaftar. | Superadmin | Should |
| FR-17 | Manajemen Akun Guru (Superadmin) | Website HARUS menyediakan panel superadmin untuk mendaftarkan akun guru baru (`create`) beserta upload foto profil, melihat daftar akun guru dengan pencarian/filter serta halaman detail profil (`read/detail`) dengan zoom foto, menyunting data profil, kontak, password & foto (`update/edit`), serta menghapus akun guru (`delete`) dengan konfirmasi. Dilengkapi notifikasi feedback aksi (toast pop-up). | Superadmin | Must |
| FR-18 | Manajemen Akun Siswa (Superadmin) | Website HARUS menyediakan panel superadmin untuk mendaftarkan akun siswa baru (`create`), melihat daftar siswa dengan pencarian & filter kelas (`read`) per 10 data (pagination), melihat rincian capaian EXP, streak & profil siswa pada halaman detail (`detail`), menyunting data profil, kelas, kontak & password siswa (`update/edit`), serta menghapus akun siswa (`delete`) dengan konfirmasi. Dilengkapi notifikasi feedback aksi (toast pop-up). | Superadmin | Must |
| FR-19 | Manajemen Level Materi (Superadmin) | Website HARUS menyediakan panel superadmin untuk membuat level/materi quiz baru (nama materi, deskripsi, reward_exp) serta CRUD/edit/hapus level materi yang ada. | Superadmin | Must |
| FR-20 | Manajemen Soal Lintas Materi (Superadmin) | Website HARUS menyediakan panel superadmin untuk membuat soal baru di materi manapun serta CRUD/edit/hapus seluruh soal di sistem, sebagai otoritas tertinggi di atas manajemen soal guru (FR-12). | Superadmin | Must |
| FR-21 | Latihan Puzzle Pakaian Adat | Sistem HARUS menyediakan latihan puzzle menyusun potongan gambar pakaian adat Jawa (selain puzzle kata di FR-4), sebagai bagian dari format evaluasi budaya yang lebih variatif. | Siswa | Should |
| FR-22 | Latihan Menulis Aksara Jawa (Tracing) | Sistem HARUS menyediakan kanvas latihan menulis aksara Jawa: siswa menarik garis mengikuti bayangan template aksara; goresan siswa dibandingkan dengan bentuk referensi untuk menghasilkan skor kemiripan, lalu goresan yang kasar/tidak rapi tersebut dianimasikan ("snap") menjadi bentuk baku sebagai feedback visual positif. Lihat Bagian 6D untuk arsitektur detail. | Siswa | Should |
| FR-23 | Penyusunan Test/Paket Latihan | Sistem HARUS memungkinkan guru (untuk soal miliknya) dan superadmin (untuk seluruh soal) menyusun satu `test` dari beberapa soal di bank soal (`test_soal`), **bebas memilih apakah test tersebut berisi campuran beberapa `tipe_soal` sekaligus, atau hanya satu tipe soal yang sama** (mis. untuk remedial/drilling 1 skill) — keputusan komposisi ada di tangan guru/superadmin, sistem tidak membatasinya di level skema. Lihat catatan desain di Bagian 6A. | Guru, Superadmin | Must |

---

## 6A. Model Data (Entity-Relationship Overview)

Ringkasan entitas dan atribut utama berdasarkan ERD yang telah dirancang tim.

| Entitas | Atribut Utama |
|---|---|
| **siswa** | id, nis, nama_lengkap, jenis_kelamin, kelas, no_telpon, email, password, created_at, updated_at |
| **guru** | id, nip, nama_lengkap, jenis_kelamin, status_pegawaian, no_telpon, email, password, foto_url, created_at, updated_at |
| **superadmin** | id, nama_lengkap, email, no_telpon, password, created_at, updated_at |
| **level_materi** | id, nama_materi, deskripsi, reward_exp, created_at, updated_at |
| **soal** | id, level_materi_id (FK), tipe_soal, pertanyaan, opsi_jawaban, kunci_jawaban, media_audio_url, bobot_exp, created_at, updated_at |
| **test** *(baru, v1.7)* | id, level_materi_id (FK), nama_test, deskripsi, dibuat_oleh (guru_id/superadmin_id), created_at, updated_at |
| **test_soal** *(baru, v1.7 — pivot)* | test_id (FK), soal_id (FK), urutan |
| **exp** | id, siswa_id (FK), total_exp, updated_at |
| **strek (streak)** | id, siswa_id (FK), current_streak, highest_streak, last_activity_date, updated_at |

> **Catatan penamaan kolom (disengaja berbeda dari laporan sumber):** laporan/ERD asli menulis beberapa kolom secara tidak konsisten antar tabel — `jenis_klamin` (typo dari `jenis_kelamin`), `st_pegawaian` (singkatan tak baku dari `status_pegawaian`), serta kolom timestamp yang bercampur antara `update_at`/`create_at` (tanpa "d") di sebagian tabel dan `updated_at`/`created_at` (standar Laravel) di tabel lain. PRD ini **sengaja menstandarkan** semua nama kolom ke ejaan penuh dan konvensi timestamp standar Laravel (`created_at`/`updated_at` di semua tabel), karena inkonsistensi tersebut berisiko menyulitkan migrasi database dan query lintas tabel. Tim developer disarankan memakai penamaan yang sudah dirapikan di tabel ini, bukan menyalin apa adanya dari laporan/ERD gambar.
>
> **Catatan desain (foto profil guru):** atribut `foto_url` pada tabel `guru` bersifat opsional (nullable) untuk menyimpan tautan/path berkas foto profil yang diunggah superadmin ke direktori lokal (`resources/image/guru/`) atau media storage, dengan fallback otomatis berupa inisial 2 huruf nama guru jika foto belum diunggah.

**Relasi utama:**
- **siswa – exp**: satu-ke-satu — setiap siswa memiliki satu rekap total EXP.
- **siswa – strek**: satu-ke-satu — setiap siswa memiliki satu rekap streak harian.
- **siswa – level_materi** ("Memuat"): banyak-ke-banyak — merepresentasikan progres siswa di berbagai level materi.
- **guru – siswa** ("Memantau"): banyak-ke-banyak — sesuai laporan resmi, **banyak guru dapat memantau banyak siswa, dan satu siswa dapat dipantau oleh lebih dari satu guru sesuai kelas atau mata pelajaran terkait**. Artinya pembatasan bukan "satu siswa hanya dipantau satu guru", melainkan tiap guru hanya melihat siswa pada kelas/mapel yang benar-benar ia ampu — jika beberapa guru (mapel berbeda) mengampu kelas yang sama, siswa di kelas itu boleh dipantau oleh semua guru tersebut. Implementasi disarankan mengaitkan `kelas` (dan idealnya `mata_pelajaran`) pada relasi guru–siswa, bukan sekadar satu kolom `guru_id` tunggal per siswa.
- **guru – soal** ("Membuat"): satu-ke-banyak — satu guru dapat membuat banyak soal.
- **level_materi – soal**: satu-ke-banyak — satu level materi memiliki banyak soal.
- **level_materi – test** *(baru, v1.7)*: satu-ke-banyak — satu level materi dapat punya banyak test/paket latihan.
- **test – soal** *(baru, v1.7, via `test_soal`)*: banyak-ke-banyak — satu test berisi satu atau lebih soal (urutan diatur lewat kolom `urutan`); satu soal di bank soal bisa dipakai ulang di beberapa test berbeda.
- **superadmin – guru** ("Membuat"): satu-ke-banyak — superadmin mendaftarkan akun guru.
- **superadmin – siswa** ("Membuat"/"Mengelola"): satu-ke-banyak — superadmin dapat mengelola akun siswa.
- **superadmin – soal** ("Menambah dan mengelola"): satu-ke-banyak — superadmin punya akses kelola atas seluruh soal, tidak dibatasi guru pembuatnya.
- **superadmin – level_materi**: satu-ke-banyak (tersirat dari alur "Manajemen Level Quiz" pada flowchart) — superadmin mengelola struktur level materi.

> **Keputusan desain (progres siswa):** tim memutuskan sistem **perlu menyimpan status progres dan level siswa**. Relasi banyak-ke-banyak `siswa–level_materi` karena itu diimplementasikan sebagai tabel pivot, mis. `progres_siswa` (siswa_id, level_materi_id, status: terkunci/berjalan/selesai, tanggal_selesai), karena ERD dasar belum menampilkan atribut pivot tersebut secara eksplisit.
>
> **Keputusan desain (struktur soal):** kolom `opsi_jawaban` dan `kunci_jawaban` pada tabel `soal` disimpan sebagai data terstruktur (mis. JSON) yang **dirancang bebas oleh tim developer seefisien dan semaksimal mungkin** sesuai kebutuhan tiap `tipe_soal` (pilihan ganda, susun kalimat, pencocokan arti, quiz suara) — PRD tidak mengunci skema JSON tertentu di level ini, hanya mensyaratkan setiap tipe soal dapat divalidasi otomatis (lihat FR-3 s.d. FR-5).
>
> **Keputusan desain (komposisi test — diatur berdasarkan `tipe_soal`, bukan "jenis test" terpisah):** tim memutuskan **tidak mengunci 1 test hanya boleh berisi 1 tipe soal**. Setiap butir soal tetap membawa `tipe_soal`-nya sendiri (pilihan ganda/susun kalimat/pencocokan arti/puzzle pakaian adat/menulis aksara/kuis suara), lalu guru atau superadmin **bebas memilih sendiri saat menyusun satu `test`**: boleh mencampur beberapa tipe soal dalam satu test (pengalaman lebih variatif, mirip Duolingo), atau memilih hanya soal dengan satu tipe yang sama (mis. untuk kebutuhan remedial/drilling satu skill tertentu). Ini diwujudkan lewat entitas `test` + pivot `test_soal` di atas — sistem tidak memberi batasan di level skema, keputusan campur/tidaknya murni ada di tangan guru/superadmin saat memilih soal dari bank soal ke dalam satu test.
>
> **Gap yang perlu diperhatikan (basis pengetahuan RAG):** laporan resmi menyebut ERD terdiri atas beberapa kelompok data, termasuk *"basis pengetahuan RAG"* — namun baik pada narasi 2.5.2.x maupun gambar ERD-nya, kelompok data ini **belum benar-benar dimodelkan sebagai entitas** (tidak ada tabel semacam `korpus`/`dokumen_referensi` dengan atributnya). PRD ini mengikuti kondisi ERD sumber apa adanya, tapi menandai bahwa desain data untuk korpus RAG (FR-9) masih perlu dilengkapi tim sebelum implementasi — lihat juga Open Questions Bagian 10.2 soal sumber korpus.

---

## 6B. Alur Sistem (Ringkasan Flowchart)

**Alur Siswa:** Mulai → pilih peran "Siswa" → cek kepemilikan akun (Daftar jika belum, Masuk jika sudah) → beranda (EXP & Streak) → pilih salah satu: (a) lanjutkan mengerjakan quiz — pilih materi → cek "materi tercapai" → jika ya, mulai sesi quiz (pilihan ganda/susun kata/quiz suara TTS-STT) → sistem evaluasi jawaban → EXP bertambah, streak tersimpan, level baru terbuka → kembali ke beranda; (b) buka chatbot RAG; (c) lihat leaderboard EXP tertinggi.

**Alur Guru:** Mulai → pilih peran "Guru" → login (tanpa opsi daftar) → pilih tujuan: (a) Manajemen Soal — tampilkan card untuk setiap Level Materi (grid responsive, setiap card menunjukkan jumlah soal) → guru klik salah satu card untuk memilih level → tampil daftar soal untuk level tersebut, dengan opsi tambah soal baru (inline form) atau CRUD/edit/hapus soal miliknya → tombol "Kembali ke Pilihan Level" untuk kembali ke card selection; (b) Manajemen Siswa — pilih kelas/cari NIS → lihat progres & perkembangan siswa.

**Alur Superadmin:** Mulai → pilih peran "Superadmin" → login → tampil dashboard (jumlah guru & siswa terdaftar) → pilih area kelola:
- **(a) Manajemen Akun Guru:** Buka halaman daftar guru (dilengkapi pencarian nama/NIP real-time dan modal zoom foto profil). Superadmin dapat:
  - Mendaftarkan guru baru (`create`) via halaman formulir terpisah dengan dukungan upload berkas foto profil (`resources/image/guru/`).
  - Melihat rincian detail profil & kredensial guru (`detail/show`) pada halaman dedicated.
  - Menyunting data profil, status kepegawaian, kontak, kredensial password, dan foto guru (`edit/update`).
  - Menghapus akun guru (`delete`) dengan konfirmasi keamanan dialog.
- **(b) Manajemen Akun Siswa:** Buka halaman daftar siswa (dilengkapi pencarian nama/NIS, filter kelas, dan pagination 10 item per halaman). Superadmin dapat:
  - Mendaftarkan siswa baru (`create`) via halaman formulir terpisah.
  - Melihat rincian profil akademik, informasi kontak, dan rekap statistik capaian belajar (total EXP, streak saat ini, streak tertinggi) pada halaman detail dedicated (`detail/show`).
  - Menyunting data profil, kelas, kontak, dan sandi siswa (`edit/update`).
  - Menghapus akun siswa (`delete`) dengan konfirmasi keamanan dialog.
- **(c) Manajemen Level Quiz:** Buat materi quiz baru atau CRUD level materi.
- **(d) Manajemen Soal:** Buat soal baru untuk materi manapun atau CRUD seluruh soal di sistem.

Seluruh aksi manipulasi data akun superadmin (tambah/edit/hapus) HARUS memberikan umpan balik visual langsung berupa notifikasi toast pop-up animasi yang meluncur di sudut kanan bawah antarmuka.

Ketiga alur bermuara pada titik akhir (Selesai) yang sama setelah aksi masing-masing tersimpan ke database.

---

## 6C. Arsitektur Speech-to-Speech (STS)

**Kenapa perlu penjelasan terpisah:** FR-8 (STS) sempat tidak dijabarkan detail di v1.1–v1.2. Ada dua opsi arsitektur yang layak dipertimbangkan tim, dengan trade-off berbeda:

### Opsi A — Pipeline Bertahap (Cascaded): Google STT → Modul Pemrosesan → Azure TTS

Cocok untuk kebutuhan **"quiz suara"** (FR-7/FR-8) di mana sistem harus **menilai benar/salah** ucapan siswa dan memberi feedback yang sesuai kunci jawaban di tabel `soal` — logika ini butuh kontrol penuh atas apa yang "diproses", bukan sekadar diterjemahkan.

1. **Input suara siswa** ditangkap dari mikrofon (mobile Flutter / web).
2. **STT — Google Cloud Speech-to-Text** (`jv-ID`, model Chirp/Chirp_2) mengubah ucapan siswa menjadi teks.
3. **Modul Pemrosesan Respons** (backend Laravel) mencocokkan teks hasil STT dengan jawaban referensi di `soal`, lalu memilih respons balasan yang sudah disiapkan (benar/salah/koreksi) — guardrailed seperti Chatbot RAG (FR-9), agar tidak "mengarang" di luar materi.
4. **TTS — Azure AI Speech** (`jv-ID-SitiNeural`/`jv-ID-DimasNeural`) mengubah teks balasan menjadi audio.

### Opsi B — Model Native: Gemini Live Translation (`gemini-3.5-live-translate-preview`)

**Koreksi dari versi sebelumnya:** Google memang menyediakan model speech-to-speech satu-panggilan (bukan pipeline gabungan) yang **resmi mendukung Bahasa Jawa** (`jv`) — ini adalah mode **Live Translation** dari Gemini Live API, terpisah dari mode "Live Agent"-nya (mode percakapan/asisten Gemini Live API hanya mendukung 24 bahasa dan **tidak** termasuk Jawa).

- **Cara kerja:** streaming audio masuk dalam satu bahasa → keluar audio hasil terjemahan dalam bahasa target secara real-time, dengan latensi rendah karena satu model/satu koneksi (bukan STT lalu TTS terpisah).
- **Cocok untuk:** fitur latihan terjemahan lisan — misalnya siswa mengucapkan kalimat Bahasa Indonesia dan langsung mendengar padanan lisannya dalam Bahasa Jawa (atau sebaliknya), sebagai model pelafalan yang bisa ditirukan siswa.
- **Keterbatasan penting:** mode ini **murni penerjemah** — tidak mendukung tools/function calling/instruksi kustom, sehingga **tidak bisa** dipakai untuk menilai benar/salah ucapan siswa terhadap kunci jawaban seperti kebutuhan "quiz suara" di Opsi A. Statusnya juga masih **preview**, jadi stabilitas/ketersediaan jangka panjang untuk demo perlu dicek mendekati waktu implementasi.

### Rekomendasi

- Untuk **FR-7/FR-8 "quiz suara"** (menilai jawaban siswa) → tetap pakai **Opsi A** (Google STT + Azure TTS), karena butuh logika penilaian kustom.
- Untuk kemungkinan **fitur tambahan** "latihan terjemahan lisan Indonesia↔Jawa" (jika tim ingin menambah nilai "wow" sesuai catatan dosen penguji) → **Opsi B** (Gemini Live Translation) layak dicoba sebagai pelengkap, karena satu model saja dan latensinya lebih rendah dibanding pipeline dua vendor.
- Kedua opsi tetap bergantung API pihak ketiga dan perlu diuji akurasi/kealamian suaranya untuk Bahasa Jawa sebelum dikunci sebagai keputusan akhir (lihat Bagian 10.2 dan Risiko).

**Implikasi desain (Opsi A):**
- Melibatkan **dua panggilan API pihak ketiga berurutan** (Google STT lalu Azure TTS) ditambah satu langkah pemrosesan internal, sehingga total latensi kemungkinan lebih tinggi dari target NFR "< 3 detik" pada FR-6/FR-7 secara individual — target performa untuk STS sebaiknya dievaluasi terpisah (lihat catatan Risiko).
- Modul Pemrosesan Respons pada MVP **tidak disarankan** memakai LLM generatif bebas untuk merangkai balasan dalam Bahasa Jawa, karena risiko hasil tidak natural/tidak sesuai unggah-ungguh untuk bahasa dengan sumber daya NLP terbatas seperti Jawa; cukup gunakan set respons terstruktur yang disusun guru/superadmin per soal.

---

## 6D. Arsitektur Latihan Menulis Aksara Jawa (Tracing)

**Konsep (FR-22):** mirip aplikasi latihan kaligrafi/aksara (mis. latihan menulis Hanzi/Kanji) — siswa melihat bentuk aksara sebagai **garis bayangan tipis (template)** di kanvas, lalu menelusurinya dengan jari/mouse. Setelah goresan selesai, sistem **tidak menampilkan garis kasar siswa apa adanya**, melainkan menganimasikannya ("snap") menjadi bentuk baku yang rapi — memberi kesan pencapaian tanpa membuat siswa minder karena tulisannya berantakan.

**Alur teknis:**

1. **Tampilkan template** — bentuk aksara referensi digambar sebagai garis bayangan (opacity rendah, mis. 20-30%) di kanvas.
2. **Tangkap goresan siswa** — setiap gerakan jari/mouse (drag) direkam sebagai rangkaian titik `(x, y)` per goresan (stroke); aksara dengan banyak goresan (mis. gabungan sandhangan) direkam sebagai beberapa stroke terpisah.
3. **Bandingkan dengan referensi** — titik-titik goresan siswa dan template dinormalisasi (skala & posisi disamakan, lalu di-*resample* jadi jumlah titik yang sama), kemudian dihitung skor kemiripan bentuk. **Rekomendasi algoritma:** **`$1 Unistroke Recognizer`** (untuk aksara satu goresan) atau ekstensinya **`$N Multistroke Recognizer`** (untuk aksara bersandhangan/lebih dari satu goresan) — algoritma geometris ringan yang sudah lama dipakai untuk pengenalan gestur tulisan tangan sederhana, **tidak butuh model ML/dataset training** sehingga cocok untuk skala tugas kuliah dan bisa jalan langsung di sisi klien (Flutter) tanpa panggilan API tambahan.
4. **Animasi "snap ke bentuk baku"** — begitu satu goresan selesai (jari diangkat), garis kasar yang baru digambar di-*morph*/dianimasikan (mis. lerp antar titik path, durasi singkat ~300-500ms) menjadi path template yang rapi. Ini **murni presentasi visual**, dijalankan terlepas dari skor kemiripan — bukan berarti tulisan siswa "dibenarkan otomatis" di balik layar.
5. **Skor & EXP** — nilai yang disimpan ke `bobot_exp`/evaluasi tetap berdasarkan **skor kemiripan asli sebelum di-snap** (langkah 3), bukan hasil animasi, supaya penilaian tetap jujur mencerminkan kemampuan siswa.

**Implikasi data:** untuk `soal` dengan `tipe_soal = "menulis_aksara"`, data path referensi aksara (rangkaian titik/kurva per goresan, dinormalisasi 0-1) disimpan di kolom `opsi_jawaban` sebagai JSON — konsisten dengan keputusan Bagian 6A bahwa struktur JSON `opsi_jawaban` diserahkan bebas ke tim dev per `tipe_soal`.

---

## 7. Non-Functional Requirements

- **Performa:** respons latihan teks (pilihan ganda/susun kalimat/pencocokan) < 1 detik; hasil STT/TTS < 3 detik pada koneksi internet standar.
- **Keamanan:** data akun (siswa, guru, superadmin) terenkripsi; akses dibatasi role-based access control (RBAC) dengan tiga peran berbeda — Siswa, Guru, Superadmin — masing-masing dengan cakupan hak akses sesuai Bagian 6.
- **Guardrail AI:** chatbot RAG wajib membatasi jawaban hanya pada korpus yang di-embed, dengan mekanisme penolakan eksplisit untuk pertanyaan di luar cakupan.
- **Skalabilitas:** arsitektur backend (Laravel REST API + PostgreSQL) dirancang modular agar dapat menambah materi/level/soal tanpa mengubah struktur inti, sejalan dengan entitas `level_materi` dan `soal` yang independen dari kode aplikasi; satu backend yang sama melayani baik website maupun aplikasi mobile Flutter.
- **Kompatibilitas:** aplikasi mobile (dibangun dengan Flutter) berjalan di Android versi umum yang dipakai siswa SMP/SMA (minimal Android 9 ke atas, disesuaikan hasil survei perangkat target).
- **Kemudahan Penggunaan (Usability):** antarmuka HARUS mudah dipahami dan dioperasikan siswa SMP/SMA tanpa pelatihan khusus, dan portabel lintas perangkat (mobile Flutter & website) dengan pengalaman yang konsisten.
- **Aksesibilitas:** dukungan TTS juga bermanfaat bagi siswa dengan kesulitan membaca.

---

## 8. Scope

### 8.1 Dalam Cakupan (MVP)

- Aplikasi mobile Android + website terintegrasi.
- Tiga peran pengguna dengan alur dan hak akses terpisah: Siswa, Guru, Superadmin.
- Materi Bahasa Jawa (5 level: Dasar, Unggah-ungguh, Aksara Jawa, Peribahasa, Cerita Rakyat), dipadukan dengan artefak budaya (busana adat, rumah adat, kesenian tradisional).
- Fitur TTS, STT, STS, chatbot RAG guardrailed.
- 4 tipe latihan: pilihan ganda, susun kalimat, pencocokan arti, puzzle pakaian adat.
- Latihan menulis aksara Jawa berbasis tracing kanvas (lihat Bagian 6D).
- Gamifikasi dasar: XP, streak, leaderboard.
- Dashboard progres siswa untuk guru; dashboard ringkasan akun untuk superadmin.
- Panel manajemen soal (guru, untuk materi ajarnya) dan panel manajemen akun guru/siswa, level materi, serta seluruh bank soal (superadmin).

### 8.2 Di Luar Cakupan (Fase Ini)

- Bahasa daerah selain Jawa (dirancang agar bisa dikembangkan nanti, tidak dibangun sekarang).
- Aplikasi iOS (fokus Android sesuai kebutuhan tugas).
- Fitur sosial lanjutan (chat antar siswa, forum diskusi).
- Pembayaran/monetisasi.
- Registrasi mandiri untuk akun guru (akun guru hanya dibuat oleh superadmin).

---

## 9. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Akurasi STT rendah untuk logat Bahasa Jawa | Skor pelafalan tidak adil bagi siswa | Uji API STT dengan sampel penutur asli sebelum implementasi penuh; sediakan toleransi skor |
| Chatbot RAG berhalusinasi di luar korpus | Informasi tata bahasa yang salah ke siswa | Guardrail ketat + prompt instruksi eksplisit menolak pertanyaan di luar cakupan; uji dengan pertanyaan jebakan |
| Keterbatasan waktu tim (1 semester) | Fitur tidak selesai tepat waktu | Prioritas MoSCoW; fitur 'Could/Won't' dikorbankan lebih dulu jika waktu mepet |
| Ketergantungan pada API pihak ketiga (TTS/STT/LLM) | Biaya atau downtime API mengganggu demo | Gunakan tier gratis/trial untuk pengembangan, siapkan fallback teks jika API down saat demo |
| Tumpang tindih hak akses soal antara Guru dan Superadmin | Konflik data (soal terhapus/berubah tanpa sepengetahuan pembuat asli) | **Keputusan:** guru hanya dapat mengedit/menghapus soal buatannya sendiri; superadmin memiliki override penuh atas seluruh soal (lintas guru), sebaiknya disertai log perubahan (audit trail) |
| Relasi banyak-ke-banyak "Memantau" (guru–siswa) tidak dibatasi struktur kelas | Guru dapat melihat data siswa di luar kelas yang diampu | **Keputusan (disesuaikan laporan resmi):** guru hanya dapat memantau siswa pada kelas/mata pelajaran yang ia ampu; satu siswa boleh dipantau lebih dari satu guru jika diampu beberapa mapel di kelas yang sama — bukan dibatasi ke satu guru tunggal per siswa |
| Pipeline STS Opsi A (STT→proses→TTS) melibatkan 2 panggilan API pihak ketiga berurutan | Latensi total bisa melebihi target performa < 3 detik, mengganggu pengalaman "percakapan" siswa | Ukur latensi gabungan secara terpisah dari target FR-6/FR-7 individual; optimalkan dengan audio pendek, proses paralel bila memungkinkan, dan tampilkan indikator "sedang memproses" di UI agar tidak terasa macet |
| Gemini Live Translation (Opsi B STS) masih berstatus preview | Model bisa berubah/dihentikan/berbayar berbeda sebelum demo, dan tidak mendukung logika penilaian kustom | Jangan jadikan satu-satunya jalur untuk fitur inti (quiz suara); posisikan sebagai fitur pelengkap opsional, dengan Opsi A tetap jadi jalur utama untuk penilaian jawaban |
| Algoritma pengenalan goresan ($1/$N Recognizer) pada latihan menulis aksara (FR-22) bisa terlalu ketat/longgar untuk variasi gaya tulisan siswa | Siswa yang tulisannya benar tapi gayanya beda bisa dinilai salah, atau sebaliknya tulisan asal-asalan lolos | Beri toleransi skor (bukan pass/fail biner), uji ambang batas kemiripan dengan sampel tulisan beberapa siswa/tim sebelum dikunci, dan tampilkan skor persentase alih-alih benar/salah mutlak |

---

## 10. Open Questions & Keputusan

### 10.1 Sudah Diputuskan Tim

| # | Pertanyaan | Keputusan |
|---|---|---|
| 3 | Uji coba melibatkan siswa sekolah nyata atau simulasi? | Hanya **simulasi internal tim**, untuk presentasi di depan dosen penguji (lihat catatan di Bagian 3.2). |
| 4 | Struktur JSON `opsi_jawaban`/`kunci_jawaban`? | Diatur bebas oleh tim developer, **seefisien dan semaksimal mungkin** per `tipe_soal`; tidak dikunci skemanya di level PRD (lihat catatan di Bagian 6A). |
| 5 | Perlu tabel pivot progres siswa? | **Ya** — sistem perlu menyimpan status progres dan level siswa; diimplementasikan sebagai tabel pivot `progres_siswa` (lihat Bagian 6A). |
| 6 | Relasi "Memantau" guru–siswa dibatasi kelas? | **Ya, dibatasi kelas/mata pelajaran yang diampu** (bukan hanya "kelas yang dibuat guru") — mengikuti kalimat laporan resmi: satu siswa boleh dipantau lebih dari satu guru sesuai kelas atau mapel terkait (lihat Bagian 6A & Risiko). |
| 7 | Guru boleh edit soal guru lain? | **Tidak** — guru hanya mengelola soal miliknya sendiri; superadmin adalah satu-satunya otoritas lintas guru (lihat FR-12 & Risiko). |

### 10.2 Masih Terbuka — Rekomendasi Awal

**1. Sumber korpus/kamus Bahasa Jawa untuk materi & basis RAG** — tim belum menentukan final, tapi ditemukan dataset akademis yang jauh lebih spesifik dari rekomendasi awal:
- **Unggah-ungguh/tata krama (utama):** dataset **`JavaneseHonorifics/Unggah-Ungguh`** di Hugging Face (dari paper ACL 2025, "Do Language Models Understand Honorific Systems in Javanese?") — 4.024 kalimat berlabel 4 tingkat honorifik (Ngoko, Ngoko Alus, Krama, Krama Alus), disusun dari **Kamus Unggah-Ungguh Basa Jawa** (Harjawiyana dkk., 2001) via OCR, bukan hasil scraping bebas. Lisensi **CC-BY-NC 4.0** — untuk riset/non-komersial, cocok dipakai untuk tugas kuliah.
- **Latihan terjemahan tingkat tutur (pelengkap):** korpus paralel **`NgokoKrama`** (1.000 pasang kalimat Indonesia↔Jawa Krama) dari penelitian benchmark terjemahan mesin Indonesia-Jawa.
- **Materi budaya (cerita rakyat, busana adat, rumah adat, kesenian):** *Wikipedia Basa Jawa* (jv.wikipedia.org, CC BY-SA) — dataset di atas fokus ke bahasa/honorifik, tidak mencakup budaya, jadi tetap perlu sumber terpisah untuk ini.
- **Contoh gaya percakapan chatbot (opsional, perlu review):** dataset **`afrizalha/Gatra-1-Javanese`** — QA edukasi berbahasa Krama, tapi **sintetis** (dihasilkan GPT-4, dikurasi manual untuk kesalahan Krama) — bukan sumber kebenaran utama, hanya referensi gaya bahasa.
- **Catatan lisensi:** karena `Unggah-Ungguh` dan kemungkinan `Gatra-1-Javanese` berlisensi non-komersial/riset, pastikan penggunaan tetap dalam konteks tugas kuliah (bukan produk yang dikomersialkan) — atau cek ulang lisensi versi terbaru sebelum implementasi final.
- Kamus klasik seperti *Baoesastra Djawa* (Poerwadarminta) tetap **tidak direkomendasikan** karena kemungkinan masih dilindungi hak cipta di Indonesia (masa berlaku hingga 70 tahun setelah pengarang wafat).

**2. API TTS/STT untuk Bahasa Jawa** — tim belum menentukan. Rekomendasi awal berdasarkan pengecekan dukungan bahasa resmi tiap vendor:
- **TTS (Text-to-Speech):** **Azure AI Speech** — satu-satunya penyedia besar yang sudah punya suara neural resmi untuk Bahasa Jawa: `jv-ID-SitiNeural` (perempuan) dan `jv-ID-DimasNeural` (laki-laki). Tersedia tier gratis (F0) yang cukup untuk kebutuhan demo tugas kuliah.
- **STT (Speech-to-Text):** **Google Cloud Speech-to-Text (V2, model Chirp/Chirp_2/Chirp_3)** — secara resmi mendukung kode bahasa `jv-ID` (Javanese, Indonesia). Catatan: Azure Speech **tidak** mendukung Javanese untuk STT, jadi tidak bisa dipakai satu vendor untuk TTS+STT sekaligus.
- **Implikasi:** kombinasi ini berarti dua akun/API key berbeda (Azure untuk TTS, Google Cloud untuk STT). Ini konsisten dengan FR-6/FR-7 dan sejalan dengan risiko "Ketergantungan pada API pihak ketiga" di Bagian 9 — tetap disarankan uji akurasi STT dengan sampel penutur asli Jawa sebelum implementasi penuh, karena "resmi didukung" tidak selalu berarti akurasinya tinggi untuk bahasa dengan sumber daya lebih sedikit seperti Jawa.
- **Integrasi dengan Laravel:** kedua vendor menyediakan REST API standar yang dapat dipanggil langsung dari backend Laravel tanpa SDK resmi PHP wajib (cukup HTTP client seperti Guzzle).
