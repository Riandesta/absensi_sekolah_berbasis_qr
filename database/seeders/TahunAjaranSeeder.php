<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tahun_ajaran')->insert([
            [
                'tahun_awal' => 2024,
                'tahun_akhir' => 2025,
                'semester' => 'Ganjil',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_awal' => 2024,
                'tahun_akhir' => 2025,
                'semester' => 'Genap',
                'is_aktif' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_awal' => 2023,
                'tahun_akhir' => 2024,
                'semester' => 'Ganjil',
                'is_aktif' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_awal' => 2023,
                'tahun_akhir' => 2024,
                'semester' => 'Genap',
                'is_aktif' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Tahun Ajaran seeded successfully.');
    }
}
