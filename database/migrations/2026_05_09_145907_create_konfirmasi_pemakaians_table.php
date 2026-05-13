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
            $table->foreignId('pemakaian_id') ->constrained('pemakaian')->onDelete('cascade');
            $table->date('tgl_konfirmasi');
            $table->enum('status_konfirmasi', ['Pending','Disetujui','Ditolak'])->default('Pending');
            $table->decimal('total_pemakaian', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu_konfirmasi')->nullable();
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