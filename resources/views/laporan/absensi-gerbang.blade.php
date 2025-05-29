<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        h1 {
            font-size: 18px;
            margin: 5px 0;
        }
        h2 {
            font-size: 16px;
            margin: 5px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .statistics {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI GERBANG</h1>
        <h2>{{ $reportTitle }}</h2>
        @if($kelas)
            <p>Kelas: {{ $kelas->nama_kelas }}</p>
        @endif
        <p>Periode: {{ $statistics['startDate']->format('d-m-Y') }} s/d {{ $statistics['endDate']->format('d-m-Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Statistik Absensi:</strong></p>
        <ul>
            <li>Total Data Absensi: {{ $statistics['totalRecords'] }}</li>
            <li>Siswa: {{ $statistics['siswaCount'] }}</li>
            <li>Karyawan: {{ $statistics['karyawanCount'] }}</li>
            <li>Absensi Lengkap (Masuk & Keluar): {{ $statistics['completeAttendance'] }}</li>
            <li>Absensi Tidak Lengkap (Hanya Masuk): {{ $statistics['incompleteAttendance'] }}</li>
        </ul>
    </div>

    @foreach($groupedData as $date => $records)
        <h3>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Discan Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($record->siswa)
                                {{ $record->siswa->nama_lengkap }} (Siswa)
                            @elseif($record->karyawan)
                                {{ $record->karyawan->nama_lengkap }} ({{ $record->karyawan->jabatan }})
                            @else
                                Data tidak tersedia
                            @endif
                        </td>
                        <td>{{ $record->status }}</td>
                        <td>{{ $record->waktu_scan_masuk }}</td>
                        <td>{{ $record->waktu_scan_keluar ?? '-' }}</td>
                        <td>{{ $record->scannedBy->name ?? 'System' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="statistics">
        <h3>Rekapitulasi Absensi</h3>
        <p>Total Data: {{ $statistics['totalRecords'] }}</p>
        <p>Siswa: {{ $statistics['siswaCount'] }} ({{ $statistics['totalRecords'] > 0 ? round(($statistics['siswaCount'] / $statistics['totalRecords']) * 100, 2) : 0 }}%)</p>
        <p>Karyawan: {{ $statistics['karyawanCount'] }} ({{ $statistics['totalRecords'] > 0 ? round(($statistics['karyawanCount'] / $statistics['totalRecords']) * 100, 2) : 0 }}%)</p>
        <p>Lengkap: {{ $statistics['completeAttendance'] }} ({{ $statistics['totalRecords'] > 0 ? round(($statistics['completeAttendance'] / $statistics['totalRecords']) * 100, 2) : 0 }}%)</p>
        <p>Tidak Lengkap: {{ $statistics['incompleteAttendance'] }} ({{ $statistics['totalRecords'] > 0 ? round(($statistics['incompleteAttendance'] / $statistics['totalRecords']) * 100, 2) : 0 }}%)</p>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'System' }}</p>
    </div>
</body>
</html>
