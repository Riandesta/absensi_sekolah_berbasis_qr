@extends('templates')
@section('header', 'Dashboard Karyawan')
@section('content')
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-primary">Selamat Datang, {{ Auth::user()->karyawan->nama_lengkap }}</h4>
                            <p class="mb-0">Anda login sebagai <span
                                    class="badge bg-primary">{{ ucfirst(Auth::user()->role) }}</span> dengan jabatan <span
                                    class="badge bg-info">{{ Auth::user()->karyawan->jabatan }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $isGuru = strcasecmp(Auth::user()->karyawan->jabatan, 'guru') === 0;
        $isWaliKelas =
            strcasecmp(Auth::user()->karyawan->jabatan, 'wali kelas') === 0 && !empty(Auth::user()->karyawan->kelas_id);
        $isKurikulum = strcasecmp(Auth::user()->karyawan->jabatan, 'kurikulum') === 0;

        $karyawanId = Auth::user()->related_id;
        $karyawan = Auth::user()->karyawan; // Make sure karyawan is always defined
        $isPetugasPiketHariIni = App\Models\PetugasPiket::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        // Check if the employee has entered gate attendance today
        $absensiGerbangHariIni = App\Models\AbsensiGerbang::where('related_id', $karyawanId)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        // Map days to Indonesian
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

        // Get schedule for today if teacher
        if ($isGuru) {
            $jadwalHariIni = App\Models\Jadwal::whereHas('jadwalPelajaran', function ($q) use ($karyawanId) {
                $q->where('guru_id', $karyawanId);
            })
                ->where('hari', $hari)
                ->orderBy('jam_mulai')
                ->get();
        } else {
            $jadwalHariIni = collect();
        }
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

                        @if ($absensiGerbangHariIni)
                            <div class="avatar avatar-xl bg-success me-3">
                                <i class="bi bi-check-circle fs-3 text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Sudah Absensi Gerbang</h5>
                                <small>Masuk: {{ $absensiGerbangHariIni->waktu_scan_masuk }}</small>
                                <br>
                                <small>Keluar:
                                    {{ $absensiGerbangHariIni->waktu_scan_keluar ?? 'Belum absen keluar' }}</small>
                            </div>
                        @else
                            <div class="avatar avatar-xl bg-danger me-3">
                                <i class="bi bi-x-circle fs-3 text-white"></i>
                            </div>
                            @if (Auth::user()->hasRole('admin'))
                                <div>
                                    <h5 class="mb-0">Belum Absensi Gerbang</h5>
                                    <a href="{{ route('admin.absensi-gerbang.scan') }}" class="btn btn-sm btn-primary mt-2">
                                        <i class="bi bi-qr-code me-1"></i> Scan Sekarang
                                    </a>
                                </div>
                            @else
                                <div>
                                    <h5 class="mb-0">Belum Absensi Gerbang</h5>
                                    <a href="{{ route('karyawan.download-qrcode', $karyawan->id) }}"
                                        class="btn btn-sm btn-primary mt-2">
                                        <i class="bi bi-qr-code me-1"></i> Unduh QR Code Saya
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if ($isPetugasPiketHariIni)
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-xl bg-warning me-3">
                                <i class="bi bi-shield-check fs-3 text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Anda Petugas Piket Hari Ini</h5>
                                <a href="{{ route('karyawan.absensi-gerbang.scan') }}" class="btn btn-sm btn-warning mt-2">
                                    <i class="bi bi-qr-code me-1"></i> Scan Absensi Siswa
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($isGuru)
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl bg-info me-3">
                                <i class="bi bi-calendar-date fs-3 text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Jadwal Mengajar Hari Ini</h5>
                                <span class="badge bg-{{ $jadwalHariIni->count() > 0 ? 'primary' : 'secondary' }}">
                                    {{ $jadwalHariIni->count() > 0 ? $jadwalHariIni->count() . ' Jadwal' : 'Tidak Ada Jadwal' }}
                                </span>
                            </div>
                        </div>
                    @endif
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
                            <a href="{{ route(Auth::user()->role . '.absensi-gerbang.scan') }}"
                                class="btn btn-primary btn-block w-100">
                                <i class="bi bi-qr-code me-1"></i> Scan Absensi
                            </a>
                        </div>

                        @if ($isGuru || $isWaliKelas || $isKurikulum)
                            <div class="col-md-4 col-6 mb-3">
                                <a href="{{ route(Auth::user()->role . '.absensi-siswa-kelas.index') }}"
                                    class="btn btn-info btn-block w-100">
                                    <i class="bi bi-mortarboard me-1"></i> Absensi Siswa
                                </a>
                            </div>
                        @endif

                        <div class="col-md-4 col-6 mb-3">
                            <a href="{{ route(Auth::user()->role . '.riwayat-absensi') }}"
                                class="btn btn-success btn-block w-100">
                                <i class="bi bi-clipboard-check me-1"></i> Riwayat Absensi
                            </a>
                        </div>

                        <div class="col-md-4 col-6 mb-3">
                            <a href="{{ route(Auth::user()->role . '.profile') }}" class="btn btn-warning btn-block w-100">
                                <i class="bi bi-person-badge me-1"></i> Profil Saya
                            </a>
                        </div>

                        @if ($isGuru)
                            <div class="col-md-4 col-6 mb-3">
                                <a href="{{ route(Auth::user()->role . '.jadwal-pelajaran.index') }}"
                                    class="btn btn-secondary btn-block w-100">
                                    <i class="bi bi-calendar-week me-1"></i> Jadwal
                                </a>
                            </div>
                        @endif

                        @if ($isWaliKelas)
                            <div class="col-md-4 col-6 mb-3">
                                <a href="{{ route(Auth::user()->role . '.laporan-absensi-siswa') }}"
                                    class="btn btn-secondary btn-block w-100">
                                    <i class="bi bi-file-text me-1"></i> Laporan Kelas
                                </a>
                            </div>
                        @endif

                        @if ($isKurikulum)
                            <div class="col-md-4 col-6 mb-3">
                                <a href="{{ route(Auth::user()->role . '.laporan-absensi-siswa') }}"
                                    class="btn btn-secondary btn-block w-100">
                                    <i class="bi bi-file-text me-1"></i> Laporan Kelas
                                </a>
                            </div>
                        @endif

                        <div class="col-md-4 col-6 mb-3">
                            <a href="#" class="btn btn-danger btn-block w-100"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isGuru && $jadwalHariIni->count() > 0)
        <!-- Jadwal Mengajar Hari Ini -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Jadwal Mengajar Hari Ini ({{ $hari }})</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                        <th>Status Absensi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalHariIni as $jadwal)
                                        @php
                                            $absensiGuru = App\Models\AbsensiGuruKelas::where('jadwal_id', $jadwal->id)
                                                ->where('karyawan_id', $karyawanId)
                                                ->whereDate('tanggal', now()->toDateString())
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td>{{ substr($jadwal->jam_mulai, 0, 5) }} -
                                                {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                            <td>{{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}
                                            </td>
                                            <td>{{ $jadwal->kelas->nama_kelas ?? 'Data tidak tersedia' }}</td>
                                            <td>
                                                @if ($absensiGuru)
                                                    <span class="badge bg-success">Sudah Absen</span>
                                                @else
                                                    <span class="badge bg-danger">Belum Absen</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($absensiGuru)
                                                    <a href="{{ route(Auth::user()->role . '.absensi-siswa-kelas.kelas', $jadwal->id) }}"
                                                        class="btn btn-sm btn-success">
                                                        <i class="bi bi-clipboard-check me-1"></i> Lihat Absensi
                                                    </a>
                                                @elseif(!$absensiGuru && !$absensiGerbangHariIni)
                                                    <button class="btn btn-sm btn-warning" disabled>
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Absen Gerbang Dulu
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-warning" disabled>
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Absen Gerbang Dulu
                                                    </button>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Grafik Absensi -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Grafik Absensi Saya</h4>
                </div>
                <div class="card-body">
                    <div id="chart-absensi-karyawan"></div>
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
                                    $riwayatAbsensi = App\Models\AbsensiGerbang::where('related_id', $karyawanId)
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
                                            @if ($absensi->waktu_scan_keluar)
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
                            <a href="{{ route(Auth::user()->role . '.riwayat-absensi') }}" class="btn btn-primary">
                                Lihat Semua Riwayat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart Absensi Karyawan
            var options = {
                series: [{
                    name: 'Absensi',
                    data: [{{ $chartData ?? 0 }}]
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                title: {
                    text: 'Kehadiran 30 Hari Terakhir',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: Array.from({
                        length: 30
                    }, (_, i) => {
                        let date = new Date();
                        date.setDate(date.getDate() - (29 - i));
                        return date.getDate() + '/' + (date.getMonth() + 1);
                    }),
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    labels: {
                        formatter: function(val) {
                            if (val === 0) return 'Absen';
                            if (val === 50) return 'Masuk';
                            if (val === 100) return 'Lengkap';
                            return val + '%';
                        }
                    }
                },
                colors: ['#435ebe']
            };

            var chart = new ApexCharts(document.querySelector("#chart-absensi-karyawan"), options);
            chart.render();
        });
    </script>

@endsection
