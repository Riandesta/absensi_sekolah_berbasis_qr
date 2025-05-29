<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Absensi Gerbang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            padding: 0;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .stats-container {
            margin-bottom: 20px;
        }
        .stats-box {
            width: 100%;
            margin-bottom: 15px;
        }
        .stats-box th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }
        .text-center {
            text-align: center;
        }
        .filter-info {
            margin-bottom: 15px;
            font-style: italic;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Absensi Gerbang</h1>
        @if($kelas)
            <p>Kelas: {{ $kelas->nama_kelas }}</p>
        @else
            <p>Semua Kelas</p>
        @endif

        <p>Periode:
            @if($period == 'daily')
                Harian ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }})
            @elseif($period == 'weekly')
                Mingguan ({{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }})
            @elseif($period == 'monthly')
                Bulanan ({{ \Carbon\Carbon::parse($startDate)->format('F Y') }})
            @elseif($period == 'semester')
                @php
                    $semester = \Carbon\Carbon::parse($startDate)->month <= 6 ? 1 : 2;
                    $tahun = \Carbon\Carbon::parse($startDate)->format('Y');
                @endphp
                Semester {{ $semester }} ({{ $tahun }})
            @elseif($period == 'yearly')
                Tahunan ({{ \Carbon\Carbon::parse($startDate)->format('Y') }})
            @elseif($period == 'custom')
                {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}
            @else
                Semua Periode
            @endif
        </p>

        @if(isset($userType) && $userType)
            <p>Tipe Pengguna: {{ ucfirst($userType) }}</p>
        @else
            <p>Tipe Pengguna: Semua</p>
        @endif
    </div>

    <div class="stats-container">
        <h3>Ringkasan</h3>
        <table class="stats-box">
            <tr>
                <th>Total Absensi</th>
                <th>Absensi Lengkap</th>
                <th>Belum Absen Keluar</th>
                <th>Total Siswa</th>
                <th>Total Karyawan</th>
            </tr>
            <tr>
                <td class="text-center">{{ $totalAbsensi }}</td>
                <td class="text-center">{{ $absensiLengkap }}</td>
                <td class="text-center">{{ $belumAbsenKeluar }}</td>
                <td class="text-center">{{ $totalSiswaAbsensi }}</td>
                <td class="text-center">{{ $totalKaryawanAbsensi }}</td>
            </tr>
        </table>
    </div>

    <h3>Data Absensi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Kelas/Jabatan</th>
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
                        <td>
                            @if($item->siswa)
                                {{ $item->siswa->nama_lengkap }}
                            @elseif($item->karyawan)
                                {{ $item->karyawan->nama_lengkap }}
                            @else
                                Tidak tersedia
                            @endif
                        </td>
                        <td>
                            @if($item->siswa)
                                Siswa
                            @elseif($item->karyawan)
                                Karyawan
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->siswa && $item->siswa->kelas)
                                {{ $item->siswa->kelas->nama_kelas }}
                            @elseif($item->karyawan)
                                {{ $item->karyawan->jabatan }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->waktu_scan_masuk ?? '-' }}</td>
                        <td>{{ $item->waktu_scan_keluar ?? '-' }}</td>
                        <td>
                            @if($item->waktu_scan_masuk && $item->waktu_scan_keluar)
                                Lengkap
                            @elseif($item->waktu_scan_masuk)
                                Belum Absen Keluar
                            @else
                                Belum Absen
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data absensi</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dihasilkan pada {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
