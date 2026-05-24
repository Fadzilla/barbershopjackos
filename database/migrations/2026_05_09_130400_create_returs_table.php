<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returs', function (Blueprint $table) {

            $table->id();

            // kode retur
            $table->string('kode_retur')->unique();

            // relasi pegawai
            $table->foreignId('pegawai_id')
                ->constrained('pegawai')
                ->cascadeOnDelete();

            // relasi produk
            $table->foreignId('produk_id')
                ->constrained('produk')
                ->cascadeOnDelete();

            // status retur
            $table->enum('status', [
                'Diproses',
                'Selesai'
            ])->default('Diproses');

            // alasan retur
            $table->enum('alasan', [
                'Salah Kirim',
                'Barang Rusak'
            ]);

            // harga
            $table->decimal('harga_per_unit', 12, 2);

            // qty
            $table->integer('qty');

            // total
            $table->decimal('total', 12, 2)
                ->default(0);

            // tanggal retur
            $table->date('tanggal_retur');

            // foto
            $table->string('foto')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returs');
    }
};