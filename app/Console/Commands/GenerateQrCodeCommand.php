<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Siswa;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class GenerateQrCodeCommand extends Command
{
    protected $signature = 'qr:generate';
    protected $description = 'Generate QR Code for all karyawan and siswa';

    public function handle()
    {
        $this->info('Generating QR Codes...');

        // Generate QR Code for all karyawan
        $karyawan = Karyawan::all();
        foreach ($karyawan as $k) {
            $qrContent = json_encode([
                'id' => $k->id,
                'nip' => $k->nip,
                'nama' => $k->nama_lengkap
            ], JSON_UNESCAPED_UNICODE);

            $qrFileName = 'qr-codes-karyawan/' . $k->nip . '.svg';
            $qrPath = storage_path('app/public/' . $qrFileName);

            // Ensure the directory exists
            if (!Storage::disk('public')->exists('qr-codes-karyawan')) {
                Storage::disk('public')->makeDirectory('qr-codes-karyawan');
            }

            QrCode::format('svg')->size(300)->generate($qrContent, $qrPath);

            $k->update([
                'qr_code' => 'storage/' . $qrFileName,
            ]);

            $this->info("QR Code generated for Karyawan with NIP: {$k->nip}");
        }

        // Generate QR Code for all siswa
        $siswa = Siswa::all();
        foreach ($siswa as $s) {
            $qrContent = json_encode([
                'id' => $s->id,
                'nis' => $s->nis,
                'nama' => $s->nama_lengkap
            ], JSON_UNESCAPED_UNICODE);

            $qrFileName = 'qr-codes-siswa/' . $s->nis . '.svg';
            $qrPath = storage_path('app/public/' . $qrFileName);

            // Ensure the directory exists
            if (!Storage::disk('public')->exists('qr-codes-siswa')) {
                Storage::disk('public')->makeDirectory('qr-codes-siswa');
            }

            QrCode::format('svg')->size(300)->generate($qrContent, $qrPath);

            $s->update([
                'qr_code' => 'storage/' . $qrFileName,
            ]);

            $this->info("QR Code generated for Siswa with NIS: {$s->nis}");
        }

        $this->info('QR Codes generated successfully.');
    }
}
