<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Karyawan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KaryawanController extends Controller
{

       /**
     * Display the dashboard for the karyawan.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = Auth::user();
        $karyawan = null;

        if ($user->role === 'karyawan' && $user->related_id) {
            $karyawan = Karyawan::find($user->related_id);
        }

        return view('karyawan.dashboard', compact('karyawan'));
    }
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $karyawan = Karyawan::with(['kelas', 'jurusan', 'tahunAjaran'])->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();
        return view('karyawan.create', compact('kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Store a newly created employee in database.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan,nip',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|regex:/^08[0-9]{9,}$/',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'username' => 'required|string|min:4|max:255|unique:users,username',
            'password' => 'required|string|min:6|max:255',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('karyawan/foto', 'public');
            $validated['foto'] = $fotoPath;
        }

        // Save employee data
        try {
            $karyawan = Karyawan::create($validated);

            // Create related user account with role "karyawan"
            User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'karyawan', // Role otomatis "karyawan"
                'related_id' => $karyawan->id,
                'status' => 'aktif',
            ]);

            // Generate QR Code
            $this->generateQrCode($karyawan);

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data karyawan.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Karyawan $karyawan)
    {
        $kelas = \App\Models\Kelas::all();
        $jurusan = \App\Models\Jurusan::all();
        $tahunAjaran = \App\Models\TahunAjaran::all();
        return view('karyawan.edit', compact('karyawan', 'kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Update the specified employee in database.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // Validate input
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan,nip,' . $karyawan->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|regex:/^08[0-9]{9,}$/',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'username' => 'nullable|string|min:6|max:255',
            'password' => 'nullable|string|min:6|max:255',
        ]);

        try {
            // Handle new photo upload if available
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                    Storage::disk('public')->delete($karyawan->foto);
                }
                $fotoPath = $request->file('foto')->store('karyawan/foto', 'public');
                $validated['foto'] = $fotoPath;
            }

            // Update password only if filled
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update employee data
            $karyawan->update($validated);

            // Update related user account
            $user = User::where('role', 'karyawan')->where('related_id', $karyawan->id)->first();
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

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data karyawan.')->withInput();
        }
    }

    /**
     * Remove the specified employee from database.
     */
    public function destroy(Karyawan $karyawan)
    {
        try {
            // Delete photo if exists
            if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            // Delete QR code if exists
            if ($karyawan->qr_code) {
                $qrPath = str_replace('storage/', '', $karyawan->qr_code);
                if (Storage::disk('public')->exists($qrPath)) {
                    Storage::disk('public')->delete($qrPath);
                }
            }

            // Delete related user account
            $user = User::where('role', 'karyawan')->where('related_id', $karyawan->id)->first();
            if ($user) {
                $user->delete();
            }

            // Delete employee data
            $karyawan->delete();

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan dan akun berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data karyawan.');
        }
    }

    /**
     * Download QR code as a PDF document for the specified employee.
     */
    public function downloadQrCode(Karyawan $karyawan)
    {
        try {
            // Generate QR code content
            $qrContent = json_encode([
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama_lengkap
            ]);

            // Generate SVG QR code optimized for mobile devices
            $qrSvg = QrCode::format('svg')
                ->size(300)     // Optimal size for mobile screens
                ->margin(1)     // Smaller margin to maximize QR size
                ->errorCorrection('H')  // High error correction for better scanning
                ->generate($qrContent);

            $qrBase64 = base64_encode($qrSvg);

            // Employee data
            $karyawanData = [
                'nama' => $karyawan->nama_lengkap,
                'nip' => $karyawan->nip,
                'jabatan' => $karyawan->jabatan ?? '',
                'qrBase64' => $qrBase64
            ];

            // Generate PDF with mobile-friendly QR code template
            $pdf = Pdf::loadView('karyawan.id-card', compact('karyawanData'));

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
            return $pdf->download('qr-code-' . $karyawan->nama_lengkap . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating QR code PDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat QR Code PDF: ' . $e->getMessage());
        }
    }

    /**
     * View QR code directly in browser for the specified employee.
     */
    public function downloadQrCodeOnly(Karyawan $karyawan)
    {
        try {
            // Generate QR code content
            $qrContent = json_encode([
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama_lengkap
            ]);

            // Generate SVG QR code optimized for mobile devices
            $qrSvg = QrCode::format('svg')
                ->size(300)     // Optimal size for mobile screens
                ->margin(1)     // Smaller margin to maximize QR size
                ->errorCorrection('H')  // High error correction for better scanning
                ->generate($qrContent);

            // Display SVG directly in browser with proper headers for mobile
            return response($qrSvg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'inline; filename="qr-code-' . $karyawan->nama_lengkap . '.svg"');
        } catch (\Exception $e) {
            Log::error('Error displaying QR code: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menampilkan QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code for the specified employee.
     */
    private function generateQrCode(Karyawan $karyawan)
    {
        // Ensure directory exists
        if (!Storage::disk('public')->exists('qr-codes')) {
            Storage::disk('public')->makeDirectory('qr-codes');
        }

        // Create unique file name for QR Code
        $fileName = 'qr-code-' . $karyawan->id . '.svg';

        // Save QR Code to storage
        $path = storage_path('app/public/qr-codes/' . $fileName);
        QrCode::size(200)->generate(json_encode([
            'id' => $karyawan->id,
            'nip' => $karyawan->nip,
            'nama' => $karyawan->nama_lengkap
        ]), $path);

        // Save path to database
        $karyawan->update([
            'qr_code' => 'storage/qr-codes/' . $fileName,
        ]);
    }

    public function laporanKaryawan(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        return redirect()->route('admin.dashboard')
            ->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini.');
    }

    $query = AbsensiGerbang::query();

    if ($request->has('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    }

    $absensiKaryawan = $query->with(['karyawan', 'scannedBy', 'jadwal'])
        ->orderBy('tanggal', 'desc')
        ->paginate(10);

    return view('absensi-gerbang.laporan-karyawan', compact('absensiKaryawan'));
}

public function profile()
{
    $user = Auth::user();
    $karyawan = Karyawan::with('kelas', 'jurusan', 'tahunAjaran')->find($user->related_id);

    if (!$karyawan) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
    }

    return view('karyawan.profile', compact('karyawan', 'user'));
}

/**
 * Update the student profile
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();
    $karyawan = Karyawan::find($user->related_id);

    if (!$karyawan) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
    }

    // Validate input
    $request->validate([
        'nama_lengkap' => 'required',
        'jenis_kelamin' => 'required',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date',
        'no_wa' => 'nullable|string|max:15',
        'username' => 'required|unique:users,username,' . $user->id,
        'password' => 'nullable|min:6|confirmed',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
    ]);

    // Handle photo upload
    if ($request->hasFile('foto')) {
        // Delete old photo if exists
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        // Save new photo
        $fotoPath = $request->file('foto')->store('foto/karyawan', 'public');
    } else {
        // Keep old photo if no new file is uploaded
        $fotoPath = $karyawan->foto;
    }

    // Update student data
    $karyawan->update([
        'nama_lengkap' => $request->nama_lengkap,
        'jenis_kelamin' => $request->jenis_kelamin,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'no_wa' => $request->no_wa,
        'email' => $request->email,
        'foto' => $fotoPath,
    ]);

    // Update related user account
    $userData = [
        'username' => $request->username
    ];

    // Update password if provided
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }

    $user->update($userData);

    return redirect()->route('karyawan.profile')->with('success', 'Profil berhasil diperbarui.');
}
}
