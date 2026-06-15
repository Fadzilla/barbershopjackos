<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')
                  ->constrained('jurnal')
                  ->onDelete('cascade');
            $table->foreignId('coa_id')
                  ->constrained('coa')
                  ->onDelete('restrict');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->timestamps();
            
            // Index untuk performance
            $table->index('jurnal_id');
            $table->index('coa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_detail');
    }
};