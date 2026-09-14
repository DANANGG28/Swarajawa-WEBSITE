<?php

namespace Database\Seeders;

use App\Models\Korpus;
use Illuminate\Database\Seeder;

class KorpusSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'judul' => 'Unggah-ungguh tembung mangan, nedha, lan dhahar',
                'kategori' => 'unggah-ungguh',
                'konten' => 'Ing basa Jawa, tembung kanggo mangan dibedakake miturut trap-trapan. Mangan kalebu ngoko lugu, kanggo awake dhewe utawa wong sadrajat. Nedha kalebu krama (krama lugu/andhap), kanggo awake dhewe nalika matur marang wong sing luwih tuwa. Dhahar kalebu krama inggil, mligi kanggo ngurmati wong liya kayata Bapak, Ibu, Simbah, lan Bapak/Ibu Guru. Tuladha: "Aku mangan sega liwet"; "Kula nembe nedha sekul"; "Bapak saweg dhahar wonten kantor".',
                'sumber' => 'Kamus Unggah-Ungguh Basa Jawa (Harjawiyana dkk., 2001)',
            ],
            [
                'judul' => 'Krama inggil tembung turu',
                'kategori' => 'unggah-ungguh',
                'konten' => 'Tembung turu yen kanggo wong sing kudu diajeni dadi sare utawa krama inggil. Contone: "Bapak lagi sare wonten kamar." Dene yen kanggo awake dhewe, nganggo tembung tilem (krama) utawa turu (ngoko). Aja nggunakake tembung krama inggil kanggo awake dhewe, amarga ora trep karo unggah-ungguh.',
                'sumber' => 'Kamus Unggah-Ungguh Basa Jawa',
            ],
            [
                'judul' => 'Tembung kriya krama inggil: tindak, siram, dhahar, sare',
                'kategori' => 'unggah-ungguh',
                'konten' => 'Krama inggil dipigunakake kanggo ngurmati wong liya. Tuladha: lunga dadi tindak, adus dadi siram, mangan dadi dhahar, turu dadi sare, teka dadi rawuh, lungguh dadi lenggah. Ukara tuladha: "Simbah badhe tindak dhateng Surabaya."',
                'sumber' => 'Paramasastra Jawa',
            ],
            [
                'judul' => 'Aksara Jawa legena lan sandhangan swara',
                'kategori' => 'aksara',
                'konten' => 'Aksara legena (nglegena) cacahé ana 20: ha na ca ra ka, da ta sa wa la, pa dha ja ya nya, ma ga ba tha nga. Sandhangan swara kanggo ngowahi swara: wulu (i), suku (u), taling (e), pepet (e), taling tarung (o). Tuladha: "ha" yen diwenehi wulu dadi "hi".',
                'sumber' => 'Buku Sinau Aksara Jawa',
            ],
            [
                'judul' => 'Aksara murda lan panganggone',
                'kategori' => 'aksara',
                'konten' => 'Aksara murda (aksara gedhe) digunakake kanggo nulis jeneng, pangkat, utawa papan kanggo pakurmatan. Tuladha: aksara murda "Na" kanggo jeneng "Nusantara". Aksara murda ora kabeh aksara duwe, mung sawetara kayata Na, Ka, Ta, Sa, Pa, Nya, Ga, Ba.',
                'sumber' => 'Buku Sinau Aksara Jawa',
            ],
            [
                'judul' => 'Paribasan becik ketitik ala ketara',
                'kategori' => 'paribasan',
                'konten' => 'Paribasan "Becik ketitik, ala ketara" tegese samubarang tumindak becik utawa ala suwe-suwe bakal ketara/kawruhan. Paribasan iki ngemutake supaya tansah tumindak becik amarga kabecikan lan kaluputan mesthi bakal katon.',
                'sumber' => 'Bausastra Jawa',
            ],
            [
                'judul' => 'Saloka adigang adigung adiguna',
                'kategori' => 'paribasan',
                'konten' => 'Saloka "Adigang, adigung, adiguna" tegese wong kang ngendelake kekuwatan, keluhuran, lan kapinteran. Lumrahe kanggo wong kang sombong lan ora ngati-ati. Tuladha ing crita: "Raden Kumbakarna adigang adigung adiguna."',
                'sumber' => 'Bausastra Jawa',
            ],
            [
                'judul' => 'Busana adat Jawa: blangkon, beskap, jarik, selop',
                'kategori' => 'budaya',
                'konten' => 'Busana adat Jawa gagrag Surakarta lan Ngayogyakarta dumadi saka blangkon (tutup sirah), beskap utawa surjan (rasukan ndhuwur), jarik (kain ngisor), lan selop (alas sikil). Jarik Sidomukti ngandhut makna kamukten lan kasugihan. Blangkon duwe makna wong Jawa kudu migunakake pikiran.',
                'sumber' => 'Wikipedia Basa Jawa',
            ],
            [
                'judul' => 'Rumah adat Joglo',
                'kategori' => 'budaya',
                'konten' => 'Omah adat Jawa kang kondhang yaiku Joglo. Payone kang dhuwur lan nduweni soko guru papat nggambarake papat arah angin. Joglo asring dipigunakake kanggo pendhapa lan nduweni filosofi keterbukaan lan pakurmatan marang tamu.',
                'sumber' => 'Wikipedia Basa Jawa',
            ],
            [
                'judul' => 'Crita rakyat Roro Jonggrang',
                'kategori' => 'budaya',
                'konten' => 'Roro Jonggrang kalebu crita rakyat (legenda) saka tlatah Prambanan. Crita iki nyritakake Bandung Bondowoso kang kasmaran marang Roro Jonggrang lan kadhawuhan nggawe sewu candhi ing sawengi. Roro Jonggrang banjur dadi arca kang kaping sewu.',
                'sumber' => 'Wikipedia Basa Jawa',
            ],
        ];

        foreach ($entries as $entry) {
            Korpus::updateOrCreate(['judul' => $entry['judul']], $entry);
        }
    }
}
