@extends('templates')
@section('header', 'Laporan Absensi Siswa')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Absensi Siswa</li>
    </ol>
@endsection
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laporan Absensi Siswa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.laporan-siswa') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <label for="kelas_id">Kelas</label>
                        <select name="kelas_id" id="kelas_id" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary mt-4">Filter</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Diinput Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiSiswa as $index => $absensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $absensi->tanggal }}</td>
                            <td>{{ $absensi->siswa->nama }}</td>
                            <td>{{ $absensi->jadwal->kelas->nama_kelas }}</td>
                            <td>{{ $absensi->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</td>
                            <td>{{ $absensi->status }}</td>
                            <td>{{ $absensi->inputBy->name }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $absensiSiswa->links() }}
        </div>
    </div>
</div>
@endsection
