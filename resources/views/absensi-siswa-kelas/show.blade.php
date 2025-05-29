@extends('templates')
@section('header', 'Detail Absensi Siswa')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('absensi-siswa-kelas.index') }}">Absensi Siswa Kelas</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Absensi Siswa</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Informasi Siswa</h5>
                            <p><strong>Nama:</strong> {{ $absensiSiswaKelas->siswa->nama_lengkap }}</p>
                            <p><strong>NIS:</strong> {{ $absensiSiswaKelas->siswa->nis }}</p>
                            <p><strong>Kelas:</strong> {{ $absensiSiswaKelas->jadwal->kelas->nama_kelas }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Informasi Absensi</h5>
                            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($absensiSiswaKelas->tanggal)->format('d F Y') }}</p>
                            <p><strong>Mata Pelajaran:</strong> {{ $absensiSiswaKelas->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</p>
                            <p><strong>Jam:</strong> {{ $absensiSiswaKelas->jadwal->jam_mulai }} - {{ $absensiSiswaKelas->jadwal->jam_selesai }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Status Kehadiran</h5>
                            <div class="mt-2">
                                @if($absensiSiswaKelas->status == 'Hadir')
                                    <span class="badge badge-success p-2"><i class="fas fa-check-circle mr-1"></i> Hadir</span>
                                @elseif($absensiSiswaKelas->status == 'Izin')
                                    <span class="badge badge-info p-2"><i class="fas fa-envelope mr-1"></i> Izin</span>
                                @elseif($absensiSiswaKelas->status == 'Sakit')
                                    <span class="badge badge-warning p-2"><i class="fas fa-procedures mr-1"></i> Sakit</span>
                                @elseif($absensiSiswaKelas->status == 'Alpa')
                                    <span class="badge badge-danger p-2"><i class="fas fa-user-slash mr-1"></i> Alpa</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Diinput Oleh</h5>
                            <p>{{ $absensiSiswaKelas->inputBy->name }}</p>
                            <p><small>{{ \Carbon\Carbon::parse($absensiSiswaKelas->created_at)->format('d F Y H:i:s') }}</small></p>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center mt-4">
                        <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>

                        @if(Auth::user()->role === 'admin')
                        <form action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.destroy', $absensiSiswaKelas->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <i class="fas fa-trash mr-2"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
