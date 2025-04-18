@extends('templates')
@section('header', 'Tambah Karyawan')

@section('content')
<div class="container my-2">
    <div class="card shadow">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0">Form Tambah Karyawan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mt-2">
                    <!-- NIP -->
                    <div class="col-md-6 mb-3">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" required>
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="col-md-6 mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-md-6 mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="">Pilih</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Jabatan -->
                    <div class="col-md-6 mb-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan') }}" required>
                        @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Kelas -->
                    <div class="col-md-6 mb-3">
                        <label>Kelas</label>
                        <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Jurusan -->
                    <div class="col-md-6 mb-3">
                        <label>Jurusan</label>
                        <select name="jurusan_id" class="form-control @error('jurusan_id') is-invalid @enderror">
                            <option value="">Pilih Jurusan</option>
                            @foreach ($jurusan as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="col-md-6 mb-3">
                        <label>Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-control @error('tahun_ajaran_id') is-invalid @enderror">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($tahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>{{ $ta->tahun_formatted }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- No. WhatsApp -->
                    <div class="col-md-6 mb-3">
                        <label>No. WhatsApp</label>
                        <input type="text" name="no_wa" class="form-control @error('no_wa') is-invalid @enderror" value="{{ old('no_wa') }}" placeholder="08xxxxxxxxxx">
                        @error('no_wa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div class="col-md-6 mb-3">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}">
                        @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                        @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Foto -->
                    <div class="col-md-6 mb-3">
                        <label>Foto</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
                        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>
                <h5>Akun Login</h5>
                <div class="row">
                    <!-- Username -->
                    <div class="col-md-6 mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
