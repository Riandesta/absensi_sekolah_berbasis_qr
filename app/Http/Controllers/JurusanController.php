<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JurusanController extends Controller
{
    // Menampilkan semua data jurusan
    public function index(Request $request)
    {
        try {
            // Mengambil query pencarian dari input
            $query = $request->input('search');

            // Query data jurusan dengan filter pencarian
            $jurusan = Jurusan::when($query, function ($q) use ($query) {
                $q->where('nama_jurusan', 'like', '%' . $query . '%')
                  ->orWhere('kode_jurusan', 'like', '%' . $query . '%');
            })->paginate(10);

            return view('jurusan.index', compact('jurusan'));
        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah saat mengambil data
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data jurusan: ' . $e->getMessage());
        }
    }

    // Menampilkan form tambah jurusan
    public function create()
    {
        return view('jurusan.create');
    }

    // Menyimpan data jurusan baru
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nama_jurusan' => 'required|string|max:255',
                'kode_jurusan' => 'required|string|max:50|unique:jurusan',
            ]);

            // Simpan data jurusan
            Jurusan::create($validated);
            return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi input
            return redirect()->back()
                ->withErrors(['create' => $e->errors()]) // Kirim error ke modal tambah
                ->withInput();
        } catch (\Exception $e) {
            // Tangani error umum
            return redirect()->back()->with('error', 'Gagal menambahkan jurusan: ' . $e->getMessage());
        }
    }





    // Menampilkan form edit jurusan
    public function edit($id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            return view('jurusan.edit', compact('jurusan'));
        } catch (\Exception $e) {
            // Tangani error jika jurusan tidak ditemukan
            return redirect()->route('jurusan.index')->with('error', 'Data jurusan tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Memperbarui data jurusan
    public function update(Request $request, $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'nama_jurusan' => 'required|string|max:255',
                'kode_jurusan' => 'required|string|max:50|unique:jurusan,kode_jurusan,' . $id,
            ]);

            // Perbarui data jurusan
            $jurusan->update($validated);
            return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi input
            return redirect()->back()->withErrors(['edit_' . $id => $e->errors()])->withInput();
        } catch (\Exception $e) {
            // Tangani error umum
            return redirect()->back()->with('error', 'Gagal memperbarui jurusan: ' . $e->getMessage());
        }
    }

    // Menghapus data jurusan
    public function destroy($id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            $jurusan->delete();
            return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            // Tangani error jika jurusan tidak ditemukan atau gagal dihapus
            return redirect()->route('jurusan.index')->with('error', 'Gagal menghapus jurusan: ' . $e->getMessage());
        }
    }
}
