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
        h3 {
            font-size: 14px;
            margin: 15px 0 5px 0;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 11px;
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
        .badge {
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 10px;
            display: inline-block;
        }
        .bg-success {
            background-color: #28a745;
            color: white;
        }
        .bg-danger {
            background-color: #dc3545;
            color: white;
        }
        .bg-warning {
            background-color: #ffc107;
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
        <h3>Statistik Absensi:</h3>
        <table>
            <tr>
                <td width="50%"><strong>Total Guru:</strong> {{ $statistics['totalTeachers'] }}</td>
                <td width="50%"><strong>Guru yang Hadir:</strong> {{ $statistics['teachersPresent'] }}</td>
            </tr>
            <tr>
                <td><strong>Total Catatan Absensi:</strong> {{ $statistics['totalAbsenceRecords'] }}</td>
                <td><strong>Total Jadwal Kelas:</strong> {{ $statistics['totalClasses'] }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Persentase Kehadiran:</strong> {{ $statistics['percentageAttendance'] }}%</td>
            </tr>
        </table>
    </div>

    <!-- Rekapitulasi per Guru -->
    <h3>Rekapitulasi Absensi per Guru</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Guru</th>
                <th width="20%">NIP</th>
                <th width="20%">Total Absensi</th>
                <th width="20%">Persentase Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allTeachers as $index => $guru)
                @php
                    $totalHadir = isset($groupedByTeacher[$guru->id]) ? $groupedByTeacher[$guru->id]->count() : 0;
                    $totalJadwal = $guru->jadwalPelajaran->flatMap(function($jp) {
                        return $jp->jadwal;
                    });

                    // Apply class filter if specified
                    if (isset($kelas)) {
                        $totalJadwal = $totalJadwal->where('kelas_id', $kelas->id);
                    }

                    $totalJadwalCount = $totalJadwal->count();
                    $persentase = $totalJadwalCount > 0 ? round(($totalHadir / $totalJadwalCount) * 100, 2) : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $guru->nama_lengkap }}</td>
                    <td>{{ $guru->nip }}</td>
                    <td>{{ $totalHadir }} / {{ $totalJadwalCount }}</td>
                    <td>{{ $persentase }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Detail Absensi per Tanggal -->
    <h3>Detail Absensi per Tanggal</h3>
    @foreach($groupedByDate as $date => $records)
        <h4>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h4>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Guru</th>
                    <th width="25%">Mata Pelajaran</th>
                    <th width="20%">Kelas</th>
                    <th width="15%">Waktu Scan</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->karyawan->nama_lengkap ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Data tidak tersedia' }}</td>
                        <td>{{ $record->kelas->nama_kelas ?? 'Data tidak tersedia' }}</td>
                        <td>{{ substr($record->waktu_scan, 0, 5) }}</td>
                        <td>
                            <span class="badge bg-success">{{ $record->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last && $loop->iteration % 3 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Dicetak oleh: {{ Auth::user()->name ?? Auth::user()->username ?? 'System' }}</p>
    </div>
</body>
</html>
