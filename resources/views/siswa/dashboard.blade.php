@extends('templates')
@section('header', 'Dashboard Siswa')
@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-primary">Selamat Datang, {{ Auth::user()->siswa->nama_lengkap }}</h4>
                        <p class="mb-0">
                            Kelas: <span class="badge bg-primary">{{ Auth::user()->siswa->kelas->nama_kelas ?? 'Tidak ada kelas' }}</span>
                            Jurusan: <span class="badge bg-info">{{ Auth::user()->siswa->jurusan->nama_jurusan ?? 'Tidak ada jurusan' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $siswaId = Auth::user()->related_id;

    // Check if the student has entered gate attendance today
    $absensiGerbangHariIni = App\Models\AbsensiGerbang::where('related_id', $siswaId)
        ->whereDate('tanggal', now()->toDateString())
        ->first();

    // Get class schedule for today
    $hariIni = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
    $hari = $hariIni[now()->format('l')];

    $kelasId = Auth::user()->siswa->kelas_id;
    $jadwalHariIni = App\Models\Jadwal::where('kelas_id', $kelasId)
        ->where('hari', $hari)
        ->with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
        ->orderBy('jam_mulai')
        ->get();

    // Get class attendance for today's lessons
    $tanggalHariIni = now()->toDateString();
    $absensiKelas = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
        ->whereDate('tanggal', $tanggalHariIni)
        ->pluck('status', 'jadwal_id')
        ->toArray();
@endphp

<div class="row">
    <!-- Status Absensi -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4>Status Absensi Hari Ini</h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    @if($absensiGerbangHariIni)
                        <div class="avatar avatar-xl bg-success me-3">
                            <i class="bi bi-check-circle fs-3 text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Sudah Absensi Gerbang</h5>
                            <small>Masuk: {{ $absensiGerbangHariIni->waktu_scan_masuk }}</small>
                            <br>
                            <small>Keluar: {{ $absensiGerbangHariIni->waktu_scan_keluar ?? 'Belum absen keluar' }}</small>
                        </div>
                    @else
                        <div class="avatar avatar-xl bg-danger me-3">
                            <i class="bi bi-x-circle fs-3 text-white"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Belum Absensi Gerbang</h5>
                            <p class="text-muted">Silahkan scan QR code di gerbang sekolah</p>
                        </div>
                    @endif
                </div>

                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl bg-info me-3">
                        <i class="bi bi-calendar-date fs-3 text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Jadwal Pelajaran Hari Ini</h5>
                        <span class="badge bg-{{ $jadwalHariIni->count() > 0 ? 'primary' : 'secondary' }}">
                            {{ $jadwalHariIni->count() > 0 ? $jadwalHariIni->count() . ' Mata Pelajaran' : 'Tidak Ada Jadwal' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Akses Cepat -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Menu Akses Cepat</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-6 mb-3">
                        <a href="{{ route('siswa.riwayat-absensi-persiswa') }}" class="btn btn-primary btn-block">
                            <i class="bi bi-clipboard-check me-1"></i> Riwayat Absensi
                        </a>
                    </div>


                    <div class="col-md-4 col-6 mb-3">
                        <a href="#jadwal-section" class="btn btn-info btn-block">
                            <i class="bi bi-calendar-week me-1"></i> Jadwal Pelajaran
                        </a>
                    </div>

                    <div class="col-md-4 col-6 mb-3">
                        <a href="{{ route('siswa.profile') }}" class="btn btn-warning btn-block">
                            <i class="bi bi-person-badge me-1"></i> Profil Saya
                        </a>
                    </div>

                    <div class="col-md-4 col-6 mb-3">
                        <a href="{{ route('siswa.download-qrcode') }}" class="btn btn-success btn-block">
                            <i class="bi bi-download me-1"></i> Download QR
                        </a>
                    </div>


                    {{-- <div class="col-md-4 col-6 mb-3">
                        <a href="#" class="btn btn-danger btn-block" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Pelajaran Hari Ini -->
<div class="row" id="jadwal-section">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Jadwal Pelajaran Hari Ini ({{ $hari }})</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Status Absensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalHariIni as $jadwal)
                            <tr>
                                <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                <td>{{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}</td>
                                <td>{{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Data tidak tersedia' }}</td>
                                <td>
                                    @if(isset($absensiKelas[$jadwal->id]))
                                        @if($absensiKelas[$jadwal->id] == 'Hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($absensiKelas[$jadwal->id] == 'Izin')
                                            <span class="badge bg-warning">Izin</span>
                                        @elseif($absensiKelas[$jadwal->id] == 'Sakit')
                                            <span class="badge bg-info">Sakit</span>
                                        @elseif($absensiKelas[$jadwal->id] == 'Alpa')
                                            <span class="badge bg-danger">Alpa</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Belum Diabsen</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada jadwal pelajaran hari ini</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Kehadiran -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Statistik Kehadiran</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="chart-kehadiran"></div>
                    </div>
                    <div class="col-md-6">
                        <div id="chart-kehadiran-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Absensi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Riwayat Absensi Gerbang</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Status</th>
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
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $absensi->waktu_scan_masuk }}</td>
                                <td>{{ $absensi->waktu_scan_keluar ?: 'Belum absen keluar' }}</td>
                                <td>
                                    @if($absensi->waktu_scan_keluar)
                                        <span class="badge bg-success">Lengkap</span>
                                    @else
                                        <span class="badge bg-warning">Belum Absen Keluar</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada riwayat absensi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('siswa.riwayat-absensi-persiswa') }}" class="btn btn-primary">
                            Lihat Semua Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Calculate stats for chart
    $totalHadir = 0;
    $totalIzin = 0;
    $totalSakit = 0;
    $totalAlpa = 0;

    // Get attendance for the last 30 days
    $startDate = now()->subDays(30)->startOfDay();
    $endDate = now()->endOfDay();

    $absensiMonth = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
        ->whereBetween('tanggal', [$startDate, $endDate])
        ->get();

    foreach($absensiMonth as $absensi) {
        if($absensi->status == 'Hadir') {
            $totalHadir++;
        } elseif($absensi->status == 'Izin') {
            $totalIzin++;
        } elseif($absensi->status == 'Sakit') {
            $totalSakit++;
        } elseif($absensi->status == 'Alpa') {
            $totalAlpa++;
        }
    }

    $totalAbsensi = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
    if($totalAbsensi > 0) {
        $persenHadir = round(($totalHadir / $totalAbsensi) * 100);
        $persenIzin = round(($totalIzin / $totalAbsensi) * 100);
        $persenSakit = round(($totalSakit / $totalAbsensi) * 100);
        $persenAlpa = round(($totalAlpa / $totalAbsensi) * 100);
    } else {
        $persenHadir = 0;
        $persenIzin = 0;
        $persenSakit = 0;
        $persenAlpa = 0;
    }

    // Get daily attendance for the last 7 days for line chart
    $dailyData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i)->format('Y-m-d');
        $displayDate = now()->subDays($i)->format('d/m');

        // Count attendance types for this day
        $dayAbsensi = App\Models\AbsensiSiswaKelas::where('siswa_id', $siswaId)
            ->whereDate('tanggal', $date)
            ->get();

        $dayHadir = 0;
        $dayAlpa = 0;

        foreach($dayAbsensi as $absensi) {
            if($absensi->status == 'Hadir') {
                $dayHadir++;
            } elseif($absensi->status == 'Alpa') {
                $dayAlpa++;
            }
        }

        $dailyData[] = [
            'date' => $displayDate,
            'hadir' => $dayHadir,
            'alpa' => $dayAlpa
        ];
    }
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Kehadiran (Pie)
        var options = {
            series: [{{ $persenHadir }}, {{ $persenIzin }}, {{ $persenSakit }}, {{ $persenAlpa }}],
            chart: {
                width: '77%',
                type: 'pie',
            },
            labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
            colors: ['#435ebe', '#55c6e8', '#5ddab4', '#ff7976'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            title: {
                text: 'Kehadiran 30 Hari Terakhir',
                align: 'left'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-kehadiran"), options);
        chart.render();

        // Chart Kehadiran Harian (Line)
        var lineOptions = {
            series: [
                {
                    name: 'Hadir',
                    data: [
                        @foreach($dailyData as $day)
                            {{ $day['hadir'] }},
                        @endforeach
                    ],
                    color: '#435ebe'
                },
                {
                    name: 'Alpa',
                    data: [
                        @foreach($dailyData as $day)
                            {{ $day['alpa'] }},
                        @endforeach
                    ],
                    color: '#ff7976'
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            xaxis: {
                categories: [
                    @foreach($dailyData as $day)
                        '{{ $day['date'] }}',
                    @endforeach
                ],
                labels: {
                    style: {
                        colors: '#9e9e9e',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Mata Pelajaran'
                },
                min: 0,
                forceNiceScale: true
            },
            title: {
                text: 'Kehadiran 7 Hari Terakhir',
                align: 'center'
            },
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            },
            grid: {
                borderColor: '#e0e0e0',
                row: {
                    colors: ['#f5f5f5', 'transparent'],
                    opacity: 0.5
                }
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center'
            }
        };

        var lineChart = new ApexCharts(document.querySelector("#chart-kehadiran-line"), lineOptions);
        lineChart.render();
    });
</script>

@endsection
