<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiSiswaKelas extends Model
{
    protected $fillable = [
        'siswa_id', 'jadwal_id', 'tanggal', 'status', 'input_by'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
