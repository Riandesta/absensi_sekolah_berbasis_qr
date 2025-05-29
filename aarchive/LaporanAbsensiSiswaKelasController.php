<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class LaporanAbsensiSiswaKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $tanggal = $request->input('tanggal');
        $report_type = $request->input('report_type', 'daily');

        // Get class data if provided
        $kelas = null;
        if ($kelas_id) {
            $kelas = Kelas::find($kelas_id);
            if (!$kelas) {
                return back()->with('error', 'Kelas tidak ditemukan');
            }
        }

        // Build the query
        $query = AbsensiSiswaKelas::query();

        // Apply filters
        if ($kelas_id) {
            // If class is provided, filter by jadwal with that class
            $query->whereHas('jadwal', function($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        // Apply date range based on report type
        $startDate = null;
        $endDate = null;
        $reportTitle = '';

        switch ($report_type) {
            case 'daily':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                $reportTitle = 'Laporan Absensi Siswa Harian - ' . $date->format('d M Y');
                break;

            case 'weekly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                $reportTitle = 'Laporan Absensi Siswa Mingguan - ' . $startDate->format('d M Y') . ' s/d ' . $endDate->format('d M Y');
                break;

            case 'monthly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                $reportTitle = 'Laporan Absensi Siswa Bulanan - ' . $date->format('F Y');
                break;

            case 'semester':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $semester = $date->month <= 6 ? 1 : 2;
                $year = $date->year;
                if ($semester == 1) {
                    $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                    $endDate = Carbon::createFromDate($year, 6, 30)->endOfDay();
                } else {
                    $startDate = Carbon::createFromDate($year, 7, 1)->startOfDay();
                    $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                }
                $reportTitle = 'Laporan Absensi Siswa Semester ' . $semester . ' - ' . $year;
                break;

            case 'yearly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                $reportTitle = 'Laporan Absensi Siswa Tahunan - ' . $date->format('Y');
                break;
        }

        // Apply date filter to query
        $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

        // Get results with relations
        $absensi = $query->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'inputBy'])->get();

        // Group data by date and student
        $groupedByDate = $absensi->groupBy(function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        $groupedByStudent = $absensi->groupBy('siswa_id');

        // Fetch all students
        $allStudents = collect();
        if ($kelas_id) {
            $allStudents = Siswa::where('kelas_id', $kelas_id)->get();
        } else {
            // If no class specified, get all students that have at least one attendance record
            $siswaIds = $absensi->pluck('siswa_id')->unique();
            $allStudents = Siswa::whereIn('id', $siswaIds)->get();
        }

        // Calculate statistics
        $totalHadir = $absensi->where('status', 'Hadir')->count();
        $totalIzin = $absensi->where('status', 'Izin')->count();
        $totalSakit = $absensi->where('status', 'Sakit')->count();
        $totalAlpa = $absensi->where('status', 'Alpa')->count();
        $totalAttendance = $absensi->count();

        // Calculate percentages
        $persenHadir = $totalAttendance > 0 ? round(($totalHadir / $totalAttendance) * 100, 2) : 0;
        $persenIzin = $totalAttendance > 0 ? round(($totalIzin / $totalAttendance) * 100, 2) : 0;
        $persenSakit = $totalAttendance > 0 ? round(($totalSakit / $totalAttendance) * 100, 2) : 0;
        $persenAlpa = $totalAttendance > 0 ? round(($totalAlpa / $totalAttendance) * 100, 2) : 0;

        // Create statistics array
        $statistics = [
            'totalStudents' => $allStudents->count(),
            'totalAttendance' => $totalAttendance,
            'totalHadir' => $totalHadir,
            'totalIzin' => $totalIzin,
            'totalSakit' => $totalSakit,
            'totalAlpa' => $totalAlpa,
            'persenHadir' => $persenHadir,
            'persenIzin' => $persenIzin,
            'persenSakit' => $persenSakit,
            'persenAlpa' => $persenAlpa,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        // Generate PDF
        $pdf = PDF::loadView('laporan.absensi-siswa-kelas', [
            'absensi' => $absensi,
            'groupedByDate' => $groupedByDate,
            'groupedByStudent' => $groupedByStudent,
            'allStudents' => $allStudents,
            'statistics' => $statistics,
            'reportTitle' => $reportTitle,
            'kelas' => $kelas,
            'report_type' => $report_type
        ]);

        // Set paper to landscape orientation for better readability
        $pdf->setPaper('a4', 'landscape');

        // Download or show the PDF
        if ($request->input('download', false)) {
            return $pdf->download('laporan_absensi_siswa_' . $report_type . '.pdf');
        } else {
            return $pdf->stream('laporan_absensi_siswa_' . $report_type . '.pdf');
        }
    }
}
