<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->integer('advertisement_id');
            $table->integer('cost');
            $table->integer('discount');
            $table->string('duration');
            $table->string('title');
            $table->text('content');
            $table->string('image');
            $table->string('link');
            $table->string('status');
            $table->string('type');
            $table->string('category');
            $table->string('sub_category');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }
};