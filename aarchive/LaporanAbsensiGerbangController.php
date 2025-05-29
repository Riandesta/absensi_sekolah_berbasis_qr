<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Karyawan;
use App\Models\AbsensiGerbang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class LaporanAbsensiGerbangController extends Controller
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
        $query = AbsensiGerbang::query();

        // Apply filters
        if ($kelas_id) {
            // If class is provided, get all students from that class
            $siswaIds = Siswa::where('kelas_id', $kelas_id)->pluck('id')->toArray();
            $query->whereIn('related_id', $siswaIds);
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
                $reportTitle = 'Laporan Harian - ' . $date->format('d M Y');
                break;

            case 'weekly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfWeek();
                $endDate = $date->copy()->endOfWeek();
                $reportTitle = 'Laporan Mingguan - ' . $startDate->format('d M Y') . ' s/d ' . $endDate->format('d M Y');
                break;

            case 'monthly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfMonth();
                $endDate = $date->copy()->endOfMonth();
                $reportTitle = 'Laporan Bulanan - ' . $date->format('F Y');
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
                $reportTitle = 'Laporan Semester ' . $semester . ' - ' . $year;
                break;

            case 'yearly':
                $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
                $startDate = $date->copy()->startOfYear();
                $endDate = $date->copy()->endOfYear();
                $reportTitle = 'Laporan Tahunan - ' . $date->format('Y');
                break;
        }

        // Apply date filter to query
        $query->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

        // Get results with relations
        $absensi = $query->with(['siswa', 'karyawan', 'scannedBy'])->get();

        // Group data by date
        $groupedData = $absensi->groupBy(function($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        });

        // Calculate statistics
        $totalRecords = $absensi->count();
        $siswaCount = $absensi->where('siswa', '!=', null)->count();
        $karyawanCount = $absensi->where('karyawan', '!=', null)->count();
        $completeAttendance = $absensi->whereNotNull('waktu_scan_keluar')->count();
        $incompleteAttendance = $absensi->whereNull('waktu_scan_keluar')->count();

        // Create statistics array
        $statistics = [
            'totalRecords' => $totalRecords,
            'siswaCount' => $siswaCount,
            'karyawanCount' => $karyawanCount,
            'completeAttendance' => $completeAttendance,
            'incompleteAttendance' => $incompleteAttendance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        // Generate PDF
        $pdf = PDF::loadView('laporan.absensi-gerbang', [
            'absensi' => $absensi,
            'groupedData' => $groupedData,
            'statistics' => $statistics,
            'reportTitle' => $reportTitle,
            'kelas' => $kelas,
            'report_type' => $report_type
        ]);

        // Set paper to landscape orientation for better readability
        $pdf->setPaper('a4', 'landscape');

        // Download or show the PDF
        if ($request->input('download', false)) {
            return $pdf->download('laporan_absensi_gerbang_' . $report_type . '.pdf');
        } else {
            return $pdf->stream('laporan_absensi_gerbang_' . $report_type . '.pdf');
        }
    }
}
