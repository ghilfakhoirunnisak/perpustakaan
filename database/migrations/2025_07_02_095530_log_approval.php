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
        Schema::create('log_approval', function (Blueprint $table) {
            $table->bigIncrements('id_log_approval');
            $table->unsignedBigInteger('id_reservasi');
            $table->unsignedBigInteger('id_verifikator');
            $table->enum('status', ['disetujui', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_reservasi')->references('id_reservasi')->on('reservasi_fasilitas')->onDelete('cascade');
            $table->foreign('id_verifikator')->references('id_verifikator')->on('verifikator')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_approval');
    }
};
