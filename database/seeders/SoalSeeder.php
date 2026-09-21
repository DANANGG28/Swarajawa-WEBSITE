<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Services\Aksara\AksaraJawaConverter;
use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    public function run(): void
    {
        $levels = LevelMateri::query()->orderBy('urutan')->get()->keyBy('urutan');
        $pembahasanMap = Pembahasan::query()->get()->keyBy(fn (Pembahasan $p) => $p->level_materi_id.'-'.$p->urutan);
        $buSri = Guru::query()->where('email', 'bu.sri@sinaujowo.test')->first();
        $pakBagus = Guru::query()->where('email', 'pak.bagus@sinaujowo.test')->first();
        $superadmin = Superadmin::query()->first();

        $items = [
            // ---------------- LEVEL 1: DASAR ----------------
            // Pembahasan 1: Salam & Sapaan
            ['level' => 1, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Salam nalika ketemu kanca ing wayah esuk yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Sugeng enjing'],
                    ['label' => 'B', 'teks' => 'Sugeng dalu'],
                    ['label' => 'C', 'teks' => 'Sugeng siang'],
                    ['label' => 'D', 'teks' => 'Sugeng sonten'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 1, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_KUIS_SUARA, 'bobot' => 20,
                'pertanyaan' => 'Ngucapna "Sugeng enjing" kanthi cetha.',
                'opsi' => ['instruksi' => 'Pencet tombol mic banjur ngucapna salam kanthi cetha.'],
                'kunci' => ['teks' => 'sugeng enjing']],

            ['level' => 1, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Salam nalika wayah dalu yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Sugeng dalu'],
                    ['label' => 'B', 'teks' => 'Sugeng enjing'],
                    ['label' => 'C', 'teks' => 'Sugeng siang'],
                    ['label' => 'D', 'teks' => 'Sugeng sonten'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 1, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Salam nalika wayah siang yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Sugeng siang'],
                    ['label' => 'B', 'teks' => 'Sugeng dalu'],
                    ['label' => 'C', 'teks' => 'Sugeng enjing'],
                    ['label' => 'D', 'teks' => 'Sugeng sonten'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 1, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_KUIS_SUARA, 'bobot' => 20,
                'pertanyaan' => 'Ngucapna "Sugeng dalu" kanthi cetha.',
                'opsi' => ['instruksi' => 'Ngucapna salam nalika wayah dalu kanthi cetha.'],
                'kunci' => ['teks' => 'sugeng dalu']],

            // Pembahasan 2: Tembung lan Ukara Dasar
            ['level' => 1, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung 'aku' kalebu basa ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Ngoko'],
                    ['label' => 'B', 'teks' => 'Krama'],
                    ['label' => 'C', 'teks' => 'Krama inggil'],
                    ['label' => 'D', 'teks' => 'Krama alus'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 1, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_SUSUN_KALIMAT, 'bobot' => 15,
                'pertanyaan' => 'Susun dadi ukara kang bener.',
                'opsi' => ['sega', 'Aku', 'goreng', 'mangan'],
                'kunci' => ['susunan' => ['Aku', 'mangan', 'sega', 'goreng']]],

            ['level' => 1, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna tembung karo tegese.',
                'opsi' => [
                    ['kiri' => 'mangan', 'kanan' => 'makan'],
                    ['kiri' => 'turu', 'kanan' => 'tidur'],
                    ['kiri' => 'lunga', 'kanan' => 'pergi'],
                    ['kiri' => 'mulih', 'kanan' => 'pulang'],
                ],
                'kunci' => ['pasangan' => ['mangan' => 'makan', 'turu' => 'tidur', 'lunga' => 'pergi', 'mulih' => 'pulang']]],

            ['level' => 1, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_SUSUN_KALIMAT, 'bobot' => 15,
                'pertanyaan' => 'Susun tembung ing ngisor iki dadi ukara kang bener.',
                'opsi' => ['sekolah', 'arep', 'Aku', 'sesuk'],
                'kunci' => ['susunan' => ['Aku', 'arep', 'sekolah', 'sesuk']]],

            ['level' => 1, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna tembung kekerabatan karo tegese.',
                'opsi' => [
                    ['kiri' => 'bapak', 'kanan' => 'ayah'],
                    ['kiri' => 'ibu', 'kanan' => 'ibu'],
                    ['kiri' => 'adhi', 'kanan' => 'adik'],
                    ['kiri' => 'kakang', 'kanan' => 'kakak'],
                ],
                'kunci' => ['pasangan' => ['bapak' => 'ayah', 'ibu' => 'ibu', 'adhi' => 'adik', 'kakang' => 'kakak']]],

            // ---------------- LEVEL 2: UNGGAH-UNGGUH ----------------
            // Pembahasan 1: Krama Inggil
            ['level' => 2, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung krama inggil kanggo 'mangan' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Dhahar'],
                    ['label' => 'B', 'teks' => 'Nedha'],
                    ['label' => 'C', 'teks' => 'Mangan'],
                    ['label' => 'D', 'teks' => 'Madhang'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 2, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Yen matur marang Bapak, tembung 'turu' dadi ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Sare'],
                    ['label' => 'B', 'teks' => 'Tilem'],
                    ['label' => 'C', 'teks' => 'Turu'],
                    ['label' => 'D', 'teks' => 'Nglilir'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 2, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna krama inggil karo tegese.',
                'opsi' => [
                    ['kiri' => 'dhahar', 'kanan' => 'makan'],
                    ['kiri' => 'sare', 'kanan' => 'tidur'],
                    ['kiri' => 'tindak', 'kanan' => 'pergi'],
                    ['kiri' => 'siram', 'kanan' => 'mandi'],
                ],
                'kunci' => ['pasangan' => ['dhahar' => 'makan', 'sare' => 'tidur', 'tindak' => 'pergi', 'siram' => 'mandi']]],

            ['level' => 2, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Krama inggil saka tembung 'sirah' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Mustaka'],
                    ['label' => 'B', 'teks' => 'Sirah'],
                    ['label' => 'C', 'teks' => 'Endhas'],
                    ['label' => 'D', 'teks' => 'Mastaka'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 2, 'pembahasan' => 1, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Krama inggil saka tembung 'tangan' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Asta'],
                    ['label' => 'B', 'teks' => 'Tangan'],
                    ['label' => 'C', 'teks' => 'Sikut'],
                    ['label' => 'D', 'teks' => 'Dhengkul'],
                ],
                'kunci' => ['jawaban' => 'A']],

            // Pembahasan 2: Krama Alus & Penerapan
            ['level' => 2, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_SUSUN_KALIMAT, 'bobot' => 15,
                'pertanyaan' => 'Susun dadi ukara krama kang bener.',
                'opsi' => ['rumiyin', 'Kula', 'nedha', 'badhe'],
                'kunci' => ['susunan' => ['Kula', 'badhe', 'nedha', 'rumiyin']]],

            ['level' => 2, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Basa kang digunakake marang wong sing luwih tuwa yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Krama'],
                    ['label' => 'B', 'teks' => 'Ngoko'],
                    ['label' => 'C', 'teks' => 'Ngoko alus'],
                    ['label' => 'D', 'teks' => 'Basa pasar'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 2, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Krama alus saka 'Aku mangan sega' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Kula nedha sekul'],
                    ['label' => 'B', 'teks' => 'Aku dhahar sega'],
                    ['label' => 'C', 'teks' => 'Kula dhahar sekul'],
                    ['label' => 'D', 'teks' => 'Aku mangan sekul'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 2, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna tembung krama karo tegese.',
                'opsi' => [
                    ['kiri' => 'nedha', 'kanan' => 'makan'],
                    ['kiri' => 'tilem', 'kanan' => 'tidur'],
                    ['kiri' => 'kesah', 'kanan' => 'pergi'],
                    ['kiri' => 'siram', 'kanan' => 'mandi'],
                ],
                'kunci' => ['pasangan' => ['nedha' => 'makan', 'tilem' => 'tidur', 'kesah' => 'pergi', 'siram' => 'mandi']]],

            ['level' => 2, 'pembahasan' => 2, 'oleh' => 'guru', 'tipe' => Soal::TIPE_KUIS_SUARA, 'bobot' => 20,
                'pertanyaan' => 'Matura "Kula badhe tindak sekolah".',
                'opsi' => ['instruksi' => 'Ngucapna ukara krama kanthi lafal kang cetha.'],
                'kunci' => ['teks' => 'kula badhe tindak sekolah']],

            // ---------------- LEVEL 3: AKSARA JAWA ----------------
            // Pembahasan 1: Aksara Legena & Sandhangan Swara
            ['level' => 3, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Aksara legena (nglegena) cacahé ana ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => '20'],
                    ['label' => 'B', 'teks' => '10'],
                    ['label' => 'C', 'teks' => '15'],
                    ['label' => 'D', 'teks' => '25'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 3, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Sandhangan swara kanggo swara 'i' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Wulu'],
                    ['label' => 'B', 'teks' => 'Suku'],
                    ['label' => 'C', 'teks' => 'Taling'],
                    ['label' => 'D', 'teks' => 'Pepet'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 3, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Aksara murda digunakake kanggo ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'pakurmatan marang jeneng lan pangkat'],
                    ['label' => 'B', 'teks' => 'nulis angka'],
                    ['label' => 'C', 'teks' => 'tandha wacan'],
                    ['label' => 'D', 'teks' => 'seselan'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 3, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Sandhangan swara kanggo swara 'u' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Suku'],
                    ['label' => 'B', 'teks' => 'Wulu'],
                    ['label' => 'C', 'teks' => 'Taling'],
                    ['label' => 'D', 'teks' => 'Pepet'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 3, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Sandhangan swara kanggo swara 'e' (pepet) yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Pepet'],
                    ['label' => 'B', 'teks' => 'Wulu'],
                    ['label' => 'C', 'teks' => 'Suku'],
                    ['label' => 'D', 'teks' => 'Cecak'],
                ],
                'kunci' => ['jawaban' => 'A']],

            // Pembahasan 2: Nulis Aksara (Tracing)
            ['level' => 3, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_MENULIS_AKSARA, 'bobot' => 25,
                'pertanyaan' => 'Tulisen aksara "ha" kanthi nggaris ing kanvas.',
                'latin' => 'ha',
                'opsi' => ['aksara' => $this->aksaraFor('ha'), 'petunjuk' => 'Tlusuri bayangan aksara saka ndhuwur tumuju ngisor.'],
                'kunci' => ['aksara' => $this->aksaraFor('ha'), 'latin' => 'ha', 'paths' => []]],

            ['level' => 3, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_MENULIS_AKSARA, 'bobot' => 25,
                'pertanyaan' => 'Tulisen aksara "na" kanthi nggaris ing kanvas.',
                'latin' => 'na',
                'opsi' => ['aksara' => $this->aksaraFor('na'), 'petunjuk' => 'Garis vertikal banjur mlengkung ing ngisor.'],
                'kunci' => ['aksara' => $this->aksaraFor('na'), 'latin' => 'na', 'paths' => []]],

            ['level' => 3, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_MENULIS_AKSARA, 'bobot' => 25,
                'pertanyaan' => 'Tulisen aksara "ka" kanthi nggaris ing kanvas.',
                'latin' => 'ka',
                'opsi' => ['aksara' => $this->aksaraFor('ka'), 'petunjuk' => 'Garis vertikal lan garis miring ing sisih tengen.'],
                'kunci' => ['aksara' => $this->aksaraFor('ka'), 'latin' => 'ka', 'paths' => []]],

            ['level' => 3, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_MENULIS_AKSARA, 'bobot' => 25,
                'pertanyaan' => 'Tulisen aksara "ca" kanthi nggaris ing kanvas.',
                'latin' => 'ca',
                'opsi' => ['aksara' => $this->aksaraFor('ca'), 'petunjuk' => 'Bentuk mlengkung kaya gelung.'],
                'kunci' => ['aksara' => $this->aksaraFor('ca'), 'latin' => 'ca', 'paths' => []]],

            ['level' => 3, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_MENULIS_AKSARA, 'bobot' => 25,
                'pertanyaan' => 'Tulisen aksara "ra" kanthi nggaris ing kanvas.',
                'latin' => 'ra',
                'opsi' => ['aksara' => $this->aksaraFor('ra'), 'petunjuk' => 'Garis miring lan gelung ing sisih tengen.'],
                'kunci' => ['aksara' => $this->aksaraFor('ra'), 'latin' => 'ra', 'paths' => []]],

            // ---------------- LEVEL 4: PERIBAHASA ----------------
            // Pembahasan 1: Paribasan & Tegese
            ['level' => 4, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tegese paribasan 'Becik ketitik, ala ketara' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'tumindak becik lan ala suwe-suwe bakal ketara'],
                    ['label' => 'B', 'teks' => 'wong kang sombong bakal cilaka'],
                    ['label' => 'C', 'teks' => 'wong kang sregep bakal sukses'],
                    ['label' => 'D', 'teks' => 'kabecikan kudu diwarisake'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 4, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_SUSUN_KALIMAT, 'bobot' => 15,
                'pertanyaan' => 'Susun dadi paribasan kang bener.',
                'opsi' => ['ketara', 'Becik', 'ala', 'ketitik'],
                'kunci' => ['susunan' => ['Becik', 'ketitik', 'ala', 'ketara']]],

            ['level' => 4, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna paribasan karo tegese.',
                'opsi' => [
                    ['kiri' => 'adigang adigung adiguna', 'kanan' => 'ngendelake kekuwatan lan kapinteran'],
                    ['kiri' => 'becik ketitik ala ketara', 'kanan' => 'kabecikan lan kaluputan bakal katon'],
                    ['kiri' => 'lenga goreng', 'kanan' => 'wong kang seneng gawe ala'],
                    ['kiri' => 'kakehan gludhug kurang udan', 'kanan' => 'akeh omong nanging ora ana nyatane'],
                ],
                'kunci' => ['pasangan' => [
                    'adigang adigung adiguna' => 'ngendelake kekuwatan lan kapinteran',
                    'becik ketitik ala ketara' => 'kabecikan lan kaluputan bakal katon',
                    'lenga goreng' => 'wong kang seneng gawe ala',
                    'kakehan gludhug kurang udan' => 'akeh omong nanging ora ana nyatane',
                ]]],

            ['level' => 4, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_KUIS_SUARA, 'bobot' => 20,
                'pertanyaan' => 'Unenana paribasan "Becik ketitik ala ketara".',
                'opsi' => ['instruksi' => 'Unenana kanthi lafal kang cetha lan bener.'],
                'kunci' => ['teks' => 'becik ketitik ala ketara']],

            ['level' => 4, 'pembahasan' => 1, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tegese paribasan 'adigang adigung adiguna' yaiku ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'ngendelake kekuwatan lan kapinteran'],
                    ['label' => 'B', 'teks' => 'wong kang seneng gawe ala'],
                    ['label' => 'C', 'teks' => 'kabecikan bakal katon'],
                    ['label' => 'D', 'teks' => 'akeh omong tanpa nyatane'],
                ],
                'kunci' => ['jawaban' => 'A']],

            // Pembahasan 2: Tembung Entar
            ['level' => 4, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung entar 'lenga goreng' tegese ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'wong kang seneng gawe ala'],
                    ['label' => 'B', 'teks' => 'wong kang pinter'],
                    ['label' => 'C', 'teks' => 'wong kang sugih'],
                    ['label' => 'D', 'teks' => 'wong kang sabar'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 4, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung entar 'gedhe endhase' tegese ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'sombong'],
                    ['label' => 'B', 'teks' => 'sabar'],
                    ['label' => 'C', 'teks' => 'pinter'],
                    ['label' => 'D', 'teks' => 'sugih'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 4, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung entar 'kembang lambe' tegese ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'dadi bahan rerasan'],
                    ['label' => 'B', 'teks' => 'wong kang ayu'],
                    ['label' => 'C', 'teks' => 'wong kang seneng mangan'],
                    ['label' => 'D', 'teks' => 'wong kang sregep'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 4, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna tembung entar karo tegese.',
                'opsi' => [
                    ['kiri' => 'gedhe endhase', 'kanan' => 'sombong'],
                    ['kiri' => 'kembang lambe', 'kanan' => 'dadi bahan rerasan'],
                    ['kiri' => 'jembar segarane', 'kanan' => 'sabar lan akeh pangapura'],
                    ['kiri' => 'panjang tangan', 'kanan' => 'seneng nyolong'],
                ],
                'kunci' => ['pasangan' => [
                    'gedhe endhase' => 'sombong',
                    'kembang lambe' => 'dadi bahan rerasan',
                    'jembar segarane' => 'sabar lan akeh pangapura',
                    'panjang tangan' => 'seneng nyolong',
                ]]],

            ['level' => 4, 'pembahasan' => 2, 'oleh' => 'superadmin', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Tembung entar 'jembar segarane' tegese ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'sabar lan akeh pangapura'],
                    ['label' => 'B', 'teks' => 'seneng nyolong'],
                    ['label' => 'C', 'teks' => 'sombong'],
                    ['label' => 'D', 'teks' => 'gampang susah'],
                ],
                'kunci' => ['jawaban' => 'A']],

            // ---------------- LEVEL 5: CERITA RAKYAT & BUDAYA ----------------
            // Pembahasan 1: Busana Adat & Omah Jawa
            ['level' => 5, 'pembahasan' => 1, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Blangkon yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'tutup sirah adat Jawa'],
                    ['label' => 'B', 'teks' => 'rasukan ndhuwur'],
                    ['label' => 'C', 'teks' => 'kain ngisor'],
                    ['label' => 'D', 'teks' => 'alas sikil'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 5, 'pembahasan' => 1, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PUZZLE_PAKAIAN_ADAT, 'bobot' => 25,
                'pertanyaan' => 'Urutna busana adat Jawa saka sirah tumuju sikil.',
                'opsi' => [
                    ['id' => 1, 'nama' => 'Blangkon'],
                    ['id' => 2, 'nama' => 'Beskap'],
                    ['id' => 3, 'nama' => 'Jarik'],
                    ['id' => 4, 'nama' => 'Selop'],
                ],
                'kunci' => ['urutan' => [1, 2, 3, 4]]],

            ['level' => 5, 'pembahasan' => 1, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna busana adat karo perangane awak.',
                'opsi' => [
                    ['kiri' => 'blangkon', 'kanan' => 'tutup sirah'],
                    ['kiri' => 'beskap', 'kanan' => 'rasukan ndhuwur'],
                    ['kiri' => 'jarik', 'kanan' => 'kain ngisor'],
                    ['kiri' => 'selop', 'kanan' => 'alas sikil'],
                ],
                'kunci' => ['pasangan' => [
                    'blangkon' => 'tutup sirah',
                    'beskap' => 'rasukan ndhuwur',
                    'jarik' => 'kain ngisor',
                    'selop' => 'alas sikil',
                ]]],

            ['level' => 5, 'pembahasan' => 1, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Omah adat Jawa kang kondhang yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Joglo'],
                    ['label' => 'B', 'teks' => 'Gadang'],
                    ['label' => 'C', 'teks' => 'Honai'],
                    ['label' => 'D', 'teks' => 'Tongkonan'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 5, 'pembahasan' => 1, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Jarik yaiku ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'kain ngisor'],
                    ['label' => 'B', 'teks' => 'tutup sirah'],
                    ['label' => 'C', 'teks' => 'rasukan ndhuwur'],
                    ['label' => 'D', 'teks' => 'alas sikil'],
                ],
                'kunci' => ['jawaban' => 'A']],

            // Pembahasan 2: Crita Rakyat
            ['level' => 5, 'pembahasan' => 2, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => 'Roro Jonggrang kagolong crita ...',
                'opsi' => [
                    ['label' => 'A', 'teks' => 'legenda'],
                    ['label' => 'B', 'teks' => 'fabel'],
                    ['label' => 'C', 'teks' => 'dongeng'],
                    ['label' => 'D', 'teks' => 'mite'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 5, 'pembahasan' => 2, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_KUIS_SUARA, 'bobot' => 20,
                'pertanyaan' => 'Unenana "Roro Jonggrang".',
                'opsi' => [
                    'instruksi' => 'Unenana jeneng paraga crita rakyat kanthi cetha.',
                    'respons_benar' => 'Pinter! Pangucapanmu wis bener.',
                    'respons_hampir_benar' => 'Hampir bener, coba dibaleni maneh kanthi cetha ya.',
                    'respons_salah' => 'Durung pas. Sing bener yaiku "Roro Jonggrang". Ayo dicoba maneh.',
                ],
                'kunci' => ['teks' => 'roro jonggrang']],

            ['level' => 5, 'pembahasan' => 2, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Crita rakyat 'Timun Mas' kagolong ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'dongeng'],
                    ['label' => 'B', 'teks' => 'legenda'],
                    ['label' => 'C', 'teks' => 'mite'],
                    ['label' => 'D', 'teks' => 'fabel'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 5, 'pembahasan' => 2, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PILIHAN_GANDA, 'bobot' => 10,
                'pertanyaan' => "Paraga 'Bandung Bondowoso' ana ing crita ...",
                'opsi' => [
                    ['label' => 'A', 'teks' => 'Roro Jonggrang'],
                    ['label' => 'B', 'teks' => 'Timun Mas'],
                    ['label' => 'C', 'teks' => 'Joko Tarub'],
                    ['label' => 'D', 'teks' => 'Sangkuriang'],
                ],
                'kunci' => ['jawaban' => 'A']],

            ['level' => 5, 'pembahasan' => 2, 'oleh' => 'guru2', 'tipe' => Soal::TIPE_PENCOCOKAN_ARTI, 'bobot' => 15,
                'pertanyaan' => 'Jodhokna paraga crita rakyat karo critane.',
                'opsi' => [
                    ['kiri' => 'Bandung Bondowoso', 'kanan' => 'Roro Jonggrang'],
                    ['kiri' => 'Sangkuriang', 'kanan' => 'Tangkuban Perahu'],
                    ['kiri' => 'Joko Tarub', 'kanan' => 'Nawang Wulan'],
                    ['kiri' => 'Ande-Ande Lumut', 'kanan' => 'Klenting Kuning'],
                ],
                'kunci' => ['pasangan' => [
                    'Bandung Bondowoso' => 'Roro Jonggrang',
                    'Sangkuriang' => 'Tangkuban Perahu',
                    'Joko Tarub' => 'Nawang Wulan',
                    'Ande-Ande Lumut' => 'Klenting Kuning',
                ]]],
        ];

        foreach ($items as $item) {
            $level = $levels->get($item['level']);

            if (! $level) {
                continue;
            }

            $creator = match ($item['oleh']) {
                'guru' => ['guru_id' => $buSri?->id, 'superadmin_id' => null],
                'guru2' => ['guru_id' => $pakBagus?->id, 'superadmin_id' => null],
                default => ['guru_id' => null, 'superadmin_id' => $superadmin?->id],
            };

            $pembahasan = $pembahasanMap->get($level->id.'-'.$item['pembahasan']);

            Soal::updateOrCreate(
                ['level_materi_id' => $level->id, 'pertanyaan' => $item['pertanyaan']],
                [
                    'pembahasan_id' => $pembahasan?->id,
                    'tipe_soal' => $item['tipe'],
                    'soal_latin' => $item['latin'] ?? null,
                    'soal_aksara' => isset($item['latin']) ? $this->aksaraFor($item['latin']) : null,
                    'opsi_jawaban' => $item['opsi'],
                    'kunci_jawaban' => $item['kunci'],
                    'bobot_exp' => $item['bobot'],
                    'guru_id' => $creator['guru_id'],
                    'superadmin_id' => $creator['superadmin_id'],
                ],
            );
        }
    }

    private function aksaraFor(string $latin): string
    {
        return (new AksaraJawaConverter)->convert($latin);
    }
}
