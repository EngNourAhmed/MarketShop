<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('image')->nullable();
            $table->string('size')->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('featured')->default(0);
            $table->boolean('new')->default(0);
            $table->boolean('sale')->default(0);
            $table->boolean('hot')->default(0);
            $table->boolean('best_seller')->default(0);
            $table->boolean('best_rated')->default(0);
            $table->boolean('best_viewed')->default(0);
            $table->boolean('best_discount')->default(0);
            $table->boolean('best_rating')->default(0);
            $table->boolean('best_view')->default(0);
            $table->boolean('best_sale')->default(0);
            $table->integer('added_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->softDeletes();

            $table->timestamps();
        });
    }
};