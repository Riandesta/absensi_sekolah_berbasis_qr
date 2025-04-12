@php
    $currentYear = now()->year;
@endphp

<div class="mb-3">
    <label for="tahun_awal" class="form-label">Tahun Ajaran</label>
    <div class="d-flex">
        <select class="form-select" id="tahun_awal" name="tahun_awal" required>
            @for ($i = 0; $i < 3; $i++)
                @php $tahunAwal = $currentYear + $i; @endphp
                <option value="{{ $tahunAwal }}"
                    {{ old('tahun_awal', $item->tahun_awal ?? '') == $tahunAwal ? 'selected' : '' }}>
                    {{ $tahunAwal }}
                </option>
            @endfor
        </select>
        <span class="mx-2">/</span>
        <select class="form-select" id="tahun_akhir" name="tahun_akhir" required>
            @for ($i = 0; $i < 3; $i++)
                @php $tahunAkhir = $currentYear + $i + 1; @endphp
                <option value="{{ $tahunAkhir }}"
                    {{ old('tahun_akhir', $item->tahun_akhir ?? '') == $tahunAkhir ? 'selected' : '' }}>
                    {{ $tahunAkhir }}
                </option>
            @endfor
        </select>
    </div>
    @error('tahun_awal')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @error('tahun_akhir')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="semester" class="form-label">Semester</label>
    <select class="form-select" id="semester" name="semester" required>
        <option value="Ganjil" {{ old('semester', $item->semester ?? '') === 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
        <option value="Genap" {{ old('semester', $item->semester ?? '') === 'Genap' ? 'selected' : '' }}>Genap</option>
    </select>
    @error('semester')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="is_aktif" name="is_aktif"
    value="1" {{ old('is_aktif', $item->is_aktif ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_aktif">Status Aktif</label>

    @error('is_aktif')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
