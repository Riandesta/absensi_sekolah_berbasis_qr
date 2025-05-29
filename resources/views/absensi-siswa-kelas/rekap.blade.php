@extends('templates')
@section('header', 'Rekap Absensi Siswa')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.index') }}">Absensi Siswa Kelas</a></li>
        <li class="breadcrumb-item active">Rekap Absensi</li>
    </ol>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Rekap Absensi Siswa</h6>
                <div>
                    @if($kelas)
                        <span class="badge badge-primary">{{ $kelas->nama_kelas }}</span>
                    @endif
                    @if($periode_type)
                        <span class="badge badge-info">{{ ucfirst($periode_type) }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.rekap') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="kelas_id">Kelas</label>
                                <select name="kelas_id" id="kelas_id" class="form-control">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="periode_type">Periode</label>
                                <select name="periode_type" id="periode_type" class="form-control">
                                    <option value="harian" {{ $periode_type == 'harian' ? 'selected' : '' }}>Harian</option>
                                    <option value="mingguan" {{ $periode_type == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="bulanan" {{ $periode_type == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="semester" {{ $periode_type == 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="tahunan" {{ $periode_type == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.rekap') }}" class="btn btn-secondary">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                    <button type="submit" class="btn btn-success" name="export" value="pdf">
                                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statHadir }}</div>
                                        <div class="text-xs text-muted">{{ $totalAbsensi > 0 ? round(($statHadir / $totalAbsensi) * 100, 1) : 0 }}%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statIzin }}</div>
                                        <div class="text-xs text-muted">{{ $totalAbsensi > 0 ? round(($statIzin / $totalAbsensi) * 100, 1) : 0 }}%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sakit</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statSakit }}</div>
                                        <div class="text-xs text-muted">{{ $totalAbsensi > 0 ? round(($statSakit / $totalAbsensi) * 100, 1) : 0 }}%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-procedures fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statAlpa }}</div>
                                        <div class="text-xs text-muted">{{ $totalAbsensi > 0 ? round(($statAlpa / $totalAbsensi) * 100, 1) : 0 }}%</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Grafik Kehadiran</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="kehadiranChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Persentase Kehadiran</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="pieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Summary -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Rekap Per Siswa</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Alpa</th>
                                        <th>Total</th>
                                        <th>% Hadir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapSiswa as $index => $rekap)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $rekap['siswa']->nis }}</td>
                                            <td>{{ $rekap['siswa']->nama_lengkap }}</td>
                                            <td>{{ $rekap['siswa']->kelas->nama_kelas ?? 'N/A' }}</td>
                                            <td class="text-success">{{ $rekap['hadir'] }}</td>
                                            <td class="text-info">{{ $rekap['izin'] }}</td>
                                            <td class="text-warning">{{ $rekap['sakit'] }}</td>
                                            <td class="text-danger">{{ $rekap['alpa'] }}</td>
                                            <td>{{ $rekap['total'] }}</td>
                                            <td>
                                                @if($rekap['total'] > 0)
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: {{ round(($rekap['hadir'] / $rekap['total']) * 100) }}%"
                                                             aria-valuenow="{{ round(($rekap['hadir'] / $rekap['total']) * 100) }}"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                            {{ round(($rekap['hadir'] / $rekap['total']) * 100) }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detail Records -->
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Detail Data Absensi</h6>
                        <span class="text-muted">{{ $absensiSiswa->count() }} Data</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="detailTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Status</th>
                                        <th>Dicatat Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absensiSiswa as $index => $absensi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $absensi->siswa->nama_lengkap ?? 'N/A' }}</td>
                                            <td>{{ $absensi->jadwal->kelas->nama_kelas ?? 'N/A' }}</td>
                                            <td>{{ $absensi->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                                            <td>
                                                @if($absensi->status == 'Hadir')
                                                    <span class="badge badge-success">Hadir</span>
                                                @elseif($absensi->status == 'Izin')
                                                    <span class="badge badge-info">Izin</span>
                                                @elseif($absensi->status == 'Sakit')
                                                    <span class="badge badge-warning">Sakit</span>
                                                @elseif($absensi->status == 'Alpa')
                                                    <span class="badge badge-danger">Alpa</span>
                                                @endif
                                            </td>
                                            <td>{{ $absensi->inputBy->name ?? 'System' }}</td>
                                            <td>
                                                <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.view', ['jadwal_id' => $absensi->jadwal_id, 'tanggal' => $absensi->tanggal]) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line chart for attendance over time
        const kehadiranCtx = document.getElementById('kehadiranChart').getContext('2d');
        const kehadiranChart = new Chart(kehadiranCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Hadir',
                        data: {!! json_encode($chartHadir) !!},
                        borderColor: '#36A2EB',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.4
                    },
                    {
                        label: 'Izin',
                        data: {!! json_encode($chartIzin) !!},
                        borderColor: '#4BC0C0',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.4
                    },
                    {
                        label: 'Sakit',
                        data: {!! json_encode($chartSakit) !!},
                        borderColor: '#FFCD56',
                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                        tension: 0.4
                    },
                    {
                        label: 'Alpa',
                        data: {!! json_encode($chartAlpa) !!},
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Perkembangan Kehadiran'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Siswa'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                }
            }
        });

        // Pie chart for attendance percentage
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    label: 'Persentase',
                    data: [
                        {{ $statHadir }},
                        {{ $statIzin }},
                        {{ $statSakit }},
                        {{ $statAlpa }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Persentase Kehadiran'
                    }
                }
            }
        });

        // Initialize dataTables
        $(document).ready(function() {
            $('#dataTable').DataTable({
                order: [[9, 'desc']]
            });

            $('#detailTable').DataTable({
                order: [[1, 'desc']]
            });
        });
    });
</script>
@endpush
