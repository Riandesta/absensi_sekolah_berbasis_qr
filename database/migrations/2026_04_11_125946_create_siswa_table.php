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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->string('nama_lengkap');
            $table->string('jenis_kelamin');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->string('no_wa')->nullable();
            $table->string('foto')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('qr_code')->nullable();
            $table->foreign('kelas_id')->references('id')->on('kelas');
            $table->foreign('jurusan_id')->references('id')->on('jurusan');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
