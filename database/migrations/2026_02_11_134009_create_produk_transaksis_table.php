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
        Schema::create('produk_transaksi', function (Blueprint $table) {
            $table->id('produk_transaksi_id');
            $table->foreignId('transaksi_id')
               ->references('transaksi_id')
                ->on('transaksi')
                ->onDelete('cascade');
            $table->foreignId('produk_id')
               ->references('produk_id')
                ->on('produk')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->string('size');
            $table->double('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_transaksi');
    }
};
