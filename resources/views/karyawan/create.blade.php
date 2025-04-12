@extends('templates')
@section('header', 'Tambah Karyawan')

@section('content')
<div class="container">
    <form action="{{ route('karyawan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-control" required>
                <option value="">Pilih</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Jurusan</label>
            <select name="jurusan_id" class="form-control">
                @foreach ($jurusan as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-control">
                @foreach ($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->tahun_formatted }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>No. WhatsApp</label>
            <input type="text" name="no_wa" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control">
        </div>

        <div class="mb-3">
            <label>Username (Akun)</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password (Akun)</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
