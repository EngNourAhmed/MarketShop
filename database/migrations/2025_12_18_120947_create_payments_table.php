<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('payment_reference');
            $table->string('payment_url');
            $table->string('payment_token');
            $table->string('payment_signature');
            $table->string('payment_ip');
            $table->string('payment_ip_address');
            $table->string('payment_ip_country');
            $table->string('payment_ip_city');
            $table->string('payment_ip_state');
            $table->string('payment_ip_zip');

            $table->softDeletes();

            $table->timestamps();
        });
    }
};