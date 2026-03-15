<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->string('role')->default('customer');
            $table->string('remember_token')->nullable();
         
            $table->string('type')->default('customer');
            $table->string('country_code')->nullable();
            $table->softDeletes();
            $table->integer('added_by')->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
};