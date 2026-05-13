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
    Schema::create('produks', function (Blueprint $table) {
        $table->id();
        $table->string('nama_produk'); // Nama produk
        $table->enum('status', ['Tersedia', 'Habis']); // Status produk
        $table->text('deskripsi_produk'); // Deskripsi produk
        $table->date('tanggal_masuk'); // Tanggal input 
        $table->string('foto_produk'); // Upload foto (jpg/png) s
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
