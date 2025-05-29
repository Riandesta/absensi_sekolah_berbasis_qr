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
        .hadir { color: green; }
        .izin { color: blue; }
        .sakit { color: orange; }
        .alpa { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI SISWA</h1>
        <h2>{{ $reportTitle }}</h2>
        @if($kelas)
            <p>Kelas: {{ $kelas->nama_kelas }}</p>
        @endif
        <p>Periode: {{ $statistics['startDate']->format('d-m-Y') }} s/d {{ $statistics['endDate']->format('d-m-Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Statistik Absensi:</strong></p>
        <ul>
            <li>Total Siswa: {{ $statistics['totalStudents'] }}</li>
            <li>Total Catatan Absensi: {{ $statistics['totalAttendance'] }}</li>
            <li>Hadir: {{ $statistics['totalHadir'] }} ({{ $statistics['persenHadir'] }}%)</li>
            <li>Izin: {{ $statistics['totalIzin'] }} ({{ $statistics['persenIzin'] }}%)</li>
            <li>Sakit: {{ $statistics['totalSakit'] }} ({{ $statistics['persenSakit'] }}%)</li>
            <li>Alpa: {{ $statistics['totalAlpa'] }} ({{ $statistics['persenAlpa'] }}%)</li>
        </ul>
    </div>

    <!-- Rekapitulasi per Siswa -->
    <h3>Rekapitulasi Absensi per Siswa</h3>
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Siswa</th>
                <th rowspan="2">NIS</th>
                <th colspan="4">Status Absensi</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allStudents as $index => $siswa)
                @php
                    $siswaAbsensi = $groupedByStudent[$siswa->id] ?? collect();
                    $hadir = $siswaAbsensi->where('status', 'Hadir')->count();
                    $izin = $siswaAbsensi->where('status', 'Izin')->count();
                    $sakit = $siswaAbsensi->where('status', 'Sakit')->count();
                    $alpa = $siswaAbsensi->where('status', 'Alpa')->count();
                    $total = $hadir + $izin + $sakit + $alpa;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td class="hadir">{{ $hadir }}</td>
                    <td class="izin">{{ $izin }}</td>
                    <td class="sakit">{{ $sakit }}</td>
                    <td class="alpa">{{ $alpa }}</td>
                    <td>{{ $total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Detail Absensi per Tanggal -->
    @foreach($groupedByDate as $date => $records)
        <h3>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Status</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->siswa->nama_lengkap ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->jadwal->kelas->nama_kelas ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}</td>
                        <td class="{{ strtolower($record->status) }}">{{ $record->status }}</td>
                        <td>{{ $record->inputBy->name ?? 'System' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="statistics">
        <h3>Ringkasan Absensi Siswa</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="hadir">Hadir</td>
                    <td>{{ $statistics['totalHadir'] }}</td>
                    <td>{{ $statistics['persenHadir'] }}%</td>
                </tr>
                <tr>
                    <td class="izin">Izin</td>
                    <td>{{ $statistics['totalIzin'] }}</td>
                    <td>{{ $statistics['persenIzin'] }}%</td>
                </tr>
                <tr>
                    <td class="sakit">Sakit</td>
                    <td>{{ $statistics['totalSakit'] }}</td>
                    <td>{{ $statistics['persenSakit'] }}%</td>
                </tr>
                <tr>
                    <td class="alpa">Alpa</td>
                    <td>{{ $statistics['totalAlpa'] }}</td>
                    <td>{{ $statistics['persenAlpa'] }}%</td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th>{{ $statistics['totalAttendance'] }}</th>
                    <th>100%</th>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'System' }}</p>
    </div>
</body>
</html>
