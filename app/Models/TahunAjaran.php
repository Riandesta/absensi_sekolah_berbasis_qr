<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun_awal',
        'tahun_akhir',
        'semester',
        'is_aktif',
    ];

    protected $attributes = [
        'is_aktif' => true, // Default value untuk is_aktif
    ];

    public function getTahunFormattedAttribute()
    {
        return "{$this->tahun_awal}/{$this->tahun_akhir} - {$this->semester}";
    }
}
