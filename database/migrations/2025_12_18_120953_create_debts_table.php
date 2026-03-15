<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            $table->string('description');
            $table->string('type');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->string('status');
            $table->timestamps();
        });
    }
};