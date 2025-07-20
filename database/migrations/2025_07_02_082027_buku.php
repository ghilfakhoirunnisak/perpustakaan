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
        Schema::create('buku', function (Blueprint $table) {
            $table->bigIncrements('id_buku');
            $table->string('cover')->nullable();
            $table->string('judul', 100);
            $table->string('isbn', 20)->nullable();
            $table->unsignedBigInteger('id_penulis_buku');
            $table->unsignedBigInteger('id_penerbit_buku');
            $table->text('genre');
            $table->year('tahun_terbit');
            $table->tinyInteger('stok');
            $table->text('sinopsis')->nullable();
            $table->timestamps();

            $table->foreign('id_penulis_buku')->references('id_penulis_buku')->on('penulis_buku')->onDelete('cascade');
            $table->foreign('id_penerbit_buku')->references('id_penerbit_buku')->on('penerbit_buku')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
