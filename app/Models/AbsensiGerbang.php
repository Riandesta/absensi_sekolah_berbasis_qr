<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGerbang extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'waktu_scan', 'status', 'scanned_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
