@extends('templates')
@section('header', 'Laporan Absensi Gerbang')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">
                    Laporan Absensi Gerbang
                    @if(isset($kelas))
                        <span class="badge bg-primary">{{ $kelas->nama_kelas }}</span>
                    @endif
                </h4>
                <div class="btn-group">
                    <a href="{{ route('karyawan.riwayat-absensi', ['type' => 'gerbang']) }}" class="btn btn-sm {{ $type == 'gerbang' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi Gerbang</a>
                    <a href="{{ route('karyawan.riwayat-absensi', ['type' => 'kelas']) }}" class="btn btn-sm {{ $type == 'kelas' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi Kelas</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Filter Laporan</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.riwayat-absensi') }}" method="GET" class="row g-3">
                                    <input type="hidden" name="type" value="{{ $type }}">

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
                                    <a href="{{ route('karyawan.riwayat-absensi.export-pdf', ['type' => $type, 'period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd, 'kelas_id' => $kelas->id ?? '']) }}" class="btn btn-danger">
                                        <i class="bi bi-file-pdf-fill me-2"></i> Ekspor PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Absensi Lengkap</h5>
                                <h2 class="mb-0">{{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', '!=', null)->count() }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Belum Absen Keluar</h5>
                                <h2 class="mb-0">{{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', null)->count() }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title text-white">Total Siswa</h5>
                                <h2 class="mb-0">{{ $absensi->count() > 0 ? $absensi->groupBy('related_id')->count() : '0' }}</h2>
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
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
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
                                        <td>{{ $item->siswa->nama_lengkap ?? 'Tidak tersedia' }}</td>
                                        <td>{{ $item->siswa->kelas->nama_kelas ?? 'Tidak tersedia' }}</td>
                                        <td>{{ $item->waktu_scan_masuk ?? '-' }}</td>
                                        <td>{{ $item->waktu_scan_keluar ?? '-' }}</td>
                                        <td>
                                            @if($item->waktu_scan_masuk && $item->waktu_scan_keluar)
                                                <span class="badge bg-success">Lengkap</span>
                                            @elseif($item->waktu_scan_masuk)
                                                <span class="badge bg-warning">Belum Absen Keluar</span>
                                            @else
                                                <span class="badge bg-danger">Belum Absen</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data absensi</td>
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
