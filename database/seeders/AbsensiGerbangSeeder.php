<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsensiGerbangSeeder extends Seeder
{
    public function run(): void
    {
        $siswaIds = Siswa::pluck('id')->toArray();
        $karyawanIds = Karyawan::pluck('id')->toArray();

        $scannerUserIds = User::whereIn('role', ['admin', 'karyawan'])
            ->where('status', 'aktif')
            ->pluck('id')
            ->toArray();

        if (empty($scannerUserIds)) {
            $scannerUserIds = [1]; // default fallback
        }

        $jadwalIds = Jadwal::pluck('id')->toArray();
        if (empty($jadwalIds)) {
            $jadwalIds = [null];
        }

        // Ambil semua tanggal kerja (weekday) dari 30 hari terakhir
        $workingDates = collect(range(0, 29))
            ->map(fn($i) => Carbon::now()->subDays($i))
            ->filter(fn($date) => $date->isWeekday())
            ->map(fn($date) => $date->toDateString())
            ->toArray();

        shuffle($workingDates);

        $absensiData = [];

        // Loop untuk isi data acak max 200 record
        while (count($absensiData) < 500) {
            $isSiswa = rand(1, 100) <= 50 && !empty($siswaIds); // 50% chance siswa
            $relatedId = $isSiswa
                ? $siswaIds[array_rand($siswaIds)]
                : $karyawanIds[array_rand($karyawanIds)];

            $tanggal = $workingDates[array_rand($workingDates)];

            // Jam masuk dan keluar
            $entryTime = $isSiswa
                ? sprintf('06:%02d:00', rand(30, 59))
                : sprintf('06:%02d:00', rand(0, 59));

            $exitTime = $isSiswa
                ? sprintf('%02d:%02d:00', rand(14, 15), rand(0, 59))
                : sprintf('%02d:%02d:00', rand(15, 16), rand(0, 59));

            // Cek apakah dia guru
            $jadwalId = null;
            if (!$isSiswa) {
                $karyawan = Karyawan::find($relatedId);
                if ($karyawan && $karyawan->jabatan === 'guru') {
                    $jadwalId = $jadwalIds[array_rand($jadwalIds)];
                }
            }

            $absensiData[] = [
                'related_id' => $relatedId,
                'tanggal' => $tanggal,
                'waktu_scan_masuk' => $entryTime,
                'waktu_scan_keluar' => $exitTime,
                'status' => 'Hadir',
                'scanned_by' => $scannerUserIds[array_rand($scannerUserIds)],
                'jadwal_id' => $jadwalId,
                'created_at' => Carbon::parse($tanggal)->setTimeFromTimeString($entryTime),
                'updated_at' => Carbon::parse($tanggal)->setTimeFromTimeString($exitTime),
            ];
        }

        DB::table('absensi_gerbang')->insert($absensiData);

        $this->command->info('AbsensiGerbang seeded with ' . count($absensiData) . ' random records.');
    }
}
