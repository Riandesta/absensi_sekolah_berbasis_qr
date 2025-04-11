<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas', 'jurusan_id', 'tingkat', 'akun_kelas_user_id'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function akunKelas()
    {
        return $this->belongsTo(User::class, 'akun_kelas_user_id');
    }
}
