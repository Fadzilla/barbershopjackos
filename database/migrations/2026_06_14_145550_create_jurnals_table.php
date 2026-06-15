<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('no_jurnal')->unique();
            $table->date('tanggal');
            $table->string('no_ref')->nullable();
            $table->string('sumber'); // pendapatan, penjualan, pembelian, pemakaian, retur
            $table->unsignedBigInteger('sumber_id');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['sumber', 'sumber_id']);
            $table->index('no_ref');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};