<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_orders', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('status')->default('CREATED')->index();
            $table->string('type')->default('katalog')->index();

            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            $table->text('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->unsignedBigInteger('destination_id')->nullable()->index();

            $table->string('shipping_id')->nullable();
            $table->unsignedBigInteger('shipping_cost')->default(0);

            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('total')->default(0);

            $table->json('items')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_orders');
    }
};

