<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $siswa = Siswa::with('kelas', 'jurusan', 'tahunAjaran')->latest()->paginate(10);
        return view('siswa.index', compact('siswa'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();

        return view('siswa.create', compact('kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'nis' => 'required|unique:siswa',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required',
            'jurusan_id' => 'required',
            'tahun_ajaran_id' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('foto/siswa', 'public'); // Save to storage/app/public/foto/siswa
        }

        // Save student data
        $siswa = Siswa::create($request->except(['username', 'password', 'foto']) + [
            'foto' => $fotoPath,
        ]);

        // Create related user account
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'related_id' => $siswa->id,
            'status' => 'aktif',
        ]);

        return redirect()->route('siswa.index')->with('success', 'Data siswa dan akun berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();

        return view('siswa.edit', compact('siswa', 'kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        // Validate input
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required',
            'jurusan_id' => 'required',
            'tahun_ajaran_id' => 'required',
            'username' => 'nullable|min:6', // Username is optional
            'password' => 'nullable|min:6', // Password is optional
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }

            // Save new photo
            $fotoPath = $request->file('foto')->store('foto/siswa', 'public');
        } else {
            // Keep old photo if no new file is uploaded
            $fotoPath = $siswa->foto;
        }

        // Update student data
        $siswa->update($request->except(['username', 'password', 'foto']) + [
            'foto' => $fotoPath,
        ]);

        // Update related user account
        $user = User::where('role', 'siswa')->where('related_id', $siswa->id)->first();
        if ($user) {
            $userData = [
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ];

            // Update username if provided
            if ($request->filled('username')) {
                $userData['username'] = $request->username;
            }

            $user->update($userData);
        }

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Siswa $siswa)
    {
        // Delete student photo if exists
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        // Delete related user account
        $user = User::where('role', 'siswa')->where('related_id', $siswa->id)->first();
        if ($user) {
            $user->delete();
        }

        // Delete student data
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Data siswa dan akun berhasil dihapus.');
    }

    /**
     * Download QR code as a PDF document for the specified student.
     */
    public function downloadQrCode(Siswa $siswa)
    {
        try {
            // Generate QR code content
            $qrContent = json_encode([
                'id' => $siswa->id,
                'nis' => $siswa->nis,
                'nama' => $siswa->nama_lengkap
            ]);

            // Generate SVG QR code optimized for mobile devices
            $qrSvg = QrCode::format('svg')
                ->size(300)     // Optimal size for mobile screens
                ->margin(1)     // Smaller margin to maximize QR size
                ->errorCorrection('H')  // High error correction for better scanning
                ->generate($qrContent);

            $qrBase64 = base64_encode($qrSvg);

            // Student data
            $siswaData = [
                'nama' => $siswa->nama_lengkap,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas->nama_kelas ?? '',
                'qrBase64' => $qrBase64
            ];

            // Generate PDF with mobile-friendly QR code template
            $pdf = Pdf::loadView('siswa.id-card', compact('siswaData'));

            // Use smaller paper size more suitable for mobile (A6 is half of A5)
            $pdf->setPaper('a6');

            // Set options for better rendering on mobile devices
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            // Return PDF download
            return $pdf->download('qr-code-' . $siswa->nama_lengkap . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generating QR code PDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat QR Code PDF: ' . $e->getMessage());
        }
    }
}
