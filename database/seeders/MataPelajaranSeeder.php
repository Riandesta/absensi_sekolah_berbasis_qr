<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mapel = [
            ['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIN'],
            ['nama_mapel' => 'Bahasa Inggris', 'kode_mapel' => 'BIG'],
            ['nama_mapel' => 'Fisika', 'kode_mapel' => 'FIS'],
            ['nama_mapel' => 'Kimia', 'kode_mapel' => 'KIM'],
            ['nama_mapel' => 'Biologi', 'kode_mapel' => 'BIO'],
            ['nama_mapel' => 'Sejarah', 'kode_mapel' => 'SEJ'],
            ['nama_mapel' => 'Geografi', 'kode_mapel' => 'GEO'],
            ['nama_mapel' => 'Ekonomi', 'kode_mapel' => 'EKO'],
            ['nama_mapel' => 'Pendidikan Agama', 'kode_mapel' => 'PAI'],
            ['nama_mapel' => 'Pendidikan Kewarganegaraan', 'kode_mapel' => 'PKN'],
            ['nama_mapel' => 'Pemrograman Dasar', 'kode_mapel' => 'PRGD'],
            ['nama_mapel' => 'Jaringan Komputer', 'kode_mapel' => 'JARK'],
            ['nama_mapel' => 'Basis Data', 'kode_mapel' => 'BASD'],
            ['nama_mapel' => 'Teknik Otomotif', 'kode_mapel' => 'TKOT'],
            ['nama_mapel' => 'Teknik Permesinan', 'kode_mapel' => 'TKPN'],
            ['nama_mapel' => 'Teknik Sepeda Motor', 'kode_mapel' => 'TKSM'],
            ['nama_mapel' => 'Pengembangan Aplikasi', 'kode_mapel' => 'PGAP'],
            ['nama_mapel' => 'Pendidikan Jasmani', 'kode_mapel' => 'PJOK'],
            ['nama_mapel' => 'Seni Budaya', 'kode_mapel' => 'SBD'],
        ];

        foreach ($mapel as &$m) {
            $m['created_at'] = now();
            $m['updated_at'] = now();
        }

        DB::table('mata_pelajaran')->insert($mapel);

        $this->command->info('Mata Pelajaran seeded successfully.');
    }
}
