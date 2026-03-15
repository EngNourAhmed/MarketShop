<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_comments')) {
            return;
        }

        if (Schema::hasColumn('product_comments', 'body')) {
            return;
        }

        Schema::table('product_comments', function (Blueprint $table) {
            $table->text('body')->nullable();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_comments')) {
            return;
        }

        if (!Schema::hasColumn('product_comments', 'body')) {
            return;
        }

        Schema::table('product_comments', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }
};
