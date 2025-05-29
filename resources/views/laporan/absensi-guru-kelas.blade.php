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
        <h1>LAPORAN ABSENSI GURU</h1>
        <h2>{{ $reportTitle }}</h2>
        @if($kelas)
            <p>Kelas: {{ $kelas->nama_kelas }}</p>
        @endif
        <p>Periode: {{ $statistics['startDate']->format('d-m-Y') }} s/d {{ $statistics['endDate']->format('d-m-Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Statistik Absensi:</strong></p>
        <ul>
            <li>Total Guru: {{ $statistics['totalTeachers'] }}</li>
            <li>Guru yang Hadir: {{ $statistics['teachersPresent'] }}</li>
            <li>Total Catatan Absensi: {{ $statistics['totalAbsenceRecords'] }}</li>
            <li>Total Jadwal Kelas: {{ $statistics['totalClasses'] }}</li>
            <li>Persentase Kehadiran: {{ $statistics['percentageAttendance'] }}%</li>
        </ul>
    </div>

    <!-- Rekapitulasi per Guru -->
    <h3>Rekapitulasi Absensi per Guru</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th>NIP</th>
                <th>Total Absensi</th>
                <th>Persentase Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allTeachers as $index => $guru)
                @php
                    $totalHadir = isset($groupedByTeacher[$guru->id]) ? $groupedByTeacher[$guru->id]->count() : 0;
                    $totalJadwal = $guru->jadwalPelajaran->flatMap(function($jp) {
                        return $jp->jadwal;
                    })->count();

                    $persentase = $totalJadwal > 0 ? round(($totalHadir / $totalJadwal) * 100, 2) : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $guru->nama_lengkap }}</td>
                    <td>{{ $guru->nip }}</td>
                    <td>{{ $totalHadir }} / {{ $totalJadwal }}</td>
                    <td>{{ $persentase }}%</td>
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
                    <th>Nama Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Waktu Scan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->karyawan->nama_lengkap ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->kelas->nama_kelas ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->waktu_scan }}</td>
                        <td>{{ $record->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="statistics">
        <h3>Ringkasan Absensi Guru</h3>
        <p>Total Guru: {{ $statistics['totalTeachers'] }}</p>
        <p>Guru yang Hadir: {{ $statistics['teachersPresent'] }} ({{ $statistics['totalTeachers'] > 0 ? round(($statistics['teachersPresent'] / $statistics['totalTeachers']) * 100, 2) : 0 }}%)</p>
        <p>Total Catatan Absensi: {{ $statistics['totalAbsenceRecords'] }}</p>
        <p>Persentase Kehadiran: {{ $statistics['percentageAttendance'] }}%</p>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Dicetak oleh: {{ Auth::user()->name ?? 'System' }}</p>
    </div>
</body>
</html>
