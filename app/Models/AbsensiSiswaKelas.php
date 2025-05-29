<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSiswaKelas extends Model
{
    use HasFactory;

    protected $table = 'absensi_siswa_kelas';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'status',
        'input_by',
        'absen_gerbang_id',
    ];

    // Relationship with Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relationship with Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    // Relationship with User who inputted the attendance
    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    // Relationship with AbsensiGerbang (gate attendance)
    public function absensiGerbang()
    {
        return $this->belongsTo(AbsensiGerbang::class, 'absen_gerbang_id');
    }
}
