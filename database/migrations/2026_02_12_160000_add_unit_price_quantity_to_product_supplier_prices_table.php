<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_supplier_prices', function (Blueprint $table) {
            if (! Schema::hasColumn('product_supplier_prices', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('product_supplier_prices', 'quantity')) {
                $table->unsignedInteger('quantity')->nullable()->after('unit_price');
            }

            $table->index(['supplier_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('product_supplier_prices', function (Blueprint $table) {
            if (Schema::hasColumn('product_supplier_prices', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('product_supplier_prices', 'unit_price')) {
                $table->dropColumn('unit_price');
            }

            $table->dropIndex(['supplier_id', 'product_id']);
        });
    }
};
