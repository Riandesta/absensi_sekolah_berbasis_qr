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
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_id')->nullable(); // Relasi dengan pengajar
            $table->unsignedBigInteger('mata_pelajaran_id'); // Relasi dengan mata pelajaran
            $table->unsignedBigInteger('tahun_ajaran_id'); // Relasi dengan tahun ajaran
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('guru_id')->references('id')->on('karyawan');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajaran');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
