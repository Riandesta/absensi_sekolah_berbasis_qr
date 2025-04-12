<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = MataPelajaran::paginate(10);
        return view('mata-pelajaran.index', compact('mataPelajaran'));
    }

    public function create()
    {
        return view('mata-pelajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|max:255',
            'kode_mapel' => 'required|max:10',
        ]);

        MataPelajaran::create($request->all());
        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = MataPelajaran::findOrFail($id);
        return view('mata-pelajaran.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|max:255',
            'kode_mapel' => 'required|max:10',
        ]);

        $item = MataPelajaran::findOrFail($id);
        $item->update($request->all());
        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = MataPelajaran::findOrFail($id);
        $item->delete();
        return redirect()->route('mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil dihapus');
    }
}
