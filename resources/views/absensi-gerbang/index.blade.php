@extends('templates')
@section('header', 'Data Absensi Gerbang')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0">Data Absensi Gerbang</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Tanggal</th>
                        <th>Waktu Scan</th>
                        <th>Status</th>
                        <th>Di-scan Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensiGerbang as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($item->siswa)
                                    {{ $item->siswa->nama_lengkap }}
                                @elseif ($item->karyawan)
                                    {{ $item->karyawan->nama_lengkap }}
                                @endif
                            </td>
                            <td>
                                @if ($item->siswa)
                                    Siswa
                                @elseif ($item->karyawan)
                                    Karyawan
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                            <td>{{ $item->waktu_scan }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->scannedBy->name }}</td>
                            <td>
                                <form action="{{ route('absensi-gerbang.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $absensiGerbang->links() }}
        </div>
    </div>
</div>
@endsection
