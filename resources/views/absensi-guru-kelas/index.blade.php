@extends('templates')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Data Absensi Guru Kelas</span>
                        <div>
                            @if (Auth::user()->role === 'kelas')
                                <a href="{{ route('kelas.absensi-guru-kelas.scan') }}" class="btn btn-primary btn-sm scan-btn">
                                    <i class="fas fa-qrcode"></i> Scan QR Code
                                </a>
                            @endif
                            <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.report') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-alt"></i> Laporan
                            </a>
                            <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Filter -->
                        <form action="{{ route(Auth::user()->role .'.absensi-guru-kelas.index') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Tanggal</span>
                                        <input type="date" class="form-control" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Kelas</span>
                                        <select class="form-control" name="kelas_id">
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}" @selected(request('kelas_id') == $kelas->id)>
                                                    {{ $kelas->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Laporan</span>
                                        <select class="form-control" name="report_type">
                                            <option value="">Pilih Jenis Laporan</option>
                                            <option value="daily" @selected(request('report_type') == 'daily')>Harian</option>
                                            <option value="weekly" @selected(request('report_type') == 'weekly')>Mingguan</option>
                                            <option value="monthly" @selected(request('report_type') == 'monthly')>Bulanan</option>
                                            <option value="semester" @selected(request('report_type') == 'semester')>Semester</option>
                                            <option value="yearly" @selected(request('report_type') == 'yearly')>Tahunan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <!-- Modal Detail Absensi -->
                        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel">Detail Absensi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>Nama Guru</th>
                                                    <td id="detail-nama"></td>
                                                </tr>
                                                <tr>
                                                    <th>Kelas</th>
                                                    <td id="detail-kelas"></td>
                                                </tr>
                                                <tr>
                                                    <th>Mata Pelajaran</th>
                                                    <td id="detail-mapel"></td>
                                                </tr>
                                                <tr>
                                                    <th>Jadwal</th>
                                                    <td id="detail-jadwal"></td>
                                                </tr>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <td id="detail-tanggal"></td>
                                                </tr>
                                                <tr>
                                                    <th>Waktu Scan</th>
                                                    <td id="detail-waktu"></td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td id="detail-status"></td>
                                                </tr>
                                                <tr>
                                                    <th>Dicatat Oleh</th>
                                                    <td id="detail-scanby"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>Waktu Scan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($absensiGuru as $index => $absensi)
                                        <tr>
                                            <td>{{ $absensiGuru->firstItem() + $index }}</td>
                                            <td>{{ $absensi->karyawan->nama_lengkap ?? 'Data Tidak Tersedia' }}</td>
                                            <td>{{ substr($absensi->waktu_scan, 0, 5) }}</td>
                                            <td><span class="badge bg-success">{{ $absensi->status }}</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showDetail(
                                                        '{{ $absensi->karyawan->nama_lengkap ?? 'N/A' }}',
                                                        '{{ $absensi->kelas->tingkat ?? '' }} {{ $absensi->kelas->nama_kelas ?? 'N/A' }}',
                                                        '{{ $absensi->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'N/A' }}',
                                                        '{{ $absensi->jadwal ? $absensi->jadwal->hari . ', ' . substr($absensi->jadwal->jam_mulai,0,5) . ' - ' . substr($absensi->jadwal->jam_selesai,0,5) : 'N/A' }}',
                                                        '{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}',
                                                        '{{ substr($absensi->waktu_scan, 0, 5) }}',
                                                        '{{ $absensi->status }}',
                                                        '{{ $absensi->scanByUser->username ?? 'Unknown' }}'
                                                    )">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </button>

                                                    @if(Auth::user()->hasRole('admin'))
                                                        <form action="{{ route(Auth::user()->role .'.absensi-guru-kelas.destroy', $absensi->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm ms-375" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                                Delete
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">Tidak ada data absensi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $absensiGuru->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showDetail(nama, kelas, mapel, jadwal, tanggal, waktu, status, scanby) {
            document.getElementById('detail-nama').innerText = nama;
            document.getElementById('detail-kelas').innerText = kelas;
            document.getElementById('detail-mapel').innerText = mapel;
            document.getElementById('detail-jadwal').innerText = jadwal;
            document.getElementById('detail-tanggal').innerText = tanggal;
            document.getElementById('detail-waktu').innerText = waktu;
            document.getElementById('detail-status').innerText = status;
            document.getElementById('detail-scanby').innerText = scanby;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const scanBtn = document.querySelector('.scan-btn');
            const absensiStatus = "{{ session('absensi_status') }}";

            if (absensiStatus === 'success') {
                scanBtn.style.display = 'none';
                const badge = document.createElement('span');
                badge.classList.add('badge', 'bg-success');
                badge.textContent = 'Sudah Absen';
                scanBtn.parentNode.insertBefore(badge, scanBtn);
            }
        });
    </script>
@endsection
