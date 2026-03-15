<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['key' => 'customers', 'name' => 'العملاء'],
            ['key' => 'suppliers', 'name' => 'الموردين'],
            ['key' => 'products', 'name' => 'المنتجات'],
            ['key' => 'categories', 'name' => 'الفئات'],
            ['key' => 'sales', 'name' => 'المبيعات'],
            ['key' => 'commissions', 'name' => 'العمولات'],
            ['key' => 'orders', 'name' => 'الاوردرات'],
            ['key' => 'shipping_orders', 'name' => 'طلبات توريد المنتجات'],
            ['key' => 'special_orders', 'name' => 'الطلبات الخاصة'],
            ['key' => 'order_returns', 'name' => 'مرتجعات الطلبات'],
            ['key' => 'messages', 'name' => 'الرسائل'],
            ['key' => 'messages_notifications', 'name' => 'تنبيهات الرسائل'],
            ['key' => 'cards', 'name' => 'مولد الكروت'],
            ['key' => 'withdrawals', 'name' => 'طلبات السحب'],
            ['key' => 'ads', 'name' => 'الاعلانات'],
            ['key' => 'shipping_companies', 'name' => 'شركات الشحن'],
            ['key' => 'expenses', 'name' => 'المصروفات'],
            ['key' => 'debts', 'name' => 'المديونيات'],
            ['key' => 'users', 'name' => 'المستخدمين'],
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['key' => $permission['key']],
                ['name' => $permission['name']]
            );
        }
    }
}
