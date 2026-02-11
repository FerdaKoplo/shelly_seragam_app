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
        Schema::create('produk_kustom', function (Blueprint $table) {
            $table->id('kustom_id');
            $table->foreignId('produk_id')
               ->references('produk_id')
                ->on('produk')
                ->onDelete('cascade');
            $table->enum('spesifikasi_khusus', ['Bundle', 'Atasan', 'Bawahan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_kustom');
    }
};
