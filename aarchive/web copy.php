<!-- <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AdminController,
    SiswaController,
    GuruController,
    KaryawanController,
    KurikulumController,
    WalikelasController,
    JurusanController,
    KelasController,
    MataPelajaranController,
    JadwalPelajaranController,
    AbsensiGerbangController,
    LaporanAbsensiGerbangController,
    PetugasPiketController,
    TahunAjaranController,
    AbsensiGuruKelasController,
    AbsensiSiswaKelasController,
    LaporanAbsensiGuruKelasController,
    LaporanAbsensiSiswaKelasController
};
use App\Models\AbsensiSiswaKelas;

// ==============================
// Auth Routes (All Roles)
// ==============================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================
// General Routes
// ==============================
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/templates', fn() => view('templates'))->name('templates');

// ==============================
// Admin Routes
// ==============================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // ==============================
    // Admin : Dashboard Admin
    // ==============================
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');


    // ==============================
    // Admin : Jurusan Route
    // ==============================
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::get('/jurusan/create', [JurusanController::class, 'create'])->name('jurusan.create');
    Route::post('/jurusan/store', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::get('/jurusan/edit/{jurusan}', [JurusanController::class, 'edit'])->name('jurusan.edit');
    Route::put('/jurusan/update/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/delete/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    // ==============================
    // Admin : Tahun Ajaran Route
    // ==============================
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
    Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');
    Route::post('/tahun-ajaran/store', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
    Route::get('/tahun-ajaran/edit/{tahunAjaran}', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');
    Route::put('/tahun-ajaran/update/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
    Route::delete('/tahun-ajaran/delete/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');

    // ==============================
    // Admin : Mata Pelajaran Route
    // ==============================
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
    Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
    Route::post('/mata-pelajaran/store', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
    Route::get('/mata-pelajaran/edit/{id}', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
    Route::put('/mata-pelajaran/update/{id}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
    Route::delete('/mata-pelajaran/delete/{id}', [MataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');

    // ==============================
    // Admin : Jadwal Pelajaran Route
    // ==============================
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('admin.jadwal-pelajaran.index');
    Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('admin.jadwal-pelajaran.create');
    Route::post('/jadwal-pelajaran/store', [JadwalPelajaranController::class, 'store'])->name('admin.jadwal-pelajaran.store');
    Route::get('/jadwal-pelajaran/edit/{jadwalPelajaran}', [JadwalPelajaranController::class, 'edit'])->name('admin.jadwal-pelajaran.edit');
    Route::put('/jadwal-pelajaran/update/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('admin.jadwal-pelajaran.update');
    Route::delete('/jadwal-pelajaran/delete/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('admin.jadwal-pelajaran.destroy');

    // ==============================
    // Admin : Siswa Route
    // ==============================
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/edit/{siswa}', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/update/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/delete/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('siswa.absensi.histori');
    Route::get('/siswa/{siswa}/download-qrcode', [SiswaController::class, 'downloadQrCode'])->name('siswa.download-qrcode');
    Route::get('/siswa/{siswa}/download-qrcode-only', [SiswaController::class, 'downloadQrCodeOnly'])->name('siswa.download-qrcode-only');

    // ==============================
    // Admin : Kelas Route
    // ==============================
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas/store', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/edit/{kelas}', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/update/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/delete/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // ==============================
    // Admin : Karyawan Route
    // ==============================
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/edit/{karyawan}', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/update/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/delete/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
    Route::get('/karyawan/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('karyawan.download-qrcode');
    Route::get('/karyawan/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('karyawan.download-qrcode-only');
    Route::get('/karyawan/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('karyawan.absensi.histori');

    // ==============================
    // Admin : Petugas Piket Route
    // ==============================
    Route::get('/petugas-piket', [PetugasPiketController::class, 'index'])->name('admin.petugas-piket.index');
    Route::get('/petugas-piket/create', [PetugasPiketController::class, 'create'])->name('admin.petugas-piket.create');
    Route::post('/petugas-piket', [PetugasPiketController::class, 'store'])->name('admin.petugas-piket.store');
    Route::get('/petugas-piket/{petugasPiket}/edit', [PetugasPiketController::class, 'edit'])->name('admin.petugas-piket.edit');
    Route::put('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'update'])->name('admin.petugas-piket.update');
    Route::delete('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'destroy'])->name('admin.petugas-piket.destroy');

    // ==============================
    // Admin : Absensi Gerbang Route
    // ==============================
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('admin.absensi-gerbang.index');
    Route::get('/absensi-gerbang/scan', [AbsensiGerbangController::class, 'scan'])->name('admin.absensi-gerbang.scan');
    Route::post('/absensi-gerbang/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('admin.absensi-gerbang.scan-process');
    Route::post('/absensi-gerbang/store', [AbsensiGerbangController::class, 'store'])->name('admin.absensi-gerbang.store');
    Route::post('/absensi-gerbang/export-pdf', [AbsensiGerbangController::class, 'exportPdf'])->name('admin.absensi-gerbang.export-pdf');
    Route::delete('/absensi-gerbang/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('admin.absensi-gerbang.destroy');

    // ==============================
    // Admin : Absensi Guru Kelas Route
    // ==============================
    Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('admin.absensi-guru-kelas.index');
    Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('admin.absensi-guru-kelas.scan');
    Route::post('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('admin.absensi-guru-kelas.scan-process');
    Route::get('/absensi-guru-kelas/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('admin.absensi-guru-kelas.show');
    Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('admin.absensi-guru-kelas.destroy');
    Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('admin.absensi-guru-kelas.report');
    Route::get('/absensi-guru-kelas/export-pdf', [AbsensiGuruKelasController::class, 'exportPdf'])->name('admin.absensi-guru-kelas.export-pdf');
    Route::get('/absensi-guru-kelas/export', [AbsensiGuruKelasController::class, 'export'])->name('admin.absensi-guru-kelas.export');

    // ==============================
    // Admin : Absensi Siswa Kelas Route
    // ==============================
    Route::get('/absensi-siswa-kelas', [AbsensiSiswaKelasController::class, 'index'])->name('admin.absensi-siswa-kelas.index');
    Route::get('/absensi-siswa-kelas/kelas/{jadwal}', [AbsensiSiswaKelasController::class, 'kelas'])->name('admin.absensi-siswa-kelas.kelas');
    Route::post('/absensi-siswa-kelas/update-status', [AbsensiSiswaKelasController::class, 'updateStatus'])->name('admin.absensi-siswa-kelas.update-status');
    Route::post('/absensi-siswa-kelas/simpan-absensi', [AbsensiSiswaKelasController::class, 'simpanAbsensi'])->name('admin.absensi-siswa-kelas.simpan-absensi');
    Route::get('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'show'])->name('admin.absensi-siswa-kelas.show');
    Route::delete('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'destroy'])->name('admin.absensi-siswa-kelas.destroy');
    Route::get('/absensi-siswa-kelas-laporan', [AbsensiSiswaKelasController::class, 'laporan'])->name('admin.absensi-siswa-kelas.laporan');
    Route::get('/absensi-siswa-kelas/laporan-siswa', [AbsensiSiswaKelasController::class, 'laporanSiswa'])->name('admin.absensi-siswa-kelas.laporan-siswa');
    Route::get('/absensi-siswa-kelas/view', [AbsensiSiswaKelasController::class, 'view'])->name('admin.absensi-siswa-kelas.view');
    Route::get('/absensi-siswa-kelas/edit', [AbsensiSiswaKelasController::class, 'edit'])->name('admin.absensi-siswa-kelas.edit');
    Route::post('/absensi-siswa-kelas/update', [AbsensiSiswaKelasController::class, 'update'])->name('admin.absensi-siswa-kelas.update');
    Route::get('/absensi-siswa-kelas/rekap', [AbsensiSiswaKelasController::class, 'rekap'])->name('admin.absensi-siswa-kelas.rekap');
});
// ==============================
// Akhir Admin Routes
// ==============================

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

// ==============================
// Siswa Route
// ==============================
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    // Existing routes...
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('siswa.absensi-gerbang.index');
    Route::get('/download-qrcode', [SiswaController::class, 'downloadMyQrCode'])->name('siswa.download-qrcode');

    // New routes for profile and attendance history
    Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
    Route::post('/profile/update', [SiswaController::class, 'updateProfile'])->name('siswa.profile.update');
    Route::get('/riwayat-absensi-siswa', [SiswaController::class, 'attendanceHistory'])->name('siswa.riwayat-absensi-persiswa');
    Route::get('/attendance-export-pdf', [SiswaController::class, 'exportAttendancePdf'])->name('siswa.attendance-export-pdf');
});
// ==============================
// Akhir Siswa Route
// ==============================

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

// ==============================
// Karyawan Route
// ==============================
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {

    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    Route::get('/profile', [KaryawanController::class, 'profile'])->name('karyawan.profile');
    Route::post('/profile/update', [KaryawanController::class, 'updateProfile'])->name('karyawan.profile.update');
    Route::get('/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('karyawan.download-qrcode');
    Route::get('/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('karyawan.download-qrcode-only');

    // ==============================
    // Karyawan : Petugas Piket Route
    // ==============================
    Route::get('/petugas-piket', [PetugasPiketController::class, 'index'])->name('karyawan.petugas-piket.index');
    Route::get('/petugas-piket/create', [PetugasPiketController::class, 'create'])->name('karyawan.petugas-piket.create');
    Route::post('/petugas-piket', [PetugasPiketController::class, 'store'])->name('karyawan.petugas-piket.store');
    Route::get('/petugas-piket/{petugasPiket}/edit', [PetugasPiketController::class, 'edit'])->name('karyawan.petugas-piket.edit');
    Route::put('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'update'])->name('karyawan.petugas-piket.update');
    Route::delete('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'destroy'])->name('karyawan.petugas-piket.destroy');

    // ==============================
    // Karyawan : Absensi Gerbang Route
    // ==============================
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('karyawan.absensi-gerbang.index');
    Route::get('/absensi-gerbang/scan', [AbsensiGerbangController::class, 'scan'])->name('karyawan.absensi-gerbang.scan');
    Route::post('/absensi-gerbang/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('karyawan.absensi-gerbang.scan-process');
    Route::post('/absensi-gerbang/store', [AbsensiGerbangController::class, 'store'])->name('karyawan.absensi-gerbang.store');
    Route::post('/absensi-gerbang/export-pdf', [AbsensiGerbangController::class, 'exportPdf'])->name('karyawan.absensi-gerbang.export-pdf');
    Route::delete('/absensi-gerbang/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('karyawan.absensi-gerbang.destroy');

    // ==============================
    // Karyawan : Absensi Siswa Kelas Route
    // ==============================
    Route::get('/absensi-siswa-kelas', [AbsensiSiswaKelasController::class, 'index'])->name('karyawan.absensi-siswa-kelas.index');
    Route::get('/absensi-siswa-kelas/kelas/{jadwal}', [AbsensiSiswaKelasController::class, 'kelas'])->name('karyawan.absensi-siswa-kelas.kelas');
    Route::post('/absensi-siswa-kelas/update-status', [AbsensiSiswaKelasController::class, 'updateStatus'])->name('karyawan.absensi-siswa-kelas.update-status');
    Route::post('/absensi-siswa-kelas/simpan-absensi', [AbsensiSiswaKelasController::class, 'simpanAbsensi'])->name('karyawan.absensi-siswa-kelas.simpan-absensi');
    Route::get('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'show'])->name('karyawan.absensi-siswa-kelas.show');
    Route::delete('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'destroy'])->name('karyawan.absensi-siswa-kelas.destroy');
    Route::get('/absensi-siswa-kelas-laporan', [AbsensiSiswaKelasController::class, 'laporan'])->name('karyawan.absensi-siswa-kelas.laporan');
    Route::get('/absensi-siswa-kelas/laporan-siswa', [AbsensiSiswaKelasController::class, 'laporanSiswa'])->name('karyawan.absensi-siswa-kelas.laporan-siswa');
    Route::get('/absensi-siswa-kelas/view', [AbsensiSiswaKelasController::class, 'view'])->name('karyawan.absensi-siswa-kelas.view');
    Route::get('/absensi-siswa-kelas/edit', [AbsensiSiswaKelasController::class, 'edit'])->name('karyawan.absensi-siswa-kelas.edit');
    Route::post('/absensi-siswa-kelas/update', [AbsensiSiswaKelasController::class, 'update'])->name('karyawan.absensi-siswa-kelas.update');
    Route::get('/absensi-siswa-kelas/rekap', [AbsensiSiswaKelasController::class, 'rekap'])->name('karyawan.absensi-siswa-kelas.rekap');

    // ==============================
    // Karyawan : Mata Pelajaran Route
    // ==============================
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('karyawan.mata-pelajaran.index');
    Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('karyawan.mata-pelajaran.create');
    Route::post('/mata-pelajaran/store', [MataPelajaranController::class, 'store'])->name('karyawan.mata-pelajaran.store');
    Route::get('/mata-pelajaran/edit/{id}', [MataPelajaranController::class, 'edit'])->name('karyawan.mata-pelajaran.edit');
    Route::put('/mata-pelajaran/update/{id}', [MataPelajaranController::class, 'update'])->name('karyawan.mata-pelajaran.update');
    Route::delete('/mata-pelajaran/delete/{id}', [MataPelajaranController::class, 'destroy'])->name('karyawan.mata-pelajaran.delete');

    // ==============================
    // Karyawan : Jadwal Pelajaran Route
    // ==============================
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('karyawan.jadwal-pelajaran.index');
    Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('karyawan.jadwal-pelajaran.create');
    Route::post('/jadwal-pelajaran/store', [JadwalPelajaranController::class, 'store'])->name('karyawan.jadwal-pelajaran.store');
    Route::get('/jadwal-pelajaran/edit/{jadwalPelajaran}', [JadwalPelajaranController::class, 'edit'])->name('karyawan.jadwal-pelajaran.edit');
    Route::put('/jadwal-pelajaran/update/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('karyawan.jadwal-pelajaran.update');
    Route::delete('/jadwal-pelajaran/delete/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('karyawan.jadwal-pelajaran.delete');
});
// ==============================
// Akhir Karyawan Route
// ==============================

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

// ==============================
// Kelas Route
// ==============================
Route::prefix('kelas')->middleware(['auth', 'role:kelas'])->group(function () {
    Route::get('/dashboard', [KelasController::class, 'dashboard'])->name('kelas.dashboard');
    Route::get('/absensi-siswa-kelas/laporan', [AbsensiSiswaKelasController::class, 'laporan'])->name('kelas.absensi-siswa-kelas.laporan');

    // ==============================
    // Kelas : Absensi Guru Kelas Route
    // ==============================
    Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('kelas.absensi-guru-kelas.index');
    Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('kelas.absensi-guru-kelas.scan');
    Route::post('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('kelas.absensi-guru-kelas.scan-process');
    Route::get('/absensi-guru-kelas/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('kelas.absensi-guru-kelas.show');
    Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('kelas.absensi-guru-kelas.destroy');
    Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('kelas.absensi-guru-kelas.report');
    Route::get('/absensi-guru-kelas/export-pdf', [AbsensiGuruKelasController::class, 'exportPdf'])->name('kelas.absensi-guru-kelas.export-pdf');
    Route::get('/absensi-guru-kelas/export', [AbsensiGuruKelasController::class, 'export'])->name('kelas.absensi-guru-kelas.export');
});
// ==============================
// Akhir Kelas Route
// ============================== -->


<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use App\Models\AbsensiGuruKelas;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Imports\AbsensiSiswaImport;

class AbsensiRiwayatController extends Controller
{
    /**
     * Menampilkan riwayat absensi berdasarkan jabatan pengguna
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'gerbang'); // Default ke gerbang
        $period = $request->input('period', 'all'); // Default ke semua
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        if ($user->role === 'karyawan') {
            $karyawan = Karyawan::find($user->related_id);
            $jabatan = strtolower($karyawan->jabatan ?? '');

            // Untuk Guru
            if ($jabatan === 'guru') {
                return $this->getGuruAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd);
            }

            // Untuk walikelas
            if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
                return $this->getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd);
            }

            // Untuk Kurikulum
            if ($jabatan === 'kurikulum') {
                return $this->getKurikulumAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd, $kelasId);
            }
        }

        // Default jika jabatan tidak dikenali
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Mendapatkan data absensi untuk Guru
     */
    private function getGuruAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd)
    {
        $karyawanId = $user->related_id;

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang guru
            $query = AbsensiGerbang::where('related_id', $karyawanId);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.riwayat-absensi', compact('absensi', 'type', 'period', 'customStart', 'customEnd'));
        } else {
            // Riwayat absensi mengajar guru
            $query = AbsensiGuruKelas::where('karyawan_id', $karyawanId)
                ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas', 'scanByUser']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('absensi.riwayat-absensi', compact('absensi', 'type', 'period', 'customStart', 'customEnd'));
        }
    }


    /**
     * Mendapatkan data absensi untuk walikelas
     */
   /**
 * Menangani rute laporan-absensi-gerbang
 * Public method untuk memastikan dapat diakses melalui route
 */
// In AbsensiRiwayatController.php
// Modify the laporanAbsensiGerbang method:

    public function laporanAbsensiGerbang(Request $request)
    {
        $user = Auth::user();
        $type = 'gerbang'; // Force type to be gerbang
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $userType = $request->input('user_type'); // New parameter for filtering by user type

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $jabatan = strtolower($karyawan->jabatan ?? '');

        // Untuk Wali Kelas
        if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
            $kelasId = $karyawan->kelas_id;
            $kelas = Kelas::find($kelasId);

            if (!$kelas) {
                return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
            }

            // Dapatkan ID siswa dari kelas yang diampunya
            $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

            // Riwayat absensi gerbang siswa di kelas
            $query = AbsensiGerbang::whereIn('related_id', $siswaIds)
                ->with(['siswa', 'karyawan']);

            // Apply user type filter if specified
            if ($userType) {
                if ($userType === 'siswa') {
                    $query->whereHas('siswa');
                } elseif ($userType === 'karyawan') {
                    $query->whereHas('karyawan');
                }
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas', 'userType'));
        }

        // Untuk Kurikulum
        if ($jabatan === 'kurikulum') {
            // Dapatkan semua kelas untuk dropdown filter
            $kelasList = Kelas::all();

            // Filter berdasarkan kelas jika ada
            $kelas = null;
            if ($kelasId) {
                $kelas = Kelas::find($kelasId);
            }

            // Riwayat absensi gerbang semua siswa atau berdasarkan kelas
            $query = AbsensiGerbang::with(['siswa', 'karyawan']);

            // Apply user type filter if specified
            if ($userType) {
                if ($userType === 'siswa') {
                    $query->whereHas('siswa');
                } elseif ($userType === 'karyawan') {
                    $query->whereHas('karyawan');
                }
            }

            // If no user type filter and kelas filter, we need to ensure we're only looking at students
            if (!$userType && $kelasId) {
                $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
                $query->whereIn('related_id', $siswaIds);
            } elseif ($kelasId) {
                // If there is both a user type filter and kelas filter, only apply class filter for students
                if ($userType === 'siswa') {
                    $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
                    $query->whereIn('related_id', $siswaIds);
                }
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

            return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas', 'userType'));
        }

        // Default jika jabatan tidak sesuai
        return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }



  /**
 * Menangani rute laporan-absensi-siswa
 */
public function laporanAbsensiSiswa(Request $request)
{
    $user = Auth::user();
    $type = 'kelas'; // Force type to be kelas
    $period = $request->input('period', 'all');
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');

    // Set tanggal berdasarkan periode
    [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

    // Cek jabatan pengguna
    $karyawan = Karyawan::find($user->related_id);

    if (!$karyawan) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
    }

    $jabatan = strtolower($karyawan->jabatan ?? '');

    // Untuk walikelas
    if ($jabatan === 'walikelas' && !empty($karyawan->kelas_id)) {
        $kelasId = $karyawan->kelas_id;
        $kelas = Kelas::find($kelasId);

        if (!$kelas) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
        }

        // Riwayat absensi siswa di kelas
        $query = AbsensiSiswaKelas::whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        // Tampilkan view tanpa dropdown kelas untuk walikelas
        return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
    }

    // Untuk Kurikulum
    if ($jabatan === 'kurikulum') {
        // Get kelasId from request for kurikulum
        $kelasId = $request->input('kelas_id');

        // Dapatkan semua kelas untuk dropdown filter
        $kelasList = Kelas::all();

        // Filter berdasarkan kelas jika ada
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
        }

        // Riwayat absensi siswa di kelas
        $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

        if ($kelasId) {
            $query->whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
    }

    // Default jika jabatan tidak sesuai
    return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
}


    /**
     * Private method to get walikelas Absensi data
     */
    public function getWaliKelasAbsensi(Request $request = null, $type = 'gerbang')
    {
        // If $request is null, get the current request
        if ($request === null) {
            $request = request();
        }

        $user = Auth::user();
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'walikelas') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $this->getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd);
    }

    /**
     * Private method to get walikelas Absensi data
     */
   /**
 * Private method to get walikelas Absensi data
 */
private function getWaliKelasAbsensiData($user, $karyawan, $type, $period, $startDate, $endDate, $customStart, $customEnd)
{
    $kelasId = $karyawan->kelas_id;
    $kelas = Kelas::find($kelasId);

    if (!$kelas) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
    }

    if ($type === 'gerbang') {
        // Dapatkan ID siswa dari kelas yang diampunya
        $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

        // Riwayat absensi gerbang siswa di kelas
        $query = AbsensiGerbang::whereIn('related_id', $siswaIds)
            ->with(['siswa']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
    } else {
        // Riwayat absensi siswa di kelas
        // FIXED: Use whereHas to filter by the related Jadwal's kelas_id
        $query = AbsensiSiswaKelas::whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelas'));
    }
}


    /**
     * Mendapatkan data absensi untuk Kurikulum
     */
   /**
 * Mendapatkan data absensi untuk Kurikulum
 */
private function getKurikulumAbsensi($user, $type, $period, $startDate, $endDate, $customStart, $customEnd, $kelasId)
{
    // Dapatkan semua kelas untuk dropdown filter
    $kelasList = Kelas::all();

    // Filter berdasarkan kelas jika ada
    $kelas = null;
    if ($kelasId) {
        $kelas = Kelas::find($kelasId);
    }

    if ($type === 'gerbang') {
        // Riwayat absensi gerbang semua siswa atau berdasarkan kelas
        $query = AbsensiGerbang::with(['siswa']);

        // Get only student records
        $studentIds = Siswa::pluck('id')->toArray();
        $query->whereIn('related_id', $studentIds);

        if ($kelasId) {
            $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
            $query->whereIn('related_id', $siswaIds);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('karyawan.laporan-absensi-gerbang', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
    } else {
        // Riwayat absensi siswa di kelas
        $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru', 'inputBy']);

        if ($kelasId) {
            // FIXED: Use whereHas to filter by the related Jadwal's kelas_id
            $query->whereHas('jadwal', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('absensi.laporan-absensi-siswa', compact('absensi', 'type', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
    }
}

    /**
     * Mendapatkan rentang tanggal berdasarkan periode
     */
    private function getDateRange($period, $customStart, $customEnd)
    {
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'daily':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'weekly':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'monthly':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'semester':
                // Asumsi semester 1: Juli-Desember, semester 2: Januari-Juni
                if (now()->month >= 7) {
                    $startDate = now()->setMonth(7)->setDay(1)->startOfDay();
                    $endDate = now()->setMonth(12)->endOfMonth()->endOfDay();
                } else {
                    $startDate = now()->setMonth(1)->setDay(1)->startOfDay();
                    $endDate = now()->setMonth(6)->endOfMonth()->endOfDay();
                }
                break;
            case 'yearly':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $startDate = Carbon::parse($customStart)->startOfDay();
                    $endDate = Carbon::parse($customEnd)->endOfDay();
                }
                break;
        }

        return [$startDate, $endDate];
    }

    /**
     * Menghasilkan laporan PDF absensi
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'gerbang');
        $period = $request->input('period', 'all');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        $kelasId = $request->input('kelas_id');
        $userType = $request->input('user_type'); // Get user type filter

        // Set tanggal berdasarkan periode
        [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

        // Cek jabatan pengguna
        if ($user->role === 'karyawan') {
            $karyawan = Karyawan::find($user->related_id);
            $jabatan = strtolower($karyawan->jabatan ?? '');

            // Untuk Wali Kelas - hanya bisa export data kelasnya
            if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
                // Force kelasId to be the wali kelas's assigned class
                $kelasId = $karyawan->kelas_id;
                return $this->exportWaliKelasAbsensiPdf($karyawan, $type, $period, $startDate, $endDate, $userType);
            }

            // Untuk Kurikulum - bisa export data semua kelas
            if ($jabatan === 'kurikulum') {
                return $this->exportKurikulumAbsensiPdf($type, $period, $startDate, $endDate, $kelasId, $userType);
            }
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengekspor data ini.');
    }

    /**
     * Export PDF untuk Guru
     */
    private function exportGuruAbsensiPdf($user, $type, $period, $startDate, $endDate)
    {
        $karyawanId = $user->related_id;
        $karyawan = Karyawan::find($karyawanId);

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang guru
            $query = AbsensiGerbang::where('related_id', $karyawanId);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            $data = [
                'karyawan' => $karyawan,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];

            $pdf = PDF::loadView('absensi.export.export-gerbang-guru', $data);
            return $pdf->download('Absensi_Gerbang_' . $karyawan->nama_lengkap . '.pdf');
        } else {
            // Riwayat absensi mengajar guru
            $query = AbsensiGuruKelas::where('karyawan_id', $karyawanId)
                ->with(['jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.kelas']);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            $data = [
                'karyawan' => $karyawan,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate
            ];

            $pdf = PDF::loadView('absensi.export.export-mengajar-guru', $data);
            return $pdf->download('Absensi_Mengajar_' . $karyawan->nama_lengkap . '.pdf');
        }
    }

    /**
     * Export PDF untuk walikelas
     */
  /**
 * Export PDF untuk walikelas
 */
private function exportWaliKelasAbsensiPdf($karyawan, $type, $period, $startDate, $endDate, $userType = null)
{
    $kelasId = $karyawan->kelas_id;
    $kelas = Kelas::find($kelasId);

    if (!$kelas) {
        return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
    }

    if ($type === 'gerbang') {
        // Dapatkan ID siswa dari kelas yang diampunya
        $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

        // Riwayat absensi gerbang siswa di kelas
        $query = AbsensiGerbang::whereIn('related_id', $siswaIds)
            ->with(['siswa', 'karyawan']);

        // Apply user type filter if specified
        if ($userType) {
            if ($userType === 'siswa') {
                $query->whereHas('siswa');
            } elseif ($userType === 'karyawan') {
                $query->whereHas('karyawan');
            }
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Hitung statistik
        $totalSiswa = count($siswaIds);
        $totalAbsensi = $absensi->count();
        $absensiLengkap = $absensi->where('waktu_scan_masuk', '!=', null)
            ->where('waktu_scan_keluar', '!=', null)->count();
        $belumAbsenKeluar = $absensi->where('waktu_scan_masuk', '!=', null)
            ->where('waktu_scan_keluar', null)->count();

        // Add counts by user type
        $totalSiswaAbsensi = $absensi->whereNotNull('siswa_id')->count();
        $totalKaryawanAbsensi = $absensi->whereNotNull('karyawan_id')->count();

        $data = [
            'kelas' => $kelas,
            'absensi' => $absensi,
            'type' => $type,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSiswa' => $totalSiswa,
            'totalAbsensi' => $totalAbsensi,
            'absensiLengkap' => $absensiLengkap,
            'belumAbsenKeluar' => $belumAbsenKeluar,
            'totalSiswaAbsensi' => $totalSiswaAbsensi,
            'totalKaryawanAbsensi' => $totalKaryawanAbsensi,
            'userType' => $userType
        ];

        $pdf = PDF::loadView('absensi.export.export-gerbang-kelas', $data);

        // Include user type in filename if specified
        $filename = 'Absensi_Gerbang_Kelas_' . $kelas->nama_kelas;
        if ($userType) {
            $filename .= '_' . ucfirst($userType);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    } else {
        // Ambil ID siswa dari kelas yang diampunya
        $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();

        // Riwayat absensi di kelas
        $query = AbsensiGuruKelas::whereIn('siswa_id', $siswaIds)
            ->with(['siswa', 'jadwal', 'jadwal.karyawan', 'jadwal.matapelajaran']);

        // Apply user type filter if specified
        if ($userType) {
            if ($userType === 'siswa') {
                $query->whereHas('siswa');
            } elseif ($userType === 'karyawan') {
                $query->whereHas('jadwal.karyawan');
            }
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->get();

        // Hitung statistik
        $totalSiswa = count($siswaIds);
        $totalAbsensi = $absensi->count();
        $absensiLengkap = $absensi->where('waktu_scan_masuk', '!=', null)
            ->where('waktu_scan_keluar', '!=', null)->count();
        $belumAbsenKeluar = $absensi->where('waktu_scan_masuk', '!=', null)
            ->where('waktu_scan_keluar', null)->count();

        // Add counts by user type
        $totalSiswaAbsensi = $absensi->whereNotNull('siswa_id')->count();
        $totalKaryawanAbsensi = $absensi->whereNotNull('jadwal.karyawan')->count();

        $data = [
            'kelas' => $kelas,
            'absensi' => $absensi,
            'type' => $type,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSiswa' => $totalSiswa,
            'totalAbsensi' => $totalAbsensi,
            'absensiLengkap' => $absensiLengkap,
            'belumAbsenKeluar' => $belumAbsenKeluar,
            'totalSiswaAbsensi' => $totalSiswaAbsensi,
            'totalKaryawanAbsensi' => $totalKaryawanAbsensi,
            'userType' => $userType
        ];

        $pdf = PDF::loadView('absensi.export.export-kelas', $data);

        $filename = 'Absensi_Kelas_' . $kelas->nama_kelas;
        if ($userType) {
            $filename .= '_' . ucfirst($userType);
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }

}


    /**
     * Export PDF untuk Kurikulum
     */
    private function exportKurikulumAbsensiPdf($type, $period, $startDate, $endDate, $kelasId, $userType = null)
    {
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
        }

        if ($type === 'gerbang') {
            // Riwayat absensi gerbang semua siswa atau berdasarkan kelas
            $query = AbsensiGerbang::with(['siswa', 'karyawan']);

            // Apply user type filter if specified
            if ($userType) {
                if ($userType === 'siswa') {
                    $query->whereHas('siswa');
                } elseif ($userType === 'karyawan') {
                    $query->whereHas('karyawan');
                }
            }

            // If class filter is specified and we're looking at students (or all users)
            if ($kelasId && ($userType === 'siswa' || !$userType)) {
                $siswaIds = Siswa::where('kelas_id', $kelasId)->pluck('id')->toArray();
                $query->whereIn('related_id', $siswaIds);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            // Calculate statistics
            $totalSiswa = Siswa::when($kelasId, function($query) use($kelasId) {
                return $query->where('kelas_id', $kelasId);
            })->count();

            $totalAbsensi = $absensi->count();
            $absensiLengkap = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', '!=', null)->count();
            $belumAbsenKeluar = $absensi->where('waktu_scan_masuk', '!=', null)
                ->where('waktu_scan_keluar', null)->count();

            // Add counts by user type
            $totalSiswaAbsensi = $absensi->whereNotNull('siswa_id')->count();
            $totalKaryawanAbsensi = $absensi->whereNotNull('karyawan_id')->count();

            $data = [
                'kelas' => $kelas,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalSiswa' => $totalSiswa,
                'totalAbsensi' => $totalAbsensi,
                'absensiLengkap' => $absensiLengkap,
                'belumAbsenKeluar' => $belumAbsenKeluar,
                'totalSiswaAbsensi' => $totalSiswaAbsensi,
                'totalKaryawanAbsensi' => $totalKaryawanAbsensi,
                'userType' => $userType
            ];

            $pdf = PDF::loadView('absensi.export.export-gerbang-kelas', $data);

            $filename = 'Absensi_Gerbang_';
            if ($userType) {
                $filename .= ucfirst($userType) . '_';
            }
            $filename .= $kelas ? 'Kelas_' . $kelas->nama_kelas : 'Semua_Kelas';
            $filename .= '.pdf';

            return $pdf->download($filename);
        } else {
            // Existing code for classroom attendance report
            // This code should remain unchanged as it's for a different report type
            $query = AbsensiSiswaKelas::with(['siswa', 'jadwal.jadwalPelajaran.mataPelajaran', 'jadwal.jadwalPelajaran.guru']);

            if ($kelasId) {
                $query->where('kelas_id', $kelasId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            $absensi = $query->orderBy('tanggal', 'desc')->get();

            // Hitung statistik
            $totalHadir = $absensi->where('status', 'Hadir')->count();
            $totalIzin = $absensi->where('status', 'Izin')->count();
            $totalSakit = $absensi->where('status', 'Sakit')->count();
            $totalAlpa = $absensi->where('status', 'Alpa')->count();

            $data = [
                'kelas' => $kelas,
                'absensi' => $absensi,
                'type' => $type,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalHadir' => $totalHadir,
                'totalIzin' => $totalIzin,
                'totalSakit' => $totalSakit,
                'totalAlpa' => $totalAlpa
            ];

            $filename = 'Absensi_Siswa_';
            $filename .= $kelas ? 'Kelas_' . $kelas->nama_kelas : 'Semua_Kelas';
            $filename .= '.pdf';

            $pdf = PDF::loadView('absensi.export.export-siswa-kelas', $data);
            return $pdf->download($filename);
        }
    }

    /**
     * Import absensi siswa dari Excel (khusus Guru)
     */
    public function importForm()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        // Ambil jadwal mengajar guru
        $jadwalMengajar = Jadwal::whereHas('jadwalPelajaran', function($q) use ($karyawan) {
            $q->where('guru_id', $karyawan->id);
        })->with(['kelas', 'jadwalPelajaran.mataPelajaran'])->get();

        return view('absensi.import-form', compact('jadwalMengajar'));
    }

    /**
     * Proses import absensi siswa dari Excel (khusus Guru)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal' => 'required|date'
        ]);

        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        // Cek apakah jadwal ini diajar oleh guru yang login
        $isValidJadwal = Jadwal::where('id', $request->jadwal_id)
            ->whereHas('jadwalPelajaran', function($q) use ($karyawan) {
                $q->where('guru_id', $karyawan->id);
            })->exists();

        if (!$isValidJadwal) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk jadwal ini.');
        }

        try {
            // Proses import CSV
            $csvFile = $request->file('file');
            $jadwalId = $request->jadwal_id;
            $tanggal = $request->tanggal;
            $userId = $user->id;

            // Baca file CSV
            $handle = fopen($csvFile->getPathname(), 'r');

            // Lewati header
            $header = fgetcsv($handle);

            // Mulai transaksi database
            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                $nis = $row[0] ?? null;
                $status = $row[1] ?? null;
                $keterangan = $row[2] ?? null;

                if (empty($nis) || empty($status)) {
                    continue;
                }

                // Cari siswa berdasarkan NIS
                $siswa = Siswa::where('nis', $nis)->first();
                if (!$siswa) {
                    Log::warning('Siswa dengan NIS ' . $nis . ' tidak ditemukan');
                    continue;
                }

                // Standarisasi status
                $status = ucfirst(strtolower(trim($status)));
                if (!in_array($status, ['Hadir', 'Izin', 'Sakit', 'Alpa'])) {
                    $status = 'Alpa'; // Default ke Alpa jika tidak valid
                }

                // Cek apakah sudah ada absensi
                $existingAbsensi = AbsensiSiswaKelas::where([
                    'related_id' => $siswa->id,
                    'jadwal_id' => $jadwalId,
                    'tanggal' => $tanggal,
                ])->first();

                if ($existingAbsensi) {
                    // Update existing record
                    $existingAbsensi->update([
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'input_by' => $userId,
                    ]);
                } else {
                    // Create new record
                    AbsensiSiswaKelas::create([
                        'related_id' => $siswa->id,
                        'jadwal_id' => $jadwalId,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => $tanggal,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'input_by' => $userId,
                    ]);
                }
            }

            fclose($handle);

            // Commit transaksi
            DB::commit();

            return redirect()->route('karyawan.riwayat-absensi')->with('success', 'Data absensi siswa berhasil diimpor.');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollBack();

            Log::error('Error importing student attendance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template untuk import absensi siswa
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan || strtolower($karyawan->jabatan) !== 'guru') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Fitur ini hanya untuk guru.');
        }

        // Daripada mencari file Excel yang mungkin tidak ada,
        // langsung saja buat template CSV sederhana
        return $this->generateTemplateFile();
    }

    /**
     * Generate template file CSV untuk import absensi
     *
     * @return \Illuminate\Http\Response
     */
  /**
     * Generate template file CSV untuk import absensi
     *
     * @return \Illuminate\Http\Response
     */
    private function generateTemplateFile()
    {
        // Buat file CSV sederhana
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_absensi_siswa.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Tambahkan header
            fputcsv($file, ['nis', 'status', 'keterangan']);

            // Tambahkan contoh data
            fputcsv($file, ['123456', 'Hadir', '']);
            fputcsv($file, ['123457', 'Izin', 'Izin keperluan keluarga']);
            fputcsv($file, ['123458', 'Sakit', 'Sakit demam']);
            fputcsv($file, ['123459', 'Alpa', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman profil karyawan
     */
    public function profile()
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::with('kelas', 'jurusan', 'tahunAjaran')->find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        return view('karyawan.profile', compact('karyawan', 'user'));
    }

    /**
     * Update profil karyawan
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'karyawan') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        $karyawan = Karyawan::find($user->related_id);

        if (!$karyawan) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_wa' => 'nullable|string|max:15',
            'username' => 'required|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            // Save new photo
            $fotoPath = $request->file('foto')->store('foto/karyawan', 'public');
        } else {
            // Keep old photo if no new file is uploaded
            $fotoPath = $karyawan->foto;
        }

        // Update karyawan data
        $karyawan->update([
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_wa' => $request->no_wa,
            'email' => $request->email,
            'foto' => $fotoPath,
        ]);

        // Update related user account
        $userData = [
            'username' => $request->username
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('karyawan.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
 * Menampilkan laporan absensi guru kelas
 */
public function laporanAbsensiGuruKelas(Request $request)
{
    $user = Auth::user();
    $period = $request->input('period', 'all');
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');
    $kelasId = $request->input('kelas_id');

    // Set tanggal berdasarkan periode
    [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

    // Cek jabatan pengguna
    $karyawan = Karyawan::find($user->related_id);

    if (!$karyawan) {
        return redirect()->route('karyawan.dashboard')->with('error', 'Data karyawan tidak ditemukan.');
    }

    $jabatan = strtolower($karyawan->jabatan ?? '');

    // Untuk Wali Kelas
    if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
        $kelasId = $karyawan->kelas_id;
        $kelas = Kelas::find($kelasId);

        if (!$kelas) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Data kelas tidak ditemukan.');
        }

        // Riwayat absensi guru yang mengajar di kelas ini
        $query = AbsensiGuruKelas::where('kelas_id', $kelasId)
            ->with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('absensi.laporan-absensi-guru-kelas', compact('absensi', 'period', 'customStart', 'customEnd', 'kelas'));
    }

    // Untuk Kurikulum
    if ($jabatan === 'kurikulum') {
        // Dapatkan semua kelas untuk dropdown filter
        $kelasList = Kelas::all();

        // Filter berdasarkan kelas jika ada
        $kelas = null;
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
        }

        // Riwayat absensi guru di semua kelas atau kelas tertentu
        $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('absensi.laporan-absensi-guru-kelas', compact('absensi', 'period', 'customStart', 'customEnd', 'kelasList', 'kelas'));
    }

    // Default jika jabatan tidak sesuai
    return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
}

/**
 * Mengekspor laporan absensi guru kelas ke PDF
 */
public function exportAbsensiGuruKelasPdf(Request $request)
{
    $user = Auth::user();
    $period = $request->input('period', 'all');
    $customStart = $request->input('start_date');
    $customEnd = $request->input('end_date');
    $kelasId = $request->input('kelas_id');

    // Set tanggal berdasarkan periode
    [$startDate, $endDate] = $this->getDateRange($period, $customStart, $customEnd);

    // Cek jabatan pengguna
    $karyawan = Karyawan::find($user->related_id);
    $jabatan = strtolower($karyawan->jabatan ?? '');

    // Untuk Wali Kelas - hanya bisa export data kelasnya
    if ($jabatan === 'wali kelas' && !empty($karyawan->kelas_id)) {
        $kelasId = $karyawan->kelas_id;
        return $this->generateAbsensiGuruKelasPdf($kelasId, $period, $startDate, $endDate);
    }

    // Untuk Kurikulum - bisa export data semua kelas atau kelas tertentu
    if ($jabatan === 'kurikulum') {
        return $this->generateAbsensiGuruKelasPdf($kelasId, $period, $startDate, $endDate);
    }

    return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengekspor data ini.');
}

/**
 * Generate PDF untuk laporan absensi guru kelas
 */
private function generateAbsensiGuruKelasPdf($kelasId, $period, $startDate, $endDate)
{
    // Filter berdasarkan kelas jika ada
    $kelas = null;
    if ($kelasId) {
        $kelas = Kelas::find($kelasId);
    }

    // Riwayat absensi guru di kelas
    $query = AbsensiGuruKelas::with(['karyawan', 'jadwal.jadwalPelajaran.mataPelajaran', 'scanByUser']);

    if ($kelasId) {
        $query->where('kelas_id', $kelasId);
    }

    if ($startDate && $endDate) {
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    $absensi = $query->orderBy('tanggal', 'desc')->get();

    // Set report title
    $reportTitle = "Laporan Absensi Guru Kelas";
    if ($kelas) {
        $reportTitle .= " - " . $kelas->nama_kelas;
    }

    // Set period text for the report
    $periodText = "";
    switch ($period) {
        case 'daily':
            $periodText = "Harian (" . Carbon::parse($startDate)->format('d-m-Y') . ")";
            break;
        case 'weekly':
            $periodText = "Mingguan (" . Carbon::parse($startDate)->format('d-m-Y') . " s/d " . Carbon::parse($endDate)->format('d-m-Y') . ")";
            break;
        case 'monthly':
            $periodText = "Bulanan (" . Carbon::parse($startDate)->format('F Y') . ")";
            break;
        case 'semester':
            $semester = Carbon::parse($startDate)->month <= 6 ? "1" : "2";
            $periodText = "Semester " . $semester . " (" . Carbon::parse($startDate)->format('Y') . ")";
            break;
        case 'yearly':
            $periodText = "Tahunan (" . Carbon::parse($startDate)->format('Y') . ")";
            break;
        case 'custom':
            $periodText = "Periode " . Carbon::parse($startDate)->format('d-m-Y') . " s/d " . Carbon::parse($endDate)->format('d-m-Y');
            break;
        default:
            $periodText = "Semua Periode";
    }

    // Group data by guru and tanggal for better reporting
    $groupedByGuru = $absensi->groupBy('karyawan_id');
    $groupedByTanggal = $absensi->groupBy('tanggal');

    // Calculate statistics
    $totalRecords = $absensi->count();
    $totalHadir = $absensi->where('status', 'Hadir')->count();
    $totalTerlambat = $absensi->where('status', 'Terlambat')->count();
    $totalIzin = $absensi->where('status', 'Izin')->count();
    $totalTidakHadir = $absensi->where('status', 'Tidak Hadir')->count();
    $totalGuru = $groupedByGuru->count();

    $data = [
        'reportTitle' => $reportTitle,
        'periodText' => $periodText,
        'kelas' => $kelas,
        'absensi' => $absensi,
        'groupedByGuru' => $groupedByGuru,
        'groupedByTanggal' => $groupedByTanggal,
        'startDate' => $startDate ? Carbon::parse($startDate) : null,
        'endDate' => $endDate ? Carbon::parse($endDate) : null,
        'statistics' => [
            'totalRecords' => $totalRecords,
            'totalHadir' => $totalHadir,
            'totalTerlambat' => $totalTerlambat,
            'totalIzin' => $totalIzin,
            'totalTidakHadir' => $totalTidakHadir,
            'totalGuru' => $totalGuru
        ]
    ];

    $pdf = PDF::loadView('absensi.export.export-guru-kelas', $data);

    $filename = "Absensi_Guru_Kelas";
    if ($kelas) {
        $filename .= "_" . str_replace(' ', '_', $kelas->nama_kelas);
    }
    $filename .= "_" . $period . ".pdf";

    return $pdf->download($filename);
}
