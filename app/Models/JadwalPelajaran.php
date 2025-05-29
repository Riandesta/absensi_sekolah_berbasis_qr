<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';
    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'tahun_ajaran_id'
    ];

    public function guru()
    {
        return $this->belongsTo(Karyawan::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'jadwal_pelajaran_id');
    }
}
