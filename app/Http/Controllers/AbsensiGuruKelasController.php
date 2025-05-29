<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\JadwalPelajaran;
use App\Models\AbsensiGuruKelas;
use App\Exports\AbsensiGuruExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AbsensiGerbang; // Add this import

class AbsensiGuruKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch all classes for the filter dropdown
        $kelasList = Kelas::all();

        // Initialize the query
        $query = AbsensiGuruKelas::query();

        // Filter by date if provided
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter by class if provided
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by report type if provided
        if ($request->has('report_type')) {
            switch ($request->report_type) {
                case 'daily':
                    $query->whereDate('tanggal', now()->startOfDay());
                    break;
                case 'monthly':
                    $query->whereMonth('tanggal', now()->month);
                    break;
                case 'semester':
                    $semester = now()->month <= 6 ? 1 : 2;
                    $query->whereBetween('tanggal', [
                        now()->startOfYear()->addMonths(($semester - 1) * 6),
                        now()->startOfYear()->addMonths($semester * 6)->subDay(),
                    ]);
                    break;
                case 'yearly':
                    $query->whereYear('tanggal', now()->year);
                    break;
            }
        }

        // Paginate the results
        $absensiGuru = $query->with(['karyawan', 'kelas', 'jadwal', 'scanByUser'])->paginate(10);

        // Return the view with the data
        return view('karyawan.absensi-guru-kelas.index', compact('absensiGuru', 'kelasList'));
    }


    public function export(Request $request)
    {
        $tanggal = $request->query('tanggal');
        $kelas_id = $request->query('kelas_id');
        $report_type = $request->query('report_type');

        return (new AbsensiGuruExport($tanggal, $kelas_id, $report_type))->download('absensi_guru_kelas.xlsx');
    }

    /**
     * Show the form for scanning QR Code.
     */
    public function scan()
    {
        // Hanya user dengan role kelas atau admin yang bisa melakukan scan
        if (Auth::user()->role !== 'kelas' && Auth::user()->role !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses untuk melakukan absensi guru.');
        }

        // Inisialisasi variabel
        $kelas = null;
        $jadwal = null;
        $kelasList = null;
        $hariIni = $this->getHariIni();

        // Untuk user dengan role kelas, ambil informasi kelas dari related_id
        if (Auth::user()->role === 'kelas') {
            $kelas = Kelas::find(Auth::user()->related_id);
            if (!$kelas) {
                return redirect()->route('admin.dashboard')->with('error', 'Data kelas tidak ditemukan.');
            }

            // Ambil jadwal pelajaran untuk kelas ini dan hari ini
            $jadwal = Jadwal::with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
                ->where('kelas_id', $kelas->id)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
        }

        // Untuk admin, siapkan daftar kelas
        if (Auth::user()->role === 'admin') {
            $kelasList = Kelas::all();
        }

        return view('absensi-guru-kelas.scan', compact('kelas', 'jadwal', 'kelasList', 'hariIni'));
    }

    /**
     * Load jadwal based on selected class (for admin users)
     */
    public function loadJadwal(Request $request)
    {
        // Validasi input
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        // Ambil jadwal untuk kelas yang dipilih
        $hariIni = $this->getHariIni();
        $jadwal = Jadwal::with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
            ->where('kelas_id', $request->kelas_id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Format response untuk JavaScript
        return response()->json([
            'jadwal' => $jadwal,
            'status' => 'success'
        ]);
    }

    /**
     * Process the QR code scan to record teacher attendance.
     */
    public function scanProcess(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'jadwal_id' => 'required|exists:jadwal,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        try {
            // Decode QR Code
            $qrData = json_decode($validated['qr_code'], true);
            if (!$qrData || !isset($qrData['id'])) {
                return back()->withErrors(['message' => 'QR Code tidak valid. Pastikan QR Code berisi ID.']);
            }

            // Ambil jadwal dengan eager loading relasi yang dibutuhkan
            $jadwal = Jadwal::with(['jadwalPelajaran.guru', 'kelas'])->findOrFail($validated['jadwal_id']);

            // Verifikasi jadwalPelajaran
            if (!$jadwal->jadwalPelajaran) {
                Log::error('Jadwal Pelajaran tidak ditemukan untuk jadwal ID: ' . $jadwal->id);
                return back()->withErrors(['message' => 'Jadwal Pelajaran tidak ditemukan.']);
            }

            // Cek apakah guru_id ada di jadwalPelajaran
            if (!$jadwal->jadwalPelajaran->guru_id) {
                Log::error('ID Guru belum diatur untuk JadwalPelajaran ID: ' . $jadwal->jadwalPelajaran->id);
                return back()->withErrors(['message' => 'ID Guru belum diatur dalam Jadwal Pelajaran.']);
            }

            // Ambil data guru
            $guru = Karyawan::find($jadwal->jadwalPelajaran->guru_id);
            if (!$guru) {
                Log::error('Guru tidak ditemukan dengan ID: ' . $jadwal->jadwalPelajaran->guru_id);
                return back()->withErrors(['message' => 'Guru tidak ditemukan dengan ID: ' . $jadwal->jadwalPelajaran->guru_id]);
            }

            // Cek QR Code apakah cocok dengan guru di jadwal
            if ($qrData['id'] != $guru->id) {
                return back()->withErrors(['message' => 'QR Code tidak sesuai dengan guru pada jadwal.']);
            }

            // Validasi akses user
            if (Auth::user()->role === 'kelas' && Auth::user()->related_id != $jadwal->kelas_id) {
                return back()->withErrors(['message' => 'Anda tidak berwenang melakukan absensi untuk kelas ini.']);
            }

            // Validasi waktu
            $waktuScan = now();
            $jamMulai = Carbon::createFromTimeString($jadwal->jam_mulai);
            $jamSelesai = Carbon::createFromTimeString($jadwal->jam_selesai);
            $batasAwal = $jamMulai->copy()->subMinutes(30);

            if ($waktuScan->lt($batasAwal) || $waktuScan->gt($jamSelesai)) {
                return back()->withErrors(['message' => 'Absensi hanya dapat dilakukan 30 menit sebelum jadwal dimulai hingga jadwal selesai.']);
            }

            // CHECK ABSENSI GERBANG: Tambahkan validasi untuk memastikan guru sudah absen gerbang hari ini
            $tanggalHariIni = now()->toDateString();
            $absensiGerbang = AbsensiGerbang::where('related_id', $guru->id)
                ->where('tanggal', $tanggalHariIni)
                ->whereNotNull('waktu_scan_masuk')
                ->first();

            if (!$absensiGerbang) {
                return back()->withErrors(['message' => 'Guru belum melakukan absensi gerbang hari ini. Harap lakukan absensi gerbang terlebih dahulu.']);
            }

            // Cek apakah sudah pernah absen
            $existingAbsensi = AbsensiGuruKelas::where('jadwal_id', $jadwal->id)
                ->where('tanggal', now()->toDateString())
                ->first();

            if ($existingAbsensi) {
                return back()->withErrors(['message' => 'Guru sudah melakukan absensi untuk jadwal ini hari ini.']);
            }

            // Simpan absensi
            AbsensiGuruKelas::create([
                'karyawan_id' => $guru->id,
                'jadwal_id' => $jadwal->id,
                'kelas_id' => $jadwal->kelas_id,
                'tanggal' => now()->toDateString(),
                'waktu_scan' => $waktuScan->toTimeString(),
                'scan_by_user_id' => Auth::id(),
                'status' => 'Hadir',
            ]);

            return redirect()->route('absensi-guru-kelas.index')->with('success', 'Absensi guru berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error during QR scan process: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function processScanGuru(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'jadwal_id' => 'required|exists:jadwal,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        try {
            $qrData = json_decode($validated['qr_code'], true);
            if (!$qrData || !isset($qrData['id'])) {
                return back()->withErrors(['message' => 'QR Code tidak valid. Pastikan QR Code berisi ID.']);
            }

            $guruId = $qrData['id'];
            $jadwal = Jadwal::with(['jadwalPelajaran.guru', 'kelas'])->findOrFail($validated['jadwal_id']);

            if (!$jadwal->jadwalPelajaran || !$jadwal->jadwalPelajaran->guru_id) {
                return back()->withErrors(['message' => 'Jadwal atau Guru tidak ditemukan.']);
            }

            if ($jadwal->jadwalPelajaran->guru_id != $guruId) {
                return back()->withErrors(['message' => 'Guru tidak sesuai dengan jadwal yang dipilih.']);
            }

            $tanggal = now()->toDateString();

            // CHECK ABSENSI GERBANG: Tambahkan validasi untuk memastikan guru sudah absen gerbang hari ini
            $absensiGerbang = AbsensiGerbang::where('related_id', $guruId)
                ->where('tanggal', $tanggal)
                ->whereNotNull('waktu_scan_masuk')
                ->first();

            if (!$absensiGerbang) {
                return back()->withErrors(['message' => 'Guru belum melakukan absensi gerbang hari ini. Harap lakukan absensi gerbang terlebih dahulu.']);
            }

            $waktu = now()->toTimeString();

            $absensi = AbsensiGuruKelas::firstOrCreate(
                [
                    'guru_id' => $guruId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'waktu' => $waktu,
                    'status' => 'Hadir',
                    'scan_by' => Auth::id()
                ]
            );

            return redirect()->route('absensi-guru-kelas.scan')->with('success', 'Absensi berhasil dicatat.');

        } catch (\Exception $e) {
            Log::error('Gagal proses absensi guru: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan saat memproses absensi.']);
        }
    }

    /**
     * Show the detail view for an attendance record.
     */
    public function show(AbsensiGuruKelas $absensiGuruKelas)
    {
        // Cek apakah user memiliki akses ke data ini
        if (Auth::user()->role === 'kelas' && Auth::user()->related_id != $absensiGuruKelas->kelas_id) {
            return redirect()->route('absensi-guru-kelas.index')->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        return view('absensi-guru-kelas.show', compact('absensiGuruKelas'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbsensiGuruKelas $absensiGuruKelas)
    {
        // Cek apakah user adalah admin
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus data absensi.');
        }

        try {
            $absensiGuruKelas->delete();
            return redirect()->route('absensi-guru-kelas.index')->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Generate report of teacher attendance.
     */

    public function report(Request $request)
    {
        // Fetch all classes for the filter dropdown
        $kelasList = Kelas::all();

        // Initialize the query
        $query = AbsensiGuruKelas::query();

        // Filter by date if provided
        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter by class if provided
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by report type if provided
        if ($request->has('report_type')) {
            switch ($request->report_type) {
                case 'daily':
                    $query->whereDate('tanggal', now()->startOfDay());
                    break;
                case 'monthly':
                    $query->whereMonth('tanggal', now()->month);
                    break;
                case 'semester':
                    $semester = now()->month <= 6 ? 1 : 2;
                    $query->whereBetween('tanggal', [
                        now()->startOfYear()->addMonths(($semester - 1) * 6),
                        now()->startOfYear()->addMonths($semester * 6)->subDay(),
                    ]);
                    break;
                case 'yearly':
                    $query->whereYear('tanggal', now()->year);
                    break;
            }
        }

        // Get the results
        $absensiGuru = $query->with(['karyawan', 'kelas', 'jadwal', 'scanByUser'])->get();


        // Return the report view with the data
        return view('absensi-guru-kelas.report', compact('absensiGuru', 'kelasList'));
    }

    /**
     * Get current day in Indonesian.
     */
    private function getHariIni()
    {
        $hari = Carbon::now()->locale('id')->dayName;
        $mapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        return $mapping[Carbon::now()->englishDayOfWeek] ?? 'Senin';
    }

    public function exportPdf(Request $request)
{
    $tanggal = $request->query('tanggal', date('Y-m-d'));
    $kelas_id = $request->query('kelas_id');
    $report_type = $request->query('report_type', 'daily');

    // Initialize query
    $query = AbsensiGuruKelas::query();

    // Get kelas data if filtered
    $kelas = null;
    if ($kelas_id) {
        $kelas = Kelas::find($kelas_id);
    }

    // Set report title and date range based on report type
    $reportTitle = "Laporan Absensi Guru Kelas";
    $startDate = now();
    $endDate = now();

    switch ($report_type) {
        case 'daily':
            $reportTitle .= " Harian";
            $query->whereDate('tanggal', $tanggal);
            $startDate = Carbon::parse($tanggal);
            $endDate = Carbon::parse($tanggal);
            break;
        case 'weekly':
            $reportTitle .= " Mingguan";
            $startDate = Carbon::parse($tanggal)->startOfWeek();
            $endDate = Carbon::parse($tanggal)->endOfWeek();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            break;
        case 'monthly':
            $reportTitle .= " Bulanan";
            $startDate = Carbon::parse($tanggal)->startOfMonth();
            $endDate = Carbon::parse($tanggal)->endOfMonth();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            break;
        case 'semester':
            $reportTitle .= " Semester";
            $month = Carbon::parse($tanggal)->month;
            $year = Carbon::parse($tanggal)->year;
            $semester = $month <= 6 ? 1 : 2;
            $startDate = Carbon::createFromDate($year, ($semester - 1) * 6 + 1, 1);
            $endDate = Carbon::createFromDate($year, $semester * 6, 1)->endOfMonth();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            break;
        case 'yearly':
            $reportTitle .= " Tahunan";
            $startDate = Carbon::parse($tanggal)->startOfYear();
            $endDate = Carbon::parse($tanggal)->endOfYear();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            break;
    }

    // Filter by class if provided
    if ($kelas_id) {
        $query->whereHas('jadwal', function ($q) use ($kelas_id) {
            $q->where('kelas_id', $kelas_id);
        });

        if ($kelas) {
            $reportTitle .= " - Kelas " . $kelas->nama_kelas;
        }
    }

    // Load related data
    $absensiGuru = $query->with(['karyawan', 'kelas', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser'])->get();

    // Group data by date and teacher for reporting
    $groupedByDate = $absensiGuru->groupBy('tanggal');
    $groupedByTeacher = $absensiGuru->groupBy('karyawan_id');

    // Get all teachers for complete reporting
    $allTeachers = Karyawan::where('jabatan', 'guru')->get();

    // Generate statistics
    $statistics = [
        'totalTeachers' => $allTeachers->count(),
        'teachersPresent' => $groupedByTeacher->count(),
        'totalAbsenceRecords' => $absensiGuru->count(),
        'totalClasses' => 0, // Will be calculated based on schedules
        'percentageAttendance' => 0,
        'startDate' => $startDate,
        'endDate' => $endDate
    ];

    // Calculate total scheduled classes
    $totalScheduled = 0;
    foreach ($allTeachers as $guru) {
        $jadwals = $guru->jadwalPelajaran->flatMap(function($jp) {
            return $jp->jadwal;
        });

        // Apply class filter if specified
        if ($kelas_id) {
            $jadwals = $jadwals->where('kelas_id', $kelas_id);
        }

        $totalScheduled += $jadwals->count();
    }

    $statistics['totalClasses'] = $totalScheduled;
    $statistics['percentageAttendance'] = $totalScheduled > 0
        ? round(($absensiGuru->count() / $totalScheduled) * 100, 2)
        : 0;

    // Generate PDF
    $pdf = PDF::loadView('absensi-guru-kelas.pdf', compact(
        'absensiGuru',
        'reportTitle',
        'kelas',
        'groupedByDate',
        'groupedByTeacher',
        'allTeachers',
        'statistics'
    ));

    // Set PDF options
    $pdf->setPaper('a4', 'portrait');

    // Generate filename
    $filename = "absensi_guru_kelas_" . $report_type . "_" . date('Ymd') . ".pdf";

    // Return PDF for download
    return $pdf->download($filename);
}
}
