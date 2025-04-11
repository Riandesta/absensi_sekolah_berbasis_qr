<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kelas;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Support\Facades\Auth;

class WalikelasController extends Controller
{
    // Dashboard Wali Kelas
    public function index()
    {
        $walikelas = Auth::user()->karyawan; // Mengambil data wali kelas dari user yang login
        $kelas = Kelas::where('akun_kelas_user_id', Auth::id())->first();

        if (!$kelas) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki kelas yang ditugaskan.']);
        }

        $absensi = AbsensiSiswaKelas::whereHas('siswa', function ($query) use ($kelas) {
            $query->where('kelas_id', $kelas->id);
        })->get();

        return view('walikelas.dashboard', compact('walikelas', 'kelas', 'absensi'));
    }

    // Cetak Laporan Absensi
    public function printReport()
    {
        $kelas = Kelas::where('akun_kelas_user_id', Auth::id())->first();

        if (!$kelas) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak memiliki kelas yang ditugaskan.']);
        }

        $absensi = AbsensiSiswaKelas::whereHas('siswa', function ($query) use ($kelas) {
            $query->where('kelas_id', $kelas->id);
        })->get();

        return view('walikelas.print_report', compact('kelas', 'absensi'));
    }
}
