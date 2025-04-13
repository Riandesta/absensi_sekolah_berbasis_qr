<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'nis', 'nama_lengkap', 'jenis_kelamin', 'kelas_id', 'jurusan_id', 'tahun_ajaran_id', 'no_wa', 'foto', 'tempat_lahir', 'tanggal_lahir', 'qr_code'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'related_id')->where('role', 'siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
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

        // Buat nama file unik untuk QR Code
        $fileName = 'qr-code-' . $this->id . '.svg';

        // Simpan QR Code di storage
        $path = storage_path('app/public/qr-codes/' . $fileName);
        QrCode::size(200)->generate($this->id, $path);

        // Simpan path QR Code ke database
        $this->update([
            'qr_code' => 'storage/qr-codes/' . $fileName,
        ]);

        return $this->qr_code;
    }

    /**
     * Event listener untuk otomatisasi pembuatan QR Code.
     */
    protected static function booted()
    {
        static::created(function ($siswa) {
            $siswa->generateQrCode();
        });
    }
}
