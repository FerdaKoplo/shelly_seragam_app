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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('pengiriman_id');
            $table->foreignId('transaksi_id')
               ->references('transaksi_id')
                ->on('transaksi')
                ->onDelete('cascade');
            $table->text('alamat_pengiriman');
            $table->text('alamat_asal');
            $table->double('bobot_pengiriman');
            $table->enum('status_pengiriman', ['pending', 'dikirim', 'selesai']);
            $table->double('ongkir');
            $table->date('estimasi_pengiriman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
