<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->foreignId('order_id')->constrained('sales')->onDelete('cascade');
            $table->string('shipping_method_id');
            $table->string('address');
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');

            $table->softDeletes();
            $table->timestamps();
        });
    }
};