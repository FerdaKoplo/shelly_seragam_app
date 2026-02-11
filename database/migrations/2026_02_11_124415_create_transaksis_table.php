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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->foreignId('user_id')
               ->references('user_id')
                ->on('user')
                ->onDelete('cascade');
            $table->string('nama_customer');
            $table->string('no_hp_customer');
            $table->text('alamat_customer');
            $table->string('no_resi_customer');
            $table->enum('status', ["Created", "Paid", "Delivered", "Done"])->default('Created');
            $table->date('tanggal_transaksi');
            $table->double('total_harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
