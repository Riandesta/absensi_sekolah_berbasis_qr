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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_pelajaran_id'); // Foreign key ke jadwal_pelajaran
            $table->unsignedBigInteger('kelas_id'); // Foreign key ke kelas
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']); // Hari jadwal
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('jadwal_pelajaran_id')->references('id')->on('jadwal_pelajaran')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
