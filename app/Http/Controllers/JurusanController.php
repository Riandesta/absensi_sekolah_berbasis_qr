<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    // Menampilkan semua data jurusan
    public function index(Request $request)
    {
        // Mengambil query pencarian dari input
        $query = $request->input('search');

        // Query data jurusan dengan filter pencarian
        $jurusan = Jurusan::when($query, function ($q) use ($query) {
            $q->where('nama_jurusan', 'like', '%' . $query . '%')
              ->orWhere('kode_jurusan', 'like', '%' . $query . '%');
        })->paginate(10);

        return view('jurusan.index', compact('jurusan'));
    }

    // Menampilkan form tambah jurusan
    public function create()
    {
        return view('jurusan.create');
    }

    // Menyimpan data jurusan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jurusan' => 'required|string|max:255',
            'kode_jurusan' => 'required|string|max:50|unique:jurusan',
        ]);

        Jurusan::create($validated);
        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    // Menampilkan form edit jurusan
    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('jurusan.edit', compact('jurusan'));
    }

    // Memperbarui data jurusan
    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $validated = $request->validate([
            'nama_jurusan' => 'required|string|max:255',
            'kode_jurusan' => 'required|string|max:50|unique:jurusan,kode_jurusan,' . $id,
        ]);

        $jurusan->update($validated);
        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    // Menghapus data jurusan
    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();
        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}
