@extends('templates')
@section('header', 'Laporan Absensi Guru Kelas')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    Laporan Absensi Guru Kelas
                    @if(isset($kelas))
                        <span class="badge bg-primary">{{ $kelas->nama_kelas }}</span>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Filter Laporan</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.laporan-absensi-guru-kelas') }}" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="period" class="form-label">Periode</label>
                                        <select name="period" id="period" class="form-select" onchange="toggleCustomDates()">
                                            <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Semua</option>
                                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Harian</option>
                                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                            <option value="semester" {{ $period == 'semester' ? 'selected' : '' }}>Semester</option>
                                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Kustom Tanggal</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $customStart }}">
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $customEnd }}">
                                    </div>

                                    @if(strtolower(Auth::user()->karyawan->jabatan) === 'kurikulum')
                                        <div class="col-md-4">
                                            <label for="kelas_id" class="form-label">Kelas</label>
                                            <select name="kelas_id" id="kelas_id" class="form-select">
                                                <option value="">Semua Kelas</option>
                                                @foreach($kelasList as $kelasItem)
                                                    <option value="{{ $kelasItem->id }}" {{ isset($kelas) && $kelas->id == $kelasItem->id ? 'selected' : '' }}>
                                                        {{ $kelasItem->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Ekspor Laporan</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('karyawan.absensi-guru-kelas.export-pdf', ['period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd, 'kelas_id' => $kelas->id ?? '']) }}" class="btn btn-danger">
                                        <i class="bi bi-file-pdf-fill me-2"></i> Ekspor PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Hadir</h5>
                                <h2 class="mb-0">{{ $absensi->where('status', 'Hadir')->count() }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Terlambat</h5>
                                <h2 class="mb-0">{{ $absensi->where('status', 'Terlambat')->count() }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Izin</h5>
                                <h2 class="mb-0">{{ $absensi->where('status', 'Izin')->count() }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Tidak Hadir</h5>
                                <h2 class="mb-0">{{ $absensi->where('status', 'Tidak Hadir')->count() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Guru</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Jam</th>
                                <th>Waktu Scan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($absensi->count() > 0)
                                @php $no = $absensi->firstItem(); @endphp
                                @foreach($absensi as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                        <td>{{ $item->karyawan->nama_lengkap ?? 'Tidak tersedia' }}</td>
                                        <td>{{ $item->jadwal->kelas->nama_kelas ?? 'Tidak tersedia' }}</td>
                                        <td>{{ $item->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Tidak tersedia' }}</td>
                                        <td>
                                            @if($item->jadwal)
                                                {{ substr($item->jadwal->jam_mulai, 0, 5) }} - {{ substr($item->jadwal->jam_selesai, 0, 5) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->waktu_scan ?? '-' }}</td>
                                        <td>
                                            @if($item->status == 'Hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($item->status == 'Terlambat')
                                                <span class="badge bg-warning">Terlambat</span>
                                            @elseif($item->status == 'Izin')
                                                <span class="badge bg-info">Izin</span>
                                            @elseif($item->status == 'Tidak Hadir')
                                                <span class="badge bg-danger">Tidak Hadir</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data absensi</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $absensi->appends(request()->except('page'))->links() }}
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

    // Run on page load to ensure correct initial state
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomDates();
    });
</script>

@endsection
