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

        // Loop untuk isi data acak max 500 record
        while (count($absensiData) < 500) {
            $isSiswa = rand(1, 100) <= 50 && !empty($siswaIds); // 50% chance siswa
            $relatedId = $isSiswa
                ? $siswaIds[array_rand($siswaIds)]
                : $karyawanIds[array_rand($karyawanIds)];

            // Set user_type berdasarkan jenis user
            $userType = $isSiswa ? 'siswa' : 'karyawan';

            $tanggal = $workingDates[array_rand($workingDates)];

            // Jam masuk dan keluar berdasarkan tipe user
            $entryTime = $isSiswa
                ? sprintf('06:%02d:00', rand(30, 59)) // Siswa masuk 06:30-06:59
                : sprintf('06:%02d:00', rand(0, 59));  // Karyawan masuk 06:00-06:59

            $exitTime = $isSiswa
                ? sprintf('%02d:%02d:00', rand(14, 15), rand(0, 59)) // Siswa keluar 14:00-15:59
                : sprintf('%02d:%02d:00', rand(15, 16), rand(0, 59)); // Karyawan keluar 15:00-16:59

            // Cek apakah dia guru (hanya untuk karyawan)
            $jadwalId = null;
            if (!$isSiswa) {
                $karyawan = Karyawan::find($relatedId);
                if ($karyawan && $karyawan->jabatan === 'guru') {
                    $jadwalId = $jadwalIds[array_rand($jadwalIds)];
                }
            }

            // Tambahkan beberapa variasi status absensi
            $statusOptions = ['Hadir', 'Terlambat', 'Pulang Cepat'];
            $weights = [70, 20, 10]; // 70% Hadir, 20% Terlambat, 10% Pulang Cepat
            $status = $this->getWeightedRandom($statusOptions, $weights);

            // Adjust waktu berdasarkan status
            if ($status === 'Terlambat') {
                $entryTime = $isSiswa
                    ? sprintf('07:%02d:00', rand(0, 30)) // Siswa terlambat 07:00-07:30
                    : sprintf('07:%02d:00', rand(0, 15)); // Karyawan terlambat 07:00-07:15
            } elseif ($status === 'Pulang Cepat') {
                $exitTime = $isSiswa
                    ? sprintf('%02d:%02d:00', rand(12, 13), rand(0, 59)) // Siswa pulang cepat 12:00-13:59
                    : sprintf('%02d:%02d:00', rand(13, 14), rand(0, 59)); // Karyawan pulang cepat 13:00-14:59
            }

            // Beberapa record hanya scan masuk (belum scan keluar)
            $hasExitScan = rand(1, 100) <= 85; // 85% ada scan keluar

            $absensiData[] = [
                'related_id' => $relatedId,
                'user_type' => $userType, // Tambahan kolom user_type
                'tanggal' => $tanggal,
                'waktu_scan_masuk' => $entryTime,
                'waktu_scan_keluar' => $hasExitScan ? $exitTime : null,
                'status' => $hasExitScan ? $status : 'Belum Scan Keluar',
                'scanned_by' => $scannerUserIds[array_rand($scannerUserIds)],
                'jadwal_id' => $jadwalId,
                'created_at' => Carbon::parse($tanggal)->setTimeFromTimeString($entryTime),
                'updated_at' => $hasExitScan
                    ? Carbon::parse($tanggal)->setTimeFromTimeString($exitTime)
                    : Carbon::parse($tanggal)->setTimeFromTimeString($entryTime),
            ];
        }

        // Insert data dalam batch untuk performa yang lebih baik
        $chunks = array_chunk($absensiData, 100);
        foreach ($chunks as $chunk) {
            DB::table('absensi_gerbang')->insert($chunk);
        }

        $this->command->info('AbsensiGerbang seeded with ' . count($absensiData) . ' random records.');

        // Tampilkan statistik
        $siswaCount = collect($absensiData)->where('user_type', 'siswa')->count();
        $karyawanCount = collect($absensiData)->where('user_type', 'karyawan')->count();

        $this->command->info("- Siswa records: {$siswaCount}");
        $this->command->info("- Karyawan records: {$karyawanCount}");
    }

    /**
     * Get weighted random selection
     */
    private function getWeightedRandom($options, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        for ($i = 0; $i < count($options); $i++) {
            $currentWeight += $weights[$i];
            if ($random <= $currentWeight) {
                return $options[$i];
            }
        }

        return $options[0]; // fallback
    }
}
