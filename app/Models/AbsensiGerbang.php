<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGerbang extends Model
{
    protected $table = 'absensi_gerbang';

    protected $fillable = [
        'related_id',
        'tanggal',
        'waktu_scan_masuk',
        'waktu_scan_keluar',
        'status',
        'scanned_by',
        'jadwal_id',
        'user_type',  // Pastikan user_type ada di fillable
    ];

    // PERBAIKAN: Relasi normal tanpa kondisi tambahan
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'related_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'related_id');
    }

    // Method helper untuk mendapatkan nama user berdasarkan tipe
    public function getUserNameAttribute()
    {
        if ($this->user_type === 'siswa' && $this->siswa) {
            return $this->siswa->nama_lengkap;
        } elseif ($this->user_type === 'karyawan' && $this->karyawan) {
            return $this->karyawan->nama_lengkap;
        }
        return 'Tidak tersedia';
    }

    // Method helper untuk mendapatkan kelas/jabatan
    public function getKelasJabatanAttribute()
    {
        if ($this->user_type === 'siswa' && $this->siswa && $this->siswa->kelas) {
            return $this->siswa->kelas->nama_kelas;
        } elseif ($this->user_type === 'karyawan' && $this->karyawan) {
            return $this->karyawan->jabatan ?? 'Tidak tersedia';
        }
        return 'Tidak tersedia';
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}
