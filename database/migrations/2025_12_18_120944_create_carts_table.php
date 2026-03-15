<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->decimal('total_after_discount', 15, 2);
            $table->decimal('tax', 15, 2);
            $table->decimal('total_after_tax', 15, 2);
            $table->decimal('shipping_cost', 15, 2);
            $table->decimal('total_after_shipping', 15, 2);
            $table->decimal('total_after_all', 15, 2);
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_delivered')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('cash');
           
            $table->softDeletes();
            $table->timestamps();
        });
    }
};