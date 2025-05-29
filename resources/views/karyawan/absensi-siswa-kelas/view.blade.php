@extends('templates')
@section('header', 'Lihat Absensi Siswa Kelas ' . $jadwal->kelas->nama_kelas)
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.index') }}">Absensi Siswa Kelas</a></li>
        <li class="breadcrumb-item active">Lihat Absensi {{ $jadwal->kelas->nama_kelas }}</li>
    </ol>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Absensi Siswa {{ $jadwal->kelas->nama_kelas }} -
                    {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</h6>
                <div>
                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mata Pelajaran:</strong> {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</p>
                            <p><strong>Waktu:</strong> {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p><strong>Kelas:</strong> {{ $jadwal->kelas->nama_kelas }}</p>
                            <p><strong>Jumlah Siswa:</strong> {{ $siswa->count() }} siswa</p>
                            <p><strong>Guru:</strong> {{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statHadir }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statIzin }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sakit</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statSakit }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-procedures fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpa</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statAlpa }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">NIS</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="20%">Status Absensi</th>
                                <th width="15%">Waktu Dicatat</th>
                                <th width="20%">Dicatat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensiSiswa as $index => $absensi)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $absensi->siswa->nis ?? 'N/A' }}</td>
                                    <td>{{ $absensi->siswa->nama_lengkap ?? 'N/A' }}</td>
                                    <td>
                                        @if($absensi->status == 'Hadir')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle mr-1"></i> Hadir
                                            </span>
                                        @elseif($absensi->status == 'Izin')
                                            <span class="badge badge-info">
                                                <i class="fas fa-envelope mr-1"></i> Izin
                                            </span>
                                        @elseif($absensi->status == 'Sakit')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-procedures mr-1"></i> Sakit
                                            </span>
                                        @elseif($absensi->status == 'Alpa')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-user-slash mr-1"></i> Alpa
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Diketahui</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($absensi->created_at)->format('H:i:s') }}</td>
                                    <td>{{ $absensi->inputBy->name ?? 'System' }}</td>
                                </tr>
                            @endforeach
                            @if(count($absensiSiswa) === 0)
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data absensi</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    @if(Auth::user()->role === 'admin' || (Auth::user()->role === 'karyawan' && Auth::user()->karyawan && Auth::user()->karyawan->jabatan === 'guru'))
                        <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.edit', ['jadwal_id' => $jadwal->id, 'tanggal' => $tanggal]) }}" class="btn btn-warning px-4">
                            <i class="fas fa-edit mr-2"></i> Edit Absensi
                        </a>
                    @endif

                    <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.laporan', ['jadwal_id' => $jadwal->id, 'tanggal' => $tanggal, 'download' => true]) }}" class="btn btn-primary px-4 ml-2">
                        <i class="fas fa-file-pdf mr-2"></i> Unduh PDF
                    </a>

                    <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.index') }}" class="btn btn-secondary px-4 ml-2">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
