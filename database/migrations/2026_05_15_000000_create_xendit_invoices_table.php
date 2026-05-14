<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xendit_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('invoice_id')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->unsignedBigInteger('amount')->default(0);
            $table->text('invoice_url')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xendit_invoices');
    }
};

