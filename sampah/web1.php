<?php

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
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::resource('/jurusan', JurusanController::class);
    Route::resource('/tahun-ajaran', TahunAjaranController::class);

    Route::resource('mata-pelajaran', MataPelajaranController::class);
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);

    Route::resource('siswa', SiswaController::class);
    Route::resource('kelas', KelasController::class);
    Route::resource('karyawan', KaryawanController::class);

    Route::prefix('absensi-gerbang')->name('absensi-gerbang.')->group(function () {
        Route::get('/', [AbsensiGerbangController::class, 'index'])->name('index');
        Route::get('/scan', [AbsensiGerbangController::class, 'scan'])->name('scan');
        Route::post('/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('laporan-karyawan');
        Route::post('/store', [AbsensiGerbangController::class, 'store'])->name('store');
        Route::post('/export-pdf', [AbsensiGerbangController::class, 'exportPdf'])->name('export-pdf');
        Route::delete('/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('destroy');
    });

    Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('absensi-guru-kelas.index');
    Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('absensi-guru-kelas.scan');
    Route::post('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('absensi-guru-kelas.scan-process');
    Route::get('/absensi-guru-kelas/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('absensi-guru-kelas.show');
    Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('absensi-guru-kelas.destroy');
    Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('absensi-guru-kelas.report');
    Route::get('/absensi-guru-kelas/export-pdf', [AbsensiGuruKelasController::class, 'exportPdf'])->name('absensi-guru-kelas.export-pdf');
});

// ==============================
// Siswa Routes (Admin & Walikelas)
// ==============================
Route::prefix('siswa')->middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('siswa.absensi.histori');
    Route::get('/{siswa}/download-qrcode', [SiswaController::class, 'downloadQrCode'])->name('siswa.download-qrcode');
    Route::get('/{siswa}/download-qrcode-only', [SiswaController::class, 'downloadQrCodeOnly'])->name('siswa.download-qrcode-only');
    Route::get('/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('siswa.absensi.histori');
});

// ==============================
// Karyawan Routes (Admin & All Karyawan)
// ==============================
Route::prefix('karyawan')->middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    Route::resource('karyawan', KaryawanController::class)->names('karyawan');
    Route::get('/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('karyawan.download-qrcode');
    Route::get('/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('karyawan.download-qrcode-only');
    Route::get('/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('karyawan.absensi.histori');
});

// ==============================
// Role : Kelas Routes (Admin & Kelas)
// ==============================
Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('absensi-guru-kelas.index');
Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('absensi-guru-kelas.scan');
Route::post('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('absensi-guru-kelas.scan-process');
Route::get('/absensi-guru-kelas/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('absensi-guru-kelas.show');
Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('absensi-guru-kelas.destroy');
Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('absensi-guru-kelas.report');
Route::get('/absensi-guru-kelas/export', [AbsensiGuruKelasController::class, 'export'])->name('absensi-guru-kelas.export');




// ==============================
// Kurikulum Routes (Admin & Jabatan: Kurikulum)
// ==============================
Route::prefix('kurikulum')->middleware(['auth', 'role:admin,karyawan'])->group(function () {
    Route::get('/dashboard', [KurikulumController::class, 'dashboard'])->name('kurikulum.dashboard');
    Route::resource('mata-pelajaran', MataPelajaranController::class)->names('kurikulum.mata-pelajaran');
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)->names('kurikulum.jadwal-pelajaran');
});

// ==============================
// Absensi Gerbang (Admin & All Karyawan)
// ==============================
Route::prefix('absensi-gerbang')->middleware(['auth', 'role:admin,karyawan'])->name('absensi-gerbang.')->group(function () {
    Route::get('/', [AbsensiGerbangController::class, 'index'])->name('index');
    Route::get('/scan', [AbsensiGerbangController::class, 'scan'])->name('scan');
    Route::post('/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('scan-process');
    Route::post('/store', [AbsensiGerbangController::class, 'store'])->name('store');
    Route::delete('/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('destroy');
    Route::get('/laporan-karyawan', [AbsensiGerbangController::class, 'laporanKaryawan'])->name('absensi-gerbang.laporan-karyawan');
});
// ==============================
// Kelas Routes (For Walikelas/Class Teachers)
// ==============================
Route::prefix('kelas')->middleware(['auth', 'role:admin,karyawan,kelas'])->group(function () {
    Route::get('/dashboard', [KelasController::class, 'dashboard'])->name('kelas.dashboard');
    // Add other kelas-specific routes here
});
// ==============================
// Absensi Guru Kelas (Admin & Role: Kelas)
// ==============================
Route::prefix('absensi-guru-kelas')->middleware(['auth', 'role:admin,karyawan,kelas'])->group(function () {
    Route::get('/', [AbsensiGuruKelasController::class, 'index'])->name('absensi-guru-kelas.index');
    Route::get('/scan', [AbsensiGuruKelasController::class, 'scan'])->name('absensi-guru-kelas.scan');
    Route::post('/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('absensi-guru-kelas.scan-process');
    Route::get('/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('absensi-guru-kelas.show');
    Route::delete('/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('absensi-guru-kelas.destroy');
    Route::get('/report', [AbsensiGuruKelasController::class, 'report'])->name('absensi-guru-kelas.report');
    Route::get('/export', [AbsensiGuruKelasController::class, 'export'])->name('absensi-guru-kelas.export');
    Route::post('/process-scan', [AbsensiGuruKelasController::class, 'processScanGuru'])->name('absensi-guru-kelas.process');
});

// In routes/web.php

// Absensi siswa kelas routes
// Main routes

Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/absensi-siswa-kelas', [AbsensiSiswaKelasController::class, 'index'])->name('absensi-siswa-kelas.index');
    Route::get('/absensi-siswa-kelas/kelas/{jadwal}', [AbsensiSiswaKelasController::class, 'kelas'])->name('absensi-siswa-kelas.kelas');
    Route::post('/absensi-siswa-kelas/update-status', [AbsensiSiswaKelasController::class, 'updateStatus'])->name('absensi-siswa-kelas.update-status');
    Route::post('/absensi-siswa-kelas/simpan-absensi', [AbsensiSiswaKelasController::class, 'simpanAbsensi'])->name('absensi-siswa-kelas.simpan-absensi');
    Route::get('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'show'])->name('absensi-siswa-kelas.show');
    Route::delete('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'destroy'])->name('absensi-siswa-kelas.destroy');
    Route::get('/absensi-siswa-kelas-laporan', [AbsensiSiswaKelasController::class, 'laporan'])->name('absensi-siswa-kelas.laporan');
    Route::get('/absensi-siswa-kelas/laporan-siswa', [AbsensiSiswaKelasController::class, 'laporanSiswa'])->name('absensi-siswa-kelas.laporan-siswa');
});

// Add these routes to your web.php file, replacing the existing AbsensiSiswaKelas routes

// Absensi Siswa Kelas routes - Open for all authenticated users
Route::middleware(['auth'])->group(function () {
    // Main routes for student attendance
    Route::get('/absensi-siswa-kelas', [AbsensiSiswaKelasController::class, 'index'])
        ->name('absensi-siswa-kelas.index');

    Route::get('/absensi-siswa-kelas/kelas/{jadwal}', [AbsensiSiswaKelasController::class, 'kelas'])
        ->name('absensi-siswa-kelas.kelas');

    Route::post('/absensi-siswa-kelas/update-status', [AbsensiSiswaKelasController::class, 'updateStatus'])
        ->name('absensi-siswa-kelas.update-status');

    Route::post('/absensi-siswa-kelas/simpan-absensi', [AbsensiSiswaKelasController::class, 'simpanAbsensi'])
        ->name('absensi-siswa-kelas.simpan-absensi');

    Route::get('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'show'])
        ->name('absensi-siswa-kelas.show');

    Route::delete('/absensi-siswa-kelas/{absensiSiswaKelas}', [AbsensiSiswaKelasController::class, 'destroy'])
        ->name('absensi-siswa-kelas.destroy');

    // Report routes
    Route::get('/absensi-siswa-kelas-laporan', [AbsensiSiswaKelasController::class, 'laporan'])
        ->name('absensi-siswa-kelas.laporan');

    Route::get('/absensi-siswa-kelas/laporan-siswa', [AbsensiSiswaKelasController::class, 'laporanSiswa'])
        ->name('absensi-siswa-kelas.laporan-siswa');

    // View specific attendance
    Route::get('absensi-siswa-kelas/view', [AbsensiSiswaKelasController::class, 'view'])
        ->name('absensi-siswa-kelas.view');

    // Edit attendance
    Route::get('absensi-siswa-kelas/edit', [AbsensiSiswaKelasController::class, 'edit'])
        ->name('absensi-siswa-kelas.edit');

    // Update attendance
    Route::post('absensi-siswa-kelas/update', [AbsensiSiswaKelasController::class, 'update'])
        ->name('absensi-siswa-kelas.update');

    // Recap attendance
    Route::get('absensi-siswa-kelas/rekap', [AbsensiSiswaKelasController::class, 'rekap'])
        ->name('absensi-siswa-kelas.rekap');
});







