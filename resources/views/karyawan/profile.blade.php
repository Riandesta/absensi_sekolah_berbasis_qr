@extends('templates')
@section('header', 'Profil Saya')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Profil Saya</h4>
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
                    <div class="col-md-4 mb-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center">
                                <div class="avatar avatar-xl mb-3 mx-auto">
                                    @if($karyawan->foto)
                                        <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_lengkap }}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="avatar bg-primary">
                                            <span class="avatar-content">{{ substr($karyawan->nama_lengkap, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="mb-1">{{ $karyawan->nama_lengkap }}</h4>
                                <p class="mb-0">{{ $karyawan->nip }}</p>
                                <div class="badge bg-primary mt-2">{{ ucfirst($karyawan->jabatan) }}</div>

                                <div class="mt-4">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('karyawan.download-qrcode', $karyawan->id) }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-qr-code me-2"></i> Download QR Code
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Panel -->
                        <div class="card border shadow-sm mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informasi Akun</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Username</span>
                                        <span class="text-muted">{{ $user->username }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Peran</span>
                                        <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Status</span>
                                        <span class="badge bg-success">{{ ucfirst($user->status) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Edit Profil</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" required>
                                            @error('nama_lengkap')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}">
                                            @error('tempat_lahir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir) }}">
                                            @error('tanggal_lahir')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                                            <input type="text" name="no_wa" id="no_wa" class="form-control @error('no_wa') is-invalid @enderror" value="{{ old('no_wa', $karyawan->no_wa) }}">
                                            @error('no_wa')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $karyawan->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="foto" class="form-label">Foto Profil</label>
                                        <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                                        @error('foto')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="form-text">Format file yang diizinkan: JPG, JPEG, PNG, GIF. Maksimal 2MB.</div>
                                    </div>

                                    <hr>

                                    <h5 class="mb-3">Informasi Akun</h5>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                        @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password Baru</label>
                                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
