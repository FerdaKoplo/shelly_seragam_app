<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('katalog_id')
                ->nullable()
                ->references('katalog_id')
                ->on('produk_katalog')
                ->onDelete('cascade');
            $table->string('nama_voucher');
            $table->text('deskripsi');
            $table->string('kode_voucher')->unique();
            $table->decimal('nilai_diskon', 8, 2);
            $table->enum('jenis_voucher', ['persentase', 'nominal']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->enum('status',['Aktif', 'Habis'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
