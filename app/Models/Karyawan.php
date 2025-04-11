<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{

    protected $table = 'karyawan';
    protected $fillable = [
        'nip', 'nuptk', 'nama_lengkap', 'jenis_kelamin', 'kelas_id', 'jurusan_id', 'tahun_ajaran_id', 'no_wa', 'foto', 'jabatan', 'tempat_lahir', 'tanggal_lahir'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'related_id', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
}
