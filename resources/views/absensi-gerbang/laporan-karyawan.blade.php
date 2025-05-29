@extends('templates')
@section('header', 'Laporan Absensi Karyawan')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Absensi Karyawan</li>
    </ol>
@endsection
@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laporan Absensi Karyawan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route(Auth::user()->role .'.absensi-gerbang.laporan-karyawan') }}" method="GET">
                <div class="row">
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
                            <th>Nama Karyawan</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Status</th>
                            <th>Diinput Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiKaryawan as $index => $absensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $absensi->tanggal }}</td>
                            <td>{{ $absensi->karyawan->nama }}</td>
                            <td>{{ $absensi->waktu_scan_masuk }}</td>
                            <td>{{ $absensi->waktu_scan_keluar }}</td>
                            <td>{{ $absensi->status }}</td>
                            <td>{{ $absensi->scannedBy->name }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $absensiKaryawan->links() }}
        </div>
    </div>
</div>
@endsection
