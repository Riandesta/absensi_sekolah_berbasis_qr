<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Karyawan extends Model
{

    protected $table = 'karyawan';
    protected $fillable = [
        'nip',
        'nuptk',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas_id',
        'jurusan_id',
        'tahun_ajaran_id',
        'no_wa',
        'foto',
        'jabatan',
        'tempat_lahir',
        'tanggal_lahir',
        'qr_code'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'related_id', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function generateQrCode()
    {
        // Pastikan direktori qr-codes ada
        if (!Storage::disk('public')->exists('qr-codes')) {
            Storage::disk('public')->makeDirectory('qr-codes');
        }

        // Generate QR content dengan format yang sesuai
        $qrContent = json_encode([
            'id' => $this->id,
            'nip' => $this->nip,
            'nama' => $this->nama_lengkap
        ]);

        // Buat nama file unik untuk QR Code
        $fileName = 'qr-code-' . $this->id . '.svg';

        // Simpan QR Code di storage
        $path = storage_path('app/public/qr-codes/' . $fileName);
        QrCode::size(200)->generate($qrContent, $path);

        // Simpan path QR Code ke database
        $this->update([
            'qr_code' => 'storage/qr-codes/' . $fileName,
        ]);

        return $this->qr_code;
    }
}
