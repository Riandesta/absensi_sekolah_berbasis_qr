<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin users don't have related_id
        DB::table('users')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'related_id' => null,
                'email' => 'admin@school.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'admin.sistem',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'related_id' => null,
                'email' => 'admin.sistem@school.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('Admin accounts created successfully.');

        // Note: The rest of the users (karyawan, siswa, kelas) will be created in their respective seeders
        // This is because we need to know the IDs from those tables first
        // So KaryawanSeeder, SiswaSeeder, and KelasSeeder should each create their own users
    }
}
