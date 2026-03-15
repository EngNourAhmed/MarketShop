<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('purchase_date');
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->decimal('paid', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->string ('payment_method');
            $table->string ('payment_status');
            $table->string ('payment_reference');
            $table->string ('payment_note');
            $table->string ('payment_method_reference');
            $table->string ('payment_method_note');
            $table->string ('payment_method_reference_note');

            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }
};