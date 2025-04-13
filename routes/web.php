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
    PetugasPiketController,
    TahunAjaranController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==============================
// Auth Routes
// ==============================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================
// General Routes
// ==============================
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/templates', fn () => view('templates'))->name('templates');

// ==============================
// Admin Routes
// ==============================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Jurusan & Tahun Ajaran
    Route::resource('/jurusan', JurusanController::class);
    Route::resource('/tahun-ajaran', TahunAjaranController::class);

    // Mata Pelajaran & Jadwal
    Route::resource('mata-pelajaran', MataPelajaranController::class);
    Route::resource('jadwal', JadwalPelajaranController::class);

    // Siswa QR
    Route::prefix('siswa')->group(function () {
        Route::get('/{siswa}/download-qrcode', [SiswaController::class, 'downloadQrCode'])->name('siswa.download-qrcode');
    });

    // Karyawan Routes
    Route::prefix('karyawan')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'index'])->name('karyawan.dashboard');
        Route::resource('karyawan', KaryawanController::class)->names('karyawan');
        Route::get('/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('karyawan.download-qrcode');
        Route::get('/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('karyawan.download-qrcode-only');
        Route::get('/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('karyawan.absensi.histori');
    });

    // Absensi Gerbang
    Route::prefix('absensi-gerbang')->name('absensi-gerbang.')->group(function () {
        Route::get('/', [AbsensiGerbangController::class, 'index'])->name('index');
        Route::get('/scan', [AbsensiGerbangController::class, 'scan'])->name('scan');
        Route::post('/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('scan-process');
        Route::post('/store', [AbsensiGerbangController::class, 'store'])->name('store');
        Route::delete('/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('destroy');
    });

    // Petugas Piket
    Route::prefix('petugas-piket')->name('petugas-piket.')->group(function () {
        Route::get('/', [PetugasPiketController::class, 'index'])->name('index');
        Route::get('/create', [PetugasPiketController::class, 'create'])->name('create');
        Route::post('/store', [PetugasPiketController::class, 'store'])->name('store');
        Route::get('/edit/{petugasPiket}', [PetugasPiketController::class, 'edit'])->name('edit');
        Route::put('/update/{petugasPiket}', [PetugasPiketController::class, 'update'])->name('update');
        Route::delete('/destroy/{petugasPiket}', [PetugasPiketController::class, 'destroy'])->name('destroy');
    });

    // Kelas
    Route::prefix('kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index'])->name('kelas.index');
        Route::get('/create', [KelasController::class, 'create'])->name('kelas.create');
        Route::post('/', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    });

    // Resource
    Route::middleware('auth')->group(function () {
        Route::resource('siswa', SiswaController::class);
        Route::resource('karyawan', KaryawanController::class);
    });
});

// ==============================
// Siswa Routes
// ==============================
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('siswa.absensi.histori');
});

// ==============================
// Guru Routes
// ==============================
Route::prefix('guru')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');
    Route::post('/input-absensi', [GuruController::class, 'inputAttendance'])->name('guru.input.absensi');
    Route::get('/absensi-histori', [GuruController::class, 'attendanceHistory'])->name('guru.absensi.histori');
});

// ==============================
// Karyawan Routes
// ==============================
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    Route::get('/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('karyawan.absensi.histori');
});

// ==============================
// Kurikulum Routes
// ==============================
Route::prefix('kurikulum')->middleware(['auth', 'role:kurikulum'])->group(function () {
    Route::get('/dashboard', [KurikulumController::class, 'dashboard'])->name('kurikulum.dashboard');
    Route::resource('mata-pelajaran', MataPelajaranController::class)->names('kurikulum.mata-pelajaran');
    // Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)->names('kurikulum.jadwal-pelajaran');
});

// ==============================
// Walikelas Routes
// ==============================
Route::prefix('walikelas')->middleware(['auth', 'role:walikelas'])->group(function () {
    Route::get('/dashboard', [WalikelasController::class, 'dashboard'])->name('walikelas.dashboard');
    Route::get('/laporan-absensi', [WalikelasController::class, 'printReport'])->name('walikelas.laporan.absensi');
});
