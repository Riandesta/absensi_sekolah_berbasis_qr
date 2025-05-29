<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as A;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        A::table('jurusan')->insert([
            [
                'nama_jurusan' => 'Teknik Komputer Jaringan',
                'kode_jurusan' => 'TKJ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Teknik Permesinan',
                'kode_jurusan' => 'TP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Teknik Bisnis Sepeda Motor',
                'kode_jurusan' => 'TBSM',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Teknik Kendaraan Ringan',
                'kode_jurusan' => 'TKR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jurusan' => 'Rekayasa Perangkat Lunak',
                'kode_jurusan' => 'RPL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Jurusan seeded successfully.');
    }
}
