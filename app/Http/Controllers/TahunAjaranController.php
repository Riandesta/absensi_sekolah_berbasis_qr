<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $perPage = request('per_page', 10);
        $tahunAjaran = TahunAjaran::paginate($perPage);
        return view('tahun-ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_awal' => [
                'required',
                'digits:4',
                'integer',
                'min:2000',
                'max:' . date('Y'),
            ],
            'tahun_akhir' => [
                'required',
                'digits:4',
                'integer',
                'gt:tahun_awal',
                'max:' . (date('Y') + 1),
            ],
            'semester' => [
                'required',
                'string',
                'in:Ganjil,Genap',
            ],
        ], [
            'tahun_awal.required' => 'Tahun awal wajib diisi.',
            'tahun_awal.digits' => 'Tahun awal harus terdiri dari 4 digit.',
            'tahun_awal.integer' => 'Tahun awal harus berupa angka.',
            'tahun_awal.min' => 'Tahun awal minimal adalah 2000.',
            'tahun_awal.max' => 'Tahun awal tidak boleh lebih dari tahun ini.',

            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.digits' => 'Tahun akhir harus terdiri dari 4 digit.',
            'tahun_akhir.integer' => 'Tahun akhir harus berupa angka.',
            'tahun_akhir.gt' => 'Tahun akhir harus lebih besar dari tahun awal.',
            'tahun_akhir.max' => 'Tahun akhir tidak boleh lebih dari tahun depan.',

            'semester.required' => 'Semester wajib diisi.',
            'semester.in' => 'Semester hanya boleh "Ganjil" atau "Genap".',
        ]);

        // Handle checkbox 'is_aktif' supaya default false jika tidak dicentang
        $isAktif = $request->has('is_aktif');

        $exists = TahunAjaran::where('tahun_awal', $validated['tahun_awal'])
            ->where('tahun_akhir', $validated['tahun_akhir'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'tahun_awal' => 'Kombinasi tahun ajaran dan semester sudah ada.',
            ])->withInput();
        }

        TahunAjaran::create([
            'tahun_awal' => $validated['tahun_awal'],
            'tahun_akhir' => $validated['tahun_akhir'],
            'semester' => $validated['semester'],
            'is_aktif' => $isAktif,
        ]);

        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'tahun_awal' => [
                'required',
                'digits:4',
                'integer',
                'min:2000',
                'max:' . date('Y'),
            ],
            'tahun_akhir' => [
                'required',
                'digits:4',
                'integer',
                'gt:tahun_awal',
                'max:' . (date('Y') + 1),
            ],
            'semester' => [
                'required',
                'string',
                'in:Ganjil,Genap',
            ],
        ], [
            'tahun_awal.required' => 'Tahun awal wajib diisi.',
            'tahun_awal.digits' => 'Tahun awal harus terdiri dari 4 digit.',
            'tahun_awal.integer' => 'Tahun awal harus berupa angka.',
            'tahun_awal.min' => 'Tahun awal minimal adalah 2000.',
            'tahun_awal.max' => 'Tahun awal tidak boleh lebih dari tahun ini.',

            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.digits' => 'Tahun akhir harus terdiri dari 4 digit.',
            'tahun_akhir.integer' => 'Tahun akhir harus berupa angka.',
            'tahun_akhir.gt' => 'Tahun akhir harus lebih besar dari tahun awal.',
            'tahun_akhir.max' => 'Tahun akhir tidak boleh lebih dari tahun depan.',

            'semester.required' => 'Semester wajib diisi.',
            'semester.in' => 'Semester hanya boleh "Ganjil" atau "Genap".',
        ]);

        $isAktif = $request->has('is_aktif');

        $exists = TahunAjaran::where('id', '!=', $tahunAjaran->id)
            ->where('tahun_awal', $validated['tahun_awal'])
            ->where('tahun_akhir', $validated['tahun_akhir'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'tahun_awal' => 'Kombinasi tahun ajaran dan semester sudah ada.',
            ])->withInput();
        }

        $tahunAjaran->update([
            'tahun_awal' => $validated['tahun_awal'],
            'tahun_akhir' => $validated['tahun_akhir'],
            'semester' => $validated['semester'],
            'is_aktif' => $isAktif,
        ]);

        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
