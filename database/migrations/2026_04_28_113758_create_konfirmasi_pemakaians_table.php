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
        Schema::create('konfirmasi_pemakaian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pemakaian_id')
                  ->constrained('pemakaian')
                  ->onDelete('cascade');

            $table->dateTime('tgl_konfirmasi')->nullable();
            $table->string('status'); 
            $table->text('catatan')->nullable(); 
            $table->string('disetujui_oleh')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_pemakaian');
    }
};