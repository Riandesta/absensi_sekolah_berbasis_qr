<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Karyawan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AbsensiGuruKelas;
use App\Models\AbsensiSiswaKelas;
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

    /**
     * Display user profile page
     */
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
     * Update the user profile
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

        // Update employee data
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

    /**
     * Display attendance history
     */
    public function attendanceTrack(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'gerbang'); // gerbang atau kelas
        $period = $request->query('period', 'all');
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range based on selected period
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // Initialize query
        if ($type === 'gerbang') {
            // Query for gate attendance
            $query = AbsensiGerbang::where('related_id', $user->related_id);
        } else {
            // Query for class attendance
            $query = AbsensiGuruKelas::where('karyawan_id', $user->related_id);
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        // Load relationships
        if ($type === 'gerbang') {
            $query->with(['siswa', 'karyawan', 'scannedBy', 'jadwal']);
        } else {
            $query->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'kelas']);
        }

        // Sort by date
        $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        // Paginate
        $absensi = $query->paginate(10);

        // Get profile data
        $profileData = Karyawan::with('kelas', 'jurusan')->find($user->related_id);

        if (!$profileData) {
            return redirect()->route($user->role . '.dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('karyawan.attendance-history', compact(
            'absensi',
            'type',
            'period',
            'customStart',
            'customEnd',
            'startDate',
            'endDate',
            'profileData'
        ));
    }

    /**
     * Export attendance history to PDF
     */
    public function exportAttendancePdf(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type', 'gerbang');
        $period = $request->query('period', 'all');
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range based on selected period
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // Initialize query
        if ($type === 'gerbang') {
            // Query for gate attendance
            $query = AbsensiGerbang::where('related_id', $user->related_id);
        } else {
            // Query for class attendance
            $query = AbsensiGuruKelas::where('karyawan_id', $user->related_id);
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        // Load relationships
        if ($type === 'gerbang') {
            $query->with(['siswa', 'karyawan', 'scannedBy', 'jadwal']);
        } else {
            $query->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'kelas']);
        }

        // Sort by date
        $query->orderBy('tanggal', 'desc');

        // Get all records (no pagination)
        $absensi = $query->get();

        // Get profile data
        $karyawan = Karyawan::with('kelas', 'jurusan')->find($user->related_id);

        if (!$karyawan) {
            return redirect()->route($user->role . '.dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        // Select correct template based on type
        $viewName = $type === 'gerbang'
            ? 'exports.attendance-gate-pdf'
            : 'exports.attendance-class-pdf';

        // Generate PDF
        $pdf = PDF::loadView($viewName, [
            'absensi' => $absensi,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
            'karyawan' => $karyawan
        ]);

        // Set file name
        $fileName = 'absensi_' . $type . '_' . now()->format('Ymd_His') . '.pdf';

        // Return PDF for download
        return $pdf->download($fileName);
    }

    /**
     * Display teacher-specific reports
     */
    public function teacherReport(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses ditolak.');
        }

        $period = $request->query('period', 'monthly');
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range based on selected period
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // Query for gate attendance
        $gateQuery = AbsensiGerbang::where('related_id', $user->related_id);

        // Query for class attendance
        $classQuery = AbsensiGuruKelas::where('karyawan_id', $user->related_id);

        // Apply date filter
        if ($startDate && $endDate) {
            $gateQuery->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
            $classQuery->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        // Load relationships and sort
        $gateAttendance = $gateQuery->with(['scannedBy', 'jadwal'])->orderBy('tanggal', 'desc')->get();
        $classAttendance = $classQuery->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas'])
            ->orderBy('tanggal', 'desc')->get();

        // Group attendance by date
        $groupedAttendance = [];
        foreach ($gateAttendance as $gate) {
            $date = $gate->tanggal;
            if (!isset($groupedAttendance[$date])) {
                $groupedAttendance[$date] = [
                    'date' => Carbon::parse($date),
                    'gate' => null,
                    'classes' => []
                ];
            }
            $groupedAttendance[$date]['gate'] = $gate;
        }

        foreach ($classAttendance as $class) {
            $date = $class->tanggal;
            if (!isset($groupedAttendance[$date])) {
                $groupedAttendance[$date] = [
                    'date' => Carbon::parse($date),
                    'gate' => null,
                    'classes' => []
                ];
            }
            $groupedAttendance[$date]['classes'][] = $class;
        }

        // Sort by date (most recent first)
        krsort($groupedAttendance);

        // Statistics
        $stats = [
            'totalDays' => count($groupedAttendance),
            'gateComplete' => $gateAttendance->whereNotNull('waktu_scan_keluar')->count(),
            'gateIncomplete' => $gateAttendance->whereNull('waktu_scan_keluar')->count(),
            'classAttendance' => $classAttendance->count(),
            'totalClasses' => 0,
            'attendanceRate' => 0
        ];

        // Calculate total scheduled classes in the period
        if ($startDate && $endDate) {
            // Get all working days between dates
            $workingDays = [];
            $current = clone $startDate;
            while ($current <= $endDate) {
                if ($current->isWeekday()) { // Only weekdays (Monday to Friday)
                    $dayName = $this->getDayNameIndonesian($current->englishDayOfWeek);
                    $workingDays[] = $dayName;
                }
                $current->addDay();
            }

            // Count scheduled classes for those days
            $totalScheduled = \App\Models\Jadwal::whereHas('jadwalPelajaran', function($query) use ($user) {
                $query->where('guru_id', $user->related_id);
            })->whereIn('hari', $workingDays)->count();

            $stats['totalClasses'] = $totalScheduled;

            // Calculate attendance rate
            if ($totalScheduled > 0) {
                $stats['attendanceRate'] = round(($classAttendance->count() / $totalScheduled) * 100, 1);
            }
        }

        return view('karyawan.teacher-report', compact(
            'groupedAttendance',
            'stats',
            'karyawan',
            'period',
            'customStart',
            'customEnd',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export teacher report to PDF
     */
    public function exportTeacherPdf(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses ditolak.');
        }

        // Similar logic to teacherReport but formatted for PDF
        $period = $request->query('period', 'monthly');
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // Get attendance data
        $gateAttendance = AbsensiGerbang::where('related_id', $user->related_id);
        $classAttendance = AbsensiGuruKelas::where('karyawan_id', $user->related_id);

        if ($startDate && $endDate) {
            $gateAttendance->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
            $classAttendance->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        $gateAttendance = $gateAttendance->with(['scannedBy'])->orderBy('tanggal', 'desc')->get();
        $classAttendance = $classAttendance->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas'])
            ->orderBy('tanggal', 'desc')->get();

        // Generate PDF
        $pdf = PDF::loadView('exports.teacher-report-pdf', [
            'karyawan' => $karyawan,
            'gateAttendance' => $gateAttendance,
            'classAttendance' => $classAttendance,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        // File name
        $fileName = 'laporan_guru_' . $karyawan->nama_lengkap . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Display class attendance report (for wali kelas and kurikulum)
     */
    public function classReport(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses ditolak.');
        }

        // Check if wali kelas or kurikulum
        $isWaliKelas = !empty($karyawan->kelas_id);
        $isKurikulum = strtolower($karyawan->jabatan) === 'kurikulum';

        if (!$isWaliKelas && !$isKurikulum) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses terbatas untuk wali kelas dan kurikulum.');
        }

        // Filters
        $kelasId = $request->query('kelas_id', $isWaliKelas ? $karyawan->kelas_id : null);
        $jurusanId = $request->query('jurusan_id');
        $period = $request->query('period', 'monthly');
        $reportType = $request->query('report_type', 'gerbang'); // 'gerbang' atau 'kelas'
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range based on selected period
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // List of available classes for filter
        if ($isKurikulum) {
            $kelasList = Kelas::all();
            $jurusanList = \App\Models\Jurusan::all();
        } else {
            $kelasList = Kelas::where('id', $karyawan->kelas_id)->get();
            $jurusanList = collect([$karyawan->jurusan])->filter();
        }

        // Query
        if ($reportType === 'gerbang') {
            $query = AbsensiGerbang::query();

            // Join to get only students from selected classes
            $query->whereHas('siswa', function($q) use ($kelasId, $jurusanId) {
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
                if ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                }
            });

            // Load relationships
            $query->with(['siswa.kelas', 'siswa.jurusan']);
        } else {
            $query = AbsensiSiswaKelas::query();

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($jurusanId) {
                $query->whereHas('siswa', function($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                });
            }

            // Load relationships
            $query->with(['siswa.kelas', 'siswa.jurusan', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        // Sort and execute query
        $absensi = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();

        // Group by student for statistics
        $groupedBySiswa = $absensi->groupBy(function ($item) {
            return $item->siswa->id ?? 'unknown';
        });

        // Calculate statistics
        $stats = [
            'totalStudents' => $groupedBySiswa->count(),
            'totalRecords' => $absensi->count(),
        ];

        if ($reportType === 'gerbang') {
            $stats['completeAttendance'] = $absensi->whereNotNull('waktu_scan_keluar')->count();
            $stats['incompleteAttendance'] = $absensi->whereNull('waktu_scan_keluar')->count();
        } else {
            $stats['presentCount'] = $absensi->where('status', 'Hadir')->count();
            $stats['permitCount'] = $absensi->where('status', 'Izin')->count();
            $stats['sickCount'] = $absensi->where('status', 'Sakit')->count();
            $stats['absentCount'] = $absensi->where('status', 'Alpa')->count();

            // Calculate attendance rate
            if ($absensi->count() > 0) {
                $stats['attendanceRate'] = round(($stats['presentCount'] / $absensi->count()) * 100, 1);
            } else {
                $stats['attendanceRate'] = 0;
            }
        }

        // Selected class information
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        $selectedJurusan = $jurusanId ? \App\Models\Jurusan::find($jurusanId) : null;

        return view('karyawan.class-report', compact(
            'absensi',
            'groupedBySiswa',
            'stats',
            'reportType',
            'period',
            'startDate',
            'endDate',
            'kelasList',
            'jurusanList',
            'kelasId',
            'jurusanId',
            'selectedKelas',
            'selectedJurusan',
            'customStart',
            'customEnd',
            'isWaliKelas',
            'isKurikulum'
        ));
    }

    /**
     * Export class report to PDF
     */
    public function exportClassReportPdf(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses ditolak.');
        }

        // Check if wali kelas or kurikulum
        $isWaliKelas = !empty($karyawan->kelas_id);
        $isKurikulum = strtolower($karyawan->jabatan) === 'kurikulum';

        if (!$isWaliKelas && !$isKurikulum) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses terbatas untuk wali kelas dan kurikulum.');
        }

        // Filters
        $kelasId = $request->query('kelas_id', $isWaliKelas ? $karyawan->kelas_id : null);
        $jurusanId = $request->query('jurusan_id');
        $period = $request->query('period', 'monthly');
        $reportType = $request->query('report_type', 'gerbang');
        $customStart = $request->query('start_date', null);
        $customEnd = $request->query('end_date', null);

        // Get date range based on selected period
        $dates = $this->getDateRange($period, $customStart, $customEnd);
        $startDate = $dates['start_date'];
        $endDate = $dates['end_date'];

        // Query
        if ($reportType === 'gerbang') {
            $query = AbsensiGerbang::query();

            // Join to get only students from selected classes
            $query->whereHas('siswa', function($q) use ($kelasId, $jurusanId) {
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
                if ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                }
            });

            // Load relationships
            $query->with(['siswa.kelas', 'siswa.jurusan']);
        } else {
            $query = AbsensiSiswaKelas::query();

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($jurusanId) {
                $query->whereHas('siswa', function($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId);
                });
            }

            // Load relationships
            $query->with(['siswa.kelas', 'siswa.jurusan', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);
        }

        // Apply date filter
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        // Sort and execute query
        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Group by student for statistics
        $groupedBySiswa = $absensi->groupBy(function ($item) {
            return $item->siswa->id ?? 'unknown';
        });

        // Calculate statistics
        $stats = [
            'totalStudents' => $groupedBySiswa->count(),
            'totalRecords' => $absensi->count(),
        ];

        if ($reportType === 'gerbang') {
            $stats['completeAttendance'] = $absensi->whereNotNull('waktu_scan_keluar')->count();
            $stats['incompleteAttendance'] = $absensi->whereNull('waktu_scan_keluar')->count();
        } else {
            $stats['presentCount'] = $absensi->where('status', 'Hadir')->count();
            $stats['permitCount'] = $absensi->where('status', 'Izin')->count();
            $stats['sickCount'] = $absensi->where('status', 'Sakit')->count();
            $stats['absentCount'] = $absensi->where('status', 'Alpa')->count();

            // Calculate attendance rate
            if ($absensi->count() > 0) {
                $stats['attendanceRate'] = round(($stats['presentCount'] / $absensi->count()) * 100, 1);
            } else {
                $stats['attendanceRate'] = 0;
            }
        }

        // Selected class information
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        $selectedJurusan = $jurusanId ? \App\Models\Jurusan::find($jurusanId) : null;

        // Select correct template
        $viewName = $reportType === 'gerbang'
            ? 'exports.class-gate-report-pdf'
            : 'exports.class-attendance-report-pdf';

        // Generate PDF
        $pdf = PDF::loadView($viewName, [
            'absensi' => $absensi,
            'groupedBySiswa' => $groupedBySiswa,
            'stats' => $stats,
            'reportType' => $reportType,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedKelas' => $selectedKelas,
            'selectedJurusan' => $selectedJurusan,
            'karyawan' => $karyawan
        ]);

        // Set file name
        $fileName = 'laporan_absensi_' . ($reportType === 'gerbang' ? 'gerbang' : 'kelas');
        if ($selectedKelas) {
            $fileName .= '_' . str_replace(' ', '_', $selectedKelas->nama_kelas);
        }
        $fileName .= '_' . now()->format('Ymd_His') . '.pdf';

        // Return PDF for download
        return $pdf->download($fileName);
    }

    /**
     * Get date range based on selected period
     */
    private function getDateRange($period, $startDateStr = null, $endDateStr = null)
    {
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'daily':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'semester':
                $currentMonth = Carbon::now()->month;
                if ($currentMonth <= 6) {
                    // First semester (January-June)
                    $startDate = Carbon::create(Carbon::now()->year, 1, 1);
                    $endDate = Carbon::create(Carbon::now()->year, 6, 30);
                } else {
                    // Second semester (July-December)
                    $startDate = Carbon::create(Carbon::now()->year, 7, 1);
                    $endDate = Carbon::create(Carbon::now()->year, 12, 31);
                }
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($startDateStr) {
                    $startDate = Carbon::parse($startDateStr);
                }
                if ($endDateStr) {
                    $endDate = Carbon::parse($endDateStr);
                }
                break;
            default: // 'all' or other value
                // Don't apply date filter
                break;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    /**
     * Get Indonesian day name
     */
    private function getDayNameIndonesian($englishDay)
    {
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        return $dayMapping[$englishDay] ?? $englishDay;
    }
}
