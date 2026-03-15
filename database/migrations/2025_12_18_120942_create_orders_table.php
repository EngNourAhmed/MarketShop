<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->decimal('subtotal', 20, 2);
            $table->decimal('tax', 20, 2);
            $table->decimal('shipping', 20, 2);
            $table->decimal('discount', 20, 2);
            $table->decimal('total', 20, 2);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('shipping_method');
            $table->string('shipping_status');
            $table->string('note');
            $table->string('payment_code');
            $table->string('shipping_code');
             $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }
};