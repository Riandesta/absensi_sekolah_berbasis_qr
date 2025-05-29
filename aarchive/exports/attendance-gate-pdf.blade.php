<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Gerbang</title>
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
        .data-guru {
            width: 100%;
            margin-bottom: 20px;
        }
        .data-guru td {
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

<h2 style="text-align: center; margin: 20px 0;">LAPORAN ABSENSI GERBANG GURU</h2>

<div class="periode">
    <table class="data-guru" border="0">
        <tr>
            <td width="20%">Nama Guru</td>
            <td width="2%">:</td>
            <td>{{ $karyawan->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:</td>
            <td>{{ $karyawan->nip }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $karyawan->jabatan }}</td>
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
            <th>Waktu Masuk</th>
            <th>Waktu Keluar</th>
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
                    <td>{{ $item->waktu_scan_masuk ?? '-' }}</td>
                    <td>{{ $item->waktu_scan_keluar ?? '-' }}</td>
                    <td>
                        @if($item->waktu_scan_masuk && $item->waktu_scan_keluar)
                            <span class="badge bg-success">Lengkap</span>
                        @elseif($item->waktu_scan_masuk)
                            <span class="badge bg-warning">Belum Absen Keluar</span>
                        @else
                            <span class="badge bg-danger">Belum Absen</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada data absensi</td>
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
            <td>Total Hari</td>
            <td>{{ $absensi->count() }}</td>
        </tr>
        <tr>
            <td>Absensi Lengkap</td>
            <td>{{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', '!=', null)->count() }}</td>
        </tr>
        <tr>
            <td>Belum Absen Keluar</td>
            <td>{{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', null)->count() }}</td>
        </tr>
        <tr>
            <td>Belum Absen</td>
            <td>{{ $absensi->where('waktu_scan_masuk', null)->count() }}</td>
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
            Guru yang Bersangkutan<br><br><br><br>
            <u><strong>{{ $karyawan->nama_lengkap }}</strong></u><br>
            NIP. {{ $karyawan->nip }}
        </td>
    </tr>
</table>

</body>
</html>
