<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('special_orders', 'product_name')) {
                $table->string('product_name')->nullable()->after('title');
            }
            if (!Schema::hasColumn('special_orders', 'quantity')) {
                $table->integer('quantity')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('special_orders', 'color')) {
                $table->string('color')->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('special_orders', 'size')) {
                $table->string('size')->nullable()->after('color');
            }
            if (!Schema::hasColumn('special_orders', 'material')) {
                $table->string('material')->nullable()->after('size');
            }
            if (!Schema::hasColumn('special_orders', 'specs')) {
                $table->text('specs')->nullable()->after('material');
            }
            if (!Schema::hasColumn('special_orders', 'reference_url')) {
                $table->string('reference_url')->nullable()->after('specs');
            }
            if (!Schema::hasColumn('special_orders', 'images')) {
                $table->text('images')->nullable()->after('reference_url');
            }
            if (!Schema::hasColumn('special_orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('special_orders', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('rejection_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            foreach ([
                'product_name',
                'quantity',
                'color',
                'size',
                'material',
                'specs',
                'reference_url',
                'images',
                'rejection_reason',
                'reviewed_at',
            ] as $col) {
                if (Schema::hasColumn('special_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
