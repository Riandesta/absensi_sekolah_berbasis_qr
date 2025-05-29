@extends('templates')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Laporan Absensi Guru Kelas</span>
                        <a href="{{ route(Auth::user()->role .'.absensi-guru-kelas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>Waktu Scan</th>
                                        <th>Status</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Jadwal</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Dicatat Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($absensiGuru as $index => $absensi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $absensi->karyawan->nama_lengkap ?? 'Data Tidak Tersedia' }}</td>
                                            <td>{{ substr($absensi->waktu_scan, 0, 5) }}</td>
                                            <td><span class="badge bg-success">{{ $absensi->status }}</span></td>
                                            <td>{{ $absensi->kelas->nama_kelas ?? 'N/A' }}</td>
                                            <td>{{ $absensi->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'N/A' }}</td>
                                            <td>{{ $absensi->jadwal ? $absensi->jadwal->hari . ', ' . substr($absensi->jadwal->jam_mulai,0,5) . ' - ' . substr($absensi->jadwal->jam_selesai,0,5) : 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</td>
                                            <td>{{ $absensi->status }}</td>
                                            <td>{{ $absensi->scanByUser->username ?? 'Unknown' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="11" class="text-center">Tidak ada data absensi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
