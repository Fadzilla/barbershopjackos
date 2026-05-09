<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {

            $table->id();

            $table->string('nama_produk');

            $table->enum('status', [
                'Tersedia',
                'Habis'
            ]);

            $table->integer('stok')
                ->default(0);

            $table->text('deskripsi_produk')
                ->nullable();

            $table->date('tanggal_masuk');

            $table->string('foto_produk')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};