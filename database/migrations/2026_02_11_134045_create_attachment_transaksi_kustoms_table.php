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
        Schema::create('attachment_transaksi_kustom', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->foreignId('order_kustom_id')
               ->references('order_kustom_id')
                ->on('order_transaksi_kustom')
                ->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_transaksi_kustom');
    }
};
