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
        Schema::create('order_transaksi_kustom', function (Blueprint $table) {
            $table->id('order_kustom_id');
            $table->foreignId('transaksi_id')
               ->references('transaksi_id')
                ->on('transaksi')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->string('ukuran_dipilih');
            $table->enum('tipe_kustom', ['Bundle', 'Atasan', 'Bawahan']);
            $table->text('catatan');
            $table->text('detail_pilihan_kustomisasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_transaksi_kustom');
    }
};
