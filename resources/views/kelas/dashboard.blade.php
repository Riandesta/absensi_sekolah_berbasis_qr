@extends('templates')
@section('header', 'Dashboard Kelas')
@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-12">
                        @php
                            $kelasId = Auth::user()->related_id;
                            $kelas = App\Models\Kelas::with(['jurusan'])->find($kelasId);
                        @endphp
                        <h4 class="text-primary">Kelas {{ $kelas->nama_kelas }}</h4>
                        <p class="mb-0">
                            Tingkat: <span class="badge bg-primary">{{ $kelas->tingkat }}</span>
                            Jurusan: <span class="badge bg-info">{{ $kelas->jurusan->nama_jurusan ?? 'Tidak ada jurusan' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Get day mapping to Indonesian
    $hariIni = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
    $hari = $hariIni[now()->format('l')];

    // Get class schedule for today
    $jadwalHariIni = App\Models\Jadwal::where('kelas_id', $kelasId)
        ->where('hari', $hari)
        ->with(['jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
        ->orderBy('jam_mulai')
        ->get();

    // Get all students in this class
    $siswaList = App\Models\Siswa::where('kelas_id', $kelasId)->get();
    $totalSiswa = $siswaList->count();

    // Get attendance statistics for today
    $tanggalHariIni = now()->toDateString();

    // Count students by attendance type
    $siswaHadir = 0;
    $siswaIzin = 0;
    $siswaSakit = 0;
    $siswaAlpa = 0;
    $siswaBelumAbsen = $totalSiswa;

    // Get teacher attendance for today's schedule
    $guruHadir = 0;
    $guruTidakHadir = 0;

    foreach($jadwalHariIni as $jadwal) {
        // Check if teacher is present
        $absensiGuru = App\Models\AbsensiGuruKelas::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        if($absensiGuru) {
            $guruHadir++;
        } else {
            $guruTidakHadir++;
        }

        // Count student attendance for this class and subject
        $absensiSiswa = App\Models\AbsensiSiswaKelas::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggalHariIni)
            ->get();

        foreach($absensiSiswa as $absensi) {
            if($absensi->status == 'Hadir') {
                $siswaHadir++;
            } elseif($absensi->status == 'Izin') {
                $siswaIzin++;
            } elseif($absensi->status == 'Sakit') {
                $siswaSakit++;
            } elseif($absensi->status == 'Alpa') {
                $siswaAlpa++;
            }
        }
    }

    // Calculate not yet attended
    $siswaBelumAbsen = $totalSiswa - ($siswaHadir + $siswaIzin + $siswaSakit + $siswaAlpa);
    if($siswaBelumAbsen < 0) $siswaBelumAbsen = 0;

    // Get gate attendance for today
    $absensiGerbang = App\Models\AbsensiGerbang::whereDate('tanggal', $tanggalHariIni)
        ->whereIn('related_id', $siswaList->pluck('id')->toArray())
        ->count();
@endphp

<!-- Attendance Status -->
<div class="row">
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Absensi Siswa Hari Ini</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card bg-light-success">
                            <div class="card-body px-3 py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon green">
                                            <i class="iconly-boldTicket-Star"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Hadir</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaHadir }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card bg-light-warning">
                            <div class="card-body px-3 py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon yellow">
                                            <i class="iconly-boldDocument"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Izin</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaIzin }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card bg-light-info">
                            <div class="card-body px-3 py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon blue">
                                            <i class="iconly-boldShield-Done"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Sakit</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaSakit }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card bg-light-danger">
                            <div class="card-body px-3 py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stats-icon red">
                                            <i class="iconly-boldDanger"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h6 class="text-muted font-semibold">Alpa</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaAlpa }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light-secondary">
                            <div class="card-body px-3 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted font-semibold">Belum Diabsen</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaBelumAbsen }}</h6>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold">Total Siswa</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalSiswa }}</h6>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold">Absen Gerbang</h6>
                                        <h6 class="font-extrabold mb-0">{{ $absensiGerbang }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Menu Akses Cepat</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.scan') }}" class="btn btn-primary btn-block">
                            <i class="bi bi-qr-code me-2"></i> Scan Absensi Guru
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="#jadwal-section" class="btn btn-info btn-block">
                            <i class="bi bi-calendar-week me-2"></i> Jadwal Hari Ini
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.index') }}" class="btn btn-success btn-block">
                            <i class="bi bi-clipboard-check me-2"></i> Data Absensi Guru
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="#chart-section" class="btn btn-warning btn-block">
                            <i class="bi bi-bar-chart me-2"></i> Statistik Kehadiran
                        </a>
                    </div>
                </div>

                <div class="teacher-attendance-status mt-4">
                    <h5>Status Kehadiran Guru Hari Ini</h5>
                    <div class="progress mt-2" style="height: 25px;">
                        @if($jadwalHariIni->count() > 0)
                            @php
                                $persenHadir = round(($guruHadir / $jadwalHariIni->count()) * 100);
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenHadir }}%"
                                 aria-valuenow="{{ $persenHadir }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $guruHadir }} Guru Hadir ({{ $persenHadir }}%)
                            </div>
                        @else
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                Tidak Ada Jadwal Pelajaran Hari Ini
                            </div>
                        @endif
                    </div>
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
                                <th>Status Absensi Guru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalHariIni as $jadwal)
                            @php
                                $absensiGuru = App\Models\AbsensiGuruKelas::where('jadwal_id', $jadwal->id)
                                    ->where('tanggal', $tanggalHariIni)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                <td>{{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}</td>
                                <td>{{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Data tidak tersedia' }}</td>
                                <td>
                                    @if($absensiGuru)
                                        <span class="badge bg-success">Sudah Absen</span>
                                    @else
                                        <span class="badge bg-danger">Belum Absen</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$absensiGuru)
                                        <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.scan') }}?jadwal_id={{ $jadwal->id }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-qr-code me-1"></i> Scan Guru
                                        </a>
                                    @else
                                        {{-- <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.show', $absensiGuru->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a> --}}
                                         <span class="badge bg-success">Sudah Absen</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada jadwal pelajaran hari ini</td>
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
<div class="row" id="chart-section">
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Statistik Kehadiran Siswa Mingguan</h4>
            </div>
            <div class="card-body">
                <div id="chart-kehadiran-siswa"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Statistik Kehadiran Guru Mingguan</h4>
            </div>
            <div class="card-body">
                <div id="chart-kehadiran-guru"></div>
            </div>
        </div>
    </div>
</div>

<!-- Laporan Absensi -->

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@php
    // Prepare data for weekly charts
    $chartDates = [];
    $chartSiswaHadir = [];
    $chartSiswaIzin = [];
    $chartSiswaSakit = [];
    $chartSiswaAlpa = [];
    $chartGuruHadir = [];
    $chartGuruTidakHadir = [];

    // Get data for the last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $chartDates[] = $date->format('d/m');

        // Student attendance stats
        $absensiSiswa = App\Models\AbsensiSiswaKelas::whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->whereDate('tanggal', $date->toDateString())
            ->get();

        $hadir = $izin = $sakit = $alpa = 0;

        foreach($absensiSiswa as $absensi) {
            if($absensi->status == 'Hadir') {
                $hadir++;
            } elseif($absensi->status == 'Izin') {
                $izin++;
            } elseif($absensi->status == 'Sakit') {
                $sakit++;
            } elseif($absensi->status == 'Alpa') {
                $alpa++;
            }
        }

        $chartSiswaHadir[] = $hadir;
        $chartSiswaIzin[] = $izin;
        $chartSiswaSakit[] = $sakit;
        $chartSiswaAlpa[] = $alpa;

        // Teacher attendance stats
        $totalGuruScheduled = App\Models\Jadwal::where('kelas_id', $kelasId)
            ->where('hari', $hariIni)
            ->count();

        $guruHadir = App\Models\AbsensiGuruKelas::whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->whereDate('tanggal', $date->toDateString())
            ->count();

        $guruTidakHadir = max(0, $totalGuruScheduled - $guruHadir);

        $chartGuruHadir[] = $guruHadir;
        $chartGuruTidakHadir[] = $guruTidakHadir;
    }
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Kehadiran Siswa
        var siswaOptions = {
            series: [{
                name: 'Hadir',
                data: {{ json_encode($chartSiswaHadir) }}
            }, {
                name: 'Izin',
                data: {{ json_encode($chartSiswaIzin) }}
            }, {
                name: 'Sakit',
                data: {{ json_encode($chartSiswaSakit) }}
            }, {
                name: 'Alpa',
                data: {{ json_encode($chartSiswaAlpa) }}
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: {{ json_encode($chartDates) }},
            },
            yaxis: {
                title: {
                    text: 'Jumlah Siswa'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " siswa"
                    }
                }
            },
            colors: ['#435ebe', '#55c6e8', '#5ddab4', '#ff7976']
        };

        var siswaChart = new ApexCharts(document.querySelector("#chart-kehadiran-siswa"), siswaOptions);
        siswaChart.render();

        // Chart Kehadiran Guru
        var guruOptions = {
            series: [{
                name: 'Hadir',
                data: {{ json_encode($chartGuruHadir) }}
            }, {
                name: 'Tidak Hadir',
                data: {{ json_encode($chartGuruTidakHadir) }}
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: {{ json_encode($chartDates) }},
            },
            yaxis: {
                title: {
                    text: 'Jumlah Guru'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " guru"
                    }
                }
            },
            colors: ['#435ebe', '#ff7976']
        };

        var guruChart = new ApexCharts(document.querySelector("#chart-kehadiran-guru"), guruOptions);
        guruChart.render();
    });
</script>

@endsection
