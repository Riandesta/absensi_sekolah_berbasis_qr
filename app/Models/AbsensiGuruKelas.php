<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuruKelas extends Model
{
    protected $fillable = [
        'karyawan_id', 'jadwal_id', 'kelas_id', 'tanggal', 'waktu_scan', 'scan_by_user_id', 'status'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function scanByUser()
    {
        return $this->belongsTo(User::class, 'scan_by_user_id');
    }
}
