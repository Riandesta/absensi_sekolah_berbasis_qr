<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuruKelas extends Model
{
    protected $fillable = [
        'karyawan_id',
        'jadwal_id',
        'kelas_id',
        'tanggal',
        'waktu_scan',
        'scan_by_user_id',
        'status'
    ];

    // Relasi ke Guru (Karyawan)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relasi ke Jadwal Pelajaran
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke User (yang melakukan scan)
    public function scanByUser()
    {
        return $this->belongsTo(User::class, 'scan_by_user_id');
    }
}
