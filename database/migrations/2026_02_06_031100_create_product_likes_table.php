<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_likes')) {
            return;
        }

        Schema::create('product_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction')->default('like');
            $table->timestamps();

            $table->unique(['product_id', 'user_id']);
            $table->index(['product_id', 'reaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_likes');
    }
};
