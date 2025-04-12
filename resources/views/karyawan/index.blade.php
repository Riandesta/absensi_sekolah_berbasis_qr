@extends('templates')
@section('header', 'Data Karyawan')

@section('content')
<div class="container">
    <a href="{{ route('karyawan.create') }}" class="btn btn-primary mb-3">Tambah Karyawan</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Lengkap</th>
                <th>Jabatan</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karyawan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nip }}</td>
                <td>{{ $item->nama_lengkap }}</td>
                <td>{{ $item->jabatan }}</td>
                <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
                <td>
                    <a href="{{ route('karyawan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data karyawan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
