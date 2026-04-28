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
        Schema::create('pemakaian', function (Blueprint $table) {
            $table->id();

            // Relasi ke pegawai (yang pakai barang)
            $table->foreignId('pegawai_id')
                  ->constrained('pegawai')
                  ->onDelete('cascade');

            $table->string('no_pemakaian'); 
            $table->string('status'); 
            $table->datetime('tgl'); 

            // nilai total pemakaian
            $table->decimal('total', 15, 2);

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian');
    }
};