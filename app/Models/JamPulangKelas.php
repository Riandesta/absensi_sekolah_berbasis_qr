<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPulangKelas extends Model
{
    protected $fillable = [
        'kelas_id', 'hari', 'jam_pulang'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
