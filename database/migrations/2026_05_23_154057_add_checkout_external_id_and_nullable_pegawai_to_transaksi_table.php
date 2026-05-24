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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('pegawai_id')->nullable()->change();
            $table->string('checkout_external_id')->nullable()->unique()->after('transaksi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropUnique(['checkout_external_id']);
            $table->dropColumn('checkout_external_id');
            $table->foreignId('pegawai_id')->nullable(false)->change();
        });
    }
};
