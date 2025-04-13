<?php

namespace App\Http\Controllers;

use App\Models\PetugasPiket;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class PetugasPiketController extends Controller
{
    public function index()
    {
        $petugasPiket = PetugasPiket::with('karyawan')->paginate(10);
        return view('petugas-piket.index', compact('petugasPiket'));
    }

    public function create()
    {
        $karyawanList = Karyawan::all();
        return view('petugas-piket.create', compact('karyawanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Siang,Sore',
            'keterangan' => 'nullable|string',
        ]);

        PetugasPiket::create($validated);

        return redirect()->route('petugas-piket.index')->with('success', 'Jadwal petugas piket berhasil ditambahkan.');
    }

    public function edit(PetugasPiket $petugasPiket)
    {
        $karyawanList = Karyawan::all();
        return view('petugas-piket.edit', compact('petugasPiket', 'karyawanList'));
    }

    public function update(Request $request, PetugasPiket $petugasPiket)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'shift' => 'required|in:Pagi,Siang,Sore',
            'keterangan' => 'nullable|string',
        ]);

        $petugasPiket->update($validated);

        return redirect()->route('petugas-piket.index')->with('success', 'Jadwal petugas piket berhasil diperbarui.');
    }

    public function validateActivePetugas($karyawanId, $tanggal, $shift)
    {
        return PetugasPiket::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggal)
            ->where('shift', $shift)
            ->exists();
    }

    public function destroy(PetugasPiket $petugasPiket)
    {
        $petugasPiket->delete();
        return redirect()->route('petugas-piket.index')->with('success', 'Jadwal petugas piket berhasil dihapus.');
    }
}
