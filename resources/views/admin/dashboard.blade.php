@extends('templates')
@section('header', 'Dashboard Admin')
@section('content')
<div class="row">
    <!-- Total Siswa -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon purple">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Siswa</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalSiswa }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon blue">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Guru</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalGuru }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Karyawan -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon green">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Karyawan</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalKaryawan }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kelas -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon red">
                            <i class="iconly-boldBookmark"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Kelas</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalKelas }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Akses Cepat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Menu Akses Cepat</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route(Auth::user()->role .'.absensi-gerbang.scan') }}" class="btn btn-primary btn-block">
                            <i class="bi bi-qr-code me-2"></i> Scan Absensi Gerbang
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.scan') }}" class="btn btn-success btn-block">
                            <i class="bi bi-person-video3 me-2"></i> Scan Absensi Guru
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('siswa.index') }}" class="btn btn-info btn-block">
                            <i class="bi bi-mortarboard me-2"></i> Manajemen Siswa
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('karyawan.index') }}" class="btn btn-warning btn-block">
                            <i class="bi bi-person-badge me-2"></i> Manajemen Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Absensi Siswa -->
<div class="row">
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Absensi Siswa</h4>
            </div>
            <div class="card-body">
                <div id="chart-absensi-siswa"></div>
            </div>
        </div>
    </div>

    <!-- Grafik Absensi Karyawan -->
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Absensi Karyawan</h4>
            </div>
            <div class="card-body">
                <div id="chart-absensi-karyawan"></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Absensi Terbaru -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Absensi Gerbang Terbaru</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestAbsensi as $absensi)
                                <tr>
                                    <td class="col-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <div class="avatar-initial rounded-circle bg-label-{{ $absensi->siswa ? 'primary' : 'info' }}">
                                                    {{ $absensi->siswa ? 'S' : 'K' }}
                                                </div>
                                            </div>
                                            <p class="font-bold ms-3 mb-0">
                                                @if($absensi->siswa)
                                                    {{ $absensi->siswa->nama_lengkap }}
                                                    <span class="badge bg-light-primary">Siswa</span>
                                                @elseif($absensi->karyawan)
                                                    {{ $absensi->karyawan->nama_lengkap }}
                                                    <span class="badge bg-light-info">Karyawan</span>
                                                @else
                                                    Data tidak ditemukan
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td class="col-auto">
                                        <span class="badge bg-success">{{ $absensi->status }}</span>
                                    </td>
                                    <td class="col-auto">
                                        {{ $absensi->waktu_scan_masuk }}
                                    </td>
                                    <td class="col-auto">
                                        {{ $absensi->waktu_scan_keluar ?: 'Belum absen keluar' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data absensi hari ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route(Auth::user()->role .'.absensi-gerbang.index') }}" class="btn btn-primary">
                            Lihat Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Absensi Siswa
        var siswaChartOptions = {
            series: [
                {
                    name: 'Hadir',
                    data: [{{ implode(',', $siswaHadirData) }}]
                },
                {
                    name: 'Izin',
                    data: [{{ implode(',', $siswaIzinData) }}]
                },
                {
                    name: 'Sakit',
                    data: [{{ implode(',', $siswaSakitData) }}]
                },
                {
                    name: 'Alpa',
                    data: [{{ implode(',', $siswaAlpaData) }}]
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                categories: {!! json_encode($chartDates) !!},
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: ['#435ebe', '#55c6e8', '#f3616d', '#fd7e14']
        };

        var siswaChart = new ApexCharts(document.querySelector("#chart-absensi-siswa"), siswaChartOptions);
        siswaChart.render();

        // Chart Absensi Karyawan
        var karyawanChartOptions = {
            series: [
                {
                    name: 'Hadir',
                    data: [{{ implode(',', $karyawanHadirData) }}]
                },
                {
                    name: 'Tidak Hadir',
                    data: [{{ implode(',', $karyawanTidakHadirData) }}]
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: true
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {
                categories: {!! json_encode($chartDates) !!},
            },
            legend: {
                position: 'right',
                offsetY: 40
            },
            fill: {
                opacity: 1
            },
            colors: ['#5ddab4', '#ff7976']
        };

        var karyawanChart = new ApexCharts(document.querySelector("#chart-absensi-karyawan"), karyawanChartOptions);
        karyawanChart.render();
    });
</script>

@endsection
