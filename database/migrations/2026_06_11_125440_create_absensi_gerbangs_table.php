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
            $table->time('waktu_scan'); // Waktu saat scan dilakukan
            $table->enum('status', ['Hadir', 'Terlambat', 'Tidak Hadir']); // Status absensi
            $table->unsignedBigInteger('scanned_by'); // Referensi ke tabel users (siapa yang mencatat absensi)
            $table->timestamps();

            // Foreign Keys
            $table->foreign('scanned_by')->references('id')->on('users')->onDelete('cascade');
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
