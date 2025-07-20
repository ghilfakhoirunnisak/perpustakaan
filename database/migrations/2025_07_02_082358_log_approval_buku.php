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
        Schema::create('log_approval_buku', function (Blueprint $table) {
            $table->bigIncrements('id_log_approval_buku');
            $table->unsignedBigInteger('id_pengajuan_buku');
            $table->unsignedBigInteger('id_verifikator');
            $table->enum('status', ['disetujui', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_pengajuan_buku')->references('id_pengajuan_buku')->on('pengajuan_buku')->onDelete('cascade');
            $table->foreign('id_verifikator')->references('id_verifikator')->on('verifikator')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_approval_buku');
    }
};
