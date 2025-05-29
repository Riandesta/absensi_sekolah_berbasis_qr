@extends('templates')
@section('header', 'Laporan Absensi Gerbang')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        Laporan Absensi Gerbang
                        @if (isset($kelas))
                            <span class="badge bg-primary">{{ $kelas->nama_kelas }}</span>
                        @endif
                        @if (isset($role) && !empty($role))
                            <span class="badge bg-info">{{ ucfirst($role) }}</span>
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
                                    <form action="{{ route('karyawan.laporan-absensi-gerbang') }}" method="GET"
                                        class="row g-3">
                                        <input type="hidden" name="type" value="{{ $type }}">

                                        @if (isset($kelasList) && strtolower(Auth::user()->karyawan->jabatan) === 'kurikulum')
                                            <div class="col-md-3">
                                                <label for="role" class="form-label">Tipe Pengguna</label>
                                                <select name="role" id="role" class="form-select"
                                                    onchange="toggleKelasFilter()">
                                                    <option value="">Semua</option>
                                                    <option value="siswa" {{ $role == 'siswa' ? 'selected' : '' }}>Siswa
                                                    </option>
                                                    <option value="karyawan" {{ $role == 'karyawan' ? 'selected' : '' }}>
                                                        Karyawan</option>
                                                </select>
                                            </div>
                                        @endif

                                        <div class="col-md-3">
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

                                        <div class="col-md-3 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ $customStart }}">
                                        </div>

                                        <div class="col-md-3 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ $customEnd }}">
                                        </div>

                                        @if (isset($kelasList) && strtolower(Auth::user()->karyawan->jabatan) === 'kurikulum')
                                            <div class="col-md-3" id="kelas-filter"
                                                style="{{ $role == 'karyawan' ? 'display: none;' : '' }}">
                                                <label for="kelas_id" class="form-label">Kelas</label>
                                                <select name="kelas_id" id="kelas_id" class="form-select">
                                                    <option value="">Semua Kelas</option>
                                                    @foreach ($kelasList as $kelasItem)
                                                        <option value="{{ $kelasItem->id }}"
                                                            {{ $kelas && $kelas->id == $kelasItem->id ? 'selected' : '' }}>
                                                            {{ $kelasItem->nama_kelas }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        <div class="col-md-3">
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
                                        <a href="{{ route('karyawan.riwayat-absensi.export-pdf', [
                                            'type' => $type,
                                            'period' => $period,
                                            'start_date' => $customStart,
                                            'end_date' => $customEnd,
                                            'role' => $role ?? '',
                                            'kelas_id' => $kelas->id ?? '',
                                        ]) }}"
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
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Absensi Lengkap</h5>
                                    <h2 class="mb-0">
                                        {{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', '!=', null)->count() }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Belum Absen Keluar</h5>
                                    <h2 class="mb-0">
                                        {{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', null)->count() }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title text-white">Total
                                        {{ isset($role) && $role == 'karyawan' ? 'Karyawan' : 'Siswa' }}</h5>
                                    <h2 class="mb-0">
                                        {{ $absensi->count() > 0 ? $absensi->groupBy('related_id')->count() : '0' }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Kelas/Jabatan</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Keluar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <!-- Bagian tabel yang diperbaiki -->
                           <!-- Table Body - Bagian yang diperbaiki -->
<tbody>
    @if ($absensi->count())
        @php $no = $absensi->firstItem(); @endphp
        @foreach ($absensi as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>

                <!-- Nama - Menggunakan method yang sudah diperbaiki -->
                <td>{{ $item->getNamaLengkap() }}</td>

                <!-- Tipe - Berdasarkan role user yang sebenarnya -->
                <td>
                    @if ($item->user && $item->user->role === 'siswa')
                        <span class="badge bg-info">Siswa</span>
                    @elseif($item->user && $item->user->role === 'karyawan')
                        <span class="badge bg-primary">Karyawan</span>
                    @else
                        <span class="badge bg-secondary">Tidak diketahui</span>
                    @endif
                </td>

                <!-- Kelas/Jabatan - Menggunakan method yang sudah diperbaiki -->
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

<!-- Debug section yang diperbaiki untuk verifikasi -->
@if(config('app.debug'))
<div class="mt-3">
    <details>
        <summary>Debug Info</summary>
        <pre>
@foreach ($absensi->take(5) as $item)
{{ json_encode([
    'related_id' => $item->related_id,
    'user_role' => $item->user ? $item->user->role : null,
    'nama_lengkap' => $item->getNamaLengkap(),
    'kelas_jabatan' => $item->getKelasJabatan(),
    'user_type' => $item->getUserType(),
], JSON_PRETTY_PRINT) }}
@endforeach
        </pre>
    </details>
</div>
@endif
                        </table>
                        {{-- <pre>
@foreach ($absensi as $item)
{{ json_encode(
    [
        'related_id' => $item->related_id,
        'siswa' => optional($item->siswa)->nama_lengkap,
        'karyawan' => optional($item->karyawan)->nama_lengkap,
    ],
    JSON_PRETTY_PRINT,
) }}
@endforeach
</pre> --}}
                        <!-- Debug -->
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $absensi->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleCustomDates() {
            const periodSelect = document.getElementById('period');
            const customDateFields = document.querySelectorAll('.custom-dates');
            if (periodSelect.value === 'custom') {
                customDateFields.forEach(field => field.classList.remove('d-none'));
            } else {
                customDateFields.forEach(field => field.classList.add('d-none'));
            }
        }

        function toggleKelasFilter() {
            const roleSelect = document.getElementById('role');
            const kelasFilter = document.getElementById('kelas-filter');
            if (kelasFilter) {
                if (roleSelect.value === 'karyawan') {
                    kelasFilter.style.display = 'none';
                } else {
                    kelasFilter.style.display = 'block';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleCustomDates();
            toggleKelasFilter();
        });
    </script>

@endsection
