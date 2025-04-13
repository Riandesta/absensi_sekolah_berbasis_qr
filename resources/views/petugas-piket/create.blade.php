@extends('templates')
@section('header', 'Tambah Jadwal Petugas Piket')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0">Form Tambah Jadwal Petugas Piket</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas-piket.store') }}" method="POST">
                @csrf

                <!-- Karyawan -->
                <div class="mb-3">
                    <label for="karyawan_id" class="form-label">Karyawan</label>
                    <select name="karyawan_id" id="karyawan_id" class="form-select" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach ($karyawanList as $karyawan)
                            <option value="{{ $karyawan->id }}">{{ $karyawan->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    @error('karyawan_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Tanggal -->
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal') }}" required>
                    @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Shift -->
                <div class="mb-3">
                    <label for="shift" class="form-label">Shift</label>
                    <select name="shift" id="shift" class="form-select" required>
                        <option value="">Pilih Shift</option>
                        <option value="Pagi" {{ old('shift') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                        <option value="Siang" {{ old('shift') == 'Siang' ? 'selected' : '' }}>Siang</option>
                        <option value="Sore" {{ old('shift') == 'Sore' ? 'selected' : '' }}>Sore</option>
                    </select>
                    @error('shift') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- Keterangan -->
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('petugas-piket.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
