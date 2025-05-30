@extends('templates')
@section('header', 'Absensi Siswa Kelas')
@section('breadcrumb', 'index')
@section('content')
<div class="container-fluid">
    @if(Auth::user()->role === 'karyawan' && Auth::user()->karyawan && strtolower(Auth::user()->karyawan->jabatan) === 'guru')
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-calendar-check me-2 text-primary mb-2"  style="font-size: 1.5rem;"></i>
                <h5 class="m-0 text-primary fw-bold ms-2">Jadwal Mengajar Hari Ini - {{ now()->format('d F Y') }}</h5>
            </div>

            <div class="row g-3">
                @if($jadwalGuru->isEmpty())
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle me-2 fs-4"></i>
                            <span>Anda tidak memiliki jadwal mengajar hari ini.</span>
                        </div>
                    </div>
                @else
                    @foreach($jadwalGuru as $jadwal)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-{{ in_array($jadwal->id, $absensiGuru) ? 'success' : 'secondary' }} shadow-sm">
                            <div class="card-header bg-{{ in_array($jadwal->id, $absensiGuru) ? 'success' : 'secondary' }} text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $jadwal->kelas->nama_kelas }}</h6>
                                <span class="badge bg-light text-dark">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-book me-2" style="font-size: 1.2rem; color: #007bff;"></i>
                                    <h5 class="card-text mt-1">{{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</h5>
                                </div>

                                @php
                                    $attendanceRecorded = \App\Models\AbsensiSiswaKelas::where('jadwal_id', $jadwal->id)
                                        ->where('tanggal', \Carbon\Carbon::now()->toDateString())
                                        ->exists();
                                @endphp

                                @if(in_array($jadwal->id, $absensiGuru) && !$attendanceRecorded)
                                    <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.kelas', $jadwal->id) }}" class="btn btn-success w-100 mt-auto">
                                        <i class="bi bi-pencil-square me-2"></i> Lakukan Absensi Siswa
                                    </a>
                                @elseif($attendanceRecorded)
                                    <button class="btn btn-outline-success w-100 mt-auto" disabled>
                                        <i class="bi bi-check-circle me-2"></i> Absensi Sudah Dilakukan
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary w-100 mt-auto" disabled>
                                        <i class="bi bi-clock-history me-2"></i> Lakukan Absen Guru Terlebih Dahulu
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Data Absensi -->
    <div class="card shadow-sm">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
            <h6 class="m-0 font-weight-bold text-primary fw-bold">
                <i class="bi bi-list-check me-2"></i> Data Absensi Siswa Kelas
            </h6>
            <a href="{{ route(Auth::user()->role .'.laporan-absensi-siswa') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-file-earmark-text me-1"></i> Rekap Absensi
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th width="20%">Nama Siswa</th>
                            <th width="15%">Kelas</th>
                            <th width="20%">Mata Pelajaran</th>
                            <th width="10%" class="text-center">Status</th>
                            {{-- <th width="10%">Diinput Oleh</th> --}}
                            <th width="8%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiSiswaKelas as $index => $absensi)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $absensi->siswa->nama_lengkap ?? $absensi->siswa->nama ?? 'N/A' }}</td>
                            <td>{{ $absensi->jadwal->kelas->nama_kelas ?? 'N/A' }}</td>
                            <td>{{ $absensi->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($absensi->status == 'Hadir')
                                    <span class="badge bg-success text-white">Hadir</span>
                                @elseif($absensi->status == 'Izin')
                                    <span class="badge bg-info text-white">Izin</span>
                                @elseif($absensi->status == 'Sakit')
                                    <span class="badge bg-warning text-dark">Sakit</span>
                                @elseif($absensi->status == 'Alpa')
                                    <span class="badge bg-danger text-white">Alpa</span>
                                @endif
                            </td>
                            {{-- <td>{{ $absensi->inputBy->name ?? 'N/A' }}</td> --}}
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.show', $absensi->id) }}" class="btn btn-sm btn-info" data-toggle="tooltip" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.destroy', $absensi->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" data-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder2-open text-muted mb-2" style="font-size: 2.5rem;"></i>
                                    <p class="text-muted mb-0 fs-6">Tidak ada data absensi siswa</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $absensiSiswaKelas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Add datatable functionality
        $('#dataTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": true,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "search": "Cari:",
                "zeroRecords": "Tidak ada data yang ditemukan"
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* General Card Styling */
    .card {
        border-radius: 10px;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #e9ecef;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Header Styling */
    .card-header {
        border-radius: 10px 10px 0 0;
        font-size: 1rem;
        background-color: #f8f9fc;
        border-bottom: 1px solid #e9ecef;
    }

    /* Table Styling */
    #dataTable th {
        font-weight: 600;
        background-color: #f8f9fc;
        color: #343a40;
        text-align: center;
    }

    #dataTable td {
        vertical-align: middle;
        text-align: center;
    }

    .badge {
        padding: 0.5em 0.7em;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .btn-group .btn i {
        font-size: 0.875rem;
    }

    /* Empty State Styling */
    .bi-folder2-open {
        font-size: 3rem;
        color: #e9ecef;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-header h6 {
            font-size: 0.9rem;
        }

        .btn-group .btn i {
            font-size: 0.75rem;
        }
    }
</style>
@endpush
