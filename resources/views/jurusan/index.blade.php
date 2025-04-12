@extends('templates')

@section('header', 'Jurusan')

@section('content')
<div class="container py-4">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    <div class="row">
        <div class="col-md-12">
            <!-- Card Utama -->
            <div class="card shadow-sm">
                <!-- Header Card -->
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f1f1f1; color: #333;">
                    <h5 class="mb-0" style="color: #000;">Data Jurusan</h5>
                    <button type="button" class="btn btn-success text-light" data-bs-toggle="modal" data-bs-target="#tambahJurusanModal">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jurusan
                    </button>
                </div>


                <!-- Body Card -->
                <div class="card-body">

                    <!-- Tabel Jurusan -->
                    <div class="table-responsive mt-5">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jurusan</th>
                                    <th>Kode Jurusan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jurusan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_jurusan }}</td>
                                        <td>{{ $item->kode_jurusan }}</td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-sm btn-warning me-1"
                                                data-bs-toggle="modal" data-bs-target="#editJurusanModal{{ $item->id }}">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>

                                            <!-- Form Hapus -->
                                            <form action="{{ route('jurusan.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Jurusan -->
                                    <div class="modal fade" id="editJurusanModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Jurusan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('jurusan.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <!-- Validasi Error -->
                                                    @if($errors->has('edit_' . $item->id))
                                                        <div class="alert alert-danger">
                                                            @foreach ($errors->get('edit_' . $item->id) as $field => $messages)
                                                                @foreach ($messages as $message)
                                                                    <li>{{ $message }}</li>
                                                                @endforeach
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Jurusan</label>
                                                            <input type="text" name="nama_jurusan" class="form-control"
                                                                value="{{ old('nama_jurusan', $item->nama_jurusan) }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kode Jurusan</label>
                                                            <input type="text" name="kode_jurusan" class="form-control"
                                                                value="{{ old('kode_jurusan', $item->kode_jurusan) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Auto Show Modal Jika Error -->
                                    @if($errors->has('edit_' . $item->id))
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                new bootstrap.Modal(document.getElementById('editJurusanModal{{ $item->id }}')).show();
                                            });
                                        </script>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data jurusan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $jurusan->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="tambahJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('jurusan.store') }}" method="POST">
                @csrf

                <!-- Validasi Error -->
                @if($errors->has('create'))
                    <div class="alert alert-danger">
                        @foreach ($errors->get('create') as $field => $messages)
                            @foreach ($messages as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control"
                            value="{{ old('nama_jurusan') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" class="form-control"
                            value="{{ old('kode_jurusan') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto Show Modal Tambah Jika Error -->
@if($errors->has('create'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('tambahJurusanModal')).show();
        });
    </script>
@endif
@endsection
