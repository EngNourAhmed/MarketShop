<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('counteries')->truncate();

        DB::table('counteries')->insert([
            [
                'name' => 'Egypt',
                'code' => 'EG',
                'flag' => '🇪🇬',
                'currency' => 'Egyptian Pound',
                'currency_code' => 'EGP',
                'currency_symbol' => 'ج.م',
                'exchange_rate' => 1.0, // Primary currency
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Saudi Arabia',
                'code' => 'SA',
                'flag' => '🇸🇦',
                'currency' => 'Saudi Riyal',
                'currency_code' => 'SAR',
                'currency_symbol' => 'ر.س',
                'exchange_rate' => 13.0, // 1 SAR = 13 EGP
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UAE',
                'code' => 'AE',
                'flag' => '🇦🇪',
                'currency' => 'UAE Dirham',
                'currency_code' => 'AED',
                'currency_symbol' => 'د.إ',
                'exchange_rate' => 13.5, // 1 AED = 13.5 EGP
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USA',
                'code' => 'US',
                'flag' => '🇺🇸',
                'currency' => 'US Dollar',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'exchange_rate' => 50.0, // 1 USD = 50 EGP
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'flag' => '🇫🇷',
                'currency' => 'Euro',
                'currency_code' => 'EUR',
                'currency_symbol' => '€',
                'exchange_rate' => 54.0, // 1 EUR = 54 EGP
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
