@extends('templates')
@section('header', 'Import Data Absensi Siswa')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Import Data Absensi Siswa</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Form Import Data</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.import-absensi') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="jadwal_id" class="form-label">Jadwal Pelajaran</label>
                                        <select name="jadwal_id" id="jadwal_id" class="form-select @error('jadwal_id') is-invalid @enderror" required>
                                            <option value="">Pilih Jadwal Pelajaran</option>
                                            @foreach($jadwalMengajar as $jadwal)
                                                <option value="{{ $jadwal->id }}">
                                                    {{ $jadwal->jadwalPelajaran->mataPelajaran->nama_mapel }} -
                                                    {{ $jadwal->kelas->nama_kelas }} -
                                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} -
                                                    {{ $jadwal->hari }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jadwal_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                                        @error('tanggal')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="file" class="form-label">File Excel</label>
                                        <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required accept=".xlsx, .xls">
                                        @error('file')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">Format file yang diizinkan: Excel (.xlsx, .xls)</div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-upload me-2"></i> Import Data
                                        </button>
                                        <a href="{{ route('karyawan.template-download') }}" class="btn btn-secondary">
                                            <i class="bi bi-download me-2"></i> Download Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Petunjuk Import</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Informasi Penting</h6>
                                    <ol class="mb-0">
                                        <li>Download template Excel terlebih dahulu.</li>
                                        <li>Isi data sesuai dengan format yang telah ditentukan:</li>
                                        <ul>
                                            <li>Kolom <strong>NIS</strong>: Nomor Induk Siswa (wajib)</li>
                                            <li>Kolom <strong>Status</strong>: Hadir/Izin/Sakit/Alpa (wajib)</li>
                                            <li>Kolom <strong>Keterangan</strong>: Opsional</li>
                                        </ul>
                                        <li>Pastikan NIS yang dimasukkan sesuai dengan NIS siswa yang terdaftar.</li>
                                        <li>Status hanya boleh diisi dengan "Hadir", "Izin", "Sakit", atau "Alpa".</li>
                                        <li>Jangan mengubah format template yang telah disediakan.</li>
                                        <li>Simpan file dalam format Excel (.xlsx atau .xls).</li>
                                    </ol>
                                </div>

                                <div class="alert alert-warning">
                                    <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Perhatian</h6>
                                    <p class="mb-0">
                                        Data absensi yang diimpor akan menggantikan data absensi yang sudah ada untuk tanggal dan jadwal yang sama.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
