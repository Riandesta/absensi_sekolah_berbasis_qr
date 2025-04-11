<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::where('jabatan', '!=', 'guru')->get();
        return view('admin.kelola_karyawan', compact('karyawan'));
    }

    public function create()
    {
        return view('admin.tambah_karywan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip',
            'nuptk' => 'nullable|unique:karyawan,nuptk',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa',
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        Karyawan::create($request->all());
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan created successfully.');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('admin.edit_karyawan', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip,' . $karyawan->id,
            'nuptk' => 'nullable|unique:karyawan,nuptk,' . $karyawan->id,
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa,' . $karyawan->id,
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $karyawan->update($request->all());
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan updated successfully.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan deleted successfully.');
    }
}
