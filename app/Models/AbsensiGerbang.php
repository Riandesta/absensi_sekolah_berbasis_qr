<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGerbang extends Model
{
    protected $table = 'absensi_gerbang';

    protected $fillable = [
        'related_id',
        'tanggal',
        'waktu_scan_masuk',
        'waktu_scan_keluar',
        'status',
        'scanned_by',
        'jadwal_id',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'related_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'related_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}
