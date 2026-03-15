<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ─────────────────────────────────────────
        // 1. CATEGORIES (10 categories)
        // ─────────────────────────────────────────
        DB::table('categories')->truncate();

        $categories = [
        ['name_ar' => 'إلكترونيات',         'name_en' => 'Electronics',       'slug' => 'electronics',      'icon' => 'cpu',        'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400', 'bg_color' => '#3B82F6'],
            ['name_ar' => 'ملابس',               'name_en' => 'Clothing',           'slug' => 'clothing',          'icon' => 'tag',        'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400', 'bg_color' => '#EC4899'],
            ['name_ar' => 'أدوات منزلية',         'name_en' => 'Home & Kitchen',     'slug' => 'home-kitchen',      'icon' => 'home',       'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=400', 'bg_color' => '#F59E0B'],
            ['name_ar' => 'رياضة',               'name_en' => 'Sports',             'slug' => 'sports',            'icon' => 'activity',   'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400', 'bg_color' => '#10B981'],
            ['name_ar' => 'إكسسوارات',            'name_en' => 'Accessories',        'slug' => 'accessories',       'icon' => 'watch',      'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'bg_color' => '#8B5CF6'],
            ['name_ar' => 'عطور ومستحضرات',       'name_en' => 'Beauty & Fragrance', 'slug' => 'beauty-fragrance',  'icon' => 'heart',      'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400', 'bg_color' => '#F43F5E'],
            ['name_ar' => 'أجهزة منزلية',          'name_en' => 'Appliances',         'slug' => 'appliances',        'icon' => 'zap',        'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400', 'bg_color' => '#06B6D4'],
            ['name_ar' => 'حقائب وشنط',           'name_en' => 'Bags & Luggage',     'slug' => 'bags-luggage',      'icon' => 'shopping-bag','image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400', 'bg_color' => '#D97706'],
            ['name_ar' => 'مكتبية وقرطاسية',      'name_en' => 'Office & Stationery','slug' => 'office-stationery', 'icon' => 'edit-3',     'image' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400', 'bg_color' => '#64748B'],
            ['name_ar' => 'ألعاب وترفيه',          'name_en' => 'Toys & Entertainment','slug' => 'toys',             'icon' => 'smile',      'image' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400', 'bg_color' => '#EF4444'],
        ];

        foreach ($categories as &$cat) {
            $cat['created_at'] = now();
            $cat['updated_at'] = now();
        }
        DB::table('categories')->insert($categories);
        $cId = DB::table('categories')->pluck('id', 'slug');

        // ─────────────────────────────────────────
        // 2. SUPPLIERS (6 suppliers)
        // ─────────────────────────────────────────
        DB::table('suppliers')->truncate();

        $suppliersData = [
            ['name' => 'TechPeak Factory',    'email' => 'techpeak@factory.com',   'phone' => '+201001234567', 'type' => 'factory', 'country' => 'Egypt', 'factory_short_details' => 'Leading electronics manufacturer in Egypt.', 'factory_long_details' => 'TechPeak has been manufacturing consumer electronics since 2005. We specialize in smartphones, accessories, and smart home devices with ISO 9001 certification.'],
            ['name' => 'StyleHub Vendor',     'email' => 'stylehub@vendor.com',    'phone' => '+201009876543', 'type' => 'vendor',  'country' => 'Egypt', 'factory_short_details' => 'Premium clothing and accessories vendor.', 'factory_long_details' => 'StyleHub supplies the latest fashion trends from top international brands at competitive prices.'],
            ['name' => 'HomeBase Factory',    'email' => 'homebase@factory.com',   'phone' => '+201005556666', 'type' => 'factory', 'country' => 'Egypt', 'factory_short_details' => 'Kitchen and home goods manufacturer.', 'factory_long_details' => 'HomeBase produces high-quality kitchenware and household essentials at factory prices.'],
            ['name' => 'SportZone Vendor',    'email' => 'sportzone@vendor.com',   'phone' => '+201007778888', 'type' => 'vendor',  'country' => 'Egypt', 'factory_short_details' => 'Sports equipment and apparel supplier.', 'factory_long_details' => 'SportZone distributes globally recognized sports brands including gym equipment, footwear, and athletic wear.'],
            ['name' => 'GadgetWorld Factory','email' => 'gadgetworld@factory.com', 'phone' => '+201002223333', 'type' => 'factory', 'country' => 'Egypt', 'factory_short_details' => 'Smart gadgets and gaming peripherals manufacturer.', 'factory_long_details' => 'GadgetWorld manufactures a wide range of smart home devices, gaming peripherals, and innovative tech gadgets.'],
            ['name' => 'LuxeBags Vendor',    'email' => 'luxebags@vendor.com',     'phone' => '+201004445555', 'type' => 'vendor',  'country' => 'Egypt', 'factory_short_details' => 'Premium bags and luggage supplier.', 'factory_long_details' => 'LuxeBags offers a curated selection of premium handbags, backpacks, and travel luggage from renowned brands.'],
        ];

        foreach ($suppliersData as &$s) {
            $s['created_at'] = now();
            $s['updated_at'] = now();
        }
        DB::table('suppliers')->insert($suppliersData);
        $sMap = DB::table('suppliers')->pluck('id', 'email');

        $techF   = $sMap['techpeak@factory.com'];
        $styleV  = $sMap['stylehub@vendor.com'];
        $homeF   = $sMap['homebase@factory.com'];
        $sportV  = $sMap['sportzone@vendor.com'];
        $gadgetF = $sMap['gadgetworld@factory.com'];
        $luxeV   = $sMap['luxebags@vendor.com'];

        // ─────────────────────────────────────────
        // 3. PRODUCTS (25 products across 10 categories)
        // ─────────────────────────────────────────
        DB::table('product_pricing_tiers')->truncate();
        DB::table('product_supplier_prices')->truncate();
        DB::table('products')->truncate();

        $defaults = [
            'sale' => 0, 'hot' => 0, 'new' => 0, 'featured' => 0,
            'best_seller' => 0, 'best_rated' => 0, 'best_viewed' => 0,
            'best_discount' => 0, 'best_rating' => 0, 'best_view' => 0,
            'best_sale' => 0, 'added_by' => 1, 'updated_by' => 1,
            'sizes' => null, 'colors' => null,
            'created_at' => now(), 'updated_at' => now(),
        ];

        $products = [
            // ── Electronics (4) ──────────────────────────
            ['sku' => 'ELEC-001', 'name' => 'Wireless Earbuds Pro',            'name_ar' => 'سماعات لاسلكية برو',         'name_en' => 'Wireless Earbuds Pro',
             'description_ar' => 'سماعات لاسلكية عالية الجودة مع إلغاء الضوضاء النشط وبطارية تدوم 24 ساعة.',
             'description_en' => 'Premium wireless earbuds with ANC, 24-hour battery life, and crystal-clear sound.',
             'price' => 1200.00, 'category_id' => $cId['electronics'], 'category' => 'electronics', 'brand' => 'TechPeak',
             'image' => 'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?w=600','https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600']),
             'quantity' => 500, 'slug' => 'wireless-earbuds-pro', 'featured' => 1, 'new' => 1,
             'colors' => json_encode(['Black', 'White', 'Blue']),
            ],
            ['sku' => 'ELEC-002', 'name' => 'Smart Watch Series X',            'name_ar' => 'ساعة ذكية سيريس X',         'name_en' => 'Smart Watch Series X',
             'description_ar' => 'ساعة ذكية متقدمة مع تتبع صحي ونظام GPS ومقاومة للماء.',
             'description_en' => 'Advanced smartwatch with GPS, health monitoring, water resistance up to 50m.',
             'price' => 3500.00, 'category_id' => $cId['electronics'], 'category' => 'electronics', 'brand' => 'TechPeak',
             'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600','https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=600']),
             'quantity' => 200, 'slug' => 'smart-watch-series-x', 'featured' => 1, 'hot' => 1,
             'colors' => json_encode(['Silver', 'Black', 'Rose Gold']),
            ],
            ['sku' => 'ELEC-003', 'name' => 'Portable Bluetooth Speaker',      'name_ar' => 'سبيكر بلوتوث محمول',         'name_en' => 'Portable Bluetooth Speaker',
             'description_ar' => 'سبيكر محمول مضاد للماء بصوت 360 درجة وبطارية 20 ساعة.',
             'description_en' => 'Waterproof Bluetooth speaker with 360° surround sound and 20-hour battery.',
             'price' => 850.00, 'category_id' => $cId['electronics'], 'category' => 'electronics', 'brand' => 'TechPeak',
             'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=600']),
             'quantity' => 350, 'slug' => 'portable-bluetooth-speaker', 'sale' => 1,
             'colors' => json_encode(['Black', 'Teal', 'Red']),
            ],
            ['sku' => 'ELEC-004', 'name' => 'Mechanical Gaming Keyboard',      'name_ar' => 'كيبورد ميكانيكي للألعاب',    'name_en' => 'Mechanical Gaming Keyboard',
             'description_ar' => 'كيبورد ميكانيكي RGB للألعاب بمفاتيح Blue Switch وإضاءة قابلة للتخصيص.',
             'description_en' => 'RGB mechanical gaming keyboard with Blue switches and full customizable backlighting.',
             'price' => 2200.00, 'category_id' => $cId['electronics'], 'category' => 'electronics', 'brand' => 'GadgetWorld',
             'image' => 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=600','https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=600']),
             'quantity' => 180, 'slug' => 'mechanical-gaming-keyboard', 'hot' => 1, 'new' => 1,
             'colors' => json_encode(['Black', 'White']),
            ],

            // ── Clothing (3) ──────────────────────────────
            ['sku' => 'CLTH-001', 'name' => 'Classic Polo Shirt',              'name_ar' => 'قميص بولو كلاسيك',           'name_en' => 'Classic Polo Shirt',
             'description_ar' => 'قميص بولو قطن 100% للاستخدام اليومي بتصميم كلاسيكي أنيق.',
             'description_en' => 'Premium 100% Egyptian cotton polo shirt with classic fit.',
             'price' => 450.00, 'category_id' => $cId['clothing'], 'category' => 'clothing', 'brand' => 'StyleHub',
             'image' => 'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=600','https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=600']),
             'quantity' => 1000, 'slug' => 'classic-polo-shirt', 'best_seller' => 1,
             'sizes' => json_encode(['S', 'M', 'L', 'XL', 'XXL']),
             'colors' => json_encode(['White', 'Navy', 'Black', 'Red']),
            ],
            ['sku' => 'CLTH-002', 'name' => 'Slim Fit Chino Pants',            'name_ar' => 'بنطلون تشينو سليم فيت',       'name_en' => 'Slim Fit Chino Pants',
             'description_ar' => 'بنطلون تشينو عصري بقصة سليم فيت مريحة ومناسبة لجميع المناسبات.',
             'description_en' => 'Modern slim fit chino trousers, comfortable stretch fabric.',
             'price' => 680.00, 'category_id' => $cId['clothing'], 'category' => 'clothing', 'brand' => 'StyleHub',
             'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600']),
             'quantity' => 600, 'slug' => 'slim-fit-chino-pants', 'new' => 1,
             'sizes' => json_encode(['28', '30', '32', '34', '36']),
             'colors' => json_encode(['Beige', 'Khaki', 'Navy', 'Olive']),
            ],
            ['sku' => 'CLTH-003', 'name' => 'Hoodie Oversized Fleece',         'name_ar' => 'هودي أوفرسايز فليس',          'name_en' => 'Hoodie Oversized Fleece',
             'description_ar' => 'هودي أوفرسايز فليس دافئ وأنيق مناسب لجميع الأوقات.',
             'description_en' => 'Warm and stylish oversized fleece hoodie, perfect for all occasions.',
             'price' => 750.00, 'category_id' => $cId['clothing'], 'category' => 'clothing', 'brand' => 'StyleHub',
             'image' => 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=600']),
             'quantity' => 800, 'slug' => 'hoodie-oversized-fleece', 'featured' => 1,
             'sizes' => json_encode(['XS', 'S', 'M', 'L', 'XL']),
             'colors' => json_encode(['Gray', 'Black', 'Cream', 'Dusty Pink']),
            ],

            // ── Home & Kitchen (3) ────────────────────────
            ['sku' => 'HOME-001', 'name' => 'Stainless Steel Cookware Set',    'name_ar' => 'طقم أواني طهي استيل',         'name_en' => 'Stainless Steel Cookware Set',
             'description_ar' => 'طقم أواني طهي احترافي 10 قطع من الاستيل عالي الجودة مناسب لجميع المطابخ.',
             'description_en' => 'Professional 10-piece stainless steel cookware set, dishwasher safe.',
             'price' => 2200.00, 'category_id' => $cId['home-kitchen'], 'category' => 'home-kitchen', 'brand' => 'HomeBase',
             'image' => 'https://images.unsplash.com/photo-1584990347449-a2f4b26e5a93?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1584990347449-a2f4b26e5a93?w=600','https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=600']),
             'quantity' => 150, 'slug' => 'stainless-steel-cookware-set', 'featured' => 1,
             'colors' => json_encode(['Silver']),
            ],
            ['sku' => 'HOME-002', 'name' => 'Air Fryer 5.5L Digital',          'name_ar' => 'قلاية هوائية رقمية 5.5 لتر',  'name_en' => 'Air Fryer 5.5L Digital',
             'description_ar' => 'قلاية هوائية رقمية 5.5 لتر مع 8 أوضاع طهي مبرمجة.',
             'description_en' => 'Digital air fryer with 8 presets, 5.5L capacity, rapid air circulation.',
             'price' => 1800.00, 'category_id' => $cId['home-kitchen'], 'category' => 'home-kitchen', 'brand' => 'HomeBase',
             'image' => 'https://images.unsplash.com/photo-1648482220516-38a55b7d3218?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1648482220516-38a55b7d3218?w=600']),
             'quantity' => 300, 'slug' => 'air-fryer-5-5l-digital', 'hot' => 1, 'sale' => 1,
             'colors' => json_encode(['Black', 'White']),
            ],
            ['sku' => 'HOME-003', 'name' => 'Bamboo Cutting Board Set',        'name_ar' => 'طقم ألواح تقطيع بامبو',        'name_en' => 'Bamboo Cutting Board Set',
             'description_ar' => 'طقم 3 ألواح تقطيع بامبو طبيعية بأحجام مختلفة مع مطاط مانع للانزلاق.',
             'description_en' => 'Set of 3 natural bamboo cutting boards in different sizes with anti-slip rubber feet.',
             'price' => 380.00, 'category_id' => $cId['home-kitchen'], 'category' => 'home-kitchen', 'brand' => 'HomeBase',
             'image' => 'https://images.unsplash.com/photo-1652541965-64e8bc5c7af0?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1652541965-64e8bc5c7af0?w=600']),
             'quantity' => 500, 'slug' => 'bamboo-cutting-board-set', 'best_seller' => 1,
            ],

            // ── Sports (2) ────────────────────────────────
            ['sku' => 'SPRT-001', 'name' => 'Pro Running Shoes',               'name_ar' => 'حذاء جري احترافي',            'name_en' => 'Pro Running Shoes',
             'description_ar' => 'حذاء جري خفيف الوزن بنعل مبطن لأقصى راحة أثناء الجري.',
             'description_en' => 'Ultra-lightweight running shoes with memory foam insole and breathable mesh upper.',
             'price' => 1500.00, 'category_id' => $cId['sports'], 'category' => 'sports', 'brand' => 'SportZone',
             'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600','https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600']),
             'quantity' => 400, 'slug' => 'pro-running-shoes', 'best_seller' => 1,
             'sizes' => json_encode(['38', '39', '40', '41', '42', '43', '44', '45']),
             'colors' => json_encode(['Black/White', 'Blue/Orange', 'Gray/Red']),
            ],
            ['sku' => 'SPRT-002', 'name' => 'Yoga Mat Premium',                'name_ar' => 'مات يوجا بريميوم',             'name_en' => 'Yoga Mat Premium',
             'description_ar' => 'مات يوجا صديق للبيئة مضاد للانزلاق بسماكة 6 مم.',
             'description_en' => 'Eco-friendly TPE yoga mat, non-slip, 6mm thick with alignment lines.',
             'price' => 350.00, 'category_id' => $cId['sports'], 'category' => 'sports', 'brand' => 'SportZone',
             'image' => 'https://images.unsplash.com/photo-1601925228001-cd45ba9d4d3a?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1601925228001-cd45ba9d4d3a?w=600']),
             'quantity' => 700, 'slug' => 'yoga-mat-premium', 'new' => 1,
             'colors' => json_encode(['Purple', 'Blue', 'Black', 'Pink']),
            ],

            // ── Accessories (2) ───────────────────────────
            ['sku' => 'ACC-001', 'name' => 'Leather Wallet Bifold',            'name_ar' => 'محفظة جلد بايفولد',           'name_en' => 'Leather Wallet Bifold',
             'description_ar' => 'محفظة جلد طبيعي بايفولد مع حماية RFID وحجرات متعددة.',
             'description_en' => 'Genuine leather bifold wallet with RFID blocking, 8 card slots.',
             'price' => 320.00, 'category_id' => $cId['accessories'], 'category' => 'accessories', 'brand' => 'StyleHub',
             'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1627123424574-724758594e93?w=600']),
             'quantity' => 800, 'slug' => 'leather-wallet-bifold', 'featured' => 1,
             'colors' => json_encode(['Brown', 'Black', 'Tan']),
            ],
            ['sku' => 'ACC-002', 'name' => 'Polarized Sunglasses Aviator',     'name_ar' => 'نظارة شمسية أفياتور بولارايزد','name_en' => 'Polarized Sunglasses Aviator',
             'description_ar' => 'نظارة شمسية أفياتور بعدسات بولارايزد وإطار معدني خفيف الوزن.',
             'description_en' => 'Classic aviator sunglasses with polarized UV400 lenses and lightweight metal frame.',
             'price' => 550.00, 'category_id' => $cId['accessories'], 'category' => 'accessories', 'brand' => 'StyleHub',
             'image' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600']),
             'quantity' => 600, 'slug' => 'polarized-sunglasses-aviator', 'hot' => 1,
             'colors' => json_encode(['Gold/Brown', 'Silver/Gray', 'Black/Green']),
            ],

            // ── Beauty & Fragrance (2) ────────────────────
            ['sku' => 'BEAU-001', 'name' => 'Luxury Perfume Oud',              'name_ar' => 'عطر فاخر عود',                'name_en' => 'Luxury Perfume Oud',
             'description_ar' => 'عطر عود شرقي فاخر 100 مل، ثبات قوي يدوم طوال اليوم.',
             'description_en' => 'Luxurious oriental oud EDP 100ml, long-lasting with warm woody notes.',
             'price' => 950.00, 'category_id' => $cId['beauty-fragrance'], 'category' => 'beauty-fragrance', 'brand' => 'Trady Scents',
             'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1541643600914-78b084683702?w=600','https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600']),
             'quantity' => 250, 'slug' => 'luxury-perfume-oud', 'hot' => 1, 'best_seller' => 1,
            ],
            ['sku' => 'BEAU-002', 'name' => 'Vitamin C Face Serum',            'name_ar' => 'سيروم فيتامين C للوجه',        'name_en' => 'Vitamin C Face Serum',
             'description_ar' => 'سيروم فيتامين C مركز 20% لتفتيح البشرة وتقليل التجاعيد.',
             'description_en' => '20% Vitamin C concentrated serum for brightening skin and reducing wrinkles.',
             'price' => 420.00, 'category_id' => $cId['beauty-fragrance'], 'category' => 'beauty-fragrance', 'brand' => 'GlowUp',
             'image' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=600']),
             'quantity' => 400, 'slug' => 'vitamin-c-face-serum', 'new' => 1, 'best_seller' => 1,
            ],

            // ── Appliances (2) ────────────────────────────
            ['sku' => 'APPL-001', 'name' => 'Robot Vacuum Cleaner',            'name_ar' => 'مكنسة روبوت ذكية',            'name_en' => 'Robot Vacuum Cleaner',
             'description_ar' => 'مكنسة روبوت ذكية مع نظام تنقل ذكي وتطبيق للهاتف.',
             'description_en' => 'Smart robot vacuum with intelligent navigation, app control, and auto-charging.',
             'price' => 4500.00, 'category_id' => $cId['appliances'], 'category' => 'appliances', 'brand' => 'SmartHome',
             'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600']),
             'quantity' => 100, 'slug' => 'robot-vacuum-cleaner', 'featured' => 1, 'hot' => 1,
             'colors' => json_encode(['Black', 'White']),
            ],
            ['sku' => 'APPL-002', 'name' => 'Coffee Maker Espresso',           'name_ar' => 'ماكينة قهوة إسبريسو',         'name_en' => 'Coffee Maker Espresso',
             'description_ar' => 'ماكينة إسبريسو احترافية بضغط 15 بار مع طحن تلقائي وإزبد الحليب.',
             'description_en' => 'Professional 15-bar espresso machine with built-in grinder and milk frother.',
             'price' => 3200.00, 'category_id' => $cId['appliances'], 'category' => 'appliances', 'brand' => 'BrewMaster',
             'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600','https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600']),
             'quantity' => 120, 'slug' => 'coffee-maker-espresso', 'best_seller' => 1,
             'colors' => json_encode(['Black', 'Silver', 'Red']),
            ],

            // ── Bags & Luggage (2) ────────────────────────
            ['sku' => 'BAG-001', 'name' => 'Leather Laptop Backpack',          'name_ar' => 'حقيبة ظهر جلد للابتوب',       'name_en' => 'Leather Laptop Backpack',
             'description_ar' => 'حقيبة ظهر جلد فاخرة لاتبوب 15.6 بوصة مع جيوب منظمة ومقاومة للماء.',
             'description_en' => 'Premium leather laptop backpack for 15.6" laptops with organized compartments.',
             'price' => 1900.00, 'category_id' => $cId['bags-luggage'], 'category' => 'bags-luggage', 'brand' => 'LuxeBags',
             'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600']),
             'quantity' => 200, 'slug' => 'leather-laptop-backpack', 'featured' => 1,
             'colors' => json_encode(['Brown', 'Black', 'Tan']),
            ],
            ['sku' => 'BAG-002', 'name' => 'Rolling Luggage 24" Hard Shell',   'name_ar' => 'شنطة سفر 24 بوصة هارد شيل',   'name_en' => 'Rolling Luggage 24" Hard Shell',
             'description_ar' => 'شنطة سفر 24 بوصة هيات صلبة مع أربع عجلات دوارة وقفل TSA.',
             'description_en' => '24" hard shell spinner luggage with 4 spinner wheels and TSA-approved lock.',
             'price' => 2800.00, 'category_id' => $cId['bags-luggage'], 'category' => 'bags-luggage', 'brand' => 'LuxeBags',
             'image' => 'https://images.unsplash.com/photo-1565026057447-bc90a3dceb87?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1565026057447-bc90a3dceb87?w=600']),
             'quantity' => 150, 'slug' => 'rolling-luggage-24-hard-shell', 'hot' => 1,
             'colors' => json_encode(['Navy', 'Black', 'Rose Gold', 'Mint']),
            ],

            // ── Office & Stationery (2) ───────────────────
            ['sku' => 'OFFC-001', 'name' => 'Ergonomic Office Chair',          'name_ar' => 'كرسي مكتبي إرغونومي',         'name_en' => 'Ergonomic Office Chair',
             'description_ar' => 'كرسي مكتبي إرغونومي مع دعم قطني قابل للتعديل وارتفاع متغير.',
             'description_en' => 'Ergonomic mesh office chair with lumbar support, adjustable height and armrests.',
             'price' => 3800.00, 'category_id' => $cId['office-stationery'], 'category' => 'office-stationery', 'brand' => 'WorkComfort',
             'image' => 'https://images.unsplash.com/photo-1505843490701-5be5d0b19d58?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1505843490701-5be5d0b19d58?w=600']),
             'quantity' => 80, 'slug' => 'ergonomic-office-chair', 'featured' => 1, 'best_seller' => 1,
             'colors' => json_encode(['Black', 'Gray', 'Blue']),
            ],
            ['sku' => 'OFFC-002', 'name' => 'Premium Fountain Pen Set',        'name_ar' => 'طقم أقلام حبر فاخر',           'name_en' => 'Premium Fountain Pen Set',
             'description_ar' => 'طقم أقلام حبر فاخر يشمل قلمين وعلبة حبر ومحفظة جلد.',
             'description_en' => 'Luxury fountain pen set with 2 pens, ink bottle, and leather case.',
             'price' => 650.00, 'category_id' => $cId['office-stationery'], 'category' => 'office-stationery', 'brand' => 'InkMaster',
             'image' => 'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=600']),
             'quantity' => 300, 'slug' => 'premium-fountain-pen-set', 'new' => 1,
             'colors' => json_encode(['Gold', 'Silver', 'Black']),
            ],

            // ── Toys & Entertainment (2) ──────────────────
            ['sku' => 'TOYS-001', 'name' => 'LEGO City Police Station',        'name_ar' => 'ليغو مركز شرطة المدينة',       'name_en' => 'LEGO City Police Station',
             'description_ar' => 'مجموعة ليغو مركز شرطة المدينة 668 قطعة للأطفال من 6 سنوات فأكثر.',
             'description_en' => 'LEGO City Police Station 668 pieces set for children 6+ years.',
             'price' => 1200.00, 'category_id' => $cId['toys'], 'category' => 'toys', 'brand' => 'LEGO',
             'image' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600']),
             'quantity' => 250, 'slug' => 'lego-city-police-station', 'hot' => 1, 'best_seller' => 1,
            ],
            ['sku' => 'TOYS-002', 'name' => 'Professional Drone 4K Camera',   'name_ar' => 'درون احترافي كاميرا 4K',        'name_en' => 'Professional Drone 4K Camera',
             'description_ar' => 'درون احترافي بكاميرا 4K مع تثبيت ثلاثي المحاور ومدة طيران 30 دقيقة.',
             'description_en' => 'Professional 4K drone with 3-axis gimbal, 30-min flight time, intelligent modes.',
             'price' => 8500.00, 'category_id' => $cId['toys'], 'category' => 'toys', 'brand' => 'AeroPro',
             'image' => 'https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600',
             'images' => json_encode(['https://images.unsplash.com/photo-1473968512647-3e447244af8f?w=600','https://images.unsplash.com/photo-1527977966376-1c8408f9f108?w=600']),
             'quantity' => 60, 'slug' => 'professional-drone-4k-camera', 'featured' => 1, 'hot' => 1,
            ],
        ];

        // Insert all products
        foreach ($products as $product) {
            $product = array_merge($defaults, $product);
            $product['description'] = $product['description_en'] ?? '';
            DB::table('products')->insert($product);
        }

        // ─────────────────────────────────────────
        // 4. SUPPLIER PRICES
        // ─────────────────────────────────────────
        $pMap = DB::table('products')->pluck('id', 'sku');

        $supplierPrices = [
            // Electronics → TechPeak factory
            ['supplier_id' => $techF,   'product_id' => $pMap['ELEC-001'], 'price' => 1000.00, 'unit_price' => 1000.00],
            ['supplier_id' => $techF,   'product_id' => $pMap['ELEC-002'], 'price' => 3000.00, 'unit_price' => 3000.00],
            ['supplier_id' => $techF,   'product_id' => $pMap['ELEC-003'], 'price' => 700.00,  'unit_price' => 700.00],
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['ELEC-004'], 'price' => 1800.00, 'unit_price' => 1800.00],
            // Clothing → StyleHub vendor
            ['supplier_id' => $styleV,  'product_id' => $pMap['CLTH-001'], 'price' => 380.00,  'unit_price' => 380.00],
            ['supplier_id' => $styleV,  'product_id' => $pMap['CLTH-002'], 'price' => 560.00,  'unit_price' => 560.00],
            ['supplier_id' => $styleV,  'product_id' => $pMap['CLTH-003'], 'price' => 600.00,  'unit_price' => 600.00],
            // Home → HomeBase factory
            ['supplier_id' => $homeF,   'product_id' => $pMap['HOME-001'], 'price' => 1800.00, 'unit_price' => 1800.00],
            ['supplier_id' => $homeF,   'product_id' => $pMap['HOME-002'], 'price' => 1500.00, 'unit_price' => 1500.00],
            ['supplier_id' => $homeF,   'product_id' => $pMap['HOME-003'], 'price' => 300.00,  'unit_price' => 300.00],
            // Sports → SportZone vendor
            ['supplier_id' => $sportV,  'product_id' => $pMap['SPRT-001'], 'price' => 1200.00, 'unit_price' => 1200.00],
            ['supplier_id' => $sportV,  'product_id' => $pMap['SPRT-002'], 'price' => 280.00,  'unit_price' => 280.00],
            // Accessories → StyleHub vendor
            ['supplier_id' => $styleV,  'product_id' => $pMap['ACC-001'],  'price' => 260.00,  'unit_price' => 260.00],
            ['supplier_id' => $styleV,  'product_id' => $pMap['ACC-002'],  'price' => 420.00,  'unit_price' => 420.00],
            // Beauty → TechPeak (proxy) + StyleHub
            ['supplier_id' => $techF,   'product_id' => $pMap['BEAU-001'], 'price' => 750.00,  'unit_price' => 750.00],
            ['supplier_id' => $styleV,  'product_id' => $pMap['BEAU-002'], 'price' => 340.00,  'unit_price' => 340.00],
            // Appliances → GadgetWorld factory
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['APPL-001'], 'price' => 3800.00, 'unit_price' => 3800.00],
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['APPL-002'], 'price' => 2600.00, 'unit_price' => 2600.00],
            // Bags → LuxeBags vendor
            ['supplier_id' => $luxeV,   'product_id' => $pMap['BAG-001'],  'price' => 1500.00, 'unit_price' => 1500.00],
            ['supplier_id' => $luxeV,   'product_id' => $pMap['BAG-002'],  'price' => 2200.00, 'unit_price' => 2200.00],
            // Office → multiple
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['OFFC-001'], 'price' => 3000.00, 'unit_price' => 3000.00],
            ['supplier_id' => $styleV,  'product_id' => $pMap['OFFC-002'], 'price' => 500.00,  'unit_price' => 500.00],
            // Toys → GadgetWorld
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['TOYS-001'], 'price' => 950.00,  'unit_price' => 950.00],
            ['supplier_id' => $gadgetF, 'product_id' => $pMap['TOYS-002'], 'price' => 7000.00, 'unit_price' => 7000.00],
        ];

        foreach ($supplierPrices as &$sp) {
            $sp['created_at'] = now();
            $sp['updated_at'] = now();
        }
        DB::table('product_supplier_prices')->insert($supplierPrices);

        // ─────────────────────────────────────────
        // 5. PRICING TIERS
        // ─────────────────────────────────────────
        $tiers = [
            // ELEC-001
            ['product_id' => $pMap['ELEC-001'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 1200.00],
            ['product_id' => $pMap['ELEC-001'], 'min_quantity' => 10, 'max_quantity' => 49,  'price_per_unit' => 1100.00],
            ['product_id' => $pMap['ELEC-001'], 'min_quantity' => 50, 'max_quantity' => null,'price_per_unit' => 1000.00],
            // ELEC-002
            ['product_id' => $pMap['ELEC-002'], 'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 3500.00],
            ['product_id' => $pMap['ELEC-002'], 'min_quantity' => 5,  'max_quantity' => 19,  'price_per_unit' => 3200.00],
            ['product_id' => $pMap['ELEC-002'], 'min_quantity' => 20, 'max_quantity' => null,'price_per_unit' => 3000.00],
            // ELEC-003
            ['product_id' => $pMap['ELEC-003'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 850.00],
            ['product_id' => $pMap['ELEC-003'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 750.00],
            // ELEC-004
            ['product_id' => $pMap['ELEC-004'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 2200.00],
            ['product_id' => $pMap['ELEC-004'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 1900.00],
            // CLTH-001
            ['product_id' => $pMap['CLTH-001'], 'min_quantity' => 1,   'max_quantity' => 19,  'price_per_unit' => 450.00],
            ['product_id' => $pMap['CLTH-001'], 'min_quantity' => 20,  'max_quantity' => 99,  'price_per_unit' => 420.00],
            ['product_id' => $pMap['CLTH-001'], 'min_quantity' => 100, 'max_quantity' => null,'price_per_unit' => 380.00],
            // CLTH-002
            ['product_id' => $pMap['CLTH-002'], 'min_quantity' => 1,  'max_quantity' => 19,  'price_per_unit' => 680.00],
            ['product_id' => $pMap['CLTH-002'], 'min_quantity' => 20, 'max_quantity' => null,'price_per_unit' => 600.00],
            // CLTH-003
            ['product_id' => $pMap['CLTH-003'], 'min_quantity' => 1,  'max_quantity' => 19,  'price_per_unit' => 750.00],
            ['product_id' => $pMap['CLTH-003'], 'min_quantity' => 20, 'max_quantity' => null,'price_per_unit' => 680.00],
            // HOME-001
            ['product_id' => $pMap['HOME-001'], 'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 2200.00],
            ['product_id' => $pMap['HOME-001'], 'min_quantity' => 5,  'max_quantity' => null,'price_per_unit' => 2000.00],
            // HOME-002
            ['product_id' => $pMap['HOME-002'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 1800.00],
            ['product_id' => $pMap['HOME-002'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 1600.00],
            // HOME-003
            ['product_id' => $pMap['HOME-003'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 380.00],
            ['product_id' => $pMap['HOME-003'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 330.00],
            // SPRT-001
            ['product_id' => $pMap['SPRT-001'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 1500.00],
            ['product_id' => $pMap['SPRT-001'], 'min_quantity' => 10, 'max_quantity' => 49,  'price_per_unit' => 1350.00],
            ['product_id' => $pMap['SPRT-001'], 'min_quantity' => 50, 'max_quantity' => null,'price_per_unit' => 1200.00],
            // SPRT-002
            ['product_id' => $pMap['SPRT-002'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 350.00],
            ['product_id' => $pMap['SPRT-002'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 300.00],
            // ACC-001
            ['product_id' => $pMap['ACC-001'],  'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 320.00],
            ['product_id' => $pMap['ACC-001'],  'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 280.00],
            // ACC-002
            ['product_id' => $pMap['ACC-002'],  'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 550.00],
            ['product_id' => $pMap['ACC-002'],  'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 480.00],
            // BEAU-001
            ['product_id' => $pMap['BEAU-001'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 950.00],
            ['product_id' => $pMap['BEAU-001'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 850.00],
            // BEAU-002
            ['product_id' => $pMap['BEAU-002'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 420.00],
            ['product_id' => $pMap['BEAU-002'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 370.00],
            // APPL-001
            ['product_id' => $pMap['APPL-001'], 'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 4500.00],
            ['product_id' => $pMap['APPL-001'], 'min_quantity' => 5,  'max_quantity' => null,'price_per_unit' => 4000.00],
            // APPL-002
            ['product_id' => $pMap['APPL-002'], 'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 3200.00],
            ['product_id' => $pMap['APPL-002'], 'min_quantity' => 5,  'max_quantity' => null,'price_per_unit' => 2900.00],
            // BAG-001
            ['product_id' => $pMap['BAG-001'],  'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 1900.00],
            ['product_id' => $pMap['BAG-001'],  'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 1650.00],
            // BAG-002
            ['product_id' => $pMap['BAG-002'],  'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 2800.00],
            ['product_id' => $pMap['BAG-002'],  'min_quantity' => 5,  'max_quantity' => null,'price_per_unit' => 2500.00],
            // OFFC-001
            ['product_id' => $pMap['OFFC-001'], 'min_quantity' => 1,  'max_quantity' => 4,   'price_per_unit' => 3800.00],
            ['product_id' => $pMap['OFFC-001'], 'min_quantity' => 5,  'max_quantity' => null,'price_per_unit' => 3400.00],
            // OFFC-002
            ['product_id' => $pMap['OFFC-002'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 650.00],
            ['product_id' => $pMap['OFFC-002'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 580.00],
            // TOYS-001
            ['product_id' => $pMap['TOYS-001'], 'min_quantity' => 1,  'max_quantity' => 9,   'price_per_unit' => 1200.00],
            ['product_id' => $pMap['TOYS-001'], 'min_quantity' => 10, 'max_quantity' => null,'price_per_unit' => 1000.00],
            // TOYS-002
            ['product_id' => $pMap['TOYS-002'], 'min_quantity' => 1,  'max_quantity' => 2,   'price_per_unit' => 8500.00],
            ['product_id' => $pMap['TOYS-002'], 'min_quantity' => 3,  'max_quantity' => null,'price_per_unit' => 7500.00],
        ];

        foreach ($tiers as &$tier) {
            $tier['created_at'] = now();
            $tier['updated_at'] = now();
        }
        DB::table('product_pricing_tiers')->insert($tiers);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Seeded: 10 categories, 6 suppliers, 25 products with pricing tiers!');
    }
}
