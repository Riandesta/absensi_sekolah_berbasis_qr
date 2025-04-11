<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    // Dashboard Guru
    public function index()
    {
        $guru = Auth::user()->karyawan; // Mengambil data guru dari user yang login
        $jadwal = JadwalPelajaran::where('guru_id', $guru->id)->get();

        return view('guru.dashboard', compact('guru', 'jadwal'));
    }

    // Input Absensi Siswa
    public function inputAttendance(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        AbsensiSiswaKelas::create([
            'siswa_id' => $request->siswa_id,
            'jadwal_id' => $request->jadwal_id,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'input_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Absensi siswa berhasil diinput.');
    }
}
