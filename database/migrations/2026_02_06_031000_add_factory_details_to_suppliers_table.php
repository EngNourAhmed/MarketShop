<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'factory_short_details')) {
                $table->text('factory_short_details')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('suppliers', 'factory_long_details')) {
                $table->text('factory_long_details')->nullable()->after('factory_short_details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'factory_long_details')) {
                $table->dropColumn('factory_long_details');
            }
            if (Schema::hasColumn('suppliers', 'factory_short_details')) {
                $table->dropColumn('factory_short_details');
            }
        });
    }
};
