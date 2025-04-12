<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'username', 'password', 'role', 'related_id', 'email', 'status'
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi dengan tabel siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id', 'related_id');
    }

    // Relasi dengan tabel karyawan
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'id', 'related_id');
    }

    // Relasi dengan tabel kelas (untuk akun_kelas)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'related_id'); // Gunakan belongsTo untuk relasi ke Kelas
    }
}
