<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Auth;

class KurikulumController extends Controller
{
    // Dashboard Kurikulum
    public function index()
    {
        $kurikulum = Auth::user()->karyawan; // Mengambil data kurikulum dari user yang login
        $mataPelajaran = MataPelajaran::all();
        $jadwal = JadwalPelajaran::all();

        return view('kurikulum.dashboard', compact('kurikulum', 'mataPelajaran', 'jadwal'));
    }

    // Tambah Mata Pelajaran
    public function addSubject(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        MataPelajaran::create([
            'nama' => $request->nama,
        ]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    // Tambah Jadwal Pelajaran
    public function addSchedule(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:karyawan,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
        ]);

        JadwalPelajaran::create($request->all());

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }
}
