<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('commission_type');
                ;

            $table->decimal('commission', 10, 2);
            $table->decimal('order_amount', 10, 2);
            $table->decimal('tax_amount', 10, 2);

            $table->date('date');
            $table->string('status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
