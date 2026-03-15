<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_pricing_tiers', function (Blueprint $table) {
            if (! Schema::hasColumn('product_pricing_tiers', 'supplier_id')) {
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('suppliers')
                    ->onDelete('cascade');
            }

            $table->index(['product_id', 'supplier_id', 'min_quantity']);
        });
    }

    public function down(): void
    {
        Schema::table('product_pricing_tiers', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'supplier_id', 'min_quantity']);

            if (Schema::hasColumn('product_pricing_tiers', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
        });
    }
};
