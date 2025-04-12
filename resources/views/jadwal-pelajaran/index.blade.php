@extends('templates')
@section('header', 'Jadwal Pelajaran')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Data Jadwal Pelajaran</h5>
                    <a href="{{ route('jadwal-pelajaran.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jadwal
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('jadwal-pelajaran.index') }}" method="GET" class="d-flex mb-3">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari jadwal..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>

                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                {{-- <th>Slot Jadwal</th> --}}
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwalPelajaran as $jadwal)
                                <tr>
                                    <td>{{ $loop->iteration + ($jadwalPelajaran->currentPage() - 1) * $jadwalPelajaran->perPage() }}</td>
                                    <td>Guru</td>
                                    {{-- <td>{{ $jadwal->guru->nama_lengkap ?? '-' }}</td> --}}
                                    <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                                    <td>{{ $jadwal->tahunAjaran->tahun_formatted }}</td>
                                    {{-- <td> --}}
                                        {{-- @foreach ($jadwal->jadwal as $slot)
                                            <span class="badge bg-secondary me-1">
                                                {{ $slot->hari }} ({{ $slot->jam_mulai }} - {{ $slot->jam_selesai }})
                                            </span>
                                        @endforeach --}}
                                    {{-- </td> --}}
                                    <td class="text-nowrap">
                                        <!-- Tombol Detail -->
                                        <button class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal"
                                            data-bs-target="#detailJadwalModal{{ $jadwal->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <!-- Tombol Edit -->
                                        <a href="{{ route('jadwal-pelajaran.edit', $jadwal->id) }}"
                                           class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('jadwal-pelajaran.destroy', $jadwal->id) }}"
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus seluruh jadwal ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Detail Jadwal -->
                                <div class="modal fade" id="detailJadwalModal{{ $jadwal->id }}" tabindex="-1"
                                     aria-labelledby="detailModalLabel{{ $jadwal->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="detailModalLabel{{ $jadwal->id }}">Detail Jadwal Pelajaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Mata Pelajaran:</strong> {{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                                <p><strong>Penanggung Jawab:</strong> {{ $jadwal->guru->nama_lengkap ?? '-' }}</p>

                                                @php
                                                    $groupedByHari = $jadwal->jadwal->groupBy('hari');
                                                @endphp

                                                @foreach ($groupedByHari as $hari => $slots)
                                                    <div class="mb-3">
                                                        <p><strong>Hari:</strong> {{ $hari }}</p>
                                                        <p><strong>Jadwal Pelajaran:</strong></p>
                                                        <ul class="list-group">
                                                            @foreach ($slots as $slot)
                                                                <li class="list-group-item">
                                                                    {{ $slot->jam_mulai }} - {{ $slot->jam_selesai }}
                                                                    ({{ $slot->kelas->nama_kelas ?? 'Kelas tidak tersedia' }})
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data jadwal pelajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center">
                        {{ $jadwalPelajaran->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
