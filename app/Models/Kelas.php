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
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    // Relasi dengan akun kelas (user yang memiliki kelas ini)
    public function akunKelas()
    {
        return $this->belongsTo(User::class, 'akun_kelas_user_id');
    }

    // Relasi ke user yang terkait dengan kelas (akun kelas)
    public function user()
    {
        return $this->hasOne(User::class, 'related_id');
    }
}
