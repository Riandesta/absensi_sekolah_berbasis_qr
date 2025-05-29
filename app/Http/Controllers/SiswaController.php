<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $siswa = null;

        if ($user->role === 'siswa' && $user->related_id) {
            $siswa = Siswa::find($user->related_id);
        }

        return view('siswa.dashboard', compact('siswa'));
    }
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

    public function downloadMyQrCode()
    {
        $user = Auth::user();
        $siswa = Siswa::find($user->related_id);

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        return $this->downloadQrCode($siswa);
    }

    // Add these methods to the SiswaController class

/**
 * Show the student profile form
 */
public function profile()
{
    $user = Auth::user();
    $siswa = Siswa::with('kelas', 'jurusan', 'tahunAjaran')->find($user->related_id);

    if (!$siswa) {
        return redirect()->route('siswa.dashboard')->with('error', 'Profil tidak ditemukan.');
    }

    return view('siswa.profile', compact('siswa', 'user'));
}

/**
 * Update the student profile
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();
    $siswa = Siswa::find($user->related_id);

    if (!$siswa) {
        return redirect()->route('siswa.dashboard')->with('error', 'Profil tidak ditemukan.');
    }

    // Validate input
    $request->validate([
        'nama_lengkap' => 'required',
        'jenis_kelamin' => 'required',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date',
        'alamat' => 'nullable|string',
        'no_telp' => 'nullable|string|max:15',
        'email' => 'nullable|email|max:100',
        'username' => 'required|unique:users,username,' . $user->id,
        'password' => 'nullable|min:6|confirmed',
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
    $siswa->update([
        'nama_lengkap' => $request->nama_lengkap,
        'jenis_kelamin' => $request->jenis_kelamin,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'alamat' => $request->alamat,
        'no_telp' => $request->no_telp,
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

    return redirect()->route('siswa.profile')->with('success', 'Profil berhasil diperbarui.');
}

/**
 * Show attendance history for the logged-in student
 */
public function attendanceHistory(Request $request)
{
    $user = Auth::user();
    $siswa = Siswa::find($user->related_id);

    if (!$siswa) {
        return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
    }

    $type = $request->input('type', 'gerbang'); // Default to gerbang
    $period = $request->input('period', 'all'); // Default to all
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');

    // Set dates based on the period
    $startDate = null;
    $endDate = null;

    switch ($period) {
        case 'daily':
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            break;
        case 'weekly':
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            break;
        case 'monthly':
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            break;
        case 'semester':
            // Assuming first semester is July to December, second is January to June
            if (now()->month >= 7) {
                $startDate = now()->year . '-07-01';
                $endDate = now()->year . '-12-31';
            } else {
                $startDate = now()->year . '-01-01';
                $endDate = now()->year . '-06-30';
            }
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            break;
        case 'yearly':
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
            break;
        case 'custom':
            if ($customStart && $customEnd) {
                $startDate = Carbon::parse($customStart)->startOfDay();
                $endDate = Carbon::parse($customEnd)->endOfDay();
            }
            break;
        default:
            $startDate = null;
            $endDate = null;
    }

    if ($type === 'gerbang') {
        $query = \App\Models\AbsensiGerbang::where('related_id', $siswa->id);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('siswa.riwayat-absensi-persiswa', compact('siswa', 'absensi', 'type', 'period', 'customStart', 'customEnd'));
    } else {
        $query = \App\Models\AbsensiSiswaKelas::where('siswa_id', $siswa->id)
            ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('siswa.riwayat-absensi-persiswa', compact('siswa', 'absensi', 'type', 'period', 'customStart', 'customEnd'));
    }
}

/**
 * Export attendance history as PDF
 */
public function exportAttendancePdf(Request $request)
{
    $user = Auth::user();
    $siswa = Siswa::with('kelas', 'jurusan', 'tahunAjaran')->find($user->related_id);

    if (!$siswa) {
        return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
    }

    $type = $request->input('type', 'gerbang'); // Default to gerbang
    $period = $request->input('period', 'all'); // Default to all
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');

    // Set dates based on the period (same logic as in attendanceHistory method)
    $startDate = null;
    $endDate = null;

    switch ($period) {
        case 'daily':
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            break;
        case 'weekly':
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            break;
        case 'monthly':
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            break;
        case 'semester':
            if (now()->month >= 7) {
                $startDate = now()->year . '-07-01';
                $endDate = now()->year . '-12-31';
            } else {
                $startDate = now()->year . '-01-01';
                $endDate = now()->year . '-06-30';
            }
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            break;
        case 'yearly':
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
            break;
        case 'custom':
            if ($customStart && $customEnd) {
                $startDate = Carbon::parse($customStart)->startOfDay();
                $endDate = Carbon::parse($customEnd)->endOfDay();
            }
            break;
        default:
            $startDate = null;
            $endDate = null;
    }

    // Get attendance data based on type
    if ($type === 'gerbang') {
        $query = \App\Models\AbsensiGerbang::where('related_id', $siswa->id);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Generate PDF
        $data = [
            'siswa' => $siswa,
            'absensi' => $absensi,
            'type' => $type,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $pdf = Pdf::loadView('siswa.exportKelas-perSiswa', $data);

    } else {
        $query = \App\Models\AbsensiSiswaKelas::where('siswa_id', $siswa->id)
            ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Generate PDF
        $data = [
            'siswa' => $siswa,
            'absensi' => $absensi,
            'type' => $type,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $pdf = Pdf::loadView('siswa.exportGerbang-perSiswa', $data);
    }

    // Format period name for file name
    $periodNames = [
        'daily' => 'Harian',
        'weekly' => 'Mingguan',
        'monthly' => 'Bulanan',
        'semester' => 'Semester',
        'yearly' => 'Tahunan',
        'custom' => 'Custom',
        'all' => 'Semua'
    ];

    $typeNames = [
        'gerbang' => 'Gerbang',
        'kelas' => 'Kelas'
    ];

    $fileName = 'Absensi_' . $typeNames[$type] . '_' . $periodNames[$period] . '_' . $siswa->nama_lengkap . '.pdf';

    return $pdf->download($fileName);
}
}
