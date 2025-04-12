@extends('templates')

@section('header', 'Tambah Kelas')

@section('content')
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Data Kelas</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('kelas.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nama_kelas" class="form-label">Nama Kelas</label>
                                <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" required>
                                @error('nama_kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="jurusan_id" class="form-label">Jurusan</label>
                                <select class="form-select @error('jurusan_id') is-invalid @enderror" id="jurusan_id" name="jurusan_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusan as $item)
                                        <option value="{{ $item->id }}" {{ old('jurusan_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('jurusan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tingkat" class="form-label">Tingkat</label>
                                <select class="form-select @error('tingkat') is-invalid @enderror" id="tingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>X</option>
                                    <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>XI</option>
                                    <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>XII</option>
                                </select>
                                @error('tingkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username Akun</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Akun</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
