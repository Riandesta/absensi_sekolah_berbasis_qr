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
       Schema::create('absensi_siswa_kelas', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('siswa_id');
    $table->unsignedBigInteger('jadwal_id');
    $table->date('tanggal');
    $table->string('status')->default('Tidak Hadir');
    $table->unsignedBigInteger('input_by');
    // Tambahan user_type
    $table->unsignedBigInteger('absen_gerbang_id')->nullable(); // This can be null if not present

    // Define the foreign key relationships
    $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade'); // Ensure cascade delete for students
    $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('cascade'); // Ensure cascade delete for schedules
    $table->foreign('input_by')->references('id')->on('users')->onDelete('cascade'); // Ensure cascade delete for users
    $table->foreign('absen_gerbang_id')->references('id')->on('absensi_gerbang')->onDelete('set null'); // Set null if absensi_gerbang is deleted

    $table->timestamps(); // Automatically adds created_at and updated_at
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswa_kelas');
    }
};
