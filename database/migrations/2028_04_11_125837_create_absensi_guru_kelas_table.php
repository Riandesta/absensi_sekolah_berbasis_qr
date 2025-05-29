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
        Schema::create('absensi_guru_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->date('tanggal');
            $table->time('waktu_scan');
            $table->unsignedBigInteger('scan_by_user_id');
            $table->string('status')->default('tidak hadir');
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('kelas_id');

            $table->foreign('jadwal_id')->references('id')->on('jadwal') ->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas');
            $table->foreign('karyawan_id')->references('id')->on('karyawan');
            $table->foreign('scan_by_user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_guru_kelas');
    }
};
