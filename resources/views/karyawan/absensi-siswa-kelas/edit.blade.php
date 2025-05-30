@extends('templates')
@section('header', 'Edit Absensi Siswa Kelas ' . $jadwal->kelas->nama_kelas)
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.index') }}">Absensi Siswa Kelas</a></li>
        <li class="breadcrumb-item active">Edit Absensi {{ $jadwal->kelas->nama_kelas }}</li>
    </ol>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Edit Absensi Siswa {{ $jadwal->kelas->nama_kelas }} -
                    {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</h6>
                <div>
                    <span class="badge badge-info">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mata Pelajaran:</strong> {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</p>
                            <p><strong>Waktu:</strong> {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p><strong>Kelas:</strong> {{ $jadwal->kelas->nama_kelas }}</p>
                            <p><strong>Jumlah Siswa:</strong> {{ $siswa->count() }} siswa</p>
                            <p><strong>Guru:</strong> {{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">NIS</th>
                                    <th width="25%">Nama Siswa</th>
                                    <th width="15%">Absen Gerbang</th>
                                    <th width="45%">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswa as $index => $s)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $s->nis }}</td>
                                        <td>{{ $s->nama_lengkap }}</td>
                                        <td>
                                            @if (in_array($s->id, $absensiGerbang))
                                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Sudah Absen</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Belum Absen</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="hidden" name="siswa[{{ $index }}][id]" value="{{ $s->id }}">
                                            <div class="btn-group btn-group-toggle attendance-buttons" data-toggle="buttons">
                                                <label class="btn btn-outline-success {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Hadir' ? 'active' : '' }} {{ !in_array($s->id, $absensiGerbang) ? 'text-muted' : '' }}"
                                                    data-siswa="{{ $s->id }}" data-status="Hadir" data-jadwal="{{ $jadwal->id }}">
                                                    <input type="radio" name="siswa[{{ $index }}][status]" value="Hadir"
                                                        {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Hadir' ? 'checked' : '' }}
                                                        {{ !in_array($s->id, $absensiGerbang) ? 'disabled' : '' }}>
                                                    <i class="fas fa-check-circle mr-1"></i> Hadir
                                                </label>

                                                <label class="btn btn-outline-info {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Izin' ? 'active' : '' }}"
                                                    data-siswa="{{ $s->id }}" data-status="Izin" data-jadwal="{{ $jadwal->id }}">
                                                    <input type="radio" name="siswa[{{ $index }}][status]" value="Izin"
                                                        {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Izin' ? 'checked' : '' }}>
                                                    <i class="fas fa-envelope mr-1"></i> Izin
                                                </label>

                                                <label class="btn btn-outline-warning {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Sakit' ? 'active' : '' }}"
                                                    data-siswa="{{ $s->id }}" data-status="Sakit" data-jadwal="{{ $jadwal->id }}">
                                                    <input type="radio" name="siswa[{{ $index }}][status]" value="Sakit"
                                                        {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Sakit' ? 'checked' : '' }}>
                                                    <i class="fas fa-procedures mr-1"></i> Sakit
                                                </label>

                                                <label class="btn btn-outline-danger {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Alpa' ? 'active' : '' }}"
                                                    data-siswa="{{ $s->id }}" data-status="Alpa" data-jadwal="{{ $jadwal->id }}">
                                                    <input type="radio" name="siswa[{{ $index }}][status]" value="Alpa"
                                                        {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Alpa' ? 'checked' : '' }}>
                                                    <i class="fas fa-user-slash mr-1"></i> Alpa
                                                </label>
                                            </div>

                                            <div class="mt-2">
                                                <small class="status-message text-muted">
                                                    @if (isset($absensiSiswa[$s->id]))
                                                        Status: <span class="font-weight-bold">{{ $absensiSiswa[$s->id] }}</span>
                                                    @else
                                                        Belum diinput
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route(Auth::user()->role .'.absensi-siswa-kelas.view', ['jadwal_id' => $jadwal->id, 'tanggal' => $tanggal]) }}" class="btn btn-secondary px-4 ml-2">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Real-time attendance update via AJAX
            $('.attendance-buttons label:not(.disabled)').click(function() {
                const siswaId = $(this).data('siswa');
                const status = $(this).data('status');
                const jadwalId = $(this).data('jadwal');
                const statusMessageElement = $(this).closest('td').find('.status-message');

                // Show loading indicator
                statusMessageElement.html('<span class="text-secondary"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>');

                // Update via AJAX
                $.ajax({
                    url: "{{ route(Auth::user()->role .'.absensi-siswa-kelas.update-status') }}",
                    type: "POST",
                    data: {
                        siswa_id: siswaId,
                        jadwal_id: jadwalId,
                        tanggal: "{{ $tanggal }}",
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show toast or notification
                            toastr.success(response.message);

                            // Update status message
                            statusMessageElement.html(
                                'Status: <span class="font-weight-bold">' + status +
                                '</span>');
                        } else {
                            toastr.error(response.message);
                            // Reset the button if the status wasn't saved
                            statusMessageElement.html('Belum diinput');
                        }
                    },
                    error: function(xhr) {
                        // Show error message from response if available
                        let errorMessage = 'Terjadi kesalahan saat memperbarui status absensi';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        toastr.error(errorMessage);
                        statusMessageElement.html('Belum diinput');
                    }
                });
            });
        });
    </script>
@endpush
