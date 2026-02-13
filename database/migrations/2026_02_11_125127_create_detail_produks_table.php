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
        Schema::create('detail_produk', function (Blueprint $table) {
            $table->id('detail_produk_id');
            $table->foreignId('produk_id')
               ->references('produk_id')
                ->on('produk')
                ->onDelete('cascade');
            $table->string('nama_detail');
            $table->string('deskripsi_detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produk');
    }
};
