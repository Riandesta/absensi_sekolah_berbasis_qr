<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';
    protected $guarded = ['id'];

    // Relasi ke Guru (Karyawan)
    public function guru()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }


    // Relasi ke Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Relasi ke Tahun Ajaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Relasi ke Jadwal
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'jadwal_pelajaran_id');
    }
}
