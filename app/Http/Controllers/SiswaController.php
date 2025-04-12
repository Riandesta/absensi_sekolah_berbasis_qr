<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\TahunAjaran;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('kelas', 'jurusan')->latest()->paginate(10);
        return view('siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();

        return view('siswa.create', compact('kelas', 'jurusan', 'tahunAjaran'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nis' => 'required|unique:siswa',
        'nama_lengkap' => 'required',
        'jenis_kelamin' => 'required',
        'kelas_id' => 'required',
        'jurusan_id' => 'required',
        'tahun_ajaran_id' => 'required',
        'username' => 'required|unique:users,username',
        'password' => 'required',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the photo
    ]);

    // Handle photo upload
    $fotoPath = null;
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto');
        $fotoPath = $foto->store('foto/siswa', 'public'); // Store file in public disk
    }

    // Create siswa data
    $siswa = Siswa::create($request->except(['username', 'password', 'foto']) + [
        'foto' => $fotoPath,
    ]);

    // Create associated user account
    User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'role' => 'siswa',
        'related_id' => $siswa->id,
        'status' => 'aktif'
    ]);

    return redirect()->route('siswa.index')->with('success', 'Data siswa dan akun berhasil ditambahkan.');
}


    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $siswa->update($request->all());

        $user = User::where('role', 'siswa')->where('related_id', $siswa->id)->first();
        if ($user) {
            $user->update([
                'username' => $request->username,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $user = User::where('role', 'siswa')->where('related_id', $siswa->id);
        $user->delete();
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa dan akun berhasil dihapus.');
    }
}
