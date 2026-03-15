<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('special_orders', 'admin_status')) {
                $table->string('admin_status')->nullable()->after('status');
            }
            if (!Schema::hasColumn('special_orders', 'admin_rejection_reason')) {
                $table->text('admin_rejection_reason')->nullable()->after('admin_status');
            }
            if (!Schema::hasColumn('special_orders', 'admin_reviewed_at')) {
                $table->timestamp('admin_reviewed_at')->nullable()->after('admin_rejection_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('special_orders', function (Blueprint $table) {
            foreach (['admin_status', 'admin_rejection_reason', 'admin_reviewed_at'] as $col) {
                if (Schema::hasColumn('special_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
