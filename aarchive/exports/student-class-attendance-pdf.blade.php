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
        .data-siswa {
            width: 100%;
            margin-bottom: 20px;
        }
        .data-siswa td {
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
    <table class="data-siswa" border="0">
        <tr>
            <td width="20%">Nama Siswa</td>
            <td width="2%">:</td>
            <td>{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $siswa->kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Jurusan</td>
            <td>:</td>
            <td>{{ $siswa->jurusan->nama_jurusan }}</td>
        </tr>
        <tr>
            <td>Tahun Ajaran</td>
            <td>:</td>
            <td>{{ $siswa->tahunAjaran->nama_tahun_ajaran }}</td>
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
    </table>
</div>

<table class="tabel-absensi">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Mata Pelajaran</th>
            <th>Guru</th>
            <th>Jam</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if($absensi->count() > 0)
            @php $no = 1; @endphp
            @foreach($absensi as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Tidak tersedia' }}</td>
                    <td>{{ $item->jadwal->jadwalPelajaran->guru->nama_lengkap ?? 'Tidak tersedia' }}</td>
                    <td>
                        @if($item->jadwal)
                            {{ substr($item->jadwal->jam_mulai, 0, 5) }} - {{ substr($item->jadwal->jam_selesai, 0, 5) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($item->status == 'Hadir')
                            <span class="badge bg-success">Hadir</span>
                        @elseif($item->status == 'Izin')
                            <span class="badge bg-warning">Izin</span>
                        @elseif($item->status == 'Sakit')
                            <span class="badge bg-info">Sakit</span>
                        @elseif($item->status == 'Alpa')
                            <span class="badge bg-danger">Alpa</span>
                        @else
                            <span class="badge bg-secondary">{{ $item->status }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data absensi</td>
            </tr>
        @endif
    </tbody>
</table>

<!-- Ringkasan -->
<table class="tabel-absensi">
    <thead>
        <tr>
            <th colspan="2">Ringkasan Absensi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Mata Pelajaran</td>
            <td>{{ $absensi->count() }}</td>
        </tr>
        <tr>
            <td>Hadir</td>
            <td>{{ $absensi->where('status', 'Hadir')->count() }}</td>
        </tr>
        <tr>
            <td>Izin</td>
            <td>{{ $absensi->where('status', 'Izin')->count() }}</td>
        </tr>
        <tr>
            <td>Sakit</td>
            <td>{{ $absensi->where('status', 'Sakit')->count() }}</td>
        </tr>
        <tr>
            <td>Alpa</td>
            <td>{{ $absensi->where('status', 'Alpa')->count() }}</td>
        </tr>
        <tr>
            <td>Persentase Kehadiran</td>
            <td>
                @php
                    $total = $absensi->count();
                    $hadir = $absensi->where('status', 'Hadir')->count();
                    $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;
                @endphp
                {{ $persentase }}%
            </td>
        </tr>
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
            Wali Kelas<br><br><br><br>
            <u><strong>{{ $siswa->kelas->waliKelas->nama_lengkap ?? 'Nama Wali Kelas' }}</strong></u><br>
            NIP. -
        </td>
    </tr>
</table>

</body>
</html>
