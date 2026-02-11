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
        Schema::create('pilihan_detail_produk', function (Blueprint $table) {
            $table->id('pilihan_detail_id');
            $table->foreignId('detail_produk_id')
               ->references('detail_produk_id')
                ->on('detail_produk')
                ->onDelete('cascade');
            $table->string('opsi');
            $table->double('pengaruh_harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pilihan_detail_produk');
    }
};
