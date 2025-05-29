<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Illuminate\Support\Str;
use App\Models\PetugasPiket;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;

class AbsensiGerbangController extends Controller
{
    public function index()
    {
        $absensiGerbang = AbsensiGerbang::with(['siswa', 'karyawan', 'scannedBy', 'jadwal'])->paginate(10);
        return view('absensi-gerbang.index', compact('absensiGerbang'));
    }

    public function scan()
    {
        return view('absensi-gerbang.scan');
    }

    public function scanProcess(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCodeData = $validated['qr_code'];
        Log::info('Raw QR Code Data:', ['qr_code' => $qrCodeData]);

        $qrData = json_decode($qrCodeData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON Parsing Error:', ['error' => json_last_error_msg(), 'raw_data' => $qrCodeData]);
            return back()->withErrors(['message' => 'QR Code tidak valid. Pastikan berisi data yang sesuai.']);
        }

        Log::info('Decoded QR Code Data:', ['qr_data' => $qrData]);

        if (!$qrData || !isset($qrData['id'])) {
            Log::error('Invalid QR Code Data:', ['qr_data' => $qrData, 'expected_fields' => ['id']]);
            return back()->withErrors(['message' => 'QR Code tidak valid. Format data tidak sesuai.']);
        }

        $id = $qrData['id'];
        $tanggal = now()->toDateString();
        $waktuScan = now()->toTimeString();
        $shift = $this->determineShift($waktuScan);

        // Day mapping to Indonesian
        $dayMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        $hariIni = $dayMapping[now()->format('l')] ?? 'Senin';

        $isAdmin = Auth::check();
        $user = User::where('related_id', $id)->whereIn('role', ['karyawan', 'siswa'])->first();

        if (!$user) {
            return back()->withErrors(['message' => 'Pengguna tidak ditemukan.']);
        }

        $model = null;
        if ($user->role === 'karyawan') {
            $model = Karyawan::find($id);
        } elseif ($user->role === 'siswa') {
            $model = Siswa::find($id);
        }

        if (!$model) {
            return back()->withErrors(['message' => 'Karyawan atau Siswa tidak ditemukan.']);
        }

        $jadwalId = null;

        if ($user->role === 'karyawan' && $model->jabatan === 'Guru') {
            // Get all jadwal_pelajaran_id for this teacher
            $jadwalPelajaran = JadwalPelajaran::where('guru_id', $model->id)->pluck('id')->toArray();

            if (empty($jadwalPelajaran)) {
                Log::error('No teaching schedules found for teacher', ['guru_id' => $model->id]);
                return back()->withErrors(['message' => 'Tidak ada jadwal pelajaran untuk guru ini.']);
            }

            // Get distinct days from jadwal for this teacher to check if there are any schedules for today
            $availableDays = Jadwal::whereIn('jadwal_pelajaran_id', $jadwalPelajaran)
                ->distinct()
                ->pluck('hari')
                ->toArray();

            Log::info('Available days for this teacher', [
                'available_days' => $availableDays,
                'today' => $hariIni
            ]);

            // Use a more lenient approach - if there's no schedule right now, just let them check in
            $currentJadwal = Jadwal::whereIn('jadwal_pelajaran_id', $jadwalPelajaran)
                ->where('hari', $hariIni)
                ->where('jam_mulai', '<=', $waktuScan)
                ->where('jam_selesai', '>=', $waktuScan)
                ->first();

            if ($currentJadwal) {
                $jadwalId = $currentJadwal->id;
                Log::info('Found active teaching schedule', [
                    'jadwal_id' => $jadwalId,
                    'jam_mulai' => $currentJadwal->jam_mulai,
                    'jam_selesai' => $currentJadwal->jam_selesai
                ]);
            } else {
                // Look for any schedule today, even if not current time
                $todayJadwal = Jadwal::whereIn('jadwal_pelajaran_id', $jadwalPelajaran)
                    ->where('hari', $hariIni)
                    ->first();

                if ($todayJadwal) {
                    $jadwalId = $todayJadwal->id;
                    Log::info('Found teaching schedule for today (not current time)', [
                        'jadwal_id' => $jadwalId,
                        'jam_mulai' => $todayJadwal->jam_mulai,
                        'jam_selesai' => $todayJadwal->jam_selesai
                    ]);
                } else {
                    // Skip the error and just let them check in anyway,
                    // but log that no schedule was found
                    Log::warning('No teaching schedule found for today', [
                        'guru_id' => $model->id,
                        'hari' => $hariIni,
                        'waktu' => $waktuScan
                    ]);

                    // Instead of returning an error, just set jadwalId to null
                    // and continue with the gate attendance process
                    $jadwalId = null;
                }
            }
        }

        $isValidPetugas = $isAdmin || PetugasPiket::where('related_id', Auth::id())
            ->where('tanggal', $tanggal)
            ->where('shift', $shift)
            ->exists();

        if (!$isValidPetugas) {
            return back()->withErrors(['message' => 'Anda bukan petugas piket aktif hari ini.']);
        }

        $absensi = AbsensiGerbang::where('related_id', $id)
            ->where('tanggal', $tanggal)
            ->whereNull('waktu_scan_keluar')
            ->first();

        if ($absensi) {
            $absensi->update([
                'waktu_scan_keluar' => $waktuScan,
                'status' => 'Hadir',
                'jadwal_id' => $jadwalId ?? $absensi->jadwal_id,
            ]);

            $message = 'Absensi keluar berhasil disimpan untuk ' . $model->nama_lengkap . '.';
        } else {
            $completeRecord = AbsensiGerbang::where('related_id', $id)
                ->where('tanggal', $tanggal)
                ->whereNotNull('waktu_scan_keluar')
                ->first();

            if ($completeRecord) {
                return back()->withErrors(['message' => 'Absensi untuk ' . $model->nama_lengkap . ' hari ini sudah lengkap (masuk dan keluar).']);
            }

            AbsensiGerbang::create([
                'related_id' => $id,
                'tanggal' => $tanggal,
                'waktu_scan_masuk' => $waktuScan,
                'waktu_scan_keluar' => null,
                'status' => 'Hadir',
                'scanned_by' => Auth::id(),
                'jadwal_id' => $jadwalId,
            ]);

            $message = 'Absensi masuk berhasil disimpan untuk ' . $model->nama_lengkap . '.';
        }

        return redirect()->route(Auth::user()->role .'.absensi-gerbang.scan')->with('success', $message);
    }

    private function determineShift($waktuScan)
    {
        $waktu = Carbon::createFromFormat('H:i:s', $waktuScan);

        if ($waktu->between(Carbon::createFromTime(6, 0), Carbon::createFromTime(12, 0))) {
            return 'Pagi';
        } elseif ($waktu->between(Carbon::createFromTime(12, 1), Carbon::createFromTime(18, 0))) {
            return 'Siang';
        } else {
            return 'Sore';
        }
    }

    public function destroy(AbsensiGerbang $absensiGerbang)
    {
        try {
            $absensiGerbang->delete();
            $role = Auth::user()->role;
            return redirect()->route($role.'.absensi-gerbang.index')->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data absensi: ' . $e->getMessage());
        }
    }

     /**
     * Export attendance data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        try {
            // Validate the request with more comprehensive options
            $validated = $request->validate([
                'periode' => 'required|string|in:harian,mingguan,bulanan,semester,tahunan,kustom',
                'role' => 'nullable|string|in:siswa,karyawan',
                'status' => 'nullable|string|in:Hadir,Terlambat,Alpha',
                'tanggal_mulai' => 'required_if:periode,kustom,bulanan,semester,tahunan|nullable|date',
                'tanggal_akhir' => 'required_if:periode,kustom|nullable|date|after_or_equal:tanggal_mulai',
                'format' => 'nullable|string|in:pdf,excel', // Future-proofing for potential Excel export
            ]);

            // Process date range based on period selection
            [$tanggalMulai, $tanggalAkhir, $periodeText] = $this->processPeriodeDates($request->periode, $request->tanggal_mulai, $request->tanggal_akhir);

            // Get attendance data with a dedicated method
            $absensiData = $this->getAttendanceData($tanggalMulai, $tanggalAkhir, $request->role, $request->status);

            // Calculate comprehensive statistics
            $statistics = $this->calculateAttendanceStatistics($absensiData, $tanggalMulai, $tanggalAkhir);

            // Group data by date for better organization
            $groupedData = $absensiData->groupBy('tanggal')->sortKeys();

            // Generate PDF view with improved data structure
            $html = view('absensi-gerbang.export-pdf', [
                'reportTitle' => 'Laporan Absensi Gerbang ' . $periodeText,
                'statistics' => $statistics,
                'groupedData' => $groupedData,
                'filters' => [
                    'periode' => $request->periode,
                    'role' => $request->role,
                    'status' => $request->status,
                ],
            ])->render();

            // Generate and return PDF
            return $this->generatePdf($html, $periodeText);

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }
    /**
 * Process date range based on selected period
 *
 * @param string $periode
 * @param string|null $tanggalMulai
 * @param string|null $tanggalAkhir
 * @return array
 */
private function processPeriodeDates($periode, $tanggalMulai, $tanggalAkhir)
{
    switch ($periode) {
        case 'harian':
            $tanggalMulai = now()->toDateString();
            $tanggalAkhir = now()->toDateString();
            $periodeText = 'Harian (' . now()->format('d F Y') . ')';
            break;
        case 'mingguan':
            $tanggalMulai = now()->startOfWeek()->toDateString();
            $tanggalAkhir = now()->endOfWeek()->toDateString();
            $periodeText = 'Mingguan (' . now()->startOfWeek()->format('d F Y') . ' - ' . now()->endOfWeek()->format('d F Y') . ')';
            break;
        case 'bulanan':
            if (!$tanggalMulai) {
                $tanggalMulai = now()->startOfMonth()->toDateString();
                $tanggalAkhir = now()->endOfMonth()->toDateString();
                $periodeText = 'Bulanan (' . now()->format('F Y') . ')';
            } else {
                $date = Carbon::parse($tanggalMulai);
                $tanggalMulai = $date->startOfMonth()->toDateString();
                $tanggalAkhir = $date->endOfMonth()->toDateString();
                $periodeText = 'Bulanan (' . $date->format('F Y') . ')';
            }
            break;
        case 'semester':
            if (!$tanggalMulai) {
                $semester = now()->month <= 6 ? 1 : 2;
                if ($semester === 1) {
                    $tanggalMulai = now()->startOfYear()->toDateString();
                    $tanggalAkhir = now()->copy()->month(6)->endOfMonth()->toDateString();
                    $periodeText = 'Semester 1 (' . now()->year . ')';
                } else {
                    $tanggalMulai = now()->copy()->month(7)->startOfMonth()->toDateString();
                    $tanggalAkhir = now()->endOfYear()->toDateString();
                    $periodeText = 'Semester 2 (' . now()->year . ')';
                }
            } else {
                $date = Carbon::parse($tanggalMulai);
                $semester = $date->month <= 6 ? 1 : 2;
                $periodeText = 'Semester ' . $semester . ' (' . $date->year . ')';
            }
            break;
        case 'tahunan':
            if (!$tanggalMulai) {
                $tanggalMulai = now()->startOfYear()->toDateString();
                $tanggalAkhir = now()->endOfYear()->toDateString();
                $periodeText = 'Tahunan (' . now()->year . ')';
            } else {
                $date = Carbon::parse($tanggalMulai);
                $tanggalMulai = Carbon::parse($tanggalMulai)->startOfYear()->toDateString();
                $tanggalAkhir = Carbon::parse($tanggalMulai)->endOfYear()->toDateString();
                $periodeText = 'Tahunan (' . $date->year . ')';
            }
            break;
        case 'kustom':
            $periodeText = 'Kustom (' . Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . Carbon::parse($tanggalAkhir)->format('d F Y') . ')';
            break;
    }

    return [$tanggalMulai, $tanggalAkhir, $periodeText];
}

/**
 * Get attendance data based on filters
 *
 * @param string $startDate
 * @param string $endDate
 * @param string|null $role
 * @param string|null $status
 * @return \Illuminate\Database\Eloquent\Collection
 */
private function getAttendanceData($startDate, $endDate, $role = null, $status = null)
{
    $query = AbsensiGerbang::with(['siswa', 'karyawan', 'scannedBy', 'jadwal'])
        ->whereBetween('tanggal', [$startDate, $endDate]);

    // Apply role filter if provided
    if ($role) {
        if ($role === 'siswa') {
            $query->whereNotNull('siswa_id')->whereNull('karyawan_id');
        } elseif ($role === 'karyawan') {
            $query->whereNotNull('karyawan_id')->whereNull('siswa_id');
        }
    }

    // Apply status filter if provided
    if ($status) {
        $query->where('status', $status);
    }

    return $query->orderBy('tanggal')->orderBy('waktu_scan_masuk')->get();
}

/**
 * Calculate comprehensive attendance statistics
 *
 * @param \Illuminate\Database\Eloquent\Collection $data
 * @param string $startDate
 * @param string $endDate
 * @return array
 */
private function calculateAttendanceStatistics($data, $startDate, $endDate)
{
    $siswaData = $data->filter(function($item) {
        return $item->siswa !== null;
    });

    $karyawanData = $data->filter(function($item) {
        return $item->karyawan !== null;
    });

    // Get attendance by status
    $hadirCount = $data->where('status', 'Hadir')->count();
    $terlambatCount = $data->where('status', 'Terlambat')->count();
    $alphaCount = $data->where('status', 'Alpha')->count();

    // Get complete vs incomplete attendance
    $completeAttendance = $data->whereNotNull('waktu_scan_keluar')->count();
    $incompleteAttendance = $data->whereNull('waktu_scan_keluar')->count();

    // Calculate daily averages
    $dayCount = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
    $dailyAverage = $dayCount > 0 ? round($data->count() / $dayCount, 1) : 0;

    // Get unique individuals
    $uniqueSiswaCount = $siswaData->pluck('related_id')->unique()->count();
    $uniqueKaryawanCount = $karyawanData->pluck('related_id')->unique()->count();

    return [
        'startDate' => Carbon::parse($startDate),
        'endDate' => Carbon::parse($endDate),
        'totalRecords' => $data->count(),
        'totalDays' => $dayCount,
        'dailyAverage' => $dailyAverage,

        // Role-based counts
        'siswaCount' => $siswaData->count(),
        'karyawanCount' => $karyawanData->count(),
        'uniqueSiswaCount' => $uniqueSiswaCount,
        'uniqueKaryawanCount' => $uniqueKaryawanCount,

        // Status-based counts
        'hadirCount' => $hadirCount,
        'terlambatCount' => $terlambatCount,
        'alphaCount' => $alphaCount,

        // Complete vs incomplete counts
        'completeAttendance' => $completeAttendance,
        'incompleteAttendance' => $incompleteAttendance,
    ];
}

/**
 * Generate and configure PDF document
 *
 * @param string $html
 * @param string $periodeText
 * @return \Illuminate\Http\Response
 */
private function generatePdf($html, $periodeText)
{
    // Configure PDF options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Arial');

    // Create DomPDF instance
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Generate filename with period info
    $filename = 'laporan_absensi_gerbang_' . Str::slug($periodeText) . '_' . now()->format('Ymd_His') . '.pdf';

    // Stream PDF for download
    return $dompdf->stream($filename, ['Attachment' => true]);
}
}
