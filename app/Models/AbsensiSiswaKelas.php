<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// App\Models\AbsensiSiswaKelas.php

class AbsensiSiswaKelas extends Model
{
    protected $table = 'absensi_siswa_kelas'; // Ensure this is correct
    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'status',
        'input_by',
        'absen_gerbang_id',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function absensiGerbang()
    {
        return $this->belongsTo(AbsensiGerbang::class, 'absen_gerbang_id');
    }
}
