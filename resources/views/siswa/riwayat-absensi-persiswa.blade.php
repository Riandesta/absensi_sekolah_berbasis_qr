@extends('templates')
@section('header', 'Riwayat Absensi')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Riwayat Absensi</h4>
                <div class="btn-group">
                    <a href="{{ route('siswa.riwayat-absensi-persiswa', ['type' => 'gerbang', 'period' => $period]) }}" class="btn btn-sm {{ $type == 'gerbang' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi Gerbang</a>
                    <a href="{{ route('siswa.riwayat-absensi-persiswa', ['type' => 'kelas', 'period' => $period]) }}" class="btn btn-sm {{ $type == 'kelas' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi Kelas</a>
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
                                <form action="{{ route('siswa.riwayat-absensi-persiswa') }}" method="GET" class="row g-3">
                                    <input type="hidden" name="type" value="{{ $type }}">

                                    <div class="col-md-4">
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
                                <a href="{{ route('siswa.attendance-export-pdf', ['type' => $type, 'period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd]) }}" class="btn btn-danger w-100">
                                    <i class="bi bi-file-pdf-fill me-2"></i> Ekspor PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    @if($type == 'gerbang')
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
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
                                        <td colspan="5" class="text-center">Tidak ada data absensi</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Jam</th>
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
                                            <td>{{ $item->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Tidak tersedia' }}</td>
                                            <td>{{ $item->jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Tidak tersedia' }}</td>
                                            <td>
                                                @if($item->jadwal)
                                                    {{ substr($item->jadwal->jam_mulai, 0, 5) }} - {{ substr($item->jadwal->jam_selesai, 0, 5) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status == 'Hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @elseif($item->status == 'Izin')
                                                    <span class="badge bg-warning">Izin</span>
                                                @elseif($item->status == 'Sakit')
                                                    <span class="badge bg-info">Sakit</span>
                                                @elseif($item->status == 'Alpa')
                                                    <span class="badge bg-danger">Alpa</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $item->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data absensi</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @endif
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
