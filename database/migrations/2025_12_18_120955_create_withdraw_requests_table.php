<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Symfony\Component\Clock\now;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('customer_id')->foreign()->references('id')->on('customers')->onDelete('cascade');

            $table->string('currency');

            $table->integer('points');

            $table->string('description');
            $table->string('payment_method');

            $table->decimal('amount', 10, 2);
            $table->string('reference');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('bank_address');
            $table->string('bank_city');

            $table->string('status');

            $table->integer('approved_by')->foreign()->references('id')->on('users')->onDelete('cascade');
            $table->string('approved_at');

            $table->integer('rejected_by')->foreign()->references('id')->on('users')->onDelete('cascade');
            $table->string('rejected_at');

          
            $table->softDeletes();
            $table->timestamps();
        });
    }
};