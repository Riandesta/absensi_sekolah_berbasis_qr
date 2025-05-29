@extends('templates')
@section('header', 'Laporan Kehadiran Guru')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Laporan Kehadiran {{ $karyawan->nama_lengkap }}</h4>
                <div class="btn-group">
                    <a href="{{ route('karyawan.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Filter Periode</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.attendance.teacher-report') }}" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <select name="period" id="period" class="form-select" onchange="toggleCustomDates()">
                                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Harian</option>
                                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                            <option value="semester" {{ $period == 'semester' ? 'selected' : '' }}>Semester</option>
                                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Kustom</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <input type="date" name="start_date" class="form-control" placeholder="Tanggal Mulai" value="{{ $customStart }}">
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <input type="date" name="end_date" class="form-control" placeholder="Tanggal Akhir" value="{{ $customEnd }}">
                                    </div>

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Ekspor PDF</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('karyawan.attendance.export-teacher-pdf', ['period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd]) }}" class="btn btn-danger w-100">
                                    <i class="bi bi-file-pdf-fill me-2"></i> Ekspor PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card bg-primary text-white shadow">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5>Total Hari</h5>
                                    <h2>{{ $stats['totalDays'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card bg-success text-white shadow">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5>Absen Gerbang Lengkap</h5>
                                    <h2>{{ $stats['gateComplete'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card bg-warning text-white shadow">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5>Kehadiran Kelas</h5>
                                    <h2>{{ $stats['classAttendance'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="card bg-info text-white shadow">
                            <div class="card-body p-3">
                                <div class="text-center">
                                    <h5>Tingkat Kehadiran</h5>
                                    <h2>{{ $stats['attendanceRate'] }}%</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Kehadiran -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Riwayat Kehadiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="attendanceTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="bg-light">
                                        <th>Tanggal</th>
                                        <th>Hari</th>
                                        <th>Absen Gerbang</th>
                                        <th>Kelas yang Diajar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($groupedAttendance) > 0)
                                        @foreach($groupedAttendance as $date => $data)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd') }}</td>
                                                <td>
                                                    @if($data['gate'])
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge {{ $data['gate']->waktu_scan_keluar ? 'bg-success' : 'bg-warning' }} me-2">
                                                                {{ $data['gate']->waktu_scan_keluar ? 'Lengkap' : 'Belum Absen Keluar' }}
                                                            </span>
                                                            <small>
                                                                Masuk: {{ $data['gate']->waktu_scan_masuk }}<br>
                                                                @if($data['gate']->waktu_scan_keluar)
                                                                    Keluar: {{ $data['gate']->waktu_scan_keluar }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-danger">Tidak Absen</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(count($data['classes']) > 0)
                                                        <div class="list-group">
                                                            @foreach($data['classes'] as $class)
                                                                <div class="list-group-item list-group-item-action flex-column align-items-start py-2 px-3 border-0">
                                                                    <div class="d-flex w-100 justify-content-between">
                                                                        <h6 class="mb-1">{{ $class->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Tidak ada data' }}</h6>
                                                                        <small>
                                                                            {{ substr($class->jadwal->jam_mulai, 0, 5) }} - {{ substr($class->jadwal->jam_selesai, 0, 5) }}
                                                                        </small>
                                                                    </div>
                                                                    <p class="mb-1">Kelas: {{ $class->jadwal->kelas->nama_kelas ?? 'Tidak ada data' }}</p>
                                                                    <small class="text-success">
                                                                        <i class="bi bi-check-circle-fill"></i> Hadir
                                                                    </small>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Tidak ada jadwal mengajar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data kehadiran untuk periode ini</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomDates() {
        const periodSelect = document.getElementById('period');
        const customDateFields = document.querySelectorAll('.custom-dates');

        if (periodSelect.value === 'custom') {
            customDateFields.forEach(field => {
                field.classList.remove('d-none');
            });
        } else {
            customDateFields.forEach(field => {
                field.classList.add('d-none');
            });
        }
    }

    // Jalankan saat halaman dimuat untuk memastikan keadaan awal yang benar
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomDates();
    });
</script>

@endsection
