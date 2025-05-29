<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Kelas;
use App\Models\User;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelas = [];
        $jurusan = DB::table('jurusan')->get();
        $tingkat = ['X', 'XI', 'XII'];

        foreach ($jurusan as $j) {
            foreach ($tingkat as $t) {
                for ($i = 1; $i <= 2; $i++) {
                    $kelas[] = [
                        'nama_kelas' => "$t {$j->kode_jurusan} $i",
                        'jurusan_id' => $j->id,
                        'tingkat' => $t,
                        'akun_kelas_user_id' => null, // Will be updated after creating users
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert kelas data
        DB::table('kelas')->insert($kelas);

        // Get all kelas IDs
        $kelas = Kelas::all();

        // Create user accounts for each kelas
        foreach ($kelas as $kelasItem) {
            $username = 'kelas_' . $kelasItem->id;
            $user = User::create([
                'username' => $username,
                'password' => Hash::make('password123'),
                'role' => 'kelas',
                'related_id' => $kelasItem->id,
                'email' => $username . '@kelas.school.com',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update kelas with user_id
            $kelasItem->update([
                'akun_kelas_user_id' => $user->id,
            ]);
        }

        $this->command->info('Kelas and related users seeded successfully.');
    }
}
