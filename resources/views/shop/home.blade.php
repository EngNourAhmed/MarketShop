@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .hide-scroll-bar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scroll-bar::-webkit-scrollbar {
            display: none;
        }

 #heroCarousel.hero-carousel {
    position: relative;
    height: 100%;
}

 #heroCarousel .carousel-indicators {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    z-index: 20;
    display: flex;
    justify-content: center;
    gap: 8px;
    list-style: none;
    padding: 0;
    margin: 0;
}

 #heroCarousel .carousel-indicators [data-bs-target] {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #94a3b8; /* slate-400 */
    opacity: 1;
    border: none;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

 #heroCarousel .carousel-indicators .active {
    background-color: #f97316 !important; /* orange-500 */
    opacity: 1;
    width: 6px;
    border-radius: 50%;
}

 #heroCarousel .carousel-inner {
    position: relative;
    width: 100%;
    overflow: hidden;
    height: 100%;
}

 #heroCarousel .carousel-item {
    position: relative;
    display: none;
    float: left;
    width: 100%;
    margin-right: -100%;
    backface-visibility: hidden;
    transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
}

 #heroCarousel .carousel-item.active,
 #heroCarousel .carousel-item-next,
 #heroCarousel .carousel-item-prev {
    display: block;
}

 #heroCarousel .carousel-item-next:not(.carousel-item-start),
 #heroCarousel .active.carousel-item-end {
    transform: translateX(100%);
}

 #heroCarousel .carousel-item-prev:not(.carousel-item-end),
 #heroCarousel .active.carousel-item-start {
    transform: translateX(-100%);
}

.text-xxs {
    font-size: 0.65rem;
    line-height: 1rem;
}


         :root {
            --marquee-duration: 18s;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes marquee2 {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(0);
            }
        }

        .marquee,
        .marquee2 {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            display: flex;
            will-change: transform;
        }

        .marquee {
            animation: marquee var(--marquee-duration) linear infinite;
        }

        .marquee2 {
            animation: marquee2 var(--marquee-duration) linear infinite;
        }

    </style>
@endpush

<x-shop-layouts.app title="Trady">
    <div class="space-y-6">
        <!-- Search Bar - Mobile Only (Desktop search is in navbar) -->
        <div class="mb-4 md:hidden">
            <form method="GET" action="{{ route('shop.search') }}" class="relative w-full">
                <input type="hidden" name="type" value="all" />
                <div class="absolute inset-y-0 {{ $isAr ? 'right-0 pr-4' : 'left-0 pl-4' }} flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-500 dark:text-slate-400"></i>
                </div>
                <input type="text" name="q" value="{{ request()->query('q', '') }}" placeholder="{{ $isAr ? 'ابحث عن المنتجات...' : 'Search for products...' }}"
                    class="w-full h-12 rounded-xl bg-white text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 {{ $isAr ? 'pr-12 pl-4' : 'pl-12 pr-4' }}" />
            </form>
        </div>

        <div class="relative w-full overflow-hidden rounded-lg bg-rose-300 dark:bg-rose-500/50 dark:text-white h-8 mb-4 mt-4">
            <div
                class="pointer-events-none absolute left-0 top-0 h-full w-12 bg-gradient-to-r from-orange-200 dark:from-orange-400/50 to-transparent z-10">
            </div>
            <div
                class="pointer-events-none absolute right-0 top-0 h-full w-12 bg-gradient-to-l from-orange-200 dark:from-orange-400/50 to-transparent z-10">
            </div>
            <ul class="marquee flex min-w-max items-center gap-8 md:gap-32 px-3 text-sm font-semibold text-white dark:text-rose-200">
                <li>{{ $isAr ? '10 نقاط مجانية مع كل طلب' : 'Get 10 points free with every order' }}</li>
                <li>{{ $isAr ? 'توصيل مجاني هذا الأسبوع' : 'Free delivery this weekend' }}</li>
                <li>{{ $isAr ? 'جديد: كروت شحن يومية' : 'New: Daily recharge card available' }}</li>
            </ul>
            <ul aria-hidden="true"
                 class="marquee2 flex min-w-max items-center gap-8 md:gap-32 px-3 text-sm font-semibold text-white dark:text-rose-200">
                <li>{{ $isAr ? 'خصم 50% عند شراء 2' : 'Buy 2 for 50% off now' }}</li>
                <li>{{ $isAr ? '10 نقاط مجانية مع كل طلب' : 'Get 10 points free with every order' }}</li>
                <li>{{ $isAr ? 'توصيل مجاني هذا الأسبوع' : 'Free delivery this weekend' }}</li>
                <li>{{ $isAr ? 'جديد: كروت شحن يومية' : 'New: Daily recharge card available' }}</li>
            </ul>
        </div>

        <style>
            .hero-carousel, .hero-carousel .carousel-inner, .hero-carousel .carousel-item {
                height: 280px !important;
            }
            @media (min-width: 768px) {
                .hero-carousel, .hero-carousel .carousel-inner, .hero-carousel .carousel-item {
                    height: 420px !important;
                }
            }
        </style>
        <!-- Bootstrap Carousel Hero -->
        <div id="heroCarousel" class="carousel slide hero-carousel rounded-2xl overflow-hidden shadow-sm bg-slate-200 dark:bg-gradient-to-br dark:from-[#1e2235] dark:to-[#2d3250]" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active h-full" data-bs-interval="2000">
                    <div class="flex items-center justify-center h-full text-4xl font-extrabold text-slate-600 dark:text-white tracking-widest bg-transparent">
                        AD 1
                    </div>
                </div>
                <div class="carousel-item h-full" data-bs-interval="2000">
                    <div class="flex items-center justify-center h-full text-4xl font-extrabold text-slate-600 dark:text-white tracking-widest bg-transparent">
                        AD 2
                    </div>
                </div>
                <div class="carousel-item h-full" data-bs-interval="2000">
                    <div class="flex items-center justify-center h-full text-4xl font-extrabold text-slate-600 dark:text-white tracking-widest bg-transparent">
                        AD 3
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full overflow-hidden mb-6">
            <div class="flex overflow-x-auto hide-scroll-bar pb-4">
                <div class="flex flex-nowrap gap-4 md:gap-6 px-1">
                    @foreach ($categories ?? [] as $cat)
                        @php($catIcon = $cat->icon ?? 'grid-2x2')
                        @php($catBg = $cat->bg_color ?? '#f3f4f6')
                        <div class="flex-shrink-0 w-20 md:w-24 group">
                            <a href="{{ route('shop.categories.show', $cat->slug) }}" class="flex flex-col items-center justify-center gap-2">
                                <div class="relative flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-full overflow-hidden shadow-sm group-hover:shadow-md transition-all group-hover:scale-105 border border-gray-100 dark:border-slate-700"
                                    style="background-color: {{ $catBg }};">
                                    @if (!empty($cat->image))
                                        <img src="{{ \App\Helpers\CurrencyHelper::imageUrl($cat->image) }}"
                                            alt="{{ $cat->name_en }}"
                                            class="cat-img w-full h-full object-cover"
                                            onerror="this.style.display='none'; var ic=document.createElement('i'); ic.setAttribute('data-lucide','{{ $catIcon }}'); ic.className='w-8 h-8 text-white'; this.parentNode.appendChild(ic); if(window.lucide){lucide.createIcons();}" />
                                    @else
                                        <i data-lucide="{{ $catIcon }}"
                                            class="w-8 h-8 text-white"></i>
                                    @endif
                                </div>
                                <span class="text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 text-center w-full px-1 line-clamp-2 leading-tight">
                                    {{ $isAr ? $cat->name_ar ?? $cat->name_en : $cat->name_en ?? $cat->name_ar }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <h2
                    class="mb-4 text-2xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] to-[60%] bg-clip-text text-transparent">

                    {{ $isAr ? 'الأكثر مبيعاً' : 'Best sellers' }}</h2>
            </div>

            <div class="swiper-container best-sellers-swiper">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @forelse(($bestSellers ?? []) as $product)
                        @php($factory = ($product->suppliers ?? collect())->firstWhere('type', 'factory'))
                        @php($supplier = ($product->suppliers ?? collect())->firstWhere('type', 'vendor'))
                        @php($factoryPrice = (float) ($factory->pivot->price ?? 0))
                        @php($supplierPrice = (float) ($supplier->pivot->price ?? 0))
                        
                        @php($factoryFormatted = \App\Helpers\CurrencyHelper::format($factoryPrice))
                        @php($supplierFormatted = \App\Helpers\CurrencyHelper::format($supplierPrice))
                        @php($suppliersForModal = ($product->suppliers ?? collect())->map(fn($s) => [
                            'id' => (int) $s->id,
                            'name' => (string) ($s->name ?? ''),
                            'type' => (string) ($s->type ?? ''),
                            'price' => (float) ($s->pivot->price ?? 0),
                            'unit_price' => (float) ($s->pivot->unit_price ?? 0),
                            'quantity' => $s->pivot->quantity !== null ? (int) $s->pivot->quantity : null,
                        ])->values())
                        @php($pricingTiersForModal = ($product->pricingTiers ?? collect())
                            ->groupBy('supplier_id')
                            ->map(fn($g) => $g->map(fn($t) => [
                                'min' => (int) $t->min_quantity,
                                'max' => $t->max_quantity ? (int) $t->max_quantity : null,
                                'price' => (float) $t->price_per_unit,
                            ])->values())
                            ->toArray())
                        @php($colorsStr = collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                        @php($sizesStr = collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                        
                        @php($avgRatingVal = (float) ($product->ratings_avg_rating ?? 0))
                        @php($ratingsCountVal = (int) ($product->ratings_count ?? 0))

                        <!-- Product Card (Updated Design to match Featured) -->
                        <div class="group relative p-3 rounded-2xl bg-white border border-gray-200 shadow-sm hover:cursor-pointer dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 dark:border-slate-800 dark:shadow-xl hover:border-rose-500/30 transition-all duration-300"
                            data-shop-product data-modal-mode="popup" data-modal-readonly="1"
                            data-product-id="{{ $product->id }}"
                            data-name-en="{{ $product->name_en ?? $product->name }}"
                            data-name-ar="{{ $product->name_ar ?? $product->name }}"
                            data-description-en="{{ $product->description_en ?? $product->description }}"
                            data-description-ar="{{ $product->description_ar ?? $product->description }}"
                            data-image="{{ \App\Helpers\CurrencyHelper::imageUrl($product->image) }}"
                            data-suppliers='@json($suppliersForModal)'
                            data-pricing-tiers='@json($pricingTiersForModal)'
                            data-colors="{{ $colorsStr }}"
                            data-sizes="{{ $sizesStr }}">
                            
                            <!-- Product Image Container -->
                            <div class="w-full h-24 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                    <img src="{{ \App\Helpers\CurrencyHelper::imageUrl($product->image) }}" alt="product"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            </div>

                            <!-- Product Name -->
                            <div class="mt-3 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 min-h-[2.5rem]">
                                {{ $isAr ? $product->name_ar ?? $product->name : $product->name_en ?? $product->name }}
                            </div>

                            <!-- Rating -->
                            <div class="mt-2 flex items-center justify-between gap-2 text-[11px] sm:text-xs">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="star" class="w-3.5 h-3.5 {{ $avgRatingVal > 0 ? 'text-yellow-400 fill-current' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($avgRatingVal, 1) }}</span>
                                </div>
                                <div class="text-gray-500 dark:text-slate-400">
                                    ({{ $ratingsCountVal }})
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="mt-3 space-y-1.5 text-[11px] sm:text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المصنع:' : 'Factory:' }}</span>
                                    <span class="text-gray-900 dark:text-slate-200 font-bold">{{ $factoryFormatted }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المورد:' : 'Supplier:' }}</span>
                                    <span class="text-gray-900 dark:text-slate-200 font-bold">{{ $supplierFormatted }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div
                                class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-dashed border-gray-300 dark:border-slate-700 text-sm text-gray-500 dark:text-gray-300 flex items-center justify-center">
                                {{ $isAr ? 'لا توجد منتجات.' : 'No products found.' }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="overflow-hidden mt-6">
            <div class="flex items-center justify-between mb-3">
                <h2
                     class="mb-4 text-2xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] to-[60%] bg-clip-text text-transparent">
                    {{ $isAr ? 'المنتجات المميزة' : 'Featured Products' }}
                </h2>
                @php($activeSort = (string) ($homeSort ?? $homeFilter ?? 'recent'))
                @php($activeType = (string) ($homeType ?? 'all'))
                @php($activeFactoryId = (int) ($factoryId ?? 0))
                @php($activeSupplierId = (int) ($supplierId ?? 0))
                @php($resourceFilters = [
                    ['key' => 'all', 'ar' => 'الكل', 'en' => 'All'],
                    ['key' => 'reviewed', 'ar' => 'الأكثر مراجعة', 'en' => 'Most Reviewed'],
                    ['key' => 'recent', 'ar' => 'المضافة حديثاً', 'en' => 'Recently Added'],
                ])
                @php($typeFilters = [
                    ['key' => 'all', 'ar' => 'الكل', 'en' => 'All'],
                    ['key' => 'factory', 'ar' => 'مصنع', 'en' => 'Factory'],
                    ['key' => 'supplier', 'ar' => 'مورد', 'en' => 'Supplier'],
                ])
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-1 bg-gray-100 dark:bg-slate-900/60 border border-gray-200 dark:border-slate-700 rounded-2xl p-1">
                        @foreach($resourceFilters as $f)
                            <a href="{{ route('customer.home', ['type' => $activeType, 'sort' => $f['key'], 'factory_id' => $activeFactoryId ?: null, 'supplier_id' => $activeSupplierId ?: null]) }}"
                                class="featured-filter-link px-4 py-2 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $activeSort === $f['key'] ? 'bg-white dark:bg-slate-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $isAr ? $f['ar'] : $f['en'] }}
                            </a>
                        @endforeach
                    </div>
                    <div class="inline-flex items-center gap-1 bg-gray-100 dark:bg-slate-900/60 border border-gray-200 dark:border-slate-700 rounded-2xl p-1">
                        @foreach($typeFilters as $tf)
                            <a href="{{ route('customer.home', ['type' => $tf['key'], 'sort' => $activeSort, 'factory_id' => $activeFactoryId ?: null, 'supplier_id' => $activeSupplierId ?: null]) }}"
                                class="featured-filter-link px-4 py-2 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $activeType === $tf['key'] ? 'bg-white dark:bg-slate-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $isAr ? $tf['ar'] : $tf['en'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="featured-products-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 transition-opacity duration-200">
                @forelse(($homeProducts ?? []) as $product)
                    @php($factory = ($product->suppliers ?? collect())->firstWhere('type', 'factory'))
                    @php($supplier = ($product->suppliers ?? collect())->firstWhere('type', 'vendor'))
                    @php($factoryPrice = (float) ($factory->pivot->price ?? 0))
                    @php($supplierPrice = (float) ($supplier->pivot->price ?? 0))
                    @php($usdRate = 50)
                    @php($factoryUsd = (int) round($factoryPrice / $usdRate))
                    @php($supplierUsd = (int) round($supplierPrice / $usdRate))
                    @php($factoryPriceK = $factoryPrice >= 1000 ? (string) round($factoryPrice / 1000) . 'k' : number_format($factoryPrice, 0, '.', ','))
                    @php($supplierPriceK = $supplierPrice >= 1000 ? (string) round($supplierPrice / 1000) . 'k' : number_format($supplierPrice, 0, '.', ','))
                    @php($suppliersForModal = ($product->suppliers ?? collect())->map(fn($s) => [
                        'id' => (int) $s->id,
                        'name' => (string) ($s->name ?? ''),
                        'type' => (string) ($s->type ?? ''),
                        'price' => (float) ($s->pivot->price ?? 0),
                        'unit_price' => (float) ($s->pivot->unit_price ?? 0),
                        'quantity' => $s->pivot->quantity !== null ? (int) $s->pivot->quantity : null,
                    ])->values())
                    @php($pricingTiersForModal = ($product->pricingTiers ?? collect())
                        ->groupBy('supplier_id')
                        ->map(fn($g) => $g->map(fn($t) => [
                            'min' => (int) $t->min_quantity,
                            'max' => $t->max_quantity ? (int) $t->max_quantity : null,
                            'price' => (float) $t->price_per_unit,
                        ])->values())
                        ->toArray())
                    @php($colorsStr = collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))
                    @php($sizesStr = collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(','))

                        <div class="p-3 rounded-2xl bg-white border border-gray-200 shadow-sm hover:cursor-pointer dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 dark:border-slate-800 dark:shadow-lg"
                        data-shop-product data-modal-mode="center" data-modal-readonly="1"
                        data-product-id="{{ $product->id }}"
                        data-name-en="{{ $product->name_en ?? $product->name }}"
                        data-name-ar="{{ $product->name_ar ?? $product->name }}"
                        data-image="{{ \App\Helpers\CurrencyHelper::imageUrl($product->image) }}"
                        data-description="{{ $product->description ?? '' }}"
                        data-description-ar="{{ $product->description_ar ?? '' }}"
                        data-description-en="{{ $product->description_en ?? '' }}"
                        data-color="{{ $product->color ?? '' }}" data-size="{{ $product->size ?? '' }}"
                        data-colors="{{ $colorsStr }}" data-sizes="{{ $sizesStr }}"
                        data-suppliers='@json($suppliersForModal)'
                        data-factory-id="{{ (int) ($factory->id ?? 0) ?: '' }}"
                        data-supplier-id="{{ (int) ($supplier->id ?? 0) ?: '' }}"
                        data-factory-name="{{ $factory->name ?? '' }}"
                        data-factory-price="{{ (string) ($factory->pivot->price ?? '') }}"
                        data-supplier-name="{{ $supplier->name ?? '' }}"
                        data-supplier-price="{{ (string) ($supplier->pivot->price ?? '') }}"
                        data-pricing-tiers='@json($pricingTiersForModal)'>

                        <div
                             class="w-full h-24 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                            <img src="{{ \App\Helpers\CurrencyHelper::imageUrl($product->image) }}" alt="product"
                                class="w-full h-full object-cover" />
                        </div>

                        <div
                            class="mt-3 text-xs sm:text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 min-h-[2.5rem]">
                            {{ $isAr ? $product->name_ar ?? $product->name : $product->name_en ?? $product->name }}
                        </div>

                        @php($avgRatingVal = (float) ($product->ratings_avg_rating ?? 0))
                        @php($ratingsCountVal = (int) ($product->ratings_count ?? 0))
                        <div class="mt-2 flex items-center justify-between gap-2 text-[11px] sm:text-xs">
                            <div class="flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    @php($filled = $avgRatingVal >= $i)
                                    <span
                                        class="text-base sm:text-lg {{ $filled ? 'text-yellow-500' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                                @endfor
                            </div>
                            <div class="text-gray-600 dark:text-slate-300">
                                <span>{{ number_format($avgRatingVal, 1, '.', ',') }}</span>
                                <span class="text-gray-500 dark:text-slate-400">({{ $ratingsCountVal }})</span>
                            </div>
                        </div>

                        <div class="mt-3 space-y-2 text-[11px] sm:text-xs text-gray-700 dark:text-slate-200">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المصنع:' : 'Factory:' }}</span>
                                <span class="text-gray-900 dark:text-slate-200 font-bold">{{ \App\Helpers\CurrencyHelper::format($factoryPrice) }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المورد:' : 'Supplier:' }}</span>
                                <span class="text-gray-900 dark:text-slate-200 font-bold">{{ \App\Helpers\CurrencyHelper::format($supplierPrice) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-2 sm:col-span-3 lg:col-span-4 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-dashed border-gray-300 dark:border-slate-700 text-sm text-gray-500 dark:text-gray-300 text-center">
                        {{ $isAr ? 'لا توجد منتجات.' : 'No products found.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-shop-layouts.app>

@push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Featured products filter animation
            var grid = document.getElementById('featured-products-grid');
            document.querySelectorAll('.featured-filter-link').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    if (!grid) return;
                    grid.style.opacity = '0.6';
                    grid.style.pointerEvents = 'none';
                    setTimeout(function () {
                        grid.style.opacity = '';
                        grid.style.pointerEvents = '';
                    }, 400);
                });
            });


            // Initialize Swipers
            if (typeof Swiper !== 'undefined') {
            // Bootstrap Carousel initialization is automatic with data-bs-ride="carousel"
            // but we can ensure it starts correctly
            const carouselEl = document.querySelector('#heroCarousel');
            if (carouselEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Carousel(carouselEl, {
                    interval: 3000,
                    ride: 'carousel',
                    pause: 'hover'
                });
            }

                // Note: Categories Swiper initialization removed as we now use native CSS horizontal scroll
            }
        });
    </script>
@endpush
