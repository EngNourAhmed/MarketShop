<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────
        // 1. CATEGORIES
        // ─────────────────────────────────────────
        DB::table('categories')->truncate();

        $categories = [
            [
                'name_ar'  => 'إلكترونيات',
                'name_en'  => 'Electronics',
                'slug'     => 'electronics',
                'icon'     => 'cpu',
                'image'    => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400',
                'bg_color' => '#3B82F6',
            ],
            [
                'name_ar'  => 'ملابس',
                'name_en'  => 'Clothing',
                'slug'     => 'clothing',
                'icon'     => 'shirt',
                'image'    => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400',
                'bg_color' => '#EC4899',
            ],
            [
                'name_ar'  => 'أدوات منزلية',
                'name_en'  => 'Home & Kitchen',
                'slug'     => 'home-kitchen',
                'icon'     => 'home',
                'image'    => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=400',
                'bg_color' => '#F59E0B',
            ],
            [
                'name_ar'  => 'رياضة',
                'name_en'  => 'Sports',
                'slug'     => 'sports',
                'icon'     => 'dumbbell',
                'image'    => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400',
                'bg_color' => '#10B981',
            ],
            [
                'name_ar'  => 'إكسسوارات',
                'name_en'  => 'Accessories',
                'slug'     => 'accessories',
                'icon'     => 'watch',
                'image'    => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400',
                'bg_color' => '#8B5CF6',
            ],
            [
                'name_ar'  => 'عطور ومستحضرات',
                'name_en'  => 'Beauty & Fragrance',
                'slug'     => 'beauty-fragrance',
                'icon'     => 'sparkles',
                'image'    => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400',
                'bg_color' => '#F43F5E',
            ],
        ];

        foreach ($categories as &$cat) {
            $cat['created_at'] = now();
            $cat['updated_at'] = now();
        }

        DB::table('categories')->insert($categories);

        $categoryIds = DB::table('categories')->pluck('id', 'slug');

        // ─────────────────────────────────────────
        // 2. SUPPLIERS
        // ─────────────────────────────────────────
        DB::table('suppliers')->truncate();

        $suppliers = [
            [
                'name'    => 'TechPeak Factory',
                'email'   => 'techpeak@factory.com',
                'phone'   => '+201001234567',
                'type'    => 'factory',
                'country' => 'Egypt',
                'factory_short_details' => 'Leading electronics manufacturer in Egypt.',
                'factory_long_details'  => 'TechPeak has been manufacturing consumer electronics since 2005. We specialize in smartphones, accessories, and smart home devices with ISO 9001 certification.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'StyleHub Vendor',
                'email'   => 'stylehub@vendor.com',
                'phone'   => '+201009876543',
                'type'    => 'vendor',
                'country' => 'Egypt',
                'factory_short_details' => 'Premium clothing vendor.',
                'factory_long_details'  => 'StyleHub supplies the latest fashion trends from top international brands at competitive prices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'HomeBase Factory',
                'email'   => 'homebase@factory.com',
                'phone'   => '+201005556666',
                'type'    => 'factory',
                'country' => 'Egypt',
                'factory_short_details' => 'Kitchen and home goods manufacturer.',
                'factory_long_details'  => 'HomeBase produces high-quality kitchenware and household essentials at factory prices.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'    => 'SportZone Vendor',
                'email'   => 'sportzone@vendor.com',
                'phone'   => '+201007778888',
                'type'    => 'vendor',
                'country' => 'Egypt',
                'factory_short_details' => 'Sports equipment and apparel supplier.',
                'factory_long_details'  => 'SportZone distributes globally recognized sports brands including gym equipment, footwear, and athletic wear.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
        $supplierIds = DB::table('suppliers')->pluck('id', 'email');

        // ─────────────────────────────────────────
        // 3. PRODUCTS
        // ─────────────────────────────────────────
        DB::table('product_pricing_tiers')->delete();
        DB::table('product_supplier_prices')->delete();
        DB::table('products')->whereNotNull('id')->delete();

        $products = [
            // Electronics
            [
                'name'           => 'Wireless Earbuds Pro',
                'name_ar'        => 'سماعات لاسلكية برو',
                'name_en'        => 'Wireless Earbuds Pro',
                'description'    => 'High-quality wireless earbuds with active noise cancellation.',
                'description_ar' => 'سماعات لاسلكية عالية الجودة مع إلغاء الضوضاء النشط وبطارية تدوم 24 ساعة.',
                'description_en' => 'Premium wireless earbuds with ANC, 24-hour battery life, and crystal-clear sound.',
                'price'          => 1200.00,
                'image'          => 'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=600',
                    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600',
                ]),
                'slug'        => 'wireless-earbuds-pro',
                'sku'         => 'ELEC-001',
                'category'    => 'electronics',
                'category_id' => $categoryIds['electronics'],
                'brand'       => 'TechPeak',
                'quantity'    => 500,
                'status'      => 1,
                'featured'    => 1,
                'new'         => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Black', 'White', 'Blue']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Smart Watch Series X',
                'name_ar'        => 'ساعة ذكية سيريس X',
                'name_en'        => 'Smart Watch Series X',
                'description'    => 'Advanced smartwatch with health tracking features.',
                'description_ar' => 'ساعة ذكية متقدمة مع تتبع صحي ونظام GPS ومقاومة للماء.',
                'description_en' => 'Advanced smartwatch with GPS, health monitoring, and water resistance up to 50m.',
                'price'          => 3500.00,
                'image'          => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600',
                    'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=600',
                ]),
                'slug'        => 'smart-watch-series-x',
                'sku'         => 'ELEC-002',
                'category'    => 'electronics',
                'category_id' => $categoryIds['electronics'],
                'brand'       => 'TechPeak',
                'quantity'    => 200,
                'status'      => 1,
                'featured'    => 1,
                'hot'         => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Silver', 'Black', 'Rose Gold']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Portable Bluetooth Speaker',
                'name_ar'        => 'سبيكر بلوتوث محمول',
                'name_en'        => 'Portable Bluetooth Speaker',
                'description'    => 'Waterproof portable speaker with 360-degree sound.',
                'description_ar' => 'سبيكر محمول مضاد للماء بصوت 360 درجة وبطارية 20 ساعة.',
                'description_en' => 'Waterproof Bluetooth speaker with 360° surround sound and 20-hour battery.',
                'price'          => 850.00,
                'image'          => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600',
                ]),
                'slug'        => 'portable-bluetooth-speaker',
                'sku'         => 'ELEC-003',
                'category'    => 'electronics',
                'category_id' => $categoryIds['electronics'],
                'brand'       => 'TechPeak',
                'quantity'    => 350,
                'status'      => 1,
                'sale'        => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Black', 'Teal', 'Red']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Clothing
            [
                'name'           => 'Classic Polo Shirt',
                'name_ar'        => 'قميص بولو كلاسيك',
                'name_en'        => 'Classic Polo Shirt',
                'description'    => 'Premium cotton polo shirt for everyday wear.',
                'description_ar' => 'قميص بولو قطن بريميوم للاستخدام اليومي بتصميم كلاسيكي أنيق.',
                'description_en' => 'Premium 100% Egyptian cotton polo shirt with classic fit.',
                'price'          => 450.00,
                'image'          => 'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600',
                    'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=600',
                ]),
                'slug'        => 'classic-polo-shirt',
                'sku'         => 'CLTH-001',
                'category'    => 'clothing',
                'category_id' => $categoryIds['clothing'],
                'brand'       => 'StyleHub',
                'quantity'    => 1000,
                'status'      => 1,
                'best_seller' => 1,
                'sizes'       => json_encode(['S', 'M', 'L', 'XL', 'XXL']),
                'colors'      => json_encode(['White', 'Navy', 'Black', 'Red']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Slim Fit Chino Pants',
                'name_ar'        => 'بنطلون تشينو سليم فيت',
                'name_en'        => 'Slim Fit Chino Pants',
                'description'    => 'Modern slim fit chino trousers.',
                'description_ar' => 'بنطلون تشينو عصري بقصة سليم فيت مريحة ومناسبة لجميع المناسبات.',
                'description_en' => 'Modern slim fit chino trousers, comfortable stretch fabric.',
                'price'          => 680.00,
                'image'          => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600',
                ]),
                'slug'        => 'slim-fit-chino-pants',
                'sku'         => 'CLTH-002',
                'category'    => 'clothing',
                'category_id' => $categoryIds['clothing'],
                'brand'       => 'StyleHub',
                'quantity'    => 600,
                'status'      => 1,
                'new'         => 1,
                'sizes'       => json_encode(['28', '30', '32', '34', '36']),
                'colors'      => json_encode(['Beige', 'Khaki', 'Navy', 'Olive']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Home & Kitchen
            [
                'name'           => 'Stainless Steel Cookware Set',
                'name_ar'        => 'طقم أواني طهي استيل',
                'name_en'        => 'Stainless Steel Cookware Set',
                'description'    => 'Professional-grade 10-piece stainless steel cookware set.',
                'description_ar' => 'طقم أواني طهي احترافي 10 قطع من الاستيل عالي الجودة مناسب لجميع المطابخ.',
                'description_en' => 'Professional 10-piece stainless steel cookware set, dishwasher safe.',
                'price'          => 2200.00,
                'image'          => 'https://images.unsplash.com/photo-1584990347449-a2f4b26e5a93?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1584990347449-a2f4b26e5a93?w=600',
                    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600',
                ]),
                'slug'        => 'stainless-steel-cookware-set',
                'sku'         => 'HOME-001',
                'category'    => 'home-kitchen',
                'category_id' => $categoryIds['home-kitchen'],
                'brand'       => 'HomeBase',
                'quantity'    => 150,
                'status'      => 1,
                'featured'    => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Silver']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Air Fryer 5.5L',
                'name_ar'        => 'قلاية هوائية 5.5 لتر',
                'name_en'        => 'Air Fryer 5.5L',
                'description'    => 'Digital air fryer with 8 cooking presets.',
                'description_ar' => 'قلاية هوائية رقمية 5.5 لتر مع 8 أوضاع طهي مبرمجة.',
                'description_en' => 'Digital air fryer with 8 presets, 5.5L capacity, rapid air circulation.',
                'price'          => 1800.00,
                'image'          => 'https://images.unsplash.com/photo-1648482220516-38a55b7d3218?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1648482220516-38a55b7d3218?w=600',
                ]),
                'slug'        => 'air-fryer-5-5l',
                'sku'         => 'HOME-002',
                'category'    => 'home-kitchen',
                'category_id' => $categoryIds['home-kitchen'],
                'brand'       => 'HomeBase',
                'quantity'    => 300,
                'status'      => 1,
                'hot'         => 1,
                'sale'        => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Black', 'White']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Sports
            [
                'name'           => 'Pro Running Shoes',
                'name_ar'        => 'حذاء جري احترافي',
                'name_en'        => 'Pro Running Shoes',
                'description'    => 'Lightweight running shoes with cushioned sole.',
                'description_ar' => 'حذاء جري خفيف الوزن بنعل مبطن لأقصى راحة أثناء الجري.',
                'description_en' => 'Ultra-lightweight running shoes with memory foam insole and breathable mesh upper.',
                'price'          => 1500.00,
                'image'          => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600',
                ]),
                'slug'        => 'pro-running-shoes',
                'sku'         => 'SPRT-001',
                'category'    => 'sports',
                'category_id' => $categoryIds['sports'],
                'brand'       => 'SportZone',
                'quantity'    => 400,
                'status'      => 1,
                'best_seller' => 1,
                'sizes'       => json_encode(['38', '39', '40', '41', '42', '43', '44', '45']),
                'colors'      => json_encode(['Black/White', 'Blue/Orange', 'Gray/Red']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'           => 'Yoga Mat Premium',
                'name_ar'        => 'مات يوجا بريميوم',
                'name_en'        => 'Yoga Mat Premium',
                'description'    => 'Eco-friendly non-slip yoga mat.',
                'description_ar' => 'مات يوجا صديق للبيئة مضاد للانزلاق بسماكة 6 مم.',
                'description_en' => 'Eco-friendly TPE yoga mat, non-slip, 6mm thick with alignment lines.',
                'price'          => 350.00,
                'image'          => 'https://images.unsplash.com/photo-1601925228001-cd45ba9d4d3a?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1601925228001-cd45ba9d4d3a?w=600',
                ]),
                'slug'        => 'yoga-mat-premium',
                'sku'         => 'SPRT-002',
                'category'    => 'sports',
                'category_id' => $categoryIds['sports'],
                'brand'       => 'SportZone',
                'quantity'    => 700,
                'status'      => 1,
                'new'         => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Purple', 'Blue', 'Black', 'Pink']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Accessories
            [
                'name'           => 'Leather Wallet Bifold',
                'name_ar'        => 'محفظة جلد بايفولد',
                'name_en'        => 'Leather Wallet Bifold',
                'description'    => 'Genuine leather bifold wallet with RFID blocking.',
                'description_ar' => 'محفظة جلد طبيعي بايفولد مع حماية RFID وحجرات متعددة.',
                'description_en' => 'Genuine leather bifold wallet with RFID blocking, 8 card slots.',
                'price'          => 320.00,
                'image'          => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600',
                ]),
                'slug'        => 'leather-wallet-bifold',
                'sku'         => 'ACC-001',
                'category'    => 'accessories',
                'category_id' => $categoryIds['accessories'],
                'brand'       => 'StyleHub',
                'quantity'    => 800,
                'status'      => 1,
                'featured'    => 1,
                'sizes'       => null,
                'colors'      => json_encode(['Brown', 'Black', 'Tan']),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            // Beauty
            [
                'name'           => 'Luxury Perfume Oud',
                'name_ar'        => 'عطر فاخر عود',
                'name_en'        => 'Luxury Perfume Oud',
                'description'    => 'Rich oriental oud perfume, 100ml EDP.',
                'description_ar' => 'عطر عود شرقي فاخر 100 مل، ثبات قوي يدوم طوال اليوم.',
                'description_en' => 'Luxurious oriental oud EDP 100ml, long-lasting with warm woody notes.',
                'price'          => 950.00,
                'image'          => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600',
                'images'         => json_encode([
                    'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600',
                    'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600',
                ]),
                'slug'        => 'luxury-perfume-oud',
                'sku'         => 'BEAU-001',
                'category'    => 'beauty-fragrance',
                'category_id' => $categoryIds['beauty-fragrance'],
                'brand'       => 'Trady Scents',
                'quantity'    => 250,
                'status'      => 1,
                'hot'         => 1,
                'best_seller' => 1,
                'sizes'       => null,
                'colors'      => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        // Insert products
        foreach ($products as $product) {
            $product += [
                'sale'        => 0,
                'hot'         => 0,
                'new'         => 0,
                'featured'    => 0,
                'best_seller' => 0,
                'best_rated'  => 0,
                'best_viewed' => 0,
                'best_discount' => 0,
                'best_rating' => 0,
                'best_view'   => 0,
                'best_sale'   => 0,
                'added_by'    => 1,
                'updated_by'  => 1,
                'sizes'       => null,
                'colors'      => null,
            ];
            DB::table('products')->insert($product);
        }

        // ─────────────────────────────────────────
        // 4. PRODUCT SUPPLIER PRICES
        // ─────────────────────────────────────────
        $productMap = DB::table('products')->pluck('id', 'sku');
        $supplierMap = DB::table('suppliers')->pluck('id', 'email');

        $techFactory  = $supplierMap['techpeak@factory.com'] ?? null;
        $styleVendor  = $supplierMap['stylehub@vendor.com'] ?? null;
        $homeFactory  = $supplierMap['homebase@factory.com'] ?? null;
        $sportVendor  = $supplierMap['sportzone@vendor.com'] ?? null;

        $supplierPrices = [
            ['supplier_id' => $techFactory,  'product_id' => $productMap['ELEC-001'], 'price' => 1000.00, 'unit_price' => 1000.00],
            ['supplier_id' => $techFactory,  'product_id' => $productMap['ELEC-002'], 'price' => 3000.00, 'unit_price' => 3000.00],
            ['supplier_id' => $techFactory,  'product_id' => $productMap['ELEC-003'], 'price' => 700.00,  'unit_price' => 700.00],
            ['supplier_id' => $styleVendor,  'product_id' => $productMap['CLTH-001'], 'price' => 380.00,  'unit_price' => 380.00],
            ['supplier_id' => $styleVendor,  'product_id' => $productMap['CLTH-002'], 'price' => 560.00,  'unit_price' => 560.00],
            ['supplier_id' => $styleVendor,  'product_id' => $productMap['ACC-001'],  'price' => 260.00,  'unit_price' => 260.00],
            ['supplier_id' => $homeFactory,  'product_id' => $productMap['HOME-001'], 'price' => 1800.00, 'unit_price' => 1800.00],
            ['supplier_id' => $homeFactory,  'product_id' => $productMap['HOME-002'], 'price' => 1500.00, 'unit_price' => 1500.00],
            ['supplier_id' => $sportVendor,  'product_id' => $productMap['SPRT-001'], 'price' => 1200.00, 'unit_price' => 1200.00],
            ['supplier_id' => $sportVendor,  'product_id' => $productMap['SPRT-002'], 'price' => 280.00,  'unit_price' => 280.00],
            ['supplier_id' => $techFactory,  'product_id' => $productMap['BEAU-001'], 'price' => 750.00,  'unit_price' => 750.00],
        ];

        foreach ($supplierPrices as &$sp) {
            $sp['created_at'] = now();
            $sp['updated_at'] = now();
        }

        DB::table('product_supplier_prices')->insert($supplierPrices);

        // ─────────────────────────────────────────
        // 5. PRICING TIERS (per product)
        // ─────────────────────────────────────────
        $tiers = [
            // Earbuds
            ['product_id' => $productMap['ELEC-001'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 1200.00],
            ['product_id' => $productMap['ELEC-001'], 'min_quantity' => 10,  'max_quantity' => 49,  'price_per_unit' => 1100.00],
            ['product_id' => $productMap['ELEC-001'], 'min_quantity' => 50,  'max_quantity' => null,'price_per_unit' => 1000.00],
            // Smart Watch
            ['product_id' => $productMap['ELEC-002'], 'min_quantity' => 1,   'max_quantity' => 4,   'price_per_unit' => 3500.00],
            ['product_id' => $productMap['ELEC-002'], 'min_quantity' => 5,   'max_quantity' => 19,  'price_per_unit' => 3200.00],
            ['product_id' => $productMap['ELEC-002'], 'min_quantity' => 20,  'max_quantity' => null,'price_per_unit' => 3000.00],
            // Speaker
            ['product_id' => $productMap['ELEC-003'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 850.00],
            ['product_id' => $productMap['ELEC-003'], 'min_quantity' => 10,  'max_quantity' => null,'price_per_unit' => 750.00],
            // Polo
            ['product_id' => $productMap['CLTH-001'], 'min_quantity' => 1,   'max_quantity' => 19,  'price_per_unit' => 450.00],
            ['product_id' => $productMap['CLTH-001'], 'min_quantity' => 20,  'max_quantity' => 99,  'price_per_unit' => 420.00],
            ['product_id' => $productMap['CLTH-001'], 'min_quantity' => 100, 'max_quantity' => null,'price_per_unit' => 380.00],
            // Chino
            ['product_id' => $productMap['CLTH-002'], 'min_quantity' => 1,   'max_quantity' => 19,  'price_per_unit' => 680.00],
            ['product_id' => $productMap['CLTH-002'], 'min_quantity' => 20,  'max_quantity' => null,'price_per_unit' => 600.00],
            // Cookware
            ['product_id' => $productMap['HOME-001'], 'min_quantity' => 1,   'max_quantity' => 4,   'price_per_unit' => 2200.00],
            ['product_id' => $productMap['HOME-001'], 'min_quantity' => 5,   'max_quantity' => null,'price_per_unit' => 2000.00],
            // Air Fryer
            ['product_id' => $productMap['HOME-002'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 1800.00],
            ['product_id' => $productMap['HOME-002'], 'min_quantity' => 10,  'max_quantity' => null,'price_per_unit' => 1600.00],
            // Running Shoes
            ['product_id' => $productMap['SPRT-001'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 1500.00],
            ['product_id' => $productMap['SPRT-001'], 'min_quantity' => 10,  'max_quantity' => 49,  'price_per_unit' => 1350.00],
            ['product_id' => $productMap['SPRT-001'], 'min_quantity' => 50,  'max_quantity' => null,'price_per_unit' => 1200.00],
            // Yoga Mat
            ['product_id' => $productMap['SPRT-002'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 350.00],
            ['product_id' => $productMap['SPRT-002'], 'min_quantity' => 10,  'max_quantity' => null,'price_per_unit' => 300.00],
            // Wallet
            ['product_id' => $productMap['ACC-001'],  'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 320.00],
            ['product_id' => $productMap['ACC-001'],  'min_quantity' => 10,  'max_quantity' => null,'price_per_unit' => 280.00],
            // Perfume
            ['product_id' => $productMap['BEAU-001'], 'min_quantity' => 1,   'max_quantity' => 9,   'price_per_unit' => 950.00],
            ['product_id' => $productMap['BEAU-001'], 'min_quantity' => 10,  'max_quantity' => null,'price_per_unit' => 850.00],
        ];

        foreach ($tiers as &$tier) {
            $tier['created_at'] = now();
            $tier['updated_at'] = now();
        }

        DB::table('product_pricing_tiers')->insert($tiers);

        $this->command->info('✅ Dummy data seeded: 6 categories, 4 suppliers, 11 products with pricing tiers!');
    }
}
