@extends('templates')
@section('header', 'Dashboard Siswa')

@push('styles')
    <style>
        .status-box {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.08);
        }

        .status-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

      .status-icon {
    width: 52px;
    height: 52px;
    display: flex;
    justify-content: center;  /* Centers horizontally */
    align-items: center;      /* Centers vertically */
    border-radius: 50%;       /* Makes the icon container circular */
    font-size: 1.5rem;        /* Adjust the size of the icon */
    color: #fff;
    margin-right: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    box-sizing: border-box;
    text-align: center;  /* Ensures the icon itself is centered */
}


        .status-content {
            flex: 1;
            min-width: 0;
        }

        .status-content h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #2c3e50;
            line-height: 1.3;
        }

        .status-content small {
            font-size: 13px;
            color: #6c757d;
            display: block;
            line-height: 1.4;
        }

        .status-content .status-time {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2px;
        }

        .badge-status {
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Status Colors */
        .bg-absen-true {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #b8dacc;
        }

        .bg-absen-false {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            border-color: #f1959b;
        }

        .icon-absen-true {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .icon-absen-false {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        }

        .bg-jadwal {
            background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%);
            border-color: #b3d9ff;
        }

        .icon-jadwal {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
        }

        /* Menu buttons */
        .menu-btn {
            text-align: center;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid transparent;
        }

        .menu-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            text-decoration: none;
        }

        .menu-btn i {
            font-size: 1.1rem;
        }

        /* Card improvements */
        .stats-card {
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 1.25rem 1.5rem 1rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .chart-container {
            min-height: 300px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .table-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        /* Badge improvements */
        .badge {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Welcome section improvements */
        .welcome-badge {
            border-radius: 8px;
            font-weight: 600;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .status-box {
                padding: 1rem;
            }

            .status-icon {
                width: 44px;
                height: 44px;
                font-size: 1.25rem;
                margin-right: 0.75rem;
            }

            .status-content h6 {
                font-size: 15px;
            }

            .status-content small {
                font-size: 12px;
            }

            .menu-btn {
                padding: 0.875rem;
                font-size: 14px;
            }
        }

        /* Animation for empty states */
        .empty-state i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stats-card border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-primary mb-3 fw-bold">
                                Selamat Datang, {{ Auth::user()->siswa->nama_lengkap }}
                            </h3>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary welcome-badge fs-6 px-3 py-2">
                                    <i class="bi bi-mortarboard me-2"></i>
                                    {{ Auth::user()->siswa->kelas->nama_kelas ?? 'Tidak ada kelas' }}
                                </span>
                                <span class="badge bg-info welcome-badge fs-6 px-3 py-2">
                                    <i class="bi bi-bookmark me-2"></i>
                                    {{ Auth::user()->siswa->jurusan->nama_jurusan ?? 'Tidak ada jurusan' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="text-muted fw-medium">
                                <i class="bi bi-calendar3 me-2"></i>
                                {{ now()->format('l, d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Menu Cepat -->
    <div class="row mb-4">
        @php
            $siswaId = Auth::user()->related_id;

            // Absensi Gerbang Hari Ini
            $absensiGerbangHariIni = App\Models\AbsensiGerbang::where('related_id', $siswaId)
                ->whereDate('tanggal', now()->toDateString())
                ->first();

            // Jadwal Hari Ini
            $hariIni = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu',
            ];
            $hari = $hariIni[now()->format('l')];
            $kelasId = Auth::user()->siswa->kelas_id;
            $jadwalHariIni = App\Models\Jadwal::where('kelas_id', $kelasId)
                ->where('hari', $hari)
                ->with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
                ->orderBy('jam_mulai')
                ->get();

            // Absensi Kelas Hari Ini
            $tanggalHariIni = now()->toDateString();
            $absensiKelas = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
                ->whereDate('tanggal', $tanggalHariIni)
                ->pluck('status', 'jadwal_id')
                ->toArray();
        @endphp

        <!-- Status Box -->
        <div class="col-12 col-lg-5 mb-4 mb-lg-0">
            <div class="card stats-card border-0 h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-clipboard-check me-2 text-primary"></i>Status Absensi Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Absensi Gerbang -->
                    <div class="status-box {{ $absensiGerbangHariIni ? 'bg-absen-true' : 'bg-absen-false' }}">
                        <div class="status-icon {{ $absensiGerbangHariIni ? 'icon-absen-true' : 'icon-absen-false' }}">
                            <i class="bi bi-{{ $absensiGerbangHariIni ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                        </div>
                        <div class="status-content">
                            <h6>{{ $absensiGerbangHariIni ? 'Sudah Absensi Gerbang' : 'Belum Absensi Gerbang' }}</h6>
                            @if ($absensiGerbangHariIni)
                                <div class="status-time">
                                    <small>
                                        <i class="bi bi-box-arrow-in-right me-1 text-success"></i>
                                        <strong>Masuk:</strong> {{ $absensiGerbangHariIni->waktu_scan_masuk }}
                                    </small>
                                </div>
                                <div class="status-time">
                                    <small class="text-{{ $absensiGerbangHariIni->waktu_scan_keluar ? 'success' : 'warning' }}">
                                        <i class="bi bi-box-arrow-right me-1"></i>
                                        <strong>Keluar:</strong> {{ $absensiGerbangHariIni->waktu_scan_keluar ?? 'Belum absen keluar' }}
                                    </small>
                                </div>
                            @else
                                <small class="text-muted">
                                    <i class="bi bi-qr-code me-1"></i>
                                    Silahkan scan QR code di gerbang sekolah
                                </small>
                            @endif
                        </div>
                    </div>

                    <!-- Jadwal Hari Ini -->
                    <div class="status-box bg-jadwal">
                        <div class="status-icon icon-jadwal">
                            <i class="bi bi-calendar-date-fill"></i>
                        </div>
                        <div class="status-content">
                            <h6>Jadwal Pelajaran Hari Ini</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-status bg-{{ $jadwalHariIni->count() > 0 ? 'primary' : 'secondary' }} text-white">
                                    <i class="bi bi-{{ $jadwalHariIni->count() > 0 ? 'book' : 'calendar-x' }}"></i>
                                    {{ $jadwalHariIni->count() > 0 ? $jadwalHariIni->count() . ' Mata Pelajaran' : 'Tidak Ada Jadwal' }}
                                </span>
                            </div>
                            @if($jadwalHariIni->count() > 0)
                                <small class="text-muted mt-1">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ substr($jadwalHariIni->first()->jam_mulai, 0, 5) }} - {{ substr($jadwalHariIni->last()->jam_selesai, 0, 5) }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Akses Cepat -->
        <div class="col-12 col-lg-7">
            <div class="card stats-card border-0 h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Menu Akses Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <a href="{{ route('siswa.profile') }}" class="menu-btn btn btn-warning">
                                <i class="bi bi-person-badge-fill"></i><span>Profil Saya</span>
                            </a>
                        </div>
                        <div class="col-md-6 col-12">
                            <a href="{{ route('siswa.download-qrcode') }}" class="menu-btn btn btn-success">
                                <i class="bi bi-qr-code"></i><span>Download QR Code</span>
                            </a>
                        </div>
                        <div class="col-md-6 col-12">
                            <a href="#jadwal-section" class="menu-btn btn btn-info">
                                <i class="bi bi-calendar-week-fill"></i><span>Jadwal Hari Ini</span>
                            </a>
                        </div>
                        <div class="col-md-6 col-12">
                            <a href="#statistik-section" class="menu-btn btn btn-primary">
                                <i class="bi bi-graph-up-arrow"></i><span>Statistik Kehadiran</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Pelajaran Hari Ini -->
    <div class="row mb-4" id="jadwal-section">
        <div class="col-12">
            <div class="card stats-card border-0">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-calendar-week me-2 text-primary"></i>Jadwal Pelajaran Hari Ini ({{ $hari }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-container">
                        <table class="table table-hover table-borderless mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-clock me-2"></i>Jam</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-book me-2"></i>Mata Pelajaran</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-person me-2"></i>Guru</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-check-circle me-2"></i>Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwalHariIni as $jadwal)
                                    <tr class="border-bottom">
                                        <td class="fw-semibold py-3">
                                            <span class="badge bg-light text-dark px-3 py-2">
                                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}
                                        </td>
                                        <td class="py-3">
                                            {{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Data tidak tersedia' }}
                                        </td>
                                        <td class="py-3">
                                            @if (isset($absensiKelas[$jadwal->id]))
                                                @switch($absensiKelas[$jadwal->id])
                                                    @case('Hadir')
                                                        <span class="badge bg-success badge-status">
                                                            <i class="bi bi-check-circle-fill"></i>Hadir
                                                        </span>
                                                        @break
                                                    @case('Izin')
                                                        <span class="badge bg-warning badge-status">
                                                            <i class="bi bi-exclamation-circle-fill"></i>Izin
                                                        </span>
                                                        @break
                                                    @case('Sakit')
                                                        <span class="badge bg-info badge-status">
                                                            <i class="bi bi-heart-pulse-fill"></i>Sakit
                                                        </span>
                                                        @break
                                                    @case('Alpa')
                                                        <span class="badge bg-danger badge-status">
                                                            <i class="bi bi-x-circle-fill"></i>Alpa
                                                        </span>
                                                        @break
                                                @endswitch
                                            @else
                                                <span class="badge bg-secondary badge-status">
                                                    <i class="bi bi-hourglass-split"></i>Belum Diabsen
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty-state text-muted">
                                                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                                <h6 class="fw-semibold">Tidak ada jadwal pelajaran hari ini</h6>
                                                <small>Silahkan nikmati waktu istirahat Anda</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Kehadiran -->
    <div class="row mb-4" id="statistik-section">
        <div class="col-12">
            <div class="card stats-card border-0">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-graph-up me-2 text-primary"></i>Statistik Kehadiran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="chart-container p-3">
                                <div id="chart-kehadiran"></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-container p-3">
                                <div id="chart-kehadiran-line"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Absensi Gerbang -->
    <div class="row">
        <div class="col-12">
            <div class="card stats-card border-0">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Absensi Gerbang (5 Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-container">
                        <table class="table table-hover table-borderless mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-calendar me-2"></i>Tanggal</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-box-arrow-right me-2"></i>Keluar</th>
                                    <th class="fw-bold border-0 py-3"><i class="bi bi-check-circle me-2"></i>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $riwayatAbsensi = App\Models\AbsensiGerbang::where('related_id', $siswaId)
                                        ->orderBy('tanggal', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($riwayatAbsensi as $absensi)
                                    <tr class="border-bottom">
                                        <td class="fw-semibold py-3">
                                            <span class="badge bg-light text-dark px-3 py-2">
                                                {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @if ($absensi->waktu_scan_masuk)
                                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>{{ $absensi->waktu_scan_masuk }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($absensi->waktu_scan_keluar)
                                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>{{ $absensi->waktu_scan_keluar }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                                    <i class="bi bi-hourglass me-1"></i>Belum absen keluar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if ($absensi->waktu_scan_keluar)
                                                <span class="badge bg-success badge-status">
                                                    <i class="bi bi-check-circle-fill"></i>Lengkap
                                                </span>
                                            @else
                                                <span class="badge bg-warning badge-status">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>Belum Lengkap
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty-state text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                <h6 class="fw-semibold">Belum ada riwayat absensi</h6>
                                                <small>Riwayat absensi akan muncul setelah Anda melakukan absensi</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
@php
    // Hitung statistik kehadiran
    $totalHadir = $totalIzin = $totalSakit = $totalAlpa = 0;
    $startDate = now()->subDays(30)->startOfDay();
    $endDate = now()->endOfDay();
    $absensiMonth = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->get();

    foreach ($absensiMonth as $absensi) {
        switch ($absensi->status) {
            case 'Hadir': $totalHadir++; break;
            case 'Izin': $totalIzin++; break;
            case 'Sakit': $totalSakit++; break;
            case 'Alpa': $totalAlpa++; break;
        }
    }

    $totalAbsensi = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
    $persenHadir = $totalAbsensi ? round(($totalHadir / $totalAbsensi) * 100) : 0;
    $persenIzin = $totalAbsensi ? round(($totalIzin / $totalAbsensi) * 100) : 0;
    $persenSakit = $totalAbsensi ? round(($totalSakit / $totalAbsensi) * 100) : 0;
    $persenAlpa = $totalAbsensi ? round(($totalAlpa / $totalAbsensi) * 100) : 0;

    // Data harian untuk chart tren
    $dailyData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $displayDate = now()->subDays($i)->format('d/m');
        $dayAbsensi = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $date)
            ->get();

        $hadir = $alpa = 0;
        foreach ($dayAbsensi as $absensi) {
            if ($absensi->status == 'Hadir') $hadir++;
            if ($absensi->status == 'Alpa') $alpa++;
        }
        $dailyData[] = ['date' => $displayDate, 'hadir' => $hadir, 'alpa' => $alpa];
    }
@endphp

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Pie Chart
        var optionsPie = {
            series: [{{ $totalHadir }}, {{ $totalIzin }}, {{ $totalSakit }}, {{ $totalAlpa }}],
            chart: {
                type: "donut",
                height: 350,
                toolbar: { show: false }
            },
            labels: ["Hadir", "Izin", "Sakit", "Alpa"],
            colors: ["#435ebe", "#55c6e8", "#5ddab4", "#ff7976"],
            title: {
                text: "Statistik Kehadiran (30 Hari)",
                align: "center",
                style: { fontSize: '16px', fontWeight: 'bold', color: '#2c3e50' }
            },
            legend: {
                position: "bottom",
                horizontalAlign: "center",
                fontSize: '14px'
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '14px', fontWeight: 'bold' }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "60%",
                        labels: {
                            total: {
                                show: true,
                                label: "Total",
                                fontSize: '16px',
                                fontWeight: 'bold',
                                color: '#2c3e50',
                                formatter: function () {
                                    return {{ $totalAbsensi }};
                                }
                            }
                        }
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 300 },
                    legend: { position: 'bottom' }
                }
            }]
        };
        new ApexCharts(document.querySelector("#chart-kehadiran"), optionsPie).render();

        // Line Chart
        var optionsLine = {
            series: [{
                name: "Hadir",
                data: [@foreach ($dailyData as $day) {{ $day['hadir'] }}, @endforeach]
            }, {
                name: "Alpa",
                data: [@foreach ($dailyData as $day) {{ $day['alpa'] }}, @endforeach]
            }],
            chart: {
                type: "area",
                height: 350,
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            colors: ['#28a745', '#dc3545'],
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.6, opacityTo: 0.1 }
            },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: [@foreach ($dailyData as $day) "{{ $day['date'] }}", @endforeach],
                labels: { style: { fontSize: '12px' } }
            },
            yaxis: {
                title: {
                    text: "Jumlah Mata Pelajaran",
                    style: { fontSize: '14px', fontWeight: 'bold', color: '#2c3e50' }
                },
                labels: { style: { fontSize: '12px' } }
            },
            title: {
                text: "Tren Kehadiran (7 Hari Terakhir)",
                align: "center",
                style: { fontSize: '16px', fontWeight: 'bold', color: '#2c3e50' }
            },
            tooltip: {
                y: { formatter: function(val) { return val + " mata pelajaran"; } }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '14px'
            },
            grid: { borderColor: '#e7e7e7', strokeDashArray: 5 },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { height: 300 },
                    title: { style: { fontSize: '14px' } }
                }
            }]
        };
        new ApexCharts(document.querySelector("#chart-kehadiran-line"), optionsLine).render();

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
</script>
@endpush
@endsection
