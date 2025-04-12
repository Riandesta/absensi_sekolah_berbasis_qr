@extends('templates')

@section('header', isset($jadwalPelajaran) ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($jadwalPelajaran) ? 'Edit' : 'Tambah' }} Jadwal Pelajaran</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            action="{{ isset($jadwalPelajaran) ? route('jadwal-pelajaran.update', $jadwalPelajaran) : route('jadwal-pelajaran.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($jadwalPelajaran))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guru_id" class="form-label">Guru</label>
                                    <select name="guru_id" id="guru_id" class="form-select" required>
                                        @foreach ($guruList as $guru)
                                            <option value="{{ $guru->id }}" @selected(old('guru_id', $jadwalPelajaran->guru_id ?? '') == $guru->id)>
                                                {{ $guru->nama_lengkap }}
                                            </option>
                                        @endforeach
                                        <option value="Guru">Guru</option>
                                    </select>
                                    @error('guru_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-select" required>
                                        @if ($mataPelajaranList->isEmpty())
                                            <option value="" disabled selected>Tidak ada data mata pelajaran</option>
                                        @else
                                            @foreach ($mataPelajaranList as $mapel)
                                                <option value="{{ $mapel->id }}" @selected(old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id ?? '') == $mapel->id)>
                                                    {{ $mapel->nama_mapel }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('mata_pelajaran_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-select" required>
                                    @if ($tahunAjaranList->isEmpty())
                                        <option value="" disabled selected>Tidak ada data tahun ajaran</option>
                                    @else
                                        @foreach ($tahunAjaranList as $th)
                                            <option value="{{ $th->id }}" @selected(old('tahun_ajaran_id', $jadwalPelajaran->tahun_ajaran_id ?? '') == $th->id)>
                                                {{ $th->tahun_formatted }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tahun_ajaran_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <h6 class="mb-3">Slot Jadwal Pelajaran</h6>

                            <div id="slots-container" class="d-flex flex-column gap-3">
                                @php
                                    $slots = old(
                                        'slots',
                                        isset($jadwalPelajaran)
                                            ? $jadwalPelajaran->jadwal->toArray()
                                            : [
                                                [
                                                    'kelas_id' => '',
                                                    'hari' => '',
                                                    'jam_mulai' => '',
                                                    'jam_selesai' => '',
                                                ],
                                            ],
                                    );
                                @endphp

                                @foreach ($slots as $i => $slot)
                                    <div class="slot-row row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Kelas</label>
                                            <select name="slots[{{ $i }}][kelas_id]" class="form-select"
                                                required>
                                                @foreach ($kelasList as $kelas)
                                                    <option value="{{ $kelas->id }}" @selected($slot['kelas_id'] == $kelas->id)>
                                                        {{ $kelas->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Hari</label>
                                            <select name="slots[{{ $i }}][hari]" class="form-select" required>
                                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                                                    <option value="{{ $h }}" @selected($slot['hari'] == $h)>
                                                        {{ $h }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="time" name="slots[{{ $i }}][jam_mulai]"
                                                class="form-control"
                                                value="{{ \Carbon\Carbon::parse($slot['jam_mulai'])->format('H:i') }}"
                                                required>
                                        </div>
                                        <div class="col-md-2">

                                            <input type="time" name="slots[{{ $i }}][jam_selesai]"
                                                class="form-control"
                                                value="{{ \Carbon\Carbon::parse($slot['jam_selesai'])->format('H:i') }}"
                                                required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-slot mt-4">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="my-3">
                                <button type="button" id="add-slot" class="btn btn-outline-secondary">
                                    <i class="bi bi-plus-circle"></i> Tambah Slot
                                </button>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('jadwal-pelajaran.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const container = document.getElementById('slots-container');
                                document.getElementById('add-slot').addEventListener('click', () => {
                                    const index = container.querySelectorAll('.slot-row').length;
                                    const div = document.createElement('div');
                                    div.className = 'slot-row row g-3 align-items-end';
                                    div.innerHTML = `
                                    <div class="col-md-3">
                                        <label class="form-label">Kelas</label>
                                        <select name="slots[${index}][kelas_id]" class="form-select" required>
                                            @foreach ($kelasList as $kelas)
                                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Hari</label>
                                        <select name="slots[${index}][hari]" class="form-select" required>
                                            <option>Senin</option><option>Selasa</option><option>Rabu</option>
                                            <option>Kamis</option><option>Jumat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jam Mulai</label>
                                        <input type="time" name="slots[${index}][jam_mulai]" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Jam Selesai</label>
                                        <input type="time" name="slots[${index}][jam_selesai]" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-slot mt-4">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                `;
                                    container.appendChild(div);
                                });

                                container.addEventListener('click', e => {
                                    if (e.target.closest('.remove-slot')) {
                                        e.target.closest('.slot-row').remove();
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
