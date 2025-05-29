<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Kelas Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop-surat h1 {
            margin: 0;
            font-size: 22px;
        }
        .kop-surat p {
            margin: 2px 0;
            font-size: 14px;
        }
        .periode {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .data-kelas {
            width: 100%;
            margin-bottom: 20px;
        }
        .data-kelas td {
            padding: 4px;
            font-size: 14px;
            vertical-align: top;
        }
        .tabel-absensi {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .tabel-absensi, .tabel-absensi th, .tabel-absensi td {
            border: 1px solid black;
        }
        .tabel-absensi th {
            background-color: #f0f0f0;
            padding: 8px 4px;
            font-size: 13px;
        }
        .tabel-absensi td {
            padding: 6px 4px;
            text-align: center;
            font-size: 13px;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        .bg-success {
            background-color: #4CAF50;
        }
        .bg-warning {
            background-color: #FFC107;
        }
        .bg-info {
            background-color: #2196F3;
        }
        .bg-danger {
            background-color: #F44336;
        }
        .ttd {
            width: 100%;
            margin-top: 40px;
        }
        .ttd td {
            text-align: center;
            padding-top: 60px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<table style="width: 100%;">
    <tr>
        <td style="width: 100px;">
            <img src="{{ asset('assets/images/logo/igasar.png') }}" alt="Logo Sekolah" style="width: 80px;">
        </td>
        <td style="text-align: left;">
            <h1 style="margin: 0; font-size: 20px;">SMK IGASAR PINDAD BANDUNG</h1>
            <p style="margin: 2px 0;">Jl. Cisaranten Kulon No.17, Cisaranten Kulon, Kec. Arcamanik, Kota Bandung, Jawa Barat 40293</p>
            <p style="margin: 0;">Telp. (021) 12345678990 | Email: info@smkigasarpindad.sch.id</p>
        </td>
    </tr>
</table>

<hr style="border: 1px solid #000; margin: 10px 0;">

<h2 style="text-align: center; margin: 20px 0;">LAPORAN ABSENSI KELAS SISWA</h2>

<div class="periode">
    <table class="data-kelas" border="0">
        <tr>
            <td width="20%">Kelas</td>
            <td width="2%">:</td>
            <td>{{ $selectedKelas ? $selectedKelas->nama_kelas : 'Semua Kelas' }}</td>
        </tr>
        @if($selectedJurusan)
        <tr>
            <td>Jurusan</td>
            <td>:</td>
            <td>{{ $selectedJurusan->nama_jurusan }}</td>
        </tr>
        @endif
        <tr>
            <td>Wali Kelas</td>
            <td>:</td>
            <td>{{ $selectedKelas && $selectedKelas->waliKelas ? $selectedKelas->waliKelas->nama_lengkap : ($karyawan->jabatan == 'Wali Kelas' ? $karyawan->nama_lengkap : '-') }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>:</td>
            <td>
                @if($period == 'daily')
                    Harian ({{ $startDate ? $startDate->format('d-m-Y') : 'Hari Ini' }})
                @elseif($period == 'weekly')
                    Mingguan ({{ $startDate ? $startDate->format('d-m-Y') : '' }} s/d {{ $endDate ? $endDate->format('d-m-Y') : '' }})
                @elseif($period == 'monthly')
                    Bulanan ({{ $startDate ? $startDate->format('F Y') : date('F Y') }})
                @elseif($period == 'semester')
                    Semester ({{ $startDate ? $startDate->format('d-m-Y') : '' }} s/d {{ $endDate ? $endDate->format('d-m-Y') : '' }})
                @elseif($period == 'yearly')
                    Tahunan ({{ $startDate ? $startDate->format('Y') : date('Y') }})
                @elseif($period == 'custom')
                    Kustom ({{ $startDate ? $startDate->format('d-m-Y') : '' }} s/d {{ $endDate ? $endDate->format('d-m-Y') : '' }})
                @else
                    Semua Riwayat
                @endif
            </td>
        </tr>
        <tr>
            <td>Dibuat Oleh</td>
            <td>:</td>
            <td>{{ $karyawan->nama_lengkap }} ({{ $karyawan->jabatan }})</td>
        </tr>
    </table>
</div>

<!-- Ringkasan Statistik -->
<table class="tabel-absensi">
    <thead>
        <tr>
            <th colspan="2">Ringkasan Statistik</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Jumlah Siswa</td>
            <td>{{ $stats['totalStudents'] }}</td>
        </tr>
        <tr>
            <td>Total Catatan Absensi</td>
            <td>{{ $stats['totalRecords'] }}</td>
        </tr>
        <tr>
            <td>Hadir</td>
            <td>{{ $stats['presentCount'] }}</td>
        </tr>
        <tr>
            <td>Izin</td>
            <td>{{ $stats['permitCount'] }}</td>
        </tr>
        <tr>
            <td>Sakit</td>
            <td>{{ $stats['sickCount'] }}</td>
        </tr>
        <tr>
            <td>Alpa</td>
            <td>{{ $stats['absentCount'] }}</td>
        </tr>
        <tr>
            <td>Persentase Kehadiran</td>
            <td>{{ $stats['attendanceRate'] }}%</td>
        </tr>
    </tbody>
</table>

<!-- Tabel Rekap per Siswa -->
<table class="tabel-absensi">
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Total</th>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alpa</th>
            <th>% Kehadiran</th>
        </tr>
    </thead>
    <tbody>
        @if($groupedBySiswa->count() > 0)
            @php $no = 1; @endphp
            @foreach($groupedBySiswa as $siswaId => $absensiSiswa)
                @php
                    $siswa = $absensiSiswa->first()->siswa ?? null;
                    if(!$siswa) continue;

                    $total = $absensiSiswa->count();
                    $hadir = $absensiSiswa->where('status', 'Hadir')->count();
                    $izin = $absensiSiswa->where('status', 'Izin')->count();
                    $sakit = $absensiSiswa->where('status', 'Sakit')->count();
                    $alpa = $absensiSiswa->where('status', 'Alpa')->count();

                    $persentaseKehadiran = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $total }}</td>
                    <td>{{ $hadir }}</td>
                    <td>{{ $izin }}</td>
                    <td>{{ $sakit }}</td>
                    <td>{{ $alpa }}</td>
                    <td>{{ $persentaseKehadiran }}%</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" style="text-align: center;">Tidak ada data absensi</td>
            </tr>
        @endif
    </tbody>
</table>

<table class="ttd" border="0">
    <tr>
        <td width="50%">
            Mengetahui,<br>
            Kepala Sekolah<br><br><br><br>
            <u><strong>Rony Harimurti, S.Pd.,MM</strong></u><br>
            NIP. -
        </td>
        <td width="50%">
            Bandung, {{ now()->format('d F Y') }}<br>
            @if($karyawan->jabatan == 'Wali Kelas')
                Wali Kelas<br><br><br><br>
                <u><strong>{{ $karyawan->nama_lengkap }}</strong></u><br>
                NIP. {{ $karyawan->nip }}
            @elseif($karyawan->jabatan == 'Kurikulum')
                Kurikulum<br><br><br><br>
                <u><strong>{{ $karyawan->nama_lengkap }}</strong></u><br>
                NIP. {{ $karyawan->nip }}
            @else
                {{ $karyawan->jabatan }}<br><br><br><br>
                <u><strong>{{ $karyawan->nama_lengkap }}</strong></u><br>
                NIP. {{ $karyawan->nip }}
            @endif
        </td>
    </tr>
</table>

</body>
</html>
