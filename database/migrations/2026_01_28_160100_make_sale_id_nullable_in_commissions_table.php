<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = (string) DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // If a previous run failed, temp tables might still exist.
            // We'll pick the best available source table and rebuild commissions safely.

            // SQLite keeps index names globally; failed runs can leave indexes behind.
            // Drop any known index names we are about to create.
            DB::statement('DROP INDEX IF EXISTS commissions_sale_id_index');
            DB::statement('DROP INDEX IF EXISTS commissions_order_id_index');
            DB::statement('DROP INDEX IF EXISTS commissions_supplier_id_index');
            DB::statement('DROP INDEX IF EXISTS commissions_commission_type_id_index');

            $hasCommissions = Schema::hasTable('commissions');
            $hasOld = Schema::hasTable('commissions_old');

            if (Schema::hasTable('commissions_src_current')) {
                Schema::drop('commissions_src_current');
            }
            if (Schema::hasTable('commissions_src_old')) {
                Schema::drop('commissions_src_old');
            }

            if (!$hasCommissions && !$hasOld) {
                return;
            }

            // Rename sources to stable names (without deleting anything).
            if ($hasCommissions) {
                Schema::rename('commissions', 'commissions_src_current');
            }
            if ($hasOld) {
                Schema::rename('commissions_old', 'commissions_src_old');
            }

            Schema::create('commissions', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();

                $table->string('commission_type');
                $table->unsignedBigInteger('commission_type_id')->nullable();

                $table->decimal('commission', 10, 2);
                $table->decimal('order_amount', 10, 2);
                $table->decimal('tax_amount', 10, 2);

                $table->date('date');
                $table->string('status');

                $table->timestamps();

                $table->index(['commission_type_id']);
            });

            $insertFrom = function (string $tableName): void {
                $hasOrderId = Schema::hasColumn($tableName, 'order_id');
                $hasSupplierId = Schema::hasColumn($tableName, 'supplier_id');
                $hasCommissionTypeId = Schema::hasColumn($tableName, 'commission_type_id');

                $sql = "INSERT OR IGNORE INTO commissions (id, user_id, sale_id, order_id, supplier_id, commission_type, commission_type_id, commission, order_amount, tax_amount, date, status, created_at, updated_at) "
                    . "SELECT id, user_id, sale_id, "
                    . ($hasOrderId ? "order_id" : "NULL") . ", "
                    . ($hasSupplierId ? "supplier_id" : "NULL") . ", "
                    . "commission_type, "
                    . ($hasCommissionTypeId ? "commission_type_id" : "NULL") . ", "
                    . "commission, order_amount, tax_amount, date, status, created_at, updated_at "
                    . "FROM {$tableName}";

                DB::statement($sql);
            };

            // Copy from both sources if present (ignore duplicates by primary key).
            if (Schema::hasTable('commissions_src_old')) {
                $insertFrom('commissions_src_old');
            }
            if (Schema::hasTable('commissions_src_current')) {
                $insertFrom('commissions_src_current');
            }

            if (Schema::hasTable('commissions_src_old')) {
                Schema::drop('commissions_src_old');
            }
            if (Schema::hasTable('commissions_src_current')) {
                Schema::drop('commissions_src_current');
            }

            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE commissions DROP FOREIGN KEY commissions_sale_id_foreign');
            DB::statement('ALTER TABLE commissions MODIFY sale_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE commissions ADD CONSTRAINT commissions_sale_id_foreign FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        $driver = (string) DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // No safe automatic rollback without potentially dropping rows that have sale_id = NULL.
            return;
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE commissions DROP FOREIGN KEY commissions_sale_id_foreign');
            DB::statement('ALTER TABLE commissions MODIFY sale_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE commissions ADD CONSTRAINT commissions_sale_id_foreign FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE');
        }
    }
};
