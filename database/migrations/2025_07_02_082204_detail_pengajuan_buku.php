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
        Schema::create('detail_pengajuan_buku', function (Blueprint $table) {
            $table->bigIncrements('id_detail_pengajuan_buku');
            $table->unsignedBigInteger('id_pengajuan_buku');
            $table->unsignedBigInteger('id_buku');
            $table->tinyInteger('jumlah');
            $table->timestamps();

            $table->foreign('id_pengajuan_buku')->references('id_pengajuan_buku')->on('pengajuan_buku')->onDelete('cascade');
            $table->foreign('id_buku')->references('id_buku')->on('buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengajuan_buku');
    }
};
