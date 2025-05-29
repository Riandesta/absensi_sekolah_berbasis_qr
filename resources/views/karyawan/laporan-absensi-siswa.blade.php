@extends('templates')
@section('header', 'Laporan Absensi Siswa')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        Laporan Absensi Siswa
                        @if (isset($kelas))
                            <span class="badge bg-primary">{{ $kelas->nama_kelas }}</span>
                        @endif
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('karyawan.laporan-absensi-gerbang', ['type' => 'gerbang']) }}"
                            class="btn btn-sm {{ $type == 'gerbang' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi
                            Gerbang</a>
                        <a href="{{ route('karyawan.laporan-absensi-siswa', ['type' => 'kelas']) }}"
                            class="btn btn-sm {{ $type == 'kelas' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi
                            Kelas</a>
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
                                    <form action="{{ route('karyawan.laporan-absensi-siswa') }}" method="GET"
                                        class="row g-3">
                                        <input type="hidden" name="type" value="{{ $type }}">

                                        <div class="col-md-4">
                                            <label for="period" class="form-label">Periode</label>
                                            <select name="period" id="period" class="form-select"
                                                onchange="toggleCustomDates()">
                                                <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Semua
                                                </option>
                                                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Harian
                                                </option>
                                                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Mingguan
                                                </option>
                                                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>
                                                    Bulanan</option>
                                                <option value="semester" {{ $period == 'semester' ? 'selected' : '' }}>
                                                    Semester</option>
                                                <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan
                                                </option>
                                                <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Kustom
                                                    Tanggal</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ $customStart }}">
                                        </div>

                                        <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ $customEnd }}">
                                        </div>

                                        @if (isset($kelasList) && strtolower(Auth::user()->karyawan->jabatan) === 'kurikulum')
                                            <div class="col-md-4">
                                                <label for="kelas_id" class="form-label">Kelas</label>
                                                <select name="kelas_id" id="kelas_id" class="form-select">
                                                    <option value="">Semua Kelas</option>
                                                    @foreach ($kelasList as $kelasItem)
                                                        <option value="{{ $kelasItem->id }}"
                                                            {{ isset($kelas) && $kelas->id == $kelasItem->id ? 'selected' : '' }}>
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
                                        <a href="{{ route('karyawan.riwayat-absensi.export-pdf', ['type' => $type, 'period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd]) }}"
                                            class="btn btn-danger">
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
                                    <h5 class="card-title text-white">Izin</h5>
                                    <h2 class="mb-0">{{ $absensi->where('status', 'Izin')->count() }}</h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Sakit</h5>
                                    <h2 class="mb-0">{{ $absensi->where('status', 'Sakit')->count() }}</h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Alpa</h5>
                                    <h2 class="mb-0">{{ $absensi->where('status', 'Alpa')->count() }}</h2>
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
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Jam</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <!-- Bagian tabel yang diperbaiki -->
                            <tbody>
                                @if ($absensi->count())
                                    @php $no = $absensi->firstItem(); @endphp
                                    @foreach ($absensi as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>

                                            <!-- Nama - Berdasarkan user role yang benar -->
                                            <td>{{ $item->getNamaLengkap() }}</td>

                                            <!-- Tipe - Berdasarkan user role yang benar -->
                                            <td>
                                                @if ($item->user && $item->user->role === 'siswa')
                                                    <span class="badge bg-info">Siswa</span>
                                                @elseif($item->user && $item->user->role === 'karyawan')
                                                    <span class="badge bg-primary">Karyawan</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak diketahui</span>
                                                @endif
                                            </td>

                                            <!-- Kelas/Jabatan - Berdasarkan user role yang benar -->
                                            <td>{{ $item->getKelasJabatan() }}</td>

                                            <!-- Waktu Scan -->
                                            <td>{{ $item->waktu_scan_masuk ?? '-' }}</td>
                                            <td>{{ $item->waktu_scan_keluar ?? '-' }}</td>

                                            <!-- Status -->
                                            <td>
                                                @if ($item->waktu_scan_masuk && $item->waktu_scan_keluar)
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
                                        <td colspan="8" class="text-center">Tidak ada data absensi</td>
                                    </tr>
                                @endif
                            </tbody>

                            <!-- Debug section yang diperbaiki -->
                            <pre>
@foreach ($absensi as $item)
{{ json_encode(
    [
        'related_id' => $item->related_id,
        'user_role' => $item->user ? $item->user->role : null,
        'siswa' => $item->siswa ? $item->siswa->nama_lengkap : null,
        'karyawan' =>
            $item->user && $item->user->role === 'karyawan' && $item->user->karyawan
                ? $item->user->karyawan->nama_lengkap
                : null,
        'user_type' => $item->getUserType(),
    ],
    JSON_PRETTY_PRINT,
) }}
@endforeach
</pre>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $absensi->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
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
