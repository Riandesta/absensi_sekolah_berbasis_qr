@extends('templates')
@section('header', 'Data Siswa')

@section('content')
<div class="container">
    <a href="{{ route('siswa.create') }}" class="btn btn-primary mb-3">Tambah Siswa</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswa as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nis }}</td>
                <td>{{ $item->nama_lengkap }}</td>
                <td>{{ $item->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $item->jurusan->nama_jurusan ?? '-' }}</td>
                <td>
                    <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('siswa.destroy', $item->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                    <!-- Tombol untuk melihat detail siswa -->
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                        Lihat Detail
                    </button>
                </td>
            </tr>

            <!-- Modal untuk detail siswa -->
            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel">Detail Siswa: {{ $item->nama_lengkap }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>NIS:</strong> {{ $item->nis }}</p>
                            <p><strong>Nama Lengkap:</strong> {{ $item->nama_lengkap }}</p>
                            <p><strong>Kelas:</strong> {{ $item->kelas->nama_kelas ?? '-' }}</p>
                            <p><strong>Jurusan:</strong> {{ $item->jurusan->nama_jurusan ?? '-' }}</p>
                            <p><strong>No. WhatsApp:</strong> {{ $item->no_wa ?? '-' }}</p>
                            <p><strong>Tempat Lahir:</strong> {{ $item->tempat_lahir ?? '-' }}</p>
                            <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') ?? '-' }}</p>
                            <hr>
                            <h5>Foto</h5>
                            <!-- Foto siswa -->
                            @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto Siswa" class="img-fluid" style="max-height: 300px; object-fit: cover;">
                            @else
                                <p>Tidak ada foto tersedia.</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data siswa.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
