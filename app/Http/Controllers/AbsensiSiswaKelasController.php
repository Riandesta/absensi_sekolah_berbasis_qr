<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use Barryvdh\DomPDF\Facade\PDF;
use App\Models\AbsensiGuruKelas;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   /**
 * Display a listing of the resource.
 */
public function index()
{
    // Check if the current user is a teacher (karyawan with jabatan 'guru')
    $isGuru = Auth::check() && Auth::user()->role === 'karyawan' &&
             Auth::user()->karyawan && strtolower(Auth::user()->karyawan->jabatan) === 'guru';

    $jadwalGuru = collect();
    $absensiGuru = [];

    if ($isGuru) {
        $hariIni = $this->getHariIni();
        $tanggalHariIni = Carbon::now()->toDateString();
        $karyawanId = Auth::user()->related_id;

        // Get teacher's schedule for today
        $jadwalGuru = Jadwal::with(['jadwalPelajaran.mataPelajaran', 'kelas'])
            ->whereHas('jadwalPelajaran', function ($q) use ($karyawanId) {
                $q->where('guru_id', $karyawanId);
            })
            ->where('hari', $hariIni)
            ->get();

        // Get teacher's attendance for today
        $absensiGuru = AbsensiGuruKelas::where('karyawan_id', $karyawanId)
            ->where('tanggal', $tanggalHariIni)
            ->pluck('jadwal_id')
            ->toArray();

        // Get student attendance for classes where teacher has attended
        $absensiSiswaQuery = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'inputBy'])
            ->where('tanggal', $tanggalHariIni)
            ->whereIn('jadwal_id', $absensiGuru);

        // Use pagination for teachers too, to make it consistent with the view
        $absensiSiswaKelas = $absensiSiswaQuery->paginate(10);
    } else {
        // For non-teachers (admin and others) - show all attendance records with pagination
        $absensiSiswaKelas = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'inputBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    return view('karyawan.absensi-siswa-kelas.index', compact('absensiSiswaKelas', 'jadwalGuru', 'absensiGuru'));
}

    /**
     * Show the form for taking attendance in a specific class and schedule.
     */
    public function kelas($jadwalId)
    {
        // IMPORTANT: Removing the redirect that was preventing access
        // if (Auth::user()->role !== 'karyawan' || Auth::user()->karyawan->jabatan !== 'guru') {
        //     return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses untuk mengakses halaman ini.');
        // }

        $jadwal = Jadwal::with(['kelas', 'jadwalPelajaran.mataPelajaran'])
            ->findOrFail($jadwalId);

        $tanggalHariIni = Carbon::now()->toDateString();

        // Check if the teacher has attendance for this schedule today
        // Make this more lenient - don't restrict access based on teacher attendance
        $guruAbsen = true;

        if (Auth::user()->role === 'karyawan' && Auth::user()->karyawan) {
            $guruAbsen = AbsensiGuruKelas::where('jadwal_id', $jadwalId)
                ->where('karyawan_id', Auth::user()->related_id)
                ->where('tanggal', $tanggalHariIni)
                ->exists();
        }

        // Check if attendance has already been recorded for this schedule today
        $attendanceRecorded = AbsensiSiswaKelas::where('jadwal_id', $jadwalId)
            ->where('tanggal', $tanggalHariIni)
            ->exists();

        // Instead of redirecting, just set a flag to show a warning
        $attendanceWarning = $attendanceRecorded;

        // Get all students in this class
        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)->get();

        // Get all students who have gate attendance today
        $absensiGerbang = AbsensiGerbang::where('tanggal', $tanggalHariIni)
            ->whereNotNull('waktu_scan_masuk')
            ->whereIn('related_id', $siswa->pluck('id')->toArray())
            ->pluck('related_id')
            ->toArray();

        // Get any existing attendance records for this schedule today
        $existingAbsensi = AbsensiSiswaKelas::where('jadwal_id', $jadwalId)
            ->where('tanggal', $tanggalHariIni)
            ->get();

        // Format the data for easier use in the view
        $absensiSiswa = [];
        foreach ($existingAbsensi as $absensi) {
            $absensiSiswa[$absensi->siswa_id] = $absensi->status;
        }

        return view('absensi-siswa-kelas.kelas', compact(
            'jadwal',
            'siswa',
            'absensiGerbang',
            'absensiSiswa',
            'guruAbsen',
            'attendanceWarning'
        ));
    }

    /**
     * Save attendance data for a class.
     */
  public function simpanAbsensi(Request $request)
{
    // Validate the request
    $validated = $request->validate([
        'jadwal_id' => 'required|exists:jadwal,id',
        'siswa' => 'required|array',
        'siswa.*.id' => 'required|exists:siswa,id',
        'siswa.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
    ]);


    $tanggalHariIni = Carbon::now()->toDateString();
    $jadwalId = $validated['jadwal_id'];
    $savedCount = 0;
    $errorMessages = [];

    try {
        // Enable query log to capture the query being executed
        DB::enableQueryLog();

        // Get the jadwal (schedule) to access class_id
        $jadwal = Jadwal::findOrFail($jadwalId);

        // Get all gate attendance records for today to avoid repeated queries
        $gateAttendances = AbsensiGerbang::where('tanggal', $tanggalHariIni)
            ->whereNotNull('waktu_scan_masuk')
            ->whereIn('related_id', array_column($validated['siswa'], 'id'))
            ->get()
            ->keyBy('related_id');

        // Loop through each student to save their attendance
        foreach ($validated['siswa'] as $siswaData) {
            $siswaId = $siswaData['id'];
            $status = $siswaData['status'];
            $absenGerbangId = null;

            if (empty($status)) {
                continue;
            }

            // For students marked as 'Hadir', check gate attendance
            if ($status === 'Hadir') {
                if (isset($gateAttendances[$siswaId])) {
                    $absenGerbangId = $gateAttendances[$siswaId]->id;
                }
                // For now, we'll allow setting "Hadir" even without gate attendance
            }

            try {
                // Log the data being saved
                Log::info('Saving attendance', [
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwalId,
                    'status' => $status,
                    'kelas_id' => $jadwal->kelas_id,
                    'absen_gerbang_id' => $absenGerbangId,
                ]);

                // Create or update the attendance record
                AbsensiSiswaKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'jadwal_id' => $jadwalId,
                        'tanggal' => $tanggalHariIni,
                    ],
                    [
                        'kelas_id' => $jadwal->kelas_id, // Store kelas_id directly
                        'status' => $status,
                        'input_by' => Auth::id(),
                        'absen_gerbang_id' => $absenGerbangId,
                    ]
                );
                $savedCount++;
            } catch (\Exception $e) {
                // Log any errors when saving attendance
                Log::error("Failed to save attendance for student $siswaId: " . $e->getMessage());
                $errorMessages[] = "Gagal menyimpan absensi untuk siswa ID $siswaId: " . $e->getMessage();
            }
        }

        // Log the executed query
        Log::info('Executed Query', DB::getQueryLog());

        // If attendance was saved successfully
        if ($savedCount > 0) {
            $message = "Berhasil menyimpan $savedCount data absensi.";
            if (!empty($errorMessages)) {
                $message .= " Beberapa data tidak tersimpan.";
            }
            return redirect()->route('karyawan.absensi-siswa-kelas.index')
                ->with('success', $message);
        } else {
            return redirect()->route('karyawan.absensi-siswa-kelas.index')
                ->with('error', 'Tidak ada data absensi yang tersimpan. ' . implode(" ", $errorMessages));
        }
    } catch (\Exception $e) {
        // Log any errors that happen during the process
        Log::error('Error saving attendance: ' . $e->getMessage());
        return redirect()->route('karyawan.absensi-siswa-kelas.index')
            ->with('error', 'Terjadi kesalahan saat menyimpan data absensi: ' . $e->getMessage());
    }
}



    /**
     * Display the specified resource.
     */
    public function show(AbsensiSiswaKelas $absensiSiswaKelas)
    {
        return view('absensi-siswa-kelas.show', compact('absensiSiswaKelas'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbsensiSiswaKelas $absensiSiswaKelas)
    {
        try {
            $absensiSiswaKelas->delete();
            return redirect()->route('karyawan.absensi-siswa-kelas.index')
                ->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Show attendance report.
     */
    public function laporan(Request $request)
    {
        $absensiSiswaKelas = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'inputBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('absensi-siswa-kelas.laporan', compact('absensiSiswaKelas'));
    }

    /**
     * Get current day in Indonesian.
     */
    private function getHariIni()
    {
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

    /**
     * Show student attendance report with filtering
     */
    public function laporanSiswa(Request $request)
    {
        $kelasList = Kelas::all();

        $query = AbsensiSiswaKelas::query();

        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $absensiSiswa = $query->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'inputBy'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('absensi-siswa-kelas.laporan-siswa', compact('absensiSiswa', 'kelasList'));
    }

    /**
     * Update attendance status via AJAX
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jadwal_id' => 'required|exists:jadwal,id',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'tanggal' => 'nullable|date'
        ]);

        $tanggalHariIni = $validated['tanggal'] ?? Carbon::now()->toDateString();
        $absenGerbangId = null;

        // Get the jadwal to access kelas_id
        $jadwal = Jadwal::findOrFail($validated['jadwal_id']);

        if ($validated['status'] === 'Hadir') {
            $absensiGerbang = AbsensiGerbang::where('related_id', $validated['siswa_id'])
                ->where('tanggal', $tanggalHariIni)
                ->whereNotNull('waktu_scan_masuk')
                ->first();

            if ($absensiGerbang) {
                $absenGerbangId = $absensiGerbang->id;
            }
        }

        try {
            AbsensiSiswaKelas::updateOrCreate(
                [
                    'siswa_id' => $validated['siswa_id'],
                    'jadwal_id' => $validated['jadwal_id'],
                    'tanggal' => $tanggalHariIni,
                ],
                [
                    'kelas_id' => $jadwal->kelas_id,
                    'status' => $validated['status'],
                    'input_by' => Auth::id(),
                    'absen_gerbang_id' => $absenGerbangId,
                ]
            );

            Log::info('Status absensi berhasil diperbarui untuk siswa: ' . $validated['siswa_id']);

            return response()->json([
                'success' => true,
                'message' => 'Status absensi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View specific attendance records for a class and date
     */
    public function view(Request $request)
    {
        $jadwal_id = $request->input('jadwal_id');
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Get jadwal with relations
        $jadwal = Jadwal::with(['kelas', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
            ->findOrFail($jadwal_id);

        // Get students in this class
        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)->get();

        // Get attendance records for this jadwal and date
        $absensiSiswa = AbsensiSiswaKelas::where('jadwal_id', $jadwal_id)
            ->where('tanggal', $tanggal)
            ->with(['siswa', 'inputBy'])
            ->get();

        // Get attendance count by status
        $statHadir = $absensiSiswa->where('status', 'Hadir')->count();
        $statIzin = $absensiSiswa->where('status', 'Izin')->count();
        $statSakit = $absensiSiswa->where('status', 'Sakit')->count();
        $statAlpa = $absensiSiswa->where('status', 'Alpa')->count();

        return view('absensi-siswa-kelas.view', compact(
            'jadwal',
            'tanggal',
            'siswa',
            'absensiSiswa',
            'statHadir',
            'statIzin',
            'statSakit',
            'statAlpa'
        ));
    }

    /**
     * Edit attendance form
     */
    public function edit(Request $request)
    {
        $jadwal_id = $request->input('jadwal_id');
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Get jadwal with relations
        $jadwal = Jadwal::with(['kelas', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
            ->findOrFail($jadwal_id);

        // Get students in this class
        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)->get();

        // Get gate attendance records for that date
        $absensiGerbang = AbsensiGerbang::where('tanggal', $tanggal)
            ->whereNotNull('waktu_scan_masuk')
            ->whereIn('related_id', $siswa->pluck('id')->toArray())
            ->pluck('related_id')
            ->toArray();

        // Get existing attendance records
        $existingAbsensi = AbsensiSiswaKelas::where('jadwal_id', $jadwal_id)
            ->where('tanggal', $tanggal)
            ->get();

        // Format data for the form
        $absensiSiswa = [];
        foreach($existingAbsensi as $absensi) {
            $absensiSiswa[$absensi->siswa_id] = $absensi->status;
        }

        return view('absensi-siswa-kelas.edit', compact(
            'jadwal',
            'tanggal',
            'siswa',
            'absensiGerbang',
            'absensiSiswa'
        ));
    }

    /**
     * Update attendance records
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal' => 'required|date',
            'siswa' => 'required|array',
            'siswa.*.id' => 'required|exists:siswa,id',
            'siswa.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
        ]);

        $jadwalId = $validated['jadwal_id'];
        $tanggal = $validated['tanggal'];
        $savedCount = 0;
        $errorMessages = [];

        try {
            // Get the jadwal to access kelas_id
            $jadwal = Jadwal::findOrFail($jadwalId);

            // Get all gate attendance records for the specified date
            $gateAttendances = AbsensiGerbang::where('tanggal', $tanggal)
                ->whereNotNull('waktu_scan_masuk')
                ->whereIn('related_id', array_column($validated['siswa'], 'id'))
                ->get()
                ->keyBy('related_id');

            foreach ($validated['siswa'] as $siswaData) {
                $siswaId = $siswaData['id'];
                $status = $siswaData['status'];
                $absenGerbangId = null;

                if (empty($status)) {
                    continue;
                }

                // For students marked as 'Hadir', check gate attendance
                if ($status === 'Hadir') {
                    if (isset($gateAttendances[$siswaId])) {
                        $absenGerbangId = $gateAttendances[$siswaId]->id;
                    }
                }

                try {
                    // Update or create the attendance record
                    AbsensiSiswaKelas::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'jadwal_id' => $jadwalId,
                            'tanggal' => $tanggal,
                        ],
                        [
                            'kelas_id' => $jadwal->kelas_id,
                            'status' => $status,
                            'input_by' => Auth::id(),
                            'absen_gerbang_id' => $absenGerbangId,
                        ]
                    );
                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error("Failed to update attendance for student $siswaId: " . $e->getMessage());
                    $errorMessages[] = "Gagal memperbarui absensi untuk siswa ID $siswaId: " . $e->getMessage();
                }
            }

            if ($savedCount > 0) {
                $message = "Berhasil memperbarui $savedCount data absensi.";
                if (!empty($errorMessages)) {
                    $message .= " Beberapa data tidak tersimpan.";
                }
                return redirect()->route('absensi-siswa-kelas.view', ['jadwal_id' => $jadwalId, 'tanggal' => $tanggal])
                    ->with('success', $message);
            } else {
                return redirect()->route('absensi-siswa-kelas.view', ['jadwal_id' => $jadwalId, 'tanggal' => $tanggal])
                    ->with('error', 'Tidak ada data absensi yang diperbarui. ' . implode(" ", $errorMessages));
            }
        } catch (\Exception $e) {
            Log::error('Error updating attendance: ' . $e->getMessage());
            return redirect()->route('absensi-siswa-kelas.view', ['jadwal_id' => $jadwalId, 'tanggal' => $tanggal])
                ->with('error', 'Terjadi kesalahan saat memperbarui data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Show attendance summary report with filtering options
     */
    public function rekap(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $tanggal = $request->input('tanggal', now()->toDateString());
        $periode_type = $request->input('periode_type', 'harian');
        $export = $request->input('export');

        // Get class data if provided
        $kelas = null;
        if ($kelas_id) {
            $kelas = Kelas::find($kelas_id);
            if (!$kelas) {
                return back()->with('error', 'Kelas tidak ditemukan');
            }
        }

        // Get all classes for filter dropdown
        $kelasList = Kelas::all();

        // Build the query
        $query = AbsensiSiswaKelas::query();

        // Apply filters
        if ($kelas_id) {
            $query->where('kelas_id', $kelas_id);
        }

        // Get date range based on period type
        $startDate = null;
        $endDate = null;

        switch ($periode_type) {
            case 'harian':
                $date = Carbon::parse($tanggal);
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                break;

            case 'mingguan':
                $date = Carbon::parse($tanggal);
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                break;

            case 'bulanan':
                $date = Carbon::parse($tanggal);
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                break;

            case 'semester':
                $date = Carbon::parse($tanggal);
                $month = $date->month;
                $year = $date->year;

                if ($month <= 6) {
                    // Semester 1 (January - June)
                    $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                    $endDate = Carbon::createFromDate($year, 6, 30)->endOfDay();
                } else {
                    // Semester 2 (July - December)
                    $startDate = Carbon::createFromDate($year, 7, 1)->startOfDay();
                    $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                }
                break;

            case 'tahunan':
                $date = Carbon::parse($tanggal);
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                break;
        }

        // Apply date range filter
        $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

        // Get attendance data with relations
        $absensiSiswa = $query->with(['siswa.kelas', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'inputBy'])->get();

        // Calculate statistics
        $statHadir = $absensiSiswa->where('status', 'Hadir')->count();
        $statIzin = $absensiSiswa->where('status', 'Izin')->count();
        $statSakit = $absensiSiswa->where('status', 'Sakit')->count();
        $statAlpa = $absensiSiswa->where('status', 'Alpa')->count();
        $totalAbsensi = $absensiSiswa->count();

        // Group data by student
        $absensiSiswaGrouped = $absensiSiswa->groupBy('siswa_id');

        // Get all students for summary
        $allStudents = collect();
        if ($kelas_id) {
            $allStudents = Siswa::where('kelas_id', $kelas_id)->get()->keyBy('id');
        } else {
            $siswaIds = $absensiSiswa->pluck('siswa_id')->unique()->toArray();
            if (!empty($siswaIds)) {
                $allStudents = Siswa::whereIn('id', $siswaIds)->with('kelas')->get()->keyBy('id');
            }
        }

        // Prepare student summary data
        $rekapSiswa = [];
        foreach ($allStudents as $id => $siswa) {
            $siswaAbsensi = $absensiSiswaGrouped[$id] ?? collect();

            $hadir = $siswaAbsensi->where('status', 'Hadir')->count();
            $izin = $siswaAbsensi->where('status', 'Izin')->count();
            $sakit = $siswaAbsensi->where('status', 'Sakit')->count();
            $alpa = $siswaAbsensi->where('status', 'Alpa')->count();
            $total = $hadir + $izin + $sakit + $alpa;

            $rekapSiswa[] = [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $total
            ];
        }

        // Prepare chart data
        $chartDates = [];
        $chartHadir = [];
        $chartIzin = [];
        $chartSakit = [];
        $chartAlpa = [];

        // Get data for chart based on period type
        $interval = '1 day';
        $format = 'd M';

        // Check if export requested
        if ($export === 'pdf') {
            $pdf = PDF::loadView('laporan.absensi-siswa-rekap', [
                'kelas' => $kelas,
                'periode_type' => $periode_type,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'absensiSiswa' => $absensiSiswa,
                'rekapSiswa' => $rekapSiswa,
                'statHadir' => $statHadir,
                'statIzin' => $statIzin,
                'statSakit' => $statSakit,
                'statAlpa' => $statAlpa,
                'totalAbsensi' => $totalAbsensi
            ]);

            return $pdf->download('rekap_absensi_siswa_' . $periode_type . '.pdf');
        }

        return view('absensi-siswa-kelas.rekap', compact(
            'kelas',
            'periode_type',
            'kelasList',
            'startDate',
            'endDate',
            'absensiSiswa',
            'rekapSiswa',
            'statHadir',
            'statIzin',
            'statSakit',
            'statAlpa',
            'totalAbsensi',
            'chartDates',
            'chartHadir',
            'chartIzin',
            'chartSakit',
            'chartAlpa'
        ));
    }
}
