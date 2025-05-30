@extends('templates')
@section('header', 'Laporan Absensi Gerbang')
@push('styles')
    <style>
        /* Custom Pagination Styling */
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 0.25rem;
        }

        .pagination .page-item {
            display: flex;
        }

        .pagination .page-link {
            position: relative;
            display: block;
            color: #6c757d;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.25;
            border-radius: 0.375rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
        }

        .pagination .page-link:hover {
            z-index: 2;
            color: #0056b3;
            text-decoration: none;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* Responsive pagination */
        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .pagination-info {
                font-size: 0.8rem;
                text-align: center;
                margin-bottom: 0.5rem;
            }

            .pagination-wrapper {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
        }

        /* Stats cards responsive */
        @media (max-width: 768px) {
            .stats-card .card-body {
                padding: 1rem 0.75rem;
            }

            .stats-card h2 {
                font-size: 1.5rem;
            }

            .stats-card h5 {
                font-size: 0.9rem;
            }
        }

        /* Table responsive improvements */
        @media (max-width: 768px) {
            .table-responsive table {
                font-size: 0.8rem;
            }

            .badge {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush
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
                        <a href="{{ route('karyawan.laporan-absensi-gerbang', array_merge(request()->all(), ['type' => 'gerbang'])) }}"
                            class="btn btn-sm {{ $type == 'gerbang' ? 'btn-primary' : 'btn-outline-primary' }}">Absensi
                            Gerbang</a>
                        <a href="{{ route('karyawan.laporan-absensi-siswa', array_merge(request()->all(), ['type' => 'kelas'])) }}"
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
                                        class="row g-3" id="filterForm">
                                        <input type="hidden" name="type" value="{{ $type }}">

                                        <!-- User Type Filter -->
                                        <div class="col-md-3">
                                            <label for="role" class="form-label">Tipe Pengguna</label>
                                            <select name="role" id="role" class="form-select">
                                                <option value="">Semua</option>
                                                <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>
                                                    Siswa</option>
                                                <option value="karyawan"
                                                    {{ request('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                            </select>
                                        </div>

                                        <!-- Period Filter -->
                                        <div class="col-md-3">
                                            <label for="period" class="form-label">Periode</label>
                                            <select name="period" id="period" class="form-select"
                                                onchange="toggleCustomDates()">
                                                <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>
                                                    Semua</option>
                                                <option value="daily"
                                                    {{ request('period') == 'daily' ? 'selected' : '' }}>Harian</option>
                                                <option value="weekly"
                                                    {{ request('period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                                <option value="monthly"
                                                    {{ request('period') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                                <option value="semester"
                                                    {{ request('period') == 'semester' ? 'selected' : '' }}>Semester
                                                </option>
                                                <option value="yearly"
                                                    {{ request('period') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                                <option value="custom"
                                                    {{ request('period') == 'custom' ? 'selected' : '' }}>Kustom Tanggal
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Date Filters (Custom Period) -->
                                        <div
                                            class="col-md-3 custom-dates {{ request('period') == 'custom' ? '' : 'd-none' }}">
                                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ request('start_date') }}">
                                        </div>

                                        <div
                                            class="col-md-3 custom-dates {{ request('period') == 'custom' ? '' : 'd-none' }}">
                                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ request('end_date') }}">
                                        </div>

                                        <!-- Class Filter (for Kurikulum) -->
                                        @if (isset($kelasList))
                                            <div class="col-md-3" id="kelas-filter">
                                                <label for="kelas_id" class="form-label">Kelas</label>
                                                <select name="kelas_id" id="kelas_id" class="form-select">
                                                    <option value="">Semua Kelas</option>
                                                    @foreach ($kelasList as $kelasItem)
                                                        <option value="{{ $kelasItem->id }}"
                                                            {{ request('kelas_id') == $kelasItem->id ? 'selected' : '' }}>
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
                                        <a href="{{ route('karyawan.riwayat-absensi.export-pdf', array_merge(request()->all(), ['type' => $type])) }}"
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
                                        {{ isset($role) && $role ? ucfirst($role) : 'Pengguna' }}</h5>
                                    <h2 class="mb-0">
                                        {{ $absensi->count() > 0 ? $absensi->unique('related_id')->count() : '0' }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted">
                                Menampilkan {{ $absensi->firstItem() ?? 0 }} sampai {{ $absensi->lastItem() ?? 0 }}
                                dari {{ $absensi->total() }} hasil
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if ($absensi->hasPages())
                                <small class="text-muted">
                                    Halaman {{ $absensi->currentPage() }} dari {{ $absensi->lastPage() }}
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Tanggal</th>
                                    <th width="20%">Nama</th>
                                    <th width="10%">Tipe</th>
                                    <th width="15%">Kelas/Jabatan</th>
                                    <th width="12%">Waktu Masuk</th>
                                    <th width="12%">Waktu Keluar</th>
                                    <th width="14%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($absensi as $index => $item)
                                    <tr>
                                        <td>{{ $absensi->firstItem() + $index }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                        <td>
                                            @if ($item->user_type === 'siswa')
                                                {{ $item->siswa->nama_lengkap ?? 'Tidak tersedia' }}
                                            @elseif ($item->user_type === 'karyawan')
                                                {{ $item->karyawan->nama_lengkap ?? 'Tidak tersedia' }}
                                            @else
                                                Tidak tersedia
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->user_type === 'siswa')
                                                <span class="badge bg-info">Siswa</span>
                                            @elseif ($item->user_type === 'karyawan')
                                                <span class="badge bg-primary">Karyawan</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak diketahui</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->user_type === 'siswa' && $item->siswa && $item->siswa->kelas)
                                                {{ $item->siswa->kelas->nama_kelas ?? 'Tidak tersedia' }}
                                            @elseif ($item->user_type === 'karyawan' && $item->karyawan)
                                                {{ $item->karyawan->jabatan ?? 'Tidak tersedia' }}
                                            @else
                                                Tidak tersedia
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->waktu_scan_masuk)
                                                {{ \Carbon\Carbon::parse($item->waktu_scan_masuk)->format('H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->waktu_scan_keluar)
                                                {{ \Carbon\Carbon::parse($item->waktu_scan_keluar)->format('H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
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
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                Tidak ada data absensi ditemukan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination -->
                    @if ($absensi->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $absensi->firstItem() }} sampai {{ $absensi->lastItem() }}
                                    dari {{ $absensi->total() }} hasil
                                </p>
                            </div>
                            <div>
                                <ul class="pagination pagination-sm">
                                    {{-- Previous Page Link --}}
                                    @if ($absensi->onFirstPage())
                                        <li class="page-item disabled" aria-disabled="true">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $absensi->previousPageUrl() }}"
                                                rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($absensi->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $absensi->nextPageUrl() }}"
                                                rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled" aria-disabled="true">
                                            <span class="page-link">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
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

        // Auto-submit form when filters change (optional)
        function setupAutoFilter() {
            const form = document.getElementById('filterForm');
            const selects = form.querySelectorAll('select');
            const inputs = form.querySelectorAll('input[type="date"]');

            // Auto-submit on select change (except for period which needs custom date handling)
            selects.forEach(select => {
                if (select.id !== 'period') {
                    select.addEventListener('change', function() {
                        // Small delay to allow UI update
                        setTimeout(() => form.submit(), 100);
                    });
                }
            });

            // Auto-submit on date change with debounce
            let dateTimeout;
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    clearTimeout(dateTimeout);
                    dateTimeout = setTimeout(() => form.submit(), 500);
                });
            });
        }

        // Run on page load to ensure correct initial state
        document.addEventListener('DOMContentLoaded', function() {
            toggleCustomDates();
            toggleKelasFilter();

            // Uncomment the line below if you want auto-filtering
            // setupAutoFilter();
        });

        // Handle role change to show/hide class filter
        document.getElementById('role').addEventListener('change', toggleKelasFilter);
    </script>

@endsection
