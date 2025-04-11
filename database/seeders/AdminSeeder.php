<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambahkan data admin ke tabel users
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('password123'), // Password: password123
            'role' => 'admin', // Role admin
            'related_id' => null, // Tidak ada related_id untuk admin
            'email' => 'admin@example.com',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin account created successfully.');
    }
}
