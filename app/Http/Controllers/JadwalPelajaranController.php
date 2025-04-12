<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Karyawan; // Guru
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    // Menampilkan daftar jadwal pelajaran
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $jadwalPelajaran = JadwalPelajaran::with(['guru', 'mataPelajaran', 'tahunAjaran', 'jadwal.kelas'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('mataPelajaran', function ($q) use ($search) {
                    $q->where('nama_mapel', 'like', "%$search%");
                });
            })
            ->paginate($perPage);

        return view('jadwal-pelajaran.index', compact('jadwalPelajaran'));
    }

    // Menampilkan form tambah jadwal pelajaran
    public function create()
    {
        $guruList = Karyawan::where('jabatan', 'guru')->get(); // Hanya guru
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();
        $tahunAjaranList = TahunAjaran::all();
        return view('jadwal-pelajaran.form', compact('guruList', 'kelasList', 'mataPelajaranList', 'tahunAjaranList'));
    }

    // Menyimpan data jadwal pelajaran baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'guru_id' => 'required|exists:gurus,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'slots' => 'required|array',
            'slots.*.kelas_id' => 'required|exists:kelas,id',
            'slots.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'slots.*.jam_mulai' => 'required|date_format:H:i',
            'slots.*.jam_selesai' => 'required|date_format:H:i',
        ]);


        // Simpan data utama jadwal pelajaran
        $jadwalPelajaran = JadwalPelajaran::create([
            // 'guru_id' => $validated['guru_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
        ]);

        // Simpan detail jadwal (hari, jam mulai, jam selesai, kelas)
        foreach ($validated['slots'] as $slot) {
            $jadwalPelajaran->jadwal()->create([
                'kelas_id' => $slot['kelas_id'],
                'hari' => $slot['hari'],
                'jam_mulai' => $slot['jam_mulai'],
                'jam_selesai' => $slot['jam_selesai'],
            ]);
        }

        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    // Menampilkan form edit jadwal pelajaran
    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $guruList = Karyawan::where('jabatan', 'guru')->get(); // Hanya guru
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();
        $tahunAjaranList = TahunAjaran::all();
        return view('jadwal-pelajaran.form', compact('jadwalPelajaran', 'guruList', 'kelasList', 'mataPelajaranList', 'tahunAjaranList'));
    }

    // Memperbarui data jadwal pelajaran
    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
{
    $validated = $request->validate([
        // 'guru_id' => 'required|exists:gurus,id',
        'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        'slots' => 'required|array',
        'slots.*.kelas_id' => 'required|exists:kelas,id',
        'slots.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
        'slots.*.jam_mulai' => 'required|date_format:H:i',
        'slots.*.jam_selesai' => 'required|date_format:H:i',
    ]);


    foreach ($validated['slots'] as $slot) {
        if (strtotime($slot['jam_selesai']) <= strtotime($slot['jam_mulai'])) {
            return back()->withErrors(['slots' => 'Jam selesai harus setelah jam mulai.'])->withInput();
        }
    }



    $jadwalPelajaran->update([
        // 'guru_id' => $validated['guru_id'],
        'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
    ]);

    // Hapus semua slot sebelumnya dan buat ulang
    $jadwalPelajaran->jadwal()->delete();
    foreach ($validated['slots'] as $slot) {
        $jadwalPelajaran->jadwal()->create($slot);
    }

    return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
}


    // Menghapus data jadwal pelajaran
    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();
        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
