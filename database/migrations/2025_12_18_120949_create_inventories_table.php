<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('unit');
            $table->string('description');
            $table->string('image');
            $table->string('code');
            $table->string('serial');
            $table->string('location');
            $table->string('status');
            $table->string('type');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};