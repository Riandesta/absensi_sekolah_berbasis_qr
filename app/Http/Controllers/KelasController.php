<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('admin.manage_classes', compact('kelas'));
    }

    public function create()
    {
        return view('admin.create_class');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required',
        ]);

        Kelas::create($request->all());
        return redirect()->route('admin.classes.index')->with('success', 'Kelas created successfully.');
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.edit_class', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required',
        ]);

        $kelas->update($request->all());
        return redirect()->route('admin.classes.index')->with('success', 'Kelas updated successfully.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Kelas deleted successfully.');
    }
}
