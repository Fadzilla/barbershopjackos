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
        Schema::create('pengiriman_emails', function (Blueprint $table) {
            $table->id();

            $table->string('nama_pengirim');
            $table->string('email_pengirim');

            $table->string('nama_penerima');
            $table->string('email_penerima');

            $table->string('subject');
            $table->text('pesan');

            $table->string('attachment')->nullable();

            $table->enum('status', ['pending', 'terkirim', 'gagal'])
                  ->default('pending');

            $table->timestamp('tanggal_kirim')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_emails');
    }
};