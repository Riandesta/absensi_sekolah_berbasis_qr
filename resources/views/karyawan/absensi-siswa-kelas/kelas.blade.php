    @extends('templates')
    @section('header', 'Absensi Siswa Kelas ' . $jadwal->kelas->nama_kelas)
    @section('breadcrumb')
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route(Auth::user()->role .'absensi-siswa-kelas.index') }}">Absensi Siswa Kelas</a></li>
            <li class="breadcrumb-item active">{{ $jadwal->kelas->nama_kelas }}</li>
        </ol>
    @endsection
    @section('content')
        <div class="container-fluid">
            <!-- Alert section for warnings -->
            @if(isset($attendanceWarning) && $attendanceWarning)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Perhatian!</strong> Absensi untuk kelas ini sudah pernah dicatat hari ini.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Main card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clipboard-check me-2"></i> Absensi Siswa {{ $jadwal->kelas->nama_kelas }} -
                        {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}
                    </h6>
                    <div>
                        <span class="badge bg-light text-dark px-3 py-2">
                            <i class="bi bi-calendar-date me-1"></i> {{ \Carbon\Carbon::now()->format('d F Y') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Info section -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-3">
                                        <h6 class="card-title text-primary mb-3">Informasi Mata Pelajaran</h6>
                                        <div class="d-flex mb-2">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-book"></i></div>
                                            <div><strong>Mata Pelajaran:</strong> {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }}</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-clock"></i></div>
                                            <div><strong>Waktu:</strong> {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-person-badge"></i></div>
                                            <div><strong>Guru:</strong> {{ $jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Belum ditentukan' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-3">
                                        <h6 class="card-title text-primary mb-3">Informasi Kelas</h6>
                                        <div class="d-flex mb-2">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-building"></i></div>
                                            <div><strong>Kelas:</strong> {{ $jadwal->kelas->nama_kelas }}</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-people"></i></div>
                                            <div><strong>Jumlah Siswa:</strong> {{ $siswa->count() }} siswa</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="me-3 text-muted" style="width: 30px;"><i class="bi bi-check-circle"></i></div>
                                            <div>
                                                <strong>Sudah Absen Gerbang:</strong>
                                                <span class="badge {{ count($absensiGerbang) == $siswa->count() ? 'bg-success' : 'bg-warning' }}">
                                                    {{ count($absensiGerbang) }} / {{ $siswa->count() }} siswa
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance form -->
                    <form action="{{ route(Auth::user()->role .'.absensi-siswa-kelas.simpan-absensi') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="10%">NIS</th>
                                        <th width="25%">Nama Siswa</th>
                                        <th width="15%" class="text-center">Absen Gerbang</th>
                                        <th width="45%">Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswa as $index => $s)
                                        <tr>
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">{{ $s->nis }}</td>
                                            <td class="align-middle">{{ $s->nama_lengkap }}</td>
                                            <td class="text-center align-middle">
                                                @if (in_array($s->id, $absensiGerbang))
                                                    <span class="badge bg-success text-white px-3 py-2">
                                                        <i class="bi bi-check-circle me-1"></i> Sudah Absen
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger text-white px-3 py-2">
                                                        <i class="bi bi-x-circle me-1"></i> Belum Absen
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="hidden" name="siswa[{{ $index }}][id]" value="{{ $s->id }}">
                                                <div class="btn-group btn-group-toggle w-100 attendance-buttons" data-toggle="buttons">
                                                    <label class="btn btn-outline-success {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Hadir' ? 'active' : '' }} {{ !in_array($s->id, $absensiGerbang) ? 'text-muted' : '' }}"
                                                        data-siswa="{{ $s->id }}" data-status="Hadir" data-jadwal="{{ $jadwal->id }}">
                                                        <input type="radio" name="siswa[{{ $index }}][status]" value="Hadir"
                                                            {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Hadir' ? 'checked' : '' }}
                                                            {{ !in_array($s->id, $absensiGerbang) ? 'disabled' : '' }}>
                                                        <i class="bi bi-check-circle me-1"></i> Hadir
                                                    </label>

                                                    <label class="btn btn-outline-info {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Izin' ? 'active' : '' }}"
                                                        data-siswa="{{ $s->id }}" data-status="Izin" data-jadwal="{{ $jadwal->id }}">
                                                        <input type="radio" name="siswa[{{ $index }}][status]" value="Izin"
                                                            {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Izin' ? 'checked' : '' }}>
                                                        <i class="bi bi-envelope me-1"></i> Izin
                                                    </label>

                                                    <label class="btn btn-outline-warning {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Sakit' ? 'active' : '' }}"
                                                        data-siswa="{{ $s->id }}" data-status="Sakit" data-jadwal="{{ $jadwal->id }}">
                                                        <input type="radio" name="siswa[{{ $index }}][status]" value="Sakit"
                                                            {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Sakit' ? 'checked' : '' }}>
                                                        <i class="bi bi-thermometer-half me-1"></i> Sakit
                                                    </label>

                                                    <label class="btn btn-outline-danger {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Alpa' ? 'active' : '' }}"
                                                        data-siswa="{{ $s->id }}" data-status="Alpa" data-jadwal="{{ $jadwal->id }}">
                                                        <input type="radio" name="siswa[{{ $index }}][status]" value="Alpa"
                                                            {{ isset($absensiSiswa[$s->id]) && $absensiSiswa[$s->id] === 'Alpa' ? 'checked' : '' }}>
                                                        <i class="bi bi-x-circle me-1"></i> Alpa
                                                    </label>
                                                </div>

                                                <div class="mt-2">
                                                    <small class="status-message text-muted">
                                                        @if (isset($absensiSiswa[$s->id]))
                                                            Status: <span class="font-weight-bold text-dark">{{ $absensiSiswa[$s->id] }}</span>
                                                        @else
                                                            <span class="text-secondary">Belum diinput</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary px-4 me-3">
                                <i class="bi bi-save me-2"></i> Simpan Absensi
                            </button>
                            <a href="{{ route(Auth::user()->role .'absensi-siswa-kelas.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
                <!-- Quick summary of current form -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-success fw-bold">Hadir: </span>
                            <span id="count-hadir">0</span> siswa
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-info fw-bold">Izin: </span>
                            <span id="count-izin">0</span> siswa
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <span class="text-warning fw-bold">Sakit: </span>
                            <span id="count-sakit">0</span> siswa
                        </div>
                        <div class="col-md-3">
                            <span class="text-danger fw-bold">Alpa: </span>
                            <span id="count-alpa">0</span> siswa
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Function to update attendance counts
                function updateAttendanceCounts() {
                    $('#count-hadir').text($('input[value="Hadir"]:checked').length);
                    $('#count-izin').text($('input[value="Izin"]:checked').length);
                    $('#count-sakit').text($('input[value="Sakit"]:checked').length);
                    $('#count-alpa').text($('input[value="Alpa"]:checked').length);
                }

                // Initialize counts
                updateAttendanceCounts();

                // Update counts when any radio button changes
                $('input[type="radio"]').change(updateAttendanceCounts);

                // Real-time attendance update via AJAX
                $('.attendance-buttons label:not(.disabled)').click(function() {
                    const siswaId = $(this).data('siswa');
                    const status = $(this).data('status');
                    const jadwalId = $(this).data('jadwal');
                    const statusMessageElement = $(this).closest('td').find('.status-message');

                    // Show loading indicator
                    statusMessageElement.html('<span class="text-secondary"><i class="bi bi-arrow-repeat spin me-1"></i> Menyimpan...</span>');

                    // Update via AJAX
                    $.ajax({
                        url: "{{ route(Auth::user()->role .'.absensi-siswa-kelas.update-status') }}",
                        type: "POST",
                        data: {
                            siswa_id: siswaId,
                            jadwal_id: jadwalId,
                            status: status,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show toast or notification
                                toastr.success(response.message);

                                // Update status message
                                statusMessageElement.html(
                                    'Status: <span class="font-weight-bold text-dark">' + status +
                                    '</span>');

                                // Update counts
                                updateAttendanceCounts();
                            } else {
                                toastr.error(response.message);
                                // If attendance failed, reset the button
                                $('.attendance-buttons label[data-siswa="' + siswaId + '"][data-status="' + status + '"]')
                                    .removeClass('active');
                                $('input[name^="siswa"][value="' + status + '"]').prop('checked', false);

                                // Reset status message
                                statusMessageElement.html('<span class="text-secondary">Belum diinput</span>');

                                // Update counts
                                updateAttendanceCounts();
                            }
                        },
                        error: function(xhr) {
                            // Show error message from response if available
                            let errorMessage = 'Terjadi kesalahan saat memperbarui status absensi';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            toastr.error(errorMessage);

                            // Reset the button
                            $('.attendance-buttons label[data-siswa="' + siswaId + '"][data-status="' + status + '"]')
                                .removeClass('active');

                            // Reset status message
                            statusMessageElement.html('<span class="text-secondary">Belum diinput</span>');

                            // Update counts
                            updateAttendanceCounts();
                        }
                    });
                });
            });
        </script>
        <style>
            .spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .attendance-buttons .btn {
                flex: 1;
                transition: all 0.2s;
            }

            .attendance-buttons .btn.active {
                box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
                transform: scale(1.05);
            }
        </style>
    @endpush
