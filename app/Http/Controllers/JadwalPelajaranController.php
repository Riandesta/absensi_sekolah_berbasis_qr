<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Karyawan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class JadwalPelajaranController extends Controller
{
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

    public function create()
    {
        $guruList = Karyawan::where('jabatan', 'Guru')->get();
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();
        $tahunAjaranList = TahunAjaran::all();
        return view('jadwal-pelajaran.form', compact('guruList', 'kelasList', 'mataPelajaranList', 'tahunAjaranList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:karyawan,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'slots' => 'required|array',
            'slots.*.kelas_id' => 'required|exists:kelas,id',
            'slots.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'slots.*.jam_mulai' => 'required|date_format:H:i',
            'slots.*.jam_selesai' => 'required|date_format:H:i',
        ]);

        $jadwalPelajaran = JadwalPelajaran::create([
            'guru_id' => $validated['guru_id'], // Pastikan ini sesuai dengan kolom di database
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
        ]);

        foreach ($validated['slots'] as $slot) {
            if ($slot['jam_mulai'] >= $slot['jam_selesai']) {
                return back()->withErrors(['slots' => 'Jam mulai harus lebih awal dari jam selesai'])->withInput();
            }
            $jadwalPelajaran->jadwal()->create([
                'kelas_id' => $slot['kelas_id'],
                'hari' => $slot['hari'],
                'jam_mulai' => $slot['jam_mulai'],
                'jam_selesai' => $slot['jam_selesai'],
            ]);
        }

        return redirect()
            ->route(Auth::user()->role . '.jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $guruList = Karyawan::where('jabatan', 'Guru')->get();
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();
        $tahunAjaranList = TahunAjaran::all();
        return view('jadwal-pelajaran.form', compact('jadwalPelajaran', 'guruList', 'kelasList', 'mataPelajaranList', 'tahunAjaranList'));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:karyawan,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'slots' => 'required|array',
            'slots.*.kelas_id' => 'required|exists:kelas,id',
            'slots.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'slots.*.jam_mulai' => 'required|date_format:H:i',
            'slots.*.jam_selesai' => 'required|date_format:H:i',
        ]);

        $jadwalPelajaran->update([
            'karyawan_id' => $validated['guru_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
        ]);

        foreach ($validated['slots'] as $slot) {
            if ($slot['jam_mulai'] >= $slot['jam_selesai']) {
                return back()->withErrors(['slots' => 'Jam mulai harus lebih awal dari jam selesai'])->withInput();
            }
        }

        $jadwalPelajaran->jadwal()->delete();
        foreach ($validated['slots'] as $slot) {
            $jadwalPelajaran->jadwal()->create($slot);
        }

        return redirect()
        ->route(Auth::user()->role . '.jadwal-pelajaran.index')
        ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();
        return redirect()->route('jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
