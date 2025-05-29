@extends('templates')

@section('header', 'Jadwal Pelajaran')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Data Jadwal Pelajaran</h3>
                    <a href="{{ route(Auth::user()->role .'.jadwal-pelajaran.create') }}" class="btn btn-primary">Tambah Jadwal</a>
                </div>

                <div class="card-body">

                    {{-- Uncomment ini jika ingin fitur pencarian --}}
                    {{--
                    <form action="{{ route('jadwal-pelajaran.index') }}" method="GET" class="form-inline mb-3">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Cari jadwal..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                    --}}

                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwalPelajaran as $jadwal)
                                <tr>
                                    <td>{{ $loop->iteration + ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() }}</td>
                                    <td>{{ optional($jadwal->guru)->nama_lengkap ?? 'Tidak ada guru' }}</td>
                                    <td>{{ optional($jadwal->mataPelajaran)->nama_mapel ?? 'Tidak ada mata pelajaran' }}</td>
                                    <td>{{ optional($jadwal->tahunAjaran)->tahun_formatted ?? 'Tidak ada tahun ajaran' }}</td>
                                    <td>
                                        <a href="{{ route(Auth::user()->role .'.jadwal-pelajaran.edit', $jadwal->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route(Auth::user()->role .'.jadwal-pelajaran.destroy', $jadwal->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data jadwal pelajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            {{ $jadwalPelajaran->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
