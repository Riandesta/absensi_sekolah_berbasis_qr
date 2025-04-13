@extends('templates')
@section('header', 'Data Petugas Piket')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0">Data Petugas Piket</h5>
        </div>
        <div class="card-body">
            <a href="{{ route('petugas-piket.create') }}" class="btn btn-primary mb-3">Tambah Jadwal</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($petugasPiket as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->karyawan->nama_lengkap }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                            <td>{{ $item->shift }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td>
                                <a href="{{ route('petugas-piket.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('petugas-piket.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $petugasPiket->links() }}
        </div>
    </div>
</div>
@endsection
