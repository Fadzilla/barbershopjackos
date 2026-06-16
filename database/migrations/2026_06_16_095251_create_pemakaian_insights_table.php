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
        Schema::create('pemakaian_insight', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemakaian_id')->constrained('pemakaian')->onDelete('cascade');
            $table->string('nama_produk')->nullable();
            $table->integer('jumlah_pemakaian')->default(1);
            $table->text('analisis_ai'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_insight');
    }
};