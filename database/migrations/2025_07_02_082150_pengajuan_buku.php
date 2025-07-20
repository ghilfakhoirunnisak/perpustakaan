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
        Schema::create('pengajuan_buku', function (Blueprint $table) {
            $table->bigIncrements('id_pengajuan_buku');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_lengkap', 100);
            $table->text('alamat');
            $table->enum('status', ['diproses', 'disetujui', 'ditolak'])->default('diproses');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_buku');
    }
};
