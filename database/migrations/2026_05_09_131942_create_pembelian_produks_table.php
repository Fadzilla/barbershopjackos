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
        Schema::create('pembelian_produks', function (Blueprint $table) {

            $table->id();

            // relasi ke pegawai
            $table->foreignId('pegawai_id')
                ->constrained('pegawais')
                ->cascadeOnDelete();

            // relasi ke produk
            $table->foreignId('produk_id')
                ->constrained('produks')
                ->cascadeOnDelete();

            // tanggal pembelian
            $table->date('tanggal');

            // harga per unit
            $table->decimal('harga_per_unit', 15, 2);

            // total harga
            $table->decimal('total', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_produks');
    }
};