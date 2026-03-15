<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->decimal('balance_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('payment_note')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('payment_gateway_id')->nullable();
            $table->string('payment_gateway_reference')->nullable();
            $table->string('payment_gateway_status')->nullable();

            $table->softDeletes();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();

            $table->timestamps();
        });
    }
};