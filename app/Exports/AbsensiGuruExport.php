<?php

namespace App\Exports;

use App\Models\AbsensiGuru;
use App\Models\AbsensiGuruKelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Log;

class AbsensiGuruExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $tanggal;
    protected $kelas_id;
    protected $report_type;

    public function __construct($tanggal = null, $kelas_id = null, $report_type = null)
    {
        $this->tanggal = $tanggal;
        $this->kelas_id = $kelas_id;
        $this->report_type = $report_type;
    }

    public function collection()
    {
        $query = AbsensiGuruKelas::query();

        // Filter by date if provided
        if ($this->tanggal) {
            $query->whereDate('tanggal', $this->tanggal);
        }

        // Filter by class if provided
        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        // Filter by report type if provided
        if ($this->report_type) {
            switch ($this->report_type) {
                case 'daily':
                    $query->whereDate('tanggal', now()->startOfDay());
                    break;
                case 'weekly':
                    $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('tanggal', now()->month);
                    break;
                case 'semester':
                    $semester = now()->month <= 6 ? 1 : 2;
                    $query->whereBetween('tanggal', [
                        now()->startOfYear()->addMonths(($semester - 1) * 6),
                        now()->startOfYear()->addMonths($semester * 6)->subDay(),
                    ]);
                    break;
                case 'yearly':
                    $query->whereYear('tanggal', now()->year);
                    break;
            }
        }

        // Get all data with necessary relationships
        $data = $query->with([
            'karyawan',
            'kelas',
            'jadwal.jadwalPelajaran.mataPelajaran',
            'scanByUser'
        ])->get();

        // Debug log to check what data we're getting
        if ($data->isEmpty()) {
            Log::info('No data found for export with filters: ', [
                'tanggal' => $this->tanggal,
                'kelas_id' => $this->kelas_id,
                'report_type' => $this->report_type
            ]);
        }

        // Map the data for export
        return $data->map(function ($item, $index) {
            // Debug any problematic items
            if (!$item->karyawan || !$item->kelas || !$item->jadwal || !$item->jadwal->jadwalPelajaran || !$item->jadwal->jadwalPelajaran->mataPelajaran) {
                Log::warning('Missing relationship data for AbsensiGuruKelas ID: ' . $item->id);
            }

            return [
                'No' => $index + 1,
                'Nama Guru' => $item->karyawan ? $item->karyawan->nama : 'Data Tidak Ada',
                'Waktu Scan' => $item->waktu_scan,
                'Status' => $item->status,
                'Kelas' => $item->kelas ? $item->kelas->nama : 'Data Tidak Ada',
                'Mata Pelajaran' => $item->jadwal && $item->jadwal->jadwalPelajaran && $item->jadwal->jadwalPelajaran->mataPelajaran
                    ? $item->jadwal->jadwalPelajaran->mataPelajaran->nama
                    : 'Data Tidak Ada',
                'Jadwal' => $item->jadwal
                    ? $item->jadwal->hari . ', ' . $item->jadwal->jam_mulai . '-' . $item->jadwal->jam_selesai
                    : 'Data Tidak Ada',
                'Tanggal' => $item->tanggal,
                'Dicatat Oleh' => $item->scanByUser ? $item->scanByUser->name : 'Data Tidak Ada',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'Waktu Scan',
            'Status',
            'Kelas',
            'Mata Pelajaran',
            'Jadwal',
            'Tanggal',
            'Dicatat Oleh',
        ];
    }
}
