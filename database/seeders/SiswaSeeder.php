<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Services\ProgresService;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $progres = app(ProgresService::class);
        $levels = LevelMateri::query()->orderBy('urutan')->get();

        $data = [
            ['nis' => '0092817421', 'nama_lengkap' => 'Andi Prasetyo', 'jenis_kelamin' => 'L', 'kelas' => '7A', 'email' => 'andi@sinaujowo.test', 'exp' => 1450, 'streak' => 5, 'highest' => 12, 'selesai' => 2],
            ['nis' => '0092817422', 'nama_lengkap' => 'Siti Rahayu', 'jenis_kelamin' => 'P', 'kelas' => '7A', 'email' => 'siti@sinaujowo.test', 'exp' => 1820, 'streak' => 12, 'highest' => 12, 'selesai' => 3],
            ['nis' => '0092817423', 'nama_lengkap' => 'Dewi Lestari', 'jenis_kelamin' => 'P', 'kelas' => '7A', 'email' => 'dewi@sinaujowo.test', 'exp' => 1310, 'streak' => 4, 'highest' => 9, 'selesai' => 1],
            ['nis' => '0092817424', 'nama_lengkap' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'kelas' => '7B', 'email' => 'budi@sinaujowo.test', 'exp' => 980, 'streak' => 2, 'highest' => 6, 'selesai' => 1],
            ['nis' => '0092817425', 'nama_lengkap' => 'Rian Bagus Saputra', 'jenis_kelamin' => 'L', 'kelas' => '7A', 'email' => 'rian@sinaujowo.test', 'exp' => 920, 'streak' => 6, 'highest' => 6, 'selesai' => 1],
            ['nis' => '0092817426', 'nama_lengkap' => 'Anisa Putri', 'jenis_kelamin' => 'P', 'kelas' => '7A', 'email' => 'anisa@sinaujowo.test', 'exp' => 875, 'streak' => 3, 'highest' => 5, 'selesai' => 0],
            ['nis' => '0092817427', 'nama_lengkap' => 'Dimas Wahyu', 'jenis_kelamin' => 'L', 'kelas' => '7C', 'email' => 'dimas@sinaujowo.test', 'exp' => 810, 'streak' => 4, 'highest' => 4, 'selesai' => 0],
            ['nis' => '0092817428', 'nama_lengkap' => 'Farhan Maulana', 'jenis_kelamin' => 'L', 'kelas' => '7A', 'email' => 'farhan@sinaujowo.test', 'exp' => 750, 'streak' => 1, 'highest' => 3, 'selesai' => 0],
        ];

        $guru = Guru::query()->orderBy('id')->get();
        $buSri = $guru->firstWhere('email', 'bu.sri@sinaujowo.test');
        $pakBagus = $guru->firstWhere('email', 'pak.bagus@sinaujowo.test');

        foreach ($data as $row) {
            $siswa = Siswa::updateOrCreate(
                ['email' => $row['email']],
                [
                    'nis' => $row['nis'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'kelas' => $row['kelas'],
                    'no_telpon' => '0812'.substr($row['nis'], -6),
                    'password' => 'password',
                ],
            );

            $siswa->exp()->updateOrCreate([], ['total_exp' => $row['exp']]);
            $siswa->strek()->updateOrCreate([], [
                'current_streak' => $row['streak'],
                'highest_streak' => $row['highest'],
                'last_activity_date' => now()->toDateString(),
            ]);

            $progres->initialize($siswa);

            // Tandai sejumlah level awal sebagai selesai (berjenjang).
            foreach ($levels->take($row['selesai']) as $level) {
                ProgresSiswa::query()
                    ->where('siswa_id', $siswa->id)
                    ->where('level_materi_id', $level->id)
                    ->update(['status' => ProgresSiswa::STATUS_SELESAI, 'tanggal_selesai' => now()->toDateString()]);
            }

            // Buka level berikutnya setelah level terakhir yang selesai.
            $next = $levels->get($row['selesai']);
            if ($next) {
                ProgresSiswa::query()
                    ->where('siswa_id', $siswa->id)
                    ->where('level_materi_id', $next->id)
                    ->update(['status' => ProgresSiswa::STATUS_BERJALAN]);
            }

            // Relasi "Memantau" (guru–siswa) dengan kelas & mata pelajaran.
            if ($buSri) {
                $buSri->siswa()->syncWithoutDetaching([
                    $siswa->id => ['kelas' => $siswa->kelas, 'mata_pelajaran' => 'Bahasa Jawa'],
                ]);
            }

            if ($pakBagus && in_array($siswa->kelas, ['7A', '7C'], true)) {
                $pakBagus->siswa()->syncWithoutDetaching([
                    $siswa->id => ['kelas' => $siswa->kelas, 'mata_pelajaran' => 'Bahasa Jawa'],
                ]);
            }
        }
    }
}
