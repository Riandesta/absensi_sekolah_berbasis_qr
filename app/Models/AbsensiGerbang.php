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
    ];

    // User (karena related_id mengacu pada users.id)
    public function user()
    {
        return $this->belongsTo(User::class, 'related_id');
    }

    // Siswa - HANYA jika user role adalah siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'related_id')
                    ->whereHas('user', function($query) {
                        $query->where('role', 'siswa');
                    });
    }

    // Method untuk mendapatkan data siswa hanya jika role = siswa
    public function getSiswaData()
    {
        if ($this->user && $this->user->role === 'siswa') {
            return Siswa::where('id', $this->related_id)->first();
        }
        return null;
    }

    // Relasi karyawan - tetap ada untuk kompatibilitas dengan controller
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'related_id')
                    ->where('role', 'karyawan')
                    ->with('karyawan');
    }

    // Method untuk mendapatkan data karyawan hanya jika role = karyawan
    public function getKaryawanData()
    {
        if ($this->user && $this->user->role === 'karyawan') {
            return $this->user->karyawan;
        }
        return null;
    }

    // Method untuk mendapatkan nama berdasarkan role user yang benar
    public function getNamaLengkap()
    {
        if (!$this->user) {
            return 'Tidak tersedia';
        }

        if ($this->user->role === 'siswa') {
            $siswa = $this->getSiswaData();
            return $siswa ? $siswa->nama_lengkap : 'Tidak tersedia';
        } elseif ($this->user->role === 'karyawan') {
            $karyawan = $this->getKaryawanData();
            return $karyawan ? $karyawan->nama_lengkap : 'Tidak tersedia';
        }

        return 'Tidak tersedia';
    }

    // Method untuk mendapatkan kelas/jabatan berdasarkan role user yang benar
    public function getKelasJabatan()
    {
        if (!$this->user) {
            return '-';
        }

        if ($this->user->role === 'siswa') {
            $siswa = $this->getSiswaData();
            return ($siswa && $siswa->kelas) ? $siswa->kelas->nama_kelas : '-';
        } elseif ($this->user->role === 'karyawan') {
            $karyawan = $this->getKaryawanData();
            return $karyawan ? $karyawan->jabatan : '-';
        }

        return '-';
    }

    // Method untuk menentukan tipe user
    public function getUserType()
    {
        return $this->user ? $this->user->role : 'unknown';
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}