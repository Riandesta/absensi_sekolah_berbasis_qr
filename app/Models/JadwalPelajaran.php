<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $fillable = [
        'karyawan_id', 'kelas_id', 'mata_pelajaran_id', 'hari', 'jam_mulai', 'jam_selesai', 'tahun_ajaran_id'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
