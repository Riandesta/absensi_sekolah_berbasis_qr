<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetugasPiket extends Model
{
    protected $fillable = [
        'karyawan_id', 'tanggal', 'shift', 'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
