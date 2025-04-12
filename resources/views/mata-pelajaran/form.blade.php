<div class="mb-3">
    <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
    <input type="text" class="form-control" id="nama_mapel" name="nama_mapel"
        value="{{ old('nama_mapel', $item->nama_mapel ?? '') }}" required>
    @error('nama_mapel')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="kode_mapel" class="form-label">Kode Mata Pelajaran</label>
    <input type="text" class="form-control" id="kode_mapel" name="kode_mapel"
        value="{{ old('kode_mapel', $item->kode_mapel ?? '') }}" required>
    @error('kode_mapel')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
