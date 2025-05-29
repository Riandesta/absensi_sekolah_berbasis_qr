@extends('templates')
@section('header', 'Histórico de Presença')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Meu Histórico de Presença</h4>
                <div class="btn-group">
                    <a href="{{ route('karyawan.attendance.track', ['type' => 'gerbang', 'period' => $period]) }}" class="btn btn-sm {{ $type == 'gerbang' ? 'btn-primary' : 'btn-outline-primary' }}">Presença no Portão</a>
                    <a href="{{ route('karyawan.attendance.track', ['type' => 'kelas', 'period' => $period]) }}" class="btn btn-sm {{ $type == 'kelas' ? 'btn-primary' : 'btn-outline-primary' }}">Presença em Sala</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Filtro por Período</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('karyawan.attendance.track') }}" method="GET" class="row g-3">
                                    <input type="hidden" name="type" value="{{ $type }}">

                                    <div class="col-md-4">
                                        <select name="period" id="period" class="form-select" onchange="toggleCustomDates()">
                                            <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Todos</option>
                                            <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Diário</option>
                                            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                            <option value="semester" {{ $period == 'semester' ? 'selected' : '' }}>Semestral</option>
                                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Anual</option>
                                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Período Personalizado</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <input type="date" name="start_date" class="form-control" placeholder="Data Inicial" value="{{ $customStart }}">
                                    </div>

                                    <div class="col-md-4 custom-dates {{ $period == 'custom' ? '' : 'd-none' }}">
                                        <input type="date" name="end_date" class="form-control" placeholder="Data Final" value="{{ $customEnd }}">
                                    </div>

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Exportar PDF</h5>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('karyawan.attendance.export-pdf', ['type' => $type, 'period' => $period, 'start_date' => $customStart, 'end_date' => $customEnd]) }}" class="btn btn-danger w-100">
                                    <i class="bi bi-file-pdf-fill me-2"></i> Exportar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    @if($type == 'gerbang')
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Data</th>
                                    <th>Horário de Entrada</th>
                                    <th>Horário de Saída</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($absensi->count() > 0)
                                    @php $no = $absensi->firstItem(); @endphp
                                    @foreach($absensi as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $item->waktu_scan_masuk ?? '-' }}</td>
                                            <td>{{ $item->waktu_scan_keluar ?? '-' }}</td>
                                            <td>
                                                @if($item->waktu_scan_masuk && $item->waktu_scan_keluar)
                                                    <span class="badge bg-success">Completo</span>
                                                @elseif($item->waktu_scan_masuk)
                                                    <span class="badge bg-warning">Sem Registro de Saída</span>
                                                @else
                                                    <span class="badge bg-danger">Sem Registro</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum registro de presença encontrado</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Data</th>
                                    <th>Disciplina</th>
                                    <th>Classe</th>
                                    <th>Horário</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($absensi->count() > 0)
                                    @php $no = $absensi->firstItem(); @endphp
                                    @foreach($absensi as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $item->jadwal->jadwalPelajaran->mataPelajaran->nama_mapel ?? 'Não disponível' }}</td>
                                            <td>{{ $item->jadwal->kelas->nama_kelas ?? 'Não disponível' }}</td>
                                            <td>
                                                @if($item->jadwal)
                                                    {{ substr($item->jadwal->jam_mulai, 0, 5) }} - {{ substr($item->jadwal->jam_selesai, 0, 5) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Presente</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum registro de presença encontrado</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $absensi->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Resumo de Presença</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($type == 'gerbang')
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Total de Registros</h5>
                                <h2 class="text-primary">{{ $absensi->total() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Entradas com Saída</h5>
                                <h2 class="text-success">{{ $absensi->where('waktu_scan_keluar', '!=', null)->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Sem Registro de Saída</h5>
                                <h2 class="text-warning">{{ $absensi->where('waktu_scan_masuk', '!=', null)->where('waktu_scan_keluar', null)->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Período</h5>
                                <p class="mb-0">
                                    @if($startDate && $endDate)
                                        {{ $startDate->format('d/m/Y') }} até {{ $endDate->format('d/m/Y') }}
                                    @else
                                        Todos os registros
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Total de Aulas</h5>
                                <h2 class="text-primary">{{ $absensi->total() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Dias de Aula</h5>
                                <h2 class="text-info">{{ $absensi->groupBy('tanggal')->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Classes Atendidas</h5>
                                <h2 class="text-success">{{ $absensi->groupBy('jadwal.kelas_id')->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card text-center bg-light p-3">
                                <h5>Período</h5>
                                <p class="mb-0">
                                    @if($startDate && $endDate)
                                        {{ $startDate->format('d/m/Y') }} até {{ $endDate->format('d/m/Y') }}
                                    @else
                                        Todos os registros
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomDates() {
        const periodSelect = document.getElementById('period');
        const customDateFields = document.querySelectorAll('.custom-dates');

        if (periodSelect.value === 'custom') {
            customDateFields.forEach(field => {
                field.classList.remove('d-none');
            });
        } else {
            customDateFields.forEach(field => {
                field.classList.add('d-none');
            });
        }
    }

    // Jalankan saat halaman dimuat untuk memastikan keadaan awal yang benar
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomDates();
    });
</script>

@endsection
