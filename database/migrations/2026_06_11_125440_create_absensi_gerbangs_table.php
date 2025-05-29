<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_gerbang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('related_id'); // ID dari siswa/karyawan
            $table->date('tanggal'); // Tanggal absensi
            $table->time('waktu_scan_masuk'); // Waktu saat scan dilakukan
            $table->time('waktu_scan_keluar')->nullable(); // Waktu saat scan dilakukan
            $table->string('status')->default('Tidak Hadir');
            $table->unsignedBigInteger('scanned_by'); // Referensi ke tabel users (siapa yang mencatat absensi)
            $table->unsignedBigInteger('jadwal_id')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('related_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('cascade');
        });
    }
    // Unique Constraint
    // $table->unique(['karyawan_id', 'tanggal', 'waktu_scan']); // Memastikan tidak ada duplikasi absensi

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_gerbangs');
    }
};
