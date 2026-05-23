<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations.
     */
    public function up(): void
    {
        Schema::create('pengiriman_emails', function (Blueprint $table) {

            $table->id();

            $table->foreignId('retur_id')
                ->constrained('returs')
                ->cascadeOnDelete();

            $table->string('email_tujuan');

            $table->string('subjek');

            $table->text('pesan');

            $table->string('status');

            $table->timestamps();

        });
    }

    /**
     * Reverse migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_emails');
    }
};