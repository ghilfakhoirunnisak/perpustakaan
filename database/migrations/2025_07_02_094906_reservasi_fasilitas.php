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
        Schema::create('reservasi_fasilitas', function (Blueprint $table) {
            $table->bigIncrements('id_reservasi');
            $table->string('kode_reservasi')->unique();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_fasilitas');
            $table->date('tanggal_kegiatan');
            $table->date('tanggal_selesai');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['diproses', 'disetujui', 'dibatalkan', 'ditolak'])->default('diproses');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('user')->onDelete('cascade');
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservasi_fasilitas');
    }
};
