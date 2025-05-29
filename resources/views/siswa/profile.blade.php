@extends('templates')
@section('header', 'Profil Saya')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Profil</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center mb-3">
                                            @if($siswa->foto)
                                                <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Profile Image" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                            @else
                                                <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="Default Profile" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="foto">Ganti Foto Profil</label>
                                            <input type="file" class="form-control" id="foto" name="foto">
                                            <small class="text-muted">Format: JPG, PNG, GIF, SVG. Maks 2MB</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Data Akademik</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>NIS</label>
                                            <p class="form-control-static">{{ $siswa->nis }}</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Kelas</label>
                                            <p class="form-control-static">{{ $siswa->kelas->nama_kelas ?? 'Tidak ada kelas' }}</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Jurusan</label>
                                            <p class="form-control-static">{{ $siswa->jurusan->nama_jurusan ?? 'Tidak ada jurusan' }}</p>
                                        </div>

                                        <div class="form-group">
                                            <label>Tahun Ajaran</label>
                                            <p class="form-control-static">{{ $siswa->tahunAjaran->tahun_ajaran ?? 'Tidak ada tahun ajaran' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Informasi Pribadi</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                                        <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tempat_lahir">Tempat Lahir</label>
                                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $siswa->alamat) }}</textarea>
                                        </div> --}}

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_wa">Nomor Telepon</label>
                                                    <input type="text" class="form-control" id="no_wa" name="no_wa" value="{{ old('no_wa', $siswa->no_wa) }}">
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $siswa->email) }}">
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Informasi Akun</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="username">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                            <small class="text-muted">Minimum 6 karakter</small>
                                        </div>

                                        <div class="form-group">
                                            <label for="password_confirmation">Konfirmasi Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
