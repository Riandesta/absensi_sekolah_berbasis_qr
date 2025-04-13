<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGerbang extends Model
{
    protected $table = 'absensi_gerbang';
    protected $fillable = [
        'related_id',
        'tanggal',
        'waktu_scan',
        'status',
        'scanned_by',
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'related_id');
    }

    // Relasi ke karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'related_id');
    }

    // Relasi ke user yang mencatat absensi
    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
