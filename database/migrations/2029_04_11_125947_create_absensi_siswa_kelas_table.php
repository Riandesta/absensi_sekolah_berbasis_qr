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
                $table->unsignedBigInteger('absen_gerbang_id')->nullable(   );

                $table->foreign('siswa_id')->references('id')->on('siswa');
                $table->foreign('jadwal_id')->references('id')->on('jadwal') ->onDelete('cascade');
                $table->foreign('input_by')->references('id')->on('users');
                $table->foreign('absen_gerbang_id')->references('id')->on('absensi_gerbang');
                $table->timestamps();

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
