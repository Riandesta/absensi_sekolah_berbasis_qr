<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'nis', 'nama_lengkap', 'jenis_kelamin', 'kelas_id', 'jurusan_id', 'tahun_ajaran_id', 'no_wa', 'foto', 'tempat_lahir', 'tanggal_lahir'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'related_id', 'id');
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
}
