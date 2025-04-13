@extends('templates')
@section('header', 'Data Siswa')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color: #f1f1f1; color: #333;">
                <h5 class="mb-0" style="color: #000;">Data Siswa</h5>
                <a href="{{ route('siswa.create') }}" class="btn btn-success text-light">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Siswa
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
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
                                        <a href="{{ route('siswa.edit', $item->id) }}" class="btn btn-warning btn-sm me-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>

                                        <form action="{{ route('siswa.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm me-1"><i class="bi bi-trash"></i>
                                                Hapus</button>
                                        </form>

                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailModal{{ $item->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>

                                        <!-- QR Code & ID Card Dropdown -->
                                        @if ($item->qr_code)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-qr-code"></i> QR/ID Card
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('siswa.download-qrcode', $item->id) }}" class="dropdown-item">
                                                            <i class="bi bi-card-heading"></i> Unduh ID Card
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @else
                                            <span class="btn btn-secondary btn-sm disabled">
                                                <i class="bi bi-exclamation-circle"></i> Tidak Ada QR Code
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal Detail -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="detailModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow-lg rounded-4" style="border-radius: 15px;">
                                            <div class="modal-header bg-light text-dark rounded-top">
                                                <h5 class="modal-title"><i class="bi bi-person-circle"></i> Detail Siswa:
                                                    {{ $item->nama_lengkap }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="text-center mb-4">
                                                    @if ($item->foto)
                                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto Siswa"
                                                            class="img-fluid"
                                                            style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #C1C1C1FF;">
                                                    @else
                                                        <p><em>Tidak ada foto tersedia.</em></p>
                                                    @endif
                                                </div>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>NIS:</strong> {{ $item->nis }}</li>
                                                    <li class="list-group-item"><strong>Nama Lengkap:</strong> {{ $item->nama_lengkap }}</li>
                                                    <li class="list-group-item"><strong>Kelas:</strong> {{ $item->kelas->nama_kelas ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Jurusan:</strong> {{ $item->jurusan->nama_jurusan ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>No. WhatsApp:</strong> {{ $item->no_wa ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tempat Lahir:</strong> {{ $item->tempat_lahir ?? '-' }}</li>
                                                    <li class="list-group-item"><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') ?? '-' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
