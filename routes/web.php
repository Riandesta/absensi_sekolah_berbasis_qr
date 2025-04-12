<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\WalikelasController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\TahunAjaranController;
use App\Models\Jurusan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('/jurusan', JurusanController::class);
    Route::resource('/tahun-ajaran', TahunAjaranController::class);
    Route::resource('mata-pelajaran', MataPelajaranController::class);
    Route::resource('jadwal', JadwalPelajaranController::class);

    Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
        Route::get('/', [JadwalPelajaranController::class, 'index'])->name('index');
        Route::get('/create', [JadwalPelajaranController::class, 'create'])->name('create');
        Route::post('/', [JadwalPelajaranController::class, 'store'])->name('store');
        Route::get('/edit/{jadwalPelajaran}', [JadwalPelajaranController::class, 'edit'])->name('edit');
        Route::put('/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('update');
        Route::delete('/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('auth')->group(function () {
        Route::resource('siswa', SiswaController::class);
        Route::resource('karyawan', KaryawanController::class);
    });

    // ---------------------------------------------------- //

    // Rute untuk menampilkan daftar kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');

    // Rute untuk menampilkan form tambah kelas
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');

    // Rute untuk menyimpan kelas baru
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');

    // Rute untuk menampilkan form edit kelas
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');

    // Rute untuk mengupdate data kelas
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');

    // Rute untuk menghapus kelas
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // ---------------------------------------------------- //
});

// Siswa routes
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');
});

// Guru routes
Route::prefix('guru')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');
});

// Karyawan routes
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/dashboard', [KaryawanController::class, 'index'])->name('karyawan.dashboard');
});

// Kurikulum routes
Route::prefix('kurikulum')->middleware(['auth', 'role:kurikulum'])->group(function () {
    Route::get('/dashboard', [KurikulumController::class, 'index'])->name('kurikulum.dashboard');
});

// Walikelas routes
Route::prefix('walikelas')->middleware(['auth', 'role:walikelas'])->group(function () {
    Route::get('/dashboard', [WalikelasController::class, 'index'])->name('walikelas.dashboard');
});

// Siswa Routes
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    // Dashboard Siswa
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');

    // Histori Absensi Siswa
    Route::get('/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('siswa.absensi.histori');
});

// Guru Routes
Route::prefix('guru')->middleware(['auth', 'role:guru'])->group(function () {
    // Dashboard Guru
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('guru.dashboard');

    // Input Absensi Siswa
    Route::post('/input-absensi', [GuruController::class, 'inputAttendance'])->name('guru.input.absensi');

    // Histori Absensi Guru
    Route::get('/absensi-histori', [GuruController::class, 'attendanceHistory'])->name('guru.absensi.histori');
});

// Karyawan Routes
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {
    // Dashboard Karyawan
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');

    // Histori Absensi Karyawan
    Route::get('/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('karyawan.absensi.histori');
});

// Kurikulum Routes
Route::prefix('kurikulum')->middleware(['auth', 'role:kurikulum'])->group(function () {
    // Dashboard Kurikulum
    Route::get('/dashboard', [KurikulumController::class, 'dashboard'])->name('kurikulum.dashboard');

    // CRUD Mata Pelajaran
    Route::resource('mata-pelajaran', MataPelajaranController::class)->names('kurikulum.mata-pelajaran');

    // // CRUD Jadwal Pelajaran
    // Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)->names('kurikulum.jadwal-pelajaran');
});

// Walikelas Routes
Route::prefix('walikelas')->middleware(['auth', 'role:walikelas'])->group(function () {
    // Dashboard Wali Kelas
    Route::get('/dashboard', [WalikelasController::class, 'dashboard'])->name('walikelas.dashboard');

    // Laporan Absensi Siswa
    Route::get('/laporan-absensi', [WalikelasController::class, 'printReport'])->name('walikelas.laporan.absensi');
});

// General Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/templates', function () {
    return view('templates');
})->name('templates');
