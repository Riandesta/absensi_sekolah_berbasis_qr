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
        Schema::create('petugas_piket', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id'); // Referensi ke tabel karyawan
            $table->date('tanggal'); // Tanggal petugas piket bertugas
            $table->enum('shift', ['Pagi', 'Siang', 'Sore']); // Shift petugas piket
            $table->text('keterangan')->nullable(); // Catatan tambahan (opsional)
            $table->timestamps();

            // Foreign Key
            $table->foreign('karyawan_id')->references('id')->on('karyawan')->onDelete('cascade');

            // Unique Constraint
            // $table->unique(['karyawan_id', 'tanggal', 'shift']); // Memastikan tidak ada duplikasi jadwal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petugas_pikets');
    }
};
