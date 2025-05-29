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
    LaporanAbsensiSiswaKelasController,
    AbsensiRiwayatController
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
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::get('/jurusan/create', [JurusanController::class, 'create'])->name('jurusan.create');
    Route::post('/jurusan/store', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::get('/jurusan/edit/{jurusan}', [JurusanController::class, 'edit'])->name('jurusan.edit');
    Route::put('/jurusan/update/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/delete/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
    Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun-ajaran.create');
    Route::post('/tahun-ajaran/store', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
    Route::get('/tahun-ajaran/edit/{tahunAjaran}', [TahunAjaranController::class, 'edit'])->name('tahun-ajaran.edit');
    Route::put('/tahun-ajaran/update/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
    Route::delete('/tahun-ajaran/delete/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');

    // Mata Pelajaran
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
    Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('mata-pelajaran.create');
    Route::post('/mata-pelajaran/store', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
    Route::get('/mata-pelajaran/edit/{id}', [MataPelajaranController::class, 'edit'])->name('mata-pelajaran.edit');
    Route::put('/mata-pelajaran/update/{id}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
    Route::delete('/mata-pelajaran/delete/{id}', [MataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');

    // Jadwal Pelajaran
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('admin.jadwal-pelajaran.index');
    Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('admin.jadwal-pelajaran.create');
    Route::post('/jadwal-pelajaran/store', [JadwalPelajaranController::class, 'store'])->name('admin.jadwal-pelajaran.store');
    Route::get('/jadwal-pelajaran/edit/{jadwalPelajaran}', [JadwalPelajaranController::class, 'edit'])->name('admin.jadwal-pelajaran.edit');
    Route::put('/jadwal-pelajaran/update/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('admin.jadwal-pelajaran.update');
    Route::delete('/jadwal-pelajaran/delete/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('admin.jadwal-pelajaran.destroy');

    // Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa/store', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/edit/{siswa}', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/update/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/delete/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/absensi-histori', [SiswaController::class, 'attendanceHistory'])->name('admin.siswa.absensi.histori');

    // Admin can download QR codes for any student using admin routes
    Route::get('/siswa/{siswa}/download-qrcode', [SiswaController::class, 'downloadQrCode'])->name('admin.siswa.download-qrcode');
    Route::get('/siswa/{siswa}/download-qrcode-only', [SiswaController::class, 'downloadQrCodeOnly'])->name('admin.siswa.download-qrcode-only');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas/store', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/edit/{kelas}', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/update/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/delete/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/edit/{karyawan}', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/update/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/delete/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    // Admin can download QR codes for any karyawan using admin routes
    Route::get('/karyawan/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('admin.karyawan.download-qrcode');
    Route::get('/karyawan/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('admin.karyawan.download-qrcode-only');
    Route::get('/karyawan/absensi-histori', [KaryawanController::class, 'attendanceHistory'])->name('admin.karyawan.absensi.histori');

    // Petugas Piket
    Route::get('/petugas-piket', [PetugasPiketController::class, 'index'])->name('admin.petugas-piket.index');
    Route::get('/petugas-piket/create', [PetugasPiketController::class, 'create'])->name('admin.petugas-piket.create');
    Route::post('/petugas-piket', [PetugasPiketController::class, 'store'])->name('admin.petugas-piket.store');
    Route::get('/petugas-piket/{petugasPiket}/edit', [PetugasPiketController::class, 'edit'])->name('admin.petugas-piket.edit');
    Route::put('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'update'])->name('admin.petugas-piket.update');
    Route::delete('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'destroy'])->name('admin.petugas-piket.destroy');

    // Absensi Gerbang
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('admin.absensi-gerbang.index');
    Route::get('/absensi-gerbang/scan', [AbsensiGerbangController::class, 'scan'])->name('admin.absensi-gerbang.scan');
    Route::post('/absensi-gerbang/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('admin.absensi-gerbang.scan-process');
    Route::post('/absensi-gerbang/store', [AbsensiGerbangController::class, 'store'])->name('admin.absensi-gerbang.store');
    Route::post('/absensi-gerbang/export-pdf', [AbsensiGerbangController::class, 'exportPdf'])->name('admin.absensi-gerbang.export-pdf');
    Route::delete('/absensi-gerbang/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('admin.absensi-gerbang.destroy');

    // Absensi Guru Kelas
    Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('admin.absensi-guru-kelas.index');
    Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('admin.absensi-guru-kelas.scan');
    Route::post('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scanProcess'])->name('admin.absensi-guru-kelas.scan-process');
    Route::get('/absensi-guru-kelas/show/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('admin.absensi-guru-kelas.show');
    Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('admin.absensi-guru-kelas.destroy');
    Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('admin.absensi-guru-kelas.report');
    Route::get('/absensi-guru-kelas/export-pdf', [AbsensiGuruKelasController::class, 'exportPdf'])->name('admin.absensi-guru-kelas.export-pdf');
    Route::get('/absensi-guru-kelas/export', [AbsensiGuruKelasController::class, 'export'])->name('admin.absensi-guru-kelas.export');

    // Absensi Siswa Kelas
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

    // Import Absensi Siswa (untuk Admin)
    Route::get('/import-absensi', [AbsensiRiwayatController::class, 'importForm'])->name('admin.import-absensi-form');
    Route::post('/import-absensi', [AbsensiRiwayatController::class, 'import'])->name('admin.import-absensi');
    Route::get('/template-download', [AbsensiRiwayatController::class, 'downloadTemplate'])->name('admin.template-download');

    // Riwayat Absensi (untuk Admin)
    Route::get('/riwayat-absensi', [AbsensiRiwayatController::class, 'index'])->name('admin.riwayat-absensi');
    Route::get('/riwayat-absensi/export-pdf', [AbsensiRiwayatController::class, 'exportPdf'])->name('admin.riwayat-absensi.export-pdf');

    // Jadwal Pelajaran Siswa - Admin view
    Route::get('/jadwal-pelajaran-siswa', [JadwalPelajaranController::class, 'indexSiswa'])->name('admin.jadwal-pelajaran-siswa.index');

});

// ==============================
// Siswa Route
// ==============================
Route::prefix('siswa')->middleware(['auth', 'role:siswa'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');

    // Absensi Gerbang
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('siswa.absensi-gerbang.index');

    // QR Code - Each siswa can only download their own QR code, not others
    Route::get('/download-qrcode', [SiswaController::class, 'downloadMyQrCode'])->name('siswa.download-qrcode');

    // Profil
    Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
    Route::post('/profile/update', [SiswaController::class, 'updateProfile'])->name('siswa.profile.update');

    // Riwayat Absensi
    Route::get('/riwayat-absensi-siswa', [SiswaController::class, 'attendanceHistory'])->name('siswa.riwayat-absensi-persiswa');
    Route::get('/attendance-export-pdf', [SiswaController::class, 'exportAttendancePdf'])->name('siswa.attendance-export-pdf');

    // Jadwal Pelajaran
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('siswa.jadwal-pelajaran.index');
});

// ==============================
// Karyawan Route
// ==============================
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');

    // Profil
    Route::get('/profile', [AbsensiRiwayatController::class, 'profile'])->name('karyawan.profile');
    Route::post('/profile/update', [AbsensiRiwayatController::class, 'updateProfile'])->name('karyawan.profile.update');

    // Kehadiran
    Route::get('/kehadiran', [KaryawanController::class, 'attendance'])->name('karyawan.attendance.track');

    // Download QR - Each karyawan can only download their own QR code
    Route::get('/{karyawan}/download-qrcode', [KaryawanController::class, 'downloadQrCode'])->name('karyawan.download-qrcode');
    Route::get('/{karyawan}/download-qrcode-only', [KaryawanController::class, 'downloadQrCodeOnly'])->name('karyawan.download-qrcode-only');

    // Petugas Piket
    Route::get('/petugas-piket', [PetugasPiketController::class, 'index'])->name('karyawan.petugas-piket.index');
    Route::get('/petugas-piket/create', [PetugasPiketController::class, 'create'])->name('karyawan.petugas-piket.create');
    Route::post('/petugas-piket', [PetugasPiketController::class, 'store'])->name('karyawan.petugas-piket.store');
    Route::get('/petugas-piket/{petugasPiket}/edit', [PetugasPiketController::class, 'edit'])->name('karyawan.petugas-piket.edit');
    Route::put('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'update'])->name('karyawan.petugas-piket.update');
    Route::delete('/petugas-piket/{petugasPiket}', [PetugasPiketController::class, 'destroy'])->name('karyawan.petugas-piket.destroy');

    // Absensi Gerbang
    Route::get('/absensi-gerbang', [AbsensiGerbangController::class, 'index'])->name('karyawan.absensi-gerbang.index');
    Route::get('/absensi-gerbang/scan', [AbsensiGerbangController::class, 'scan'])->name('karyawan.absensi-gerbang.scan');
    Route::post('/absensi-gerbang/scan-process', [AbsensiGerbangController::class, 'scanProcess'])->name('karyawan.absensi-gerbang.scan-process');
    Route::post('/absensi-gerbang/store', [AbsensiGerbangController::class, 'store'])->name('karyawan.absensi-gerbang.store');
    Route::post('/absensi-gerbang/export-pdf', [AbsensiGerbangController::class, 'exportPdf'])->name('karyawan.absensi-gerbang.export-pdf');
    Route::delete('/absensi-gerbang/destroy/{absensiGerbang}', [AbsensiGerbangController::class, 'destroy'])->name('karyawan.absensi-gerbang.destroy');

    // Absensi Siswa Kelas
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

    // Absensi Guru Kelas
    Route::get('/absensi-guru-kelas', [AbsensiGuruKelasController::class, 'index'])->name('karyawan.absensi-guru-kelas.index');
    Route::get('/absensi-guru-kelas/scan', [AbsensiGuruKelasController::class, 'scan'])->name('karyawan.absensi-guru-kelas.scan');
    Route::post('/absensi-guru-kelas/scan-process', [AbsensiGuruKelasController::class, 'scanProcess'])->name('karyawan.absensi-guru-kelas.scan-process');
    Route::get('/absensi-guru-kelas/report', [AbsensiGuruKelasController::class, 'report'])->name('karyawan.absensi-guru-kelas.report');
    Route::get('/absensi-guru-kelas/export-pdf', [AbsensiGuruKelasController::class, 'exportPdf'])->name('karyawan.absensi-guru-kelas.export-pdf');
    Route::get('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'show'])->name('karyawan.absensi-guru-kelas.show');
    Route::delete('/absensi-guru-kelas/{absensiGuruKelas}', [AbsensiGuruKelasController::class, 'destroy'])->name('karyawan.absensi-guru-kelas.destroy');

    // Mata Pelajaran
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('karyawan.mata-pelajaran.index');
    Route::get('/mata-pelajaran/create', [MataPelajaranController::class, 'create'])->name('karyawan.mata-pelajaran.create');
    Route::post('/mata-pelajaran/store', [MataPelajaranController::class, 'store'])->name('karyawan.mata-pelajaran.store');
    Route::get('/mata-pelajaran/edit/{id}', [MataPelajaranController::class, 'edit'])->name('karyawan.mata-pelajaran.edit');
    Route::put('/mata-pelajaran/update/{id}', [MataPelajaranController::class, 'update'])->name('karyawan.mata-pelajaran.update');
    Route::delete('/mata-pelajaran/delete/{id}', [MataPelajaranController::class, 'destroy'])->name('karyawan.mata-pelajaran.delete');

    // Jadwal Pelajaran
    Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('karyawan.jadwal-pelajaran.index');
    Route::get('/jadwal-pelajaran/create', [JadwalPelajaranController::class, 'create'])->name('karyawan.jadwal-pelajaran.create');
    Route::post('/jadwal-pelajaran/store', [JadwalPelajaranController::class, 'store'])->name('karyawan.jadwal-pelajaran.store');
    Route::get('/jadwal-pelajaran/edit/{jadwalPelajaran}', [JadwalPelajaranController::class, 'edit'])->name('karyawan.jadwal-pelajaran.edit');
    Route::put('/jadwal-pelajaran/update/{jadwalPelajaran}', [JadwalPelajaranController::class, 'update'])->name('karyawan.jadwal-pelajaran.update');
    Route::delete('/jadwal-pelajaran/delete/{jadwalPelajaran}', [JadwalPelajaranController::class, 'destroy'])->name('karyawan.jadwal-pelajaran.delete');

    // Riwayat Absensi (untuk Guru, Wali Kelas dan Kurikulum)
    Route::get('/riwayat-absensi', [AbsensiRiwayatController::class, 'index'])->name('karyawan.riwayat-absensi');
    Route::get('/riwayat-absensi/export-pdf', [AbsensiRiwayatController::class, 'exportPdf'])->name('karyawan.riwayat-absensi.export-pdf');
    

    // Laporan Absensi untuk Wali Kelas dan Kurikulum
    Route::get('/laporan-absensi-gerbang', [AbsensiRiwayatController::class, 'laporanAbsensiGerbang'])->name('karyawan.laporan-absensi-gerbang');
    Route::get('/laporan-absensi-siswa', [AbsensiRiwayatController::class, 'laporanAbsensiSiswa'])->name('karyawan.laporan-absensi-siswa');
    Route::get('/laporan-absensi-guru-kelas', [AbsensiRiwayatController::class, 'laporanAbsensiGuruKelas'])->name('karyawan.laporan-absensi-guru-kelas');

    // Import Absensi Siswa (khusus Guru)
    Route::get('/import-absensi', [AbsensiRiwayatController::class, 'importForm'])->name('karyawan.import-absensi-form');
    Route::post('/import-absensi', [AbsensiRiwayatController::class, 'import'])->name('karyawan.import-absensi');
    Route::get('/template-download', [AbsensiRiwayatController::class, 'downloadTemplate'])->name('karyawan.template-download');
});


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
// ==============================
