<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use App\Models\AbsensiGuruKelas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LaporanAbsensiGuruKelasController extends Controller
{
    /**
     * Display the teacher classroom attendance report
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $guruId = $request->input('guru_id');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $jabatan = strtolower($karyawan->jabatan ?? '');

        // Untuk Wali Kelas - hanya bisa lihat laporan untuk kelasnya
        if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
            $kelasId = $karyawan->kelas_id;
            $kelas = Kelas::find($kelasId);

            if (!$kelas) {
                return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
            }

            // Riwayat absensi guru di kelas
            $query = AbsensiGuruKelas::where('kelas_id', $kelasId)
                ->with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensiGuru = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-guru', compact('absensiGuru', 'period', 'customStart', 'customEnd', 'kelas'));
        }

        // Untuk Kurikulum - bisa filter berdasarkan kelas dan guru
        if ($jabatan === 'kurikulum') {
            // Dapatkan semua kelas dan guru untuk dropdown filter
            $kelasList = Kelas::all();
            $guruList = Karyawan::where('jabatan', 'Guru')->orWhere('jabatan', 'guru')->get();

            // Filter berdasarkan kelas jika ada
            $kelas = null;
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }

            // Riwayat absensi guru di kelas
            $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($guruId) {
                $query->where('karyawan_id', $guruId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensiGuru = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-guru', compact('absensiGuru', 'period', 'customStart', 'customEnd', 'kelasList', 'guruList', 'kelas', 'guruId'));
        }

        // Default jika jabatan tidak sesuai
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Export teacher classroom attendance report to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $guruId = $request->input('guru_id');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $jabatan = strtolower($karyawan->jabatan ?? '');

        // Untuk Wali Kelas - hanya bisa export data kelasnya
        if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
            $kelasId = $karyawan->kelas_id;
            $kelas = Kelas::find($kelasId);

            if (!$kelas) {
                return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
            }

            // Riwayat absensi guru di kelas
            $query = AbsensiGuruKelas::where('kelas_id', $kelasId)
                ->with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensiGuru = $query->orderBy('tanggal', 'desc')->get();

            // Hitung statistik
            $totalHadir = $absensiGuru->where('status', 'Hadir')->count();
            $totalIzin = $absensiGuru->where('status', 'Izin')->count();
            $totalSakit = $absensiGuru->where('status', 'Sakit')->count();
            $totalAlpa = $absensiGuru->where('status', 'Alpa')->count();

            $data = [
                'kelas' => $kelas,
                'absensiGuru' => $absensiGuru,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalHadir' => $totalHadir,
                'totalIzin' => $totalIzin,
                'totalSakit' => $totalSakit,
                'totalAlpa' => $totalAlpa
            ];

            $pdf = PDF::loadView('absensi.export.export-guru-kelas', $data);
            return $pdf->download('Absensi_Guru_Kelas_' . $kelas->nama_kelas . '.pdf');
        }

        // Untuk Kurikulum - bisa export data semua kelas atau filter berdasarkan kelas dan guru
        if ($jabatan === 'kurikulum') {
            $kelas = null;
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }

            $guru = null;
            if ($guruId) {
                $guru = Karyawan::find($guruId);
            }

            // Riwayat absensi guru di kelas
            $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($guruId) {
                $query->where('karyawan_id', $guruId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensiGuru = $query->orderBy('tanggal', 'desc')->get();

            // Hitung statistik
            $totalHadir = $absensiGuru->where('status', 'Hadir')->count();
            $totalIzin = $absensiGuru->where('status', 'Izin')->count();
            $totalSakit = $absensiGuru->where('status', 'Sakit')->count();
            $totalAlpa = $absensiGuru->where('status', 'Alpa')->count();

            $data = [
                'kelas' => $kelas,
                'guru' => $guru,
                'absensiGuru' => $absensiGuru,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalHadir' => $totalHadir,
                'totalIzin' => $totalIzin,
                'totalSakit' => $totalSakit,
                'totalAlpa' => $totalAlpa
            ];

            $filename = 'Absensi_Guru_';
            $filename .= $kelas ? 'Kelas_' . $kelas->nama_kelas . '_' : '';
            $filename .= $guru ? 'Guru_' . $guru->nama_lengkap . '_' : '';
            $filename .= date('Y-m-d') . '.pdf';

            $pdf = PDF::loadView('absensi.export.export-guru-kelas', $data);
            return $pdf->download($filename);
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengekspor data ini.');
    }

    /**
     * Mendapatkan rentang tanggal berdasarkan periode
     */
    private function getDateRange($period, $customStart, $customEnd)
    {
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
                // Asumsi semester 1: Juli-Desember, semester 2: Januari-Juni
                if (now()->month >= 7) {
                    $startDate = now()->setMonth(7)->setDay(1)->startOfDay();
                    $endDate = now()->setMonth(12)->endOfMonth()->endOfDay();
                } else {
                    $startDate = now()->setMonth(1)->setDay(1)->startOfDay();
                    $endDate = now()->setMonth(6)->endOfMonth()->endOfDay();
                }
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
        }

        return [$startDate, $endDate];
    }
}
