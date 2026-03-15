<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('special_orders', 'factory_status')) {
                $table->string('factory_status')->nullable()->after('admin_reviewed_at');
            }
            if (!Schema::hasColumn('special_orders', 'factory_updated_at')) {
                $table->timestamp('factory_updated_at')->nullable()->after('factory_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            foreach (['factory_status', 'factory_updated_at'] as $col) {
                if (Schema::hasColumn('special_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
