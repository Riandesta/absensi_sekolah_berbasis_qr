<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Karyawan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::with('kelas', 'jurusan')->latest()->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
{
    $jurusan = Jurusan::all(); // Assuming you have a Jurusan model to get the list
    $tahunAjaran = TahunAjaran::all(); // Assuming you have a TahunAjaran model for the data
    return view('karyawan.create', compact('jurusan', 'tahunAjaran'));
}


    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'jabatan' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required',
        ]);

        $karyawan = Karyawan::create($request->except(['username', 'password']) + [
            'foto' => $request->foto ?? null,
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
            'related_id' => $karyawan->id,
            'status' => 'aktif'
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $karyawan->update($request->all());

        $user = User::where('role', 'karyawan')->where('related_id', $karyawan->id)->first();
        if ($user) {
            $user->update([
                'username' => $request->username,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);
        }

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $user = User::where('role', 'karyawan')->where('related_id', $karyawan->id);
        $user->delete();
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun berhasil dihapus.');
    }
}
