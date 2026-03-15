<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('card_type');
            $table->string('card_holder');
            $table->string('cvv');
            $table->string('expiry_date');
            $table->string('type');


            $table->integer('points')->default(0);
            $table->integer('points_used')->default(0);
            $table->integer('points_remaining')->default(0);
            $table->integer('points_spent')->default(0);
            $table->integer('points_spent_only')->default(0);

            $table->integer('points_spent_on')->default(0);

            $table->decimal('amount', 10, 2)->default(0);

            $table->decimal('price_in_eg', 10, 2)->default(0);
            $table->decimal('price_in_us', 10, 2)->default(0);
            $table->decimal('price_in_uk', 10, 2)->default(0);

            $table->decimal('balance', 10, 2);
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->timestamps();
        });
    }
};