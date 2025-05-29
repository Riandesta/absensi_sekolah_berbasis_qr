<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KaryawanSeeder extends Seeder
{
    public function run()
    {
        $jabatan = ['guru', 'kurikulum', 'walikelas'];
        $jenis_kelamin = ['L', 'P'];
        $kelas = DB::table('kelas')->get();
        $jurusan = DB::table('jurusan')->get();
        $tahun_ajaran = DB::table('tahun_ajaran')->where('is_aktif', true)->first();

        if (!$tahun_ajaran) {
            $this->command->error('No active tahun_ajaran found. Please insert an active tahun_ajaran record.');
            return;
        }

        $users = [];

        for ($i = 1; $i <= 40; $i++) {
            $jk = $jenis_kelamin[rand(0, 1)];
            $nama_depan = $jk == 'L'
                ? ['Budi', 'Agus', 'Dedi', 'Eko', 'Faisal', 'Gunawan', 'Hadi', 'Irwan', 'Joko', 'Koko'][rand(0, 9)]
                : ['Ani', 'Bintang', 'Citra', 'Dewi', 'Endah', 'Fitri', 'Gita', 'Hana', 'Indah', 'Juwita'][rand(0, 9)];

            $nama_belakang = ['Santoso', 'Wijaya', 'Hidayat', 'Kusuma', 'Nugraha', 'Putra', 'Saputra', 'Pratama', 'Utama', 'Setiawan'][rand(0, 9)];
            $nama_lengkap = "$nama_depan $nama_belakang";

            $jabatan_karyawan = $i <= 30 ? 'guru' : ($i <= 35 ? 'kurikulum' : 'walikelas');
            $kelas_id = null;
            $jurusan_id = null;

            if ($jabatan_karyawan === 'walikelas') {
                $kelas_id = rand(1, count($kelas));
                $jurusan_id = $kelas[$kelas_id - 1]->jurusan_id;
            }

            $nip = '19' . rand(70, 99) . rand(10, 12) . rand(10, 28) . rand(1000, 9999);
            $tanggal_lahir = rand(1970, 1995) . '-' . rand(1, 12) . '-' . rand(1, 28);

            $karyawan_id = DB::table('karyawan')->insertGetId([
                'nip' => $nip,
                'nuptk' => rand(1000000000000000, 9999999999999999),
                'nama_lengkap' => $nama_lengkap,
                'jenis_kelamin' => $jk,
                'kelas_id' => $kelas_id,
                'jurusan_id' => $jurusan_id,
                'tahun_ajaran_id' => $tahun_ajaran->id,
                'no_wa' => '08' . rand(10000000000, 99999999999),
                'foto' => null,
                'jabatan' => $jabatan_karyawan,
                'tempat_lahir' => ['Jakarta', 'Bandung', 'Surabaya', 'Semarang', 'Yogyakarta'][rand(0, 4)],
                'tanggal_lahir' => $tanggal_lahir,
                'qr_code' => null, // QR Code will be generated later
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $users[] = [
                'username' => strtolower(Str::slug($nama_lengkap)) . rand(1, 999),
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'related_id' => $karyawan_id,
                'email' => strtolower(str_replace(' ', '.', $nama_lengkap)) . '@school.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);

        // Update akun_kelas_user_id for specific classes
        $walikelas_users = DB::table('users')
            ->where('role', 'karyawan')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        foreach ($walikelas_users as $index => $user) {
            DB::table('kelas')
                ->where('id', $index + 1)
                ->update(['akun_kelas_user_id' => $user->id]);
        }

        $this->command->info('Karyawan and related users seeded successfully.');
    }
}
