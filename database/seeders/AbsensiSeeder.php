<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ada data Siswa, Guru, Jadwal, dan Kelas sebelum menjalankan Seeder ini
        $this->seedAbsensiSiswa();
        $this->seedAbsensiGuru();
    }

    /**
     * Seed data absensi siswa kelas
     */
    private function seedAbsensiSiswa()
    {
        // Ambil daftar siswa, jadwal, dan tanggal hari ini
        $siswa = \App\Models\Siswa::all();
        $jadwal = \App\Models\Jadwal::all();
        $tanggalHariIni = Carbon::now()->toDateString();

        // Loop melalui setiap siswa dan jadwal untuk membuat absensi
        foreach ($siswa as $s) {
            foreach ($jadwal as $j) {
                // Simulasikan status absensi acak
                $status = ['Hadir', 'Izin', 'Sakit', 'Alpa'][random_int(0, 3)];

                // Masukkan data absensi ke dalam tabel
                DB::table('absensi_siswa_kelas')->insert([
                    'siswa_id' => $s->id,
                    'jadwal_id' => $j->id,
                    'kelas_id' => $j->kelas_id,
                    'tanggal' => $tanggalHariIni,
                    'status' => $status,
                    'input_by' => 1, // ID user admin atau pengguna default
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Seed data absensi guru kelas
     */
    private function seedAbsensiGuru()
    {
        // Ambil daftar guru, jadwal, dan tanggal hari ini
        $guru = \App\Models\Karyawan::where('jabatan', 'guru')->get();
        $jadwal = \App\Models\Jadwal::all();
        $tanggalHariIni = Carbon::now()->toDateString();

        // Loop melalui setiap guru dan jadwal untuk membuat absensi
        foreach ($guru as $g) {
            foreach ($jadwal as $j) {
                // Simulasikan status absensi acak
                $status = ['Hadir', 'Izin', 'Sakit', 'Alpa'][random_int(0, 3)];

                // Masukkan data absensi ke dalam tabel
                DB::table('absensi_guru_kelas')->insert([
                    'karyawan_id' => $g->id,
                    'jadwal_id' => $j->id,
                    'kelas_id' => $j->kelas_id,
                    'tanggal' => $tanggalHariIni,
                    'status' => $status,
                    'input_by' => 1, // ID user admin atau pengguna default
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}