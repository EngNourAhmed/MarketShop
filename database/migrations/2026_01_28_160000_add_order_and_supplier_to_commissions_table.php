<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('commissions', 'commission_type_id')) {
                $table->unsignedBigInteger('commission_type_id')->nullable()->after('commission_type');
                $table->index(['commission_type_id']);
            }

            if (!Schema::hasColumn('commissions', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('sale_id')->constrained('orders')->nullOnDelete();
                $table->index(['order_id']);
            }

            if (!Schema::hasColumn('commissions', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('order_id')->constrained('suppliers')->nullOnDelete();
                $table->index(['supplier_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }

            if (Schema::hasColumn('commissions', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }

            if (Schema::hasColumn('commissions', 'commission_type_id')) {
                $table->dropIndex(['commission_type_id']);
                $table->dropColumn('commission_type_id');
            }
        });
    }
};
