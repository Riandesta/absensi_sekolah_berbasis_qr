<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelasController extends Controller
{
    // Menampilkan daftar kelas
    public function index()
    {
        // Menggunakan relasi yang benar untuk user
        $kelas = Kelas::with(['jurusan', 'user'])->paginate(10); // 'user' adalah relasi yang valid
        return view('kelas.index', compact('kelas'));
    }

    // Menampilkan form tambah kelas
    public function create()
    {
        $jurusan = Jurusan::all();
        return view('kelas.create', compact('jurusan'));
    }

    // Menyimpan data kelas baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan_id' => 'required|exists:jurusan,id',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ],
        [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.string' => 'Nama kelas harus berupa teks.',
            'nama_kelas.max' => 'Nama kelas tidak boleh lebih dari 255 karakter.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
            'tingkat.in' => 'Tingkat kelas harus di antara X, XI, atau XII.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password harus minimal 6 karakter.',
        ]);

        // Menyimpan data kelas
        $kelas = new Kelas();
        $kelas->nama_kelas = $request->nama_kelas;
        $kelas->jurusan_id = $request->jurusan_id;
        $kelas->tingkat = $request->tingkat;
        $kelas->save();

        // Menyimpan data user terkait
        $user = new User();
        $user->role = 'kelas';
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->related_id = $kelas->id; // Mengaitkan user ke kelas yang baru saja disimpan
        $user->save();

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    // Menampilkan form edit kelas
    public function edit(Kelas $kelas)
    {
        $jurusan = Jurusan::all();
        return view('kelas.edit', compact('kelas', 'jurusan'));
    }

    // Memperbarui data kelas
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan_id' => 'required|exists:jurusan,id',
            'username' => 'required|string|unique:users,username,' . $kelas->user->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Update akun user jika ada
        $user = $kelas->user;
        if ($user) {
            $user->update([
                'username' => $validated['username'],
                'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
            ]);
        }

        // Update data kelas
        $kelas->update([
            'nama_kelas' => $validated['nama_kelas'],
            'tingkat' => $validated['tingkat'],
            'jurusan_id' => $validated['jurusan_id'],
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    // Menghapus data kelas
    public function destroy(Kelas $kelas)
    {
        // Hapus user akun kelas jika ada
        if ($kelas->user) {
            $kelas->user->delete();
        }

        // Hapus kelas
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
