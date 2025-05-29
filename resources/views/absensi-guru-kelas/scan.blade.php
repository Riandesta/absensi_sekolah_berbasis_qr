@extends('templates')
@section('header', 'Scan QR Code Absensi Guru Kelas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">Scan QR Code Absensi Guru Kelas</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Info Kelas -->
                    <div class="mb-4">
                        @if ($kelas)
                            <h5>Kelas: {{ $kelas->tingkat }} {{ $kelas->nama_kelas }} {{ $kelas->jurusan->nama_jurusan ?? '' }}</h5>
                        @else
                            <h5>Pilih Kelas Terlebih Dahulu</h5>
                        @endif
                        <p>Hari Ini: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>

                    <!-- Pilihan Kelas (Admin) -->
                    @if (Auth::user()->role === 'admin')
                        <div class="mb-4">
                            <h5>Pilih Kelas</h5>
                            <div class="form-group mb-3">
                                <label for="kelas_id">Kelas</label>
                                <select class="form-select" id="kelas_id" name="kelas_id" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama_kelas }} {{ $k->jurusan->nama_jurusan ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Jadwal Hari Ini -->
                    <div class="mb-4" id="jadwal-container" style="display: {{ Auth::user()->role === 'kelas' ? 'block' : 'none' }};">
                        <h5>Jadwal Pelajaran Hari Ini</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="jadwal-body">
                                @if(Auth::user()->role === 'kelas' && isset($jadwal) && count($jadwal) > 0)
                                    @foreach($jadwal as $j)
                                        <tr>
                                            <td>{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                                            <td>{{ $j->jadwalPelajaran->mataPelajaran->nama_mapel ?? '-' }}</td>
                                            <td>{{ $j->jadwalPelajaran->guru->nama_lengkap ?? 'Belum ditentukan' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary scan-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#scanModal"
                                                    data-jadwal-id="{{ $j->id }}">
                                                    Scan QR
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4" class="text-center">Tidak ada jadwal pelajaran hari ini.</td></tr>
                                @endif
                            </tbody>
                        </table>
                        <div id="no-jadwal" class="alert alert-info" style="display: none;">
                            Tidak ada jadwal tersedia.
                        </div>
                    </div>

                    <!-- Modal QR Scan -->
                    <div class="modal fade" id="scanModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Scan QR Code Guru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="scanForm" action="{{ route(Auth::user()->role .'.absensi-guru-kelas.scan-process') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="jadwal_id" id="jadwalId">
                                        <input type="hidden" name="kelas_id" id="kelasIdHidden" value="{{ $kelas?->id }}">
                                        <div class="mb-3">
                                            <div id="reader" style="width: 100%; height: 300px;"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="qr_code">Hasil Scan</label>
                                            <input type="text" name="qr_code" id="qr_code" class="form-control" readonly>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" id="submitButton" class="btn btn-success" disabled>Kirim Absensi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- card-body -->
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
   // Add this script to your scan.blade.php view or modify the existing script
document.addEventListener('DOMContentLoaded', function () {
    // Existing code for attaching scan events
    attachScanEvents();

    // For admin role: handle class selection
    const kelasSelect = document.getElementById('kelas_id');
    if (kelasSelect) {
        kelasSelect.addEventListener('change', function() {
            const kelasId = this.value;
            if (kelasId) {
                // Update hidden input for form submission
                document.getElementById('kelasIdHidden').value = kelasId;

                // Fetch jadwal for selected class
                fetchJadwal(kelasId);

                // Show jadwal container
                document.getElementById('jadwal-container').style.display = 'block';
            } else {
                document.getElementById('jadwal-container').style.display = 'none';
            }
        });
    }

    const modal = document.getElementById('scanModal');
    modal.addEventListener('shown.bs.modal', startScanning);
    modal.addEventListener('hidden.bs.modal', stopScanning);
});

// Function to fetch jadwal based on selected class (for admin)
function fetchJadwal(kelasId) {
    fetch(`/absensi-guru-kelas/load-jadwal?kelas_id=${kelasId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateJadwalTable(data.jadwal);
        } else {
            console.error('Error fetching jadwal:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to update jadwal table with fetched data
function updateJadwalTable(jadwalData) {
    const tableBody = document.getElementById('jadwal-body');
    const noJadwal = document.getElementById('no-jadwal');

    // Clear existing rows
    tableBody.innerHTML = '';

    if (jadwalData && jadwalData.length > 0) {
        jadwalData.forEach(j => {
            const row = document.createElement('tr');

            // Format jam
            const jamMulai = j.jam_mulai ? j.jam_mulai.substring(0, 5) : '--:--';
            const jamSelesai = j.jam_selesai ? j.jam_selesai.substring(0, 5) : '--:--';

            // Get mapel name
            const mapelName = j.jadwal_pelajaran && j.jadwal_pelajaran.mata_pelajaran ?
                j.jadwal_pelajaran.mata_pelajaran.nama_mapel : '-';

            // Get guru name
            let guruName = 'Belum ditentukan';
            if (j.jadwal_pelajaran && j.jadwal_pelajaran.guru) {
                guruName = j.jadwal_pelajaran.guru.nama_lengkap;
            }

            row.innerHTML = `
                <td>${jamMulai} - ${jamSelesai}</td>
                <td>${mapelName}</td>
                <td>${guruName}</td>
                <td>
                    <button class="btn btn-sm btn-primary scan-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#scanModal"
                        data-jadwal-id="${j.id}">
                        Scan QR
                    </button>
                </td>
            `;

            tableBody.appendChild(row);
        });

        noJadwal.style.display = 'none';
        // Re-attach event listeners to new buttons
        attachScanEvents();
    } else {
        tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada jadwal pelajaran hari ini.</td></tr>';
        noJadwal.style.display = 'block';
    }
}

// Make sure the HTML5QR scanning functions remain intact
let html5QrCode;
let scanning = false;

function startScanning() {
    if (scanning) return;

    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250
        },
        qrCodeSuccessCallback,
        error => console.warn(error)
    ).then(() => scanning = true)
     .catch(err => console.error("Camera start error", err));
}

function stopScanning() {
    if (!scanning) return;

    html5QrCode.stop().then(() => {
        html5QrCode.clear();
        scanning = false;
    }).catch(err => console.error("Camera stop error", err));
}

function qrCodeSuccessCallback(decodedText) {
    document.getElementById('qr_code').value = decodedText;
    document.getElementById('submitButton').disabled = false;
    stopScanning();
}

function attachScanEvents() {
    document.querySelectorAll('.scan-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const jadwalId = this.getAttribute('data-jadwal-id');
            document.getElementById('jadwalId').value = jadwalId;
            document.getElementById('qr_code').value = '';
            document.getElementById('submitButton').disabled = true;
        });
    });
}
</script>
