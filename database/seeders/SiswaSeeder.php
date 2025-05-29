<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Siswa;
use App\Models\User;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $jenis_kelamin = ['L', 'P'];
        $kelas = DB::table('kelas')->get();
        $jurusan = DB::table('jurusan')->get();
        $tahun_ajaran = DB::table('tahun_ajaran')->where('is_aktif', true)->first();

        if (!$tahun_ajaran) {
            $this->command->error('No active tahun_ajaran found. Please insert an active tahun_ajaran record.');
            return;
        }

        for ($i = 1; $i <= 100; $i++) {
            $jk = $jenis_kelamin[rand(0, 1)];

            $nama_depan = $jk == 'L' ?
                ['Ahmad', 'Bima', 'Candra', 'Dani', 'Edi', 'Farhan', 'Galih', 'Hendra', 'Ilham', 'Johan'][rand(0, 9)] :
                ['Ayu', 'Bunga', 'Cinta', 'Dina', 'Eka', 'Farah', 'Gita', 'Hani', 'Indah', 'Jessica'][rand(0, 9)];

            $nama_belakang = ['Wibowo', 'Sulistyo', 'Purnama', 'Rahman', 'Prayoga', 'Susanto', 'Hartono', 'Prasetyo', 'Gunawan', 'Permana'][rand(0, 9)];

            $nama_lengkap = "$nama_depan $nama_belakang";

            $kelas_random = $kelas[rand(0, count($kelas) - 1)];
            $jurusan_id = $kelas_random->jurusan_id;

            $nis = '2024' . str_pad($jurusan_id, 2, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT);

            $tanggal_lahir = rand(2003, 2008) . '-' . rand(1, 12) . '-' . rand(1, 28);

            $siswa = Siswa::create([
                'nis' => $nis,
                'nama_lengkap' => $nama_lengkap,
                'jenis_kelamin' => $jk,
                'kelas_id' => $kelas_random->id,
                'jurusan_id' => $jurusan_id,
                'tahun_ajaran_id' => $tahun_ajaran->id,
                'no_wa' => '08' . rand(10000000000, 99999999999),
                'foto' => null,
                'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta', 'Medan', 'Makassar', 'Palembang', 'Padang', 'Malang'][rand(0, 9)],
                'tanggal_lahir' => $tanggal_lahir,
                'qr_code' => null, // QR Code will be generated later
            ]);

            User::create([
                'username' => 'siswa.' . strtolower(str_replace(' ', '', $nama_lengkap)) . rand(1, 99),
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'related_id' => $siswa->id,
                'email' => strtolower(str_replace(' ', '.', $nama_lengkap)) . '@student.school.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Siswa and related users seeded successfully.');
    }
}
