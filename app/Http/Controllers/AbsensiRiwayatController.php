<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use App\Models\AbsensiGuruKelas;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Imports\AbsensiSiswaImport;

class AbsensiRiwayatController extends Controller
{
    /**
     * Menampilkan riwayat absensi berdasarkan jabatan pengguna
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'gerbang'); // Default ke gerbang
        $period = $request->input('period', 'all'); // Default ke semua
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        if ($user->role === 'karyawan') {
            $karyawan = Karyawan::find($user->related_id);
            $jabatan = strtolower($karyawan->jabatan ?? '');

            // Untuk Guru
            if ($jabatan === 'guru') {
                return $this->getGuruAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd);
            }

            // Untuk walikelas
            if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
                return $this->getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd);
            }

            // Untuk Kurikulum
            if ($jabatan === 'kurikulum') {
                return $this->getKurikulumAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd, $kelasId);
            }
        }

        // Default jika jabatan tidak dikenali
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Mendapatkan data absensi untuk Guru
     */
    private function getGuruAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd)
    {
        $karyawanId = $user->related_id;

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang guru
            $query = AbsensiGerbang::where('related_id', $karyawanId);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.riwayat-absensi', compact('absensi', 'type', 'period', 'customStart', 'customEnd'));
        } else {
            // Riwayat absensi mengajar guru
            $query = AbsensiGuruKelas::where('karyawan_id', $karyawanId)
                ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'scanByUser']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.riwayat-absensi', compact('absensi', 'type', 'period', 'customStart', 'customEnd'));
        }
    }


 public function laporanAbsensiGerbang(Request $request)
{
    $user = Auth::user();
    $type = 'gerbang';
    $period = $request->input('period', 'all');
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');
    $kelasId = $request->input('kelas_id');
    $role = $request->input('role');

    [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

    $karyawan = Karyawan::find($user->related_id);
    if (!$karyawan) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
    }

    $jabatan = strtolower($karyawan->jabatan ?? '');

    // Debug: Mari kita lihat struktur data yang sebenarnya
    if (request()->has('debug')) {
        $debugData = AbsensiGerbang::with(['user', 'siswa'])->take(5)->get();
        foreach ($debugData as $item) {
            echo "<pre>";
            echo "AbsensiGerbang ID: " . $item->id . "\n";
            echo "Related ID: " . $item->related_id . "\n";
            echo "User exists: " . ($item->user ? 'YES' : 'NO') . "\n";
            if ($item->user) {
                echo "User ID: " . $item->user->id . "\n";
                echo "User Role: " . $item->user->role . "\n";
            }
            echo "Siswa exists: " . ($item->siswa ? 'YES' : 'NO') . "\n";
            if ($item->siswa) {
                echo "Siswa ID: " . $item->siswa->id . "\n";
                echo "Siswa Nama: " . $item->siswa->nama_lengkap . "\n";
            }
            echo "---\n";
            echo "</pre>";
        }
        exit;
    }

    // Untuk Kurikulum
  if ($jabatan === 'kurikulum') {
    $kelasList = Kelas::all();
    $kelas = $kelasId ? Kelas::find($kelasId) : null;

    // Base query - perbaikan relasi
    $query = AbsensiGerbang::with(['user', 'user.karyawan', 'user.siswa', 'user.siswa.kelas']);

    if ($role === 'siswa') {
        // Filter untuk siswa: ambil yang user rolenya siswa
        $query->whereHas('user', function($q) {
            $q->where('role', 'siswa');
        });
        
        if ($kelasId) {
            $query->whereHas('user.siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
    } elseif ($role === 'karyawan') {
        // Filter untuk karyawan: ambil yang user rolenya karyawan
        $query->whereHas('user', function($q) {
            $q->where('role', 'karyawan');
        });
    } else {
        // Jika tidak ada filter role, tampilkan semua
        if ($kelasId) {
            $query->where(function($q) use ($kelasId) {
                $q->whereHas('user', function($userQ) {
                    $userQ->where('role', 'siswa');
                })->whereHas('user.siswa', function($siswaQ) use ($kelasId) {
                    $siswaQ->where('kelas_id', $kelasId);
                })->orWhereHas('user', function($userQ) {
                    $userQ->where('role', 'karyawan');
                });
            });
        }
    }

    if ($startDate && $endDate) {
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

    return view('karyawan.laporan-absensi-gerbang', compact(
        'absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas', 'role'
    ));
}
    // Untuk Wali Kelas (similar logic)
   if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
    $kelasId = $karyawan->kelas_id;
    $kelas = Kelas::find($kelasId);
    if (!$kelas) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
    }

    // Perbaikan query untuk wali kelas
    $query = AbsensiGerbang::with(['user', 'user.karyawan', 'user.siswa', 'user.siswa.kelas']);

    if ($role === 'siswa') {
        $query->whereHas('user', function($q) {
            $q->where('role', 'siswa');
        })->whereHas('user.siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    } elseif ($role === 'karyawan') {
        $query->whereHas('user', function($q) {
            $q->where('role', 'karyawan');
        });
    } else {
        // Default untuk wali kelas: tampilkan siswa di kelasnya
        $query->whereHas('user', function($q) {
            $q->where('role', 'siswa');
        })->whereHas('user.siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    }

    if ($startDate && $endDate) {
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);
    return view('karyawan.laporan-absensi-gerbang', compact(
        'absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas', 'role'
    ));
}
}

    /**
     * Menangani rute laporan-absensi-siswa
     */
    public function laporanAbsensiSiswa(Request $request)
    {
        $user = Auth::user();
        $type = 'kelas'; // Force type to be kelas
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $jabatan = strtolower($karyawan->jabatan ?? '');

        // Untuk walikelas
        if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
            $kelasId = $karyawan->kelas_id;
            $kelas = Kelas::find($kelasId);

            if (!$kelas) {
                return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
            }

            // Riwayat absensi siswa di kelas
            $query = AbsensiSiswaKelas::whereHas('jadwal', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
                ->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            // Tampilkan view tanpa dropdown kelas untuk walikelas
            return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
        }

        // Untuk Kurikulum
        if ($jabatan === 'kurikulum') {
            // Get kelasId from request for kurikulum
            $kelasId = $request->input('kelas_id');

            // Dapatkan semua kelas untuk dropdown filter
            $kelasList = Kelas::all();

            // Filter berdasarkan kelas jika ada
            $kelas = null;
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }

            // Riwayat absensi siswa di kelas
            $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

            if ($kelasId) {
                $query->whereHas('jadwal', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
        }

        // Default jika jabatan tidak sesuai
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }


    /**
     * Private method to get walikelas Absensi data
     */
    public function getWaliKelasAbsensi(Request $request = null, $type = 'gerbang')
    {
        // If $request is null, get the current request
        if ($request === null) {
            $request = request();
        }

        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'walikelas') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $this->getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd);
    }


    private function getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd)
    {
        $kelasId = $karyawan->kelas_id;
        $kelas = Kelas::find($kelasId);

        if (!$kelas) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
        }

        if ($type === 'gerbang') {
            // Dapatkan ID siswa dari kelas yang diampunya
            $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

            // Riwayat absensi gerbang siswa di kelas
            $query = AbsensiGerbang::whereIn('related_id', $siswaIds)
                ->with(['siswa']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
        } else {
            // Riwayat absensi siswa di kelas
            // FIXED: Use whereHas to filter by the related Jadwal's kelas_id
            $query = AbsensiSiswaKelas::whereHas('jadwal', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
                ->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
        }
    }

    private function getKurikulumAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd, $kelasId)
    {
        // Dapatkan semua kelas untuk dropdown filter
        $kelasList = Kelas::all();

        // Filter berdasarkan kelas jika ada
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
        }

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang semua siswa atau berdasarkan kelas
            $query = AbsensiGerbang::with(['siswa']);

            // Get only student records
            $studentIds = Siswa::pluck('id')->toArray();
            $query->whereIn('related_id', $studentIds);

            if ($kelasId) {
                $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
                $query->whereIn('related_id', $siswaIds);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
        } else {
            // Riwayat absensi siswa di kelas
            $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

            if ($kelasId) {
                // FIXED: Use whereHas to filter by the related Jadwal's kelas_id
                $query->whereHas('jadwal', function ($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                });
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
        }
    }

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

        Log::info('Date range:', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return [$startDate, $endDate];
    }


    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'gerbang');
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $userType = $request->input('role'); // Get user type filter

        Log::info('Export PDF parameters:', [
            'type' => $type,
            'period' => $period,
            'customStart' => $customStart,
            'customEnd' => $customEnd,
            'kelasId' => $kelasId,
            'userType' => $userType,
        ]);

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        if ($user->role === 'karyawan') {
            $karyawan = Karyawan::find($user->related_id);
            $jabatan = strtolower($karyawan->jabatan ?? '');

            // Untuk Wali Kelas - hanya bisa export data kelasnya
            if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
                // Force kelasId to be the wali kelas's assigned class
                $kelasId = $karyawan->kelas_id;
                return $this->exportWaliKelasAbsensiPdf($karyawan, $type, $period, $startDate, $endDate, $userType);
            }

            // Untuk Kurikulum - bisa export data semua kelas
            if ($jabatan === 'kurikulum') {
                return $this->exportKurikulumAbsensiPdf($type, $period, $startDate, $endDate, $kelasId, $userType);
            }
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengekspor data ini.');
    }

    /**
     * Export PDF untuk Guru
     */
    private function exportGuruAbsensiPdf($user, $type, $period, $startDate, $endDate)
    {
        $karyawanId = $user->related_id;
        $karyawan = Karyawan::find($karyawanId);

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang guru
            $query = AbsensiGerbang::where('related_id', $karyawanId);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            $data = [
                'karyawan' => $karyawan,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];

            $pdf = PDF::loadView('absensi.export.export-gerbang-guru', $data);
            return $pdf->download('Absensi_Gerbang_' . $karyawan->nama_lengkap . '.pdf');
        } else {
            // Riwayat absensi mengajar guru
            $query = AbsensiGuruKelas::where('karyawan_id', $karyawanId)
                ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            $data = [
                'karyawan' => $karyawan,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];

            $pdf = PDF::loadView('absensi.export.export-mengajar-guru', $data);
            return $pdf->download('Absensi_Mengajar_' . $karyawan->nama_lengkap . '.pdf');
        }
    }

    /**
     * Export PDF untuk walikelas
     */
    /**
     * Export PDF untuk walikelas
     */
    private function exportWaliKelasAbsensiPdf($karyawan, $type, $period, $startDate, $endDate, $userType = null)
    {
        $kelasId = $karyawan->kelas_id;
        $kelas = Kelas::find($kelasId);

        if (!$kelas) {
            return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
        }

        if ($type === 'gerbang') {
            // Dapatkan ID siswa dari kelas yang diampunya
            $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

            // Riwayat absensi gerbang siswa di kelas
            $query = AbsensiGerbang::whereIn('related_id', $siswaIds)
                ->with(['siswa', 'karyawan']);

            // Apply user type filter if specified
            if ($userType) {
                if ($userType === 'siswa') {
                    $query->whereHas('siswa');
                } elseif ($userType === 'karyawan') {
                    $query->whereHas('karyawan');
                }
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            // Hitung statistik
            $totalSiswa = count($siswaIds);
            $totalAbsensi = $absensi->count();
            $absensiLengkap = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', '!=', null)->count();
            $belumAbsenKeluar = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', null)->count();

            // Add counts by user type
            $totalSiswaAbsensi = $absensi->whereNotNull('siswa_id')->count();
            $totalKaryawanAbsensi = $absensi->whereNotNull('karyawan_id')->count();

            $data = [
                'kelas' => $kelas,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalSiswa' => $totalSiswa,
                'totalAbsensi' => $totalAbsensi,
                'absensiLengkap' => $absensiLengkap,
                'belumAbsenKeluar' => $belumAbsenKeluar,
                'totalSiswaAbsensi' => $totalSiswaAbsensi,
                'totalKaryawanAbsensi' => $totalKaryawanAbsensi,
                'userType' => $userType
            ];

            $pdf = PDF::loadView('absensi.export.export-gerbang-kelas', $data);

            // Include user type in filename if specified
            $filename = 'Absensi_Gerbang_Kelas_' . $kelas->nama_kelas;
            if ($userType) {
                $filename .= '_' . ucfirst($userType);
            }
            $filename .= '.pdf';

            return $pdf->download($filename);
        } else {
            // Code for class attendance remains unchanged
            // ...
        }
    }

    private function exportKurikulumAbsensiPdf($type, $period, $startDate, $endDate, $kelasId, $userType = null)
    {
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
        }

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang semua siswa atau berdasarkan kelas
            $query = AbsensiGerbang::with(['siswa', 'karyawan']);

            // Apply user type filter if specified
            if ($userType) {
                if ($userType === 'siswa') {
                    $query->whereHas('siswa');
                } elseif ($userType === 'karyawan') {
                    $query->whereHas('karyawan');
                }
            }

            // If class filter is specified and we're looking at students (or all users)
            if ($kelasId && ($userType === 'siswa' || !$userType)) {
                $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
                $query->whereIn('related_id', $siswaIds);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            // Calculate statistics
            $totalSiswa = Siswa::when($kelasId, function ($query) use ($kelasId) {
                return $query->where('kelas_id', $kelasId);
            })->count();

            $totalAbsensi = $absensi->count();
            $absensiLengkap = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', '!=', null)->count();
            $belumAbsenKeluar = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', null)->count();

            // Add counts by user type
            $totalSiswaAbsensi = $absensi->whereNotNull('siswa_id')->count();
            $totalKaryawanAbsensi = $absensi->whereNotNull('karyawan_id')->count();

            $data = [
                'kelas' => $kelas,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalSiswa' => $totalSiswa,
                'totalAbsensi' => $totalAbsensi,
                'absensiLengkap' => $absensiLengkap,
                'belumAbsenKeluar' => $belumAbsenKeluar,
                'totalSiswaAbsensi' => $totalSiswaAbsensi,
                'totalKaryawanAbsensi' => $totalKaryawanAbsensi,
                'userType' => $userType
            ];

            $pdf = PDF::loadView('absensi.export.export-gerbang-kelas', $data);

            $filename = 'Absensi_Gerbang_';
            if ($userType) {
                $filename .= ucfirst($userType) . '_';
            }
            $filename .= $kelas ? 'Kelas_' . $kelas->nama_kelas : 'Semua_Kelas';
            $filename .= '.pdf';

            return $pdf->download($filename);
        } else {
            // Existing code for classroom attendance report
            // This code should remain unchanged as it's for a different report type
            $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            // Hitung statistik
            $totalHadir = $absensi->where('status', 'Hadir')->count();
            $totalIzin = $absensi->where('status', 'Izin')->count();
            $totalSakit = $absensi->where('status', 'Sakit')->count();
            $totalAlpa = $absensi->where('status', 'Alpa')->count();

            $data = [
                'kelas' => $kelas,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalHadir' => $totalHadir,
                'totalIzin' => $totalIzin,
                'totalSakit' => $totalSakit,
                'totalAlpa' => $totalAlpa
            ];

            $filename = 'Absensi_Siswa_';
            $filename .= $kelas ? 'Kelas_' . $kelas->nama_kelas : 'Semua_Kelas';
            $filename .= '.pdf';

            $pdf = PDF::loadView('absensi.export.export-siswa-kelas', $data);
            return $pdf->download($filename);
        }
    }

    /**
     * Import absensi siswa dari Excel (khusus Guru)
     */
    public function importForm()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        // Ambil jadwal mengajar guru
        $jadwalMengajar = Jadwal::whereHas('jadwalPelajaran', function ($q) use ($karyawan) {
            $q->where('guru_id', $karyawan->id);
        })->with(['kelas', 'jadwalPelajaran.mataPelajaran'])->get();

        return view('absensi.import-form', compact('jadwalMengajar'));
    }

    /**
     * Proses import absensi siswa dari Excel (khusus Guru)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal' => 'required|date'
        ]);

        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        // Cek apakah jadwal ini diajar oleh guru yang login
        $isValidJadwal = Jadwal::where('id', $request->jadwal_id)
            ->whereHas('jadwalPelajaran', function ($q) use ($karyawan) {
                $q->where('guru_id', $karyawan->id);
            })->exists();

        if (!$isValidJadwal) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk jadwal ini.');
        }

        try {
            // Proses import CSV
            $csvFile = $request->file('file');
            $jadwalId = $request->jadwal_id;
            $tanggal = $request->tanggal;
            $userId = $user->id;

            // Baca file CSV
            $handle = fopen($csvFile->getPathname(), 'r');

            // Lewati header
            $header = fgetcsv($handle);

            // Mulai transaksi database
            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                $nis = $row[0] ?? null;
                $status = $row[1] ?? null;
                $keterangan = $row[2] ?? null;

                if (empty($nis) || empty($status)) {
                    continue;
                }

                // Cari siswa berdasarkan NIS
                $siswa = Siswa::where('nis', $nis)->first();
                if (!$siswa) {
                    Log::warning('Siswa dengan NIS ' . $nis . ' tidak ditemukan');
                    continue;
                }

                // Standarisasi status
                $status = ucfirst(strtolower(trim($status)));
                if (!in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpa'])) {
                    $status = 'Alpa'; // Default ke Alpa jika tidak valid
                }

                // Cek apakah sudah ada absensi
                $existingAbsensi = AbsensiSiswaKelas::where([
                    'related_id' => $siswa->id,
                    'jadwal_id' => $jadwalId,
                    'tanggal' => $tanggal,
                ])->first();

                if ($existingAbsensi) {
                    // Update existing record
                    $existingAbsensi->update([
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'input_by' => $userId,
                    ]);
                } else {
                    // Create new record
                    AbsensiSiswaKelas::create([
                        'related_id' => $siswa->id,
                        'jadwal_id' => $jadwalId,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => $tanggal,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'input_by' => $userId,
                    ]);
                }
            }

            fclose($handle);

            // Commit transaksi
            DB::commit();

            return redirect()->route('karyawan.riwayat-absensi')->with('success', 'Data absensi siswa berhasil diimpor.');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollBack();

            Log::error('Error importing student attendance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template untuk import absensi siswa
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        // Daripada mencari file Excel yang mungkin tidak ada,
        // langsung saja buat template CSV sederhana
        return $this->generateTemplateFile();
    }

    /**
     * Generate template file CSV untuk import absensi
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Generate template file CSV untuk import absensi
     *
     * @return \Illuminate\Http\Response
     */
    private function generateTemplateFile()
    {
        // Buat file CSV sederhana
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_absensi_siswa.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Tambahkan header
            fputcsv($file, ['nis', 'status', 'keterangan']);

            // Tambahkan contoh data
            fputcsv($file, ['123456', 'Hadir', '']);
            fputcsv($file, ['123457', 'Izin', 'Izin keperluan keluarga']);
            fputcsv($file, ['123458', 'Sakit', 'Sakit demam']);
            fputcsv($file, ['123459', 'Alpa', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman profil karyawan
     */
    public function profile()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::with('kelas', 'jurusan', 'tahunAjaran')->find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        return view('karyawan.profile', compact('karyawan', 'user'));
    }

    /**
     * Update profil karyawan
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_wa' => 'nullable|string|max:15',
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        // Update karyawan data
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
     * Menampilkan laporan absensi guru kelas
     */
    public function laporanAbsensiGuruKelas(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $jabatan = strtolower($karyawan->jabatan ?? '');

        // Untuk Wali Kelas
        if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
            $kelasId = $karyawan->kelas_id;
            $kelas = Kelas::find($kelasId);

            if (!$kelas) {
                return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
            }

            // Riwayat absensi guru yang mengajar di kelas ini
            $query = AbsensiGuruKelas::where('kelas_id', $kelasId)
                ->with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-guru-kelas', compact('absensi', 'period', 'customStart', 'customEnd', 'kelas'));
        }

        // Untuk Kurikulum
        if ($jabatan === 'kurikulum') {
            // Dapatkan semua kelas untuk dropdown filter
            $kelasList = Kelas::all();

            // Filter berdasarkan kelas jika ada
            $kelas = null;
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }

            // Riwayat absensi guru di semua kelas atau kelas tertentu
            $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.laporan-absensi-guru-kelas', compact('absensi', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
        }

        // Default jika jabatan tidak sesuai
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Mengekspor laporan absensi guru kelas ke PDF
     */
    public function exportAbsensiGuruKelasPdf(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');

        // Validasi input
        if (!$user || !$period) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        return $this->generateAbsensiGuruKelasPdf($kelasId, $period, $startDate, $endDate);
    }
    /**
     * Generate PDF untuk laporan absensi guru kelas
     */
    private function generateAbsensiGuruKelasPdf($kelasId, $period, $startDate, $endDate)
    {
        // Filter berdasarkan kelas jika ada
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
            if (!$kelas) {
                return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
            }
        }

        // Query absensi guru
        $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Handle jika tidak ada data absensi
        if ($absensi->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data absensi untuk diekspor.');
        }

        // Lanjutkan proses pembuatan PDF
        $data = [
            'reportTitle' => "Laporan Absensi Guru Kelas",
            'kelas' => $kelas,
            'absensi' => $absensi,
            'startDate' => $startDate ? Carbon::parse($startDate) : null,
            'endDate' => $endDate ? Carbon::parse($endDate) : null,
        ];

        $pdf = PDF::loadView('absensi.export.export-guru-kelas', $data);
        $filename = "Absensi_Guru_Kelas_" . now()->format('Ymd') . ".pdf";

        return $pdf->download($filename);
    }
}
