<!DOCTYPE html>
@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')
@php($shopUser = auth()->user())
@php($cartItemsCount = $shopUser ? (int) \App\Models\Cart::query()->where('user_id', $shopUser->id)->sum('quantity') : 0)
@php($countries = \App\Models\Countery::all())
@php($currentCountry = \App\Helpers\CurrencyHelper::getCurrentCountry())
<html lang="{{ $lang }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Trady Shop' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" />

    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scroll { overflow: hidden; }

        button,
        [role="button"],
        [data-shop-product] {
            cursor: pointer;
        }

        @keyframes shopModalPopIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes shopModalPopOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.98); }
        }

        @keyframes shopOverlayFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes shopOverlayFadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes shopSheetIn {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        @keyframes shopSheetOut {
            from { transform: translateY(0); }
            to { transform: translateY(100%); }
        }

        .shop-modal-anim-pop-in { animation: shopModalPopIn 400ms cubic-bezier(.2,.8,.2,1) both; }
        .shop-modal-anim-pop-out { animation: shopModalPopOut 300ms cubic-bezier(.4,0,.6,1) both; }
        .shop-modal-anim-overlay-in { animation: shopOverlayFadeIn 300ms ease-out both; }
        .shop-modal-anim-overlay-out { animation: shopOverlayFadeOut 250ms ease-in both; }
        .shop-modal-anim-sheet-in { animation: shopSheetIn 400ms cubic-bezier(.2,.8,.2,1) both; }
        .shop-modal-anim-sheet-out { animation: shopSheetOut 300ms cubic-bezier(.4,0,.6,1) both; }

        /* Country Modal Animations */
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        @keyframes slideDown {
            from { transform: translateY(0); }
            to { transform: translateY(100%); }
        }
        .slide-up { animation: slideUp 0.3s ease-out forwards; }
        .slide-down { animation: slideDown 0.3s ease-in forwards; }

        .expanding-clone {
            position: fixed;
            z-index: 99999;
            border-radius: 16px;
            overflow: hidden;
            transition: all 320ms cubic-bezier(.2, .8, .2, 1);
            will-change: top, left, width, height, opacity;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            background: inherit;
        }

        @media (min-width: 768px) {
            .expanding-clone {
                border-radius: 24px;
            }
        }
    </style>

    <script>
        // Global Currency Helpers
        window.exchangeRate = {{ \App\Helpers\CurrencyHelper::getCurrentCountry()?->exchange_rate ?? 1 }};
        window.currencySymbol = '{{ \App\Helpers\CurrencyHelper::getSymbol() }}';
        window.isAr = {{ session('lang') === 'ar' ? 'true' : 'false' }};

        window.formatMoney = function(num) {
            const n = (Number(num || 0)) / window.exchangeRate;
            if (!Number.isFinite(n)) return '';
            
            const formatted = n.toLocaleString(undefined, { 
                maximumFractionDigits: n < 1 ? 2 : 0,
                minimumFractionDigits: n < 1 ? 2 : 0
            });

            return window.isAr ? formatted + ' ' + window.currencySymbol : window.currencySymbol + ' ' + formatted;
        };

        window.formatUsd = function(priceEgp) {
            return '';
        };

        window.formatEgp = function(price) {
            return window.formatMoney(price);
        };
    </script>

    @stack('head')

</head>
<body class="flex justify-center bg-slate-100 dark:bg-slate-950">
    <div class="w-full flex justify-center">
        <div class="w-full flex justify-center {{ $isAr ? 'flex-row-reverse' : '' }}">
            @include('shop.partials.sidebar')

            <div class="mobile-container w-full relative overflow-hidden min-h-screen bg-white dark:bg-slate-900 shadow-[0_4px_12px_rgba(0,0,0,0.1)]">
            <header class="sticky top-0 z-30 border-b border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-3">
                        @if(isset($headerActions))
                            {{ $headerActions }}
                        @elseif(View::hasSection('header-actions'))
                            @yield('header-actions')
                        @else
                            <button id="shop-menu-btn" class="md:hidden text-gray-700 dark:text-slate-200">
                                <i data-lucide="align-justify" class="w-6 h-6"></i>
                            </button>
                        @endif

                        @if(isset($headerTitle))
                           <div class="flex flex-col">
                                {{ $headerTitle }}
                           </div>
                        @elseif(View::hasSection('header-title'))
                            @yield('header-title')
                        @else
                            <h1 class="text-3xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent md:hidden">{{ $title ?? 'Trady' }}</h1>
                        @endif
                    </div>

                    <!-- Desktop Search Bar - Full Width (Home Only) -->
                    <div class="hidden md:flex flex-1 mx-4">
                        @if(request()->routeIs('customer.home') || request()->routeIs('shop.home'))
                            <form action="{{ route('shop.search') }}" method="GET" class="w-full relative">
                                <input type="text" name="q" placeholder="{{ $isAr ? 'ابحث عن منتجات...' : 'Search for products...' }}"
                                    class="w-full h-10 pl-10 pr-4 rounded-full bg-gray-100 dark:bg-slate-800 border-none focus:ring-2 focus:ring-rose-500 text-sm focus:outline-none dark:text-white">
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                            </form>
                        @else
                            <div class="flex items-center">
                                @if(isset($headerTitle))
                                    {{ $headerTitle }}
                                @elseif(View::hasSection('header-title'))
                                    @yield('header-title')
                                @else
                                    <h1 class="text-xl font-bold bg-gradient-to-tr from-[#FFF687] to-[#F6416C] bg-clip-text text-transparent truncate">{{ $title ?? 'Trady' }}</h1>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('shop.cart.index') }}" class="relative inline-flex items-center justify-center p-2 text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-colors">
                            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                            @if($cartItemsCount > 0)
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold bg-rose-600 text-white shadow-sm ring-2 ring-white dark:ring-slate-900">
                                    {{ $cartItemsCount > 99 ? '99+' : $cartItemsCount }}
                                </span>
                            @endif
                        </a>

                        <button type="button" onclick="openCountryModal()" class="flex items-center gap-2 p-1 px-2 bg-gray-100 dark:bg-slate-800 rounded-full border border-gray-200 dark:border-slate-700 hover:ring-2 hover:ring-rose-500/20 transition-all">
                            <span class="fi fi-{{ strtolower($currentCountry->code ?? 'eg') }} rounded-sm shadow-sm"></span>
                            <span class="hidden sm:inline text-xs font-semibold text-gray-700 dark:text-slate-300 uppercase">{{ $currentCountry->currency_code ?? 'EGP' }}</span>
                            <i data-lucide="globe" class="w-3 h-3 text-gray-400"></i>
                        </button>

                        <button type="button" id="shop-lang-toggle-btn" class="p-1.5 bg-gray-50 dark:bg-slate-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
                             <i data-lucide="globe-2" class="w-5 h-5 text-gray-500 dark:text-slate-400"></i>
                        </button>
                    </div>
            </header>

            <main class="p-4 md:p-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            <!-- Country Selection Modal -->
            <div id="country-modal-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] hidden transition-opacity duration-300"></div>
            <div id="country-modal" class="fixed bottom-0 left-0 right-0 max-w-[420px] mx-auto bg-white dark:bg-gray-900 rounded-t-3xl z-[70] hidden shadow-2xl overflow-hidden max-h-[85vh] flex flex-col translate-y-full">
                <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'اختر الدولة' : 'Select Country' }}</h3>
                        <button onclick="closeCountryModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
                        </button>
                    </div>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" id="country-search" placeholder="{{ $isAr ? 'البحث عن دولة...' : 'Search for a country...' }}" 
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-rose-500/20 dark:text-white text-sm">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-2" id="country-list">
                    @foreach($countries as $country)
                    <form action="{{ route('shop.country.set') }}" method="POST" class="country-item m-0">
                        @csrf
                        <input type="hidden" name="country_id" value="{{ $country->id }}">
                        <button type="submit" class="w-full flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-2xl transition-all group">
                            <span class="fi fi-{{ strtolower($country->code) }} text-xl rounded-sm shadow-sm"></span>
                            <div class="flex-1 text-left {{ $isAr ? 'text-right' : '' }}">
                                <div class="font-bold text-gray-900 dark:text-white group-hover:text-rose-500 transition-colors">{{ $country->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $country->currency }} ({{ $country->currency_code }})</div>
                            </div>
                            @if(($currentCountry->id ?? null) == $country->id)
                                <i data-lucide="check" class="w-5 h-5 text-rose-500"></i>
                            @endif
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
            <!-- Bottom Navigation Bar -->
            <footer class="fixed md:hidden bottom-0 left-0 right-0 max-w-[420px] mx-auto bg-white border-t border-gray-200 z-50 dark:bg-gray-800 dark:border-gray-700 mb-0">
                <div class="flex items-center justify-around py-2 px-2">
                    <a href="{{ route('customer.home') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('customer.home') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="home" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('customer.home') ? 'font-semibold' : '' }}">{{ $isAr ? 'الرئيسية' : 'Home' }}</span>
                    </a>
                    <a href="{{ route('shop.notifications.index') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('shop.notifications.index') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('shop.notifications.index') ? 'font-semibold' : '' }}">{{ $isAr ? 'إشعارات' : 'Notifications' }}</span>
                    </a>
                    <a href="{{ route('shop.orders.index') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('shop.orders.index') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('shop.orders.index') ? 'font-semibold' : '' }}">{{ $isAr ? 'طلبات' : 'Orders' }}</span>
                    </a>
                    <a href="{{ route('shop.special_orders.create') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('shop.special_orders.*') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="star" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('shop.special_orders.*') ? 'font-semibold' : '' }}">{{ $isAr ? 'خاص' : 'Special' }}</span>
                    </a>
                    <a href="{{ route('shop.messages.index') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('shop.messages.index') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('shop.messages.index') ? 'font-semibold' : '' }}">{{ $isAr ? 'رسائل' : 'Messages' }}</span>
                    </a>
                    <a href="{{ route('shop.account') }}" class="flex flex-col items-center flex-1 gap-1 {{ request()->routeIs('shop.account') ? 'text-rose-600 dark:text-rose-400' : 'text-gray-500 dark:text-gray-400' }}">
                        <i data-lucide="user" class="w-6 h-6"></i>
                        <span class="text-[10px] {{ request()->routeIs('shop.account') ? 'font-semibold' : '' }}">{{ $isAr ? 'حساب' : 'Account' }}</span>
                    </a>
                </div>
            </footer>

            <div id="shop-product-modal-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div id="shop-product-modal" class="hidden relative z-[60] w-full md:w-[720px] max-w-full max-h-[85vh] rounded-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden flex flex-col">
                    <div class="p-4 relative flex items-center justify-center flex-shrink-0">
                        <h2  class="text-xl md:text-2xl font-bold text-center bg-gradient-to-r from-[#F6416C] via-orange-300 to-[#F6416C] bg-clip-text text-transparent mb-6" id="shop-product-modal-title">{{ $isAr ? 'خيارات الطلب' : 'Ordering Options' }}</h2>
                        <button id="shop-product-modal-close" class="absolute top-1/2 -translate-y-1/2 {{ $isAr ? 'left-4' : 'right-4' }} cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                            <i data-lucide="x" class="w-6 h-6 text-gray-400"></i>
                        </button>
                    </div>

                    <div id="shop-product-modal-order-summary" class="px-4 py-2 border-b border-gray-100 bg-gray-50/60 dark:bg-slate-900/40 dark:border-gray-700 hidden">
                        <div class="flex flex-wrap items-center justify-between gap-3 text-xs md:text-sm">
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'رقم الطلب' : 'Order code' }}</span>
                                <span id="shop-order-code" class="font-semibold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'تاريخ الطلب' : 'Ordered at' }}</span>
                                <span id="shop-order-date" class="font-semibold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex flex-col items-start {{ $isAr ? 'md:items-end' : 'md:items-start' }}">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'الحالة' : 'Status' }}</span>
                                <span id="shop-order-status-text" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"></span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'إجمالي الفاتورة' : 'Total' }}</span>
                                <span id="shop-order-total" class="font-semibold text-rose-600 dark:text-rose-400"></span>
                            </div>
                        </div>
                    </div>

                    <div id="shop-product-modal-type-toggle" class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hidden">
                        <div class="inline-flex items-center gap-2 bg-gray-100 dark:bg-slate-900/60 border border-gray-200 dark:border-slate-700 rounded-2xl p-1">
                            <button type="button" id="shop-modal-type-factory" class="px-4 py-2 rounded-2xl text-sm font-semibold">
                                {{ $isAr ? 'مصنع' : 'Factory' }}
                            </button>
                            <button type="button" id="shop-modal-type-supplier" class="px-4 py-2 rounded-2xl text-sm font-semibold">
                                {{ $isAr ? 'مورد' : 'Supplier' }}
                            </button>
                        </div>
                    </div>

                    <div id="shop-product-modal-global-supplier-wrap" class="px-4 pb-3 border-b border-gray-100 dark:border-gray-700 hidden">
                        <label for="shop-product-modal-global-supplier" class="text-xs text-gray-600 dark:text-slate-300">{{ $isAr ? 'اختر المورد' : 'Choose supplier' }}</label>
                        <select id="shop-product-modal-global-supplier" class="mt-2 w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3">
                            <option value="">-</option>
                        </select>
                    </div>

                    <form id="shop-product-modal-form" method="POST" action="{{ route('shop.cart.store') }}" class="flex-1 overflow-y-auto overflow-x-hidden">
                        @csrf
                        <input type="hidden" name="product_id" id="shop-product-modal-product-id-input" value="" />
                        <input type="hidden" name="supplier_id" id="shop-product-modal-supplier-id-input" value="" />
                        <input type="hidden" name="quantity" id="shop-product-modal-quantity-hidden" value="1" />

                        <div id="shop-product-modal-grid" class="p-4 md:p-6 overflow-y-auto overflow-x-hidden space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 bg-white dark:bg-gray-900">
                            <div id="shop-product-modal-factory-section" class="space-y-4 rounded-2xl p-4">
                                <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                    <i data-lucide="package" class="w-5 h-5"></i>
                                    <span>{{ $isAr ? 'مصنع' : 'Factory' }}</span>
                                </div>

                                <div id="shop-product-modal-factory-select-wrap" class="hidden mt-3">
                                <select id="shop-product-modal-factory-select" class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3">
                                    <option value="">-</option>
                                </select>
                            </div>

                            <div class="mt-4 w-full h-28 md:h-32 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                <img id="shop-product-modal-factory-image" src="" alt="product" class="w-full h-full object-cover" />
                            </div>

                            <div class="mt-4">
                                <div class="font-semibold text-gray-900 dark:text-white" id="shop-product-modal-product-name-factory"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1" id="shop-product-modal-description-factory"></div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-gray-900 dark:text-white font-semibold" id="shop-product-modal-factory-usd"></div>
                                <div class="text-rose-500 dark:text-rose-400 font-semibold" id="shop-product-modal-factory-egp"></div>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-sm">
                                <div class="text-gray-400" id="shop-product-modal-factory-unit"></div>
                                <div class="text-gray-900 dark:text-white font-semibold" id="shop-product-modal-factory-total"></div>
                            </div>

                            <div id="shop-product-modal-factory-details-wrap">
                                <a id="shop-product-modal-factory-details" href="#" class="w-full inline-flex items-center justify-center h-11 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90 !no-underline">
                                    <span>{{ $isAr ? 'المزيد من التفاصيل' : 'More details' }}</span>
                                </a>
                            </div>

                            <div class="pt-4 space-y-6">
                                <div>
                                    <label for="shop-product-modal-factory-quantity" class="text-base font-semibold dark:text-white mb-2 block">{{ $isAr ? 'الكمية' : 'Quantity' }}</label>
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-left">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $isAr ? 'أقل كمية: 1' : 'Min. Order: 1 unit' }}</p>
                                        </div>
                                        <input id="shop-product-modal-factory-quantity" type="number" min="1" value="1" class="w-28 h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3" />
                                    </div>
                                </div>

                                <button id="shop-product-modal-factory-add" type="button" class="w-full px-8 py-3 text-lg font-bold text-white bg-gradient-to-r from-[#F6416C] via-rose-500 to-orange-400 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                    {{ $isAr ? 'أضف إلى السلة' : 'Add to cart' }}
                                </button>
                            </div>
                        </div>

                        <div id="shop-product-modal-supplier-section" class="space-y-4 rounded-2xl p-4">
                            <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                                <span>{{ $isAr ? 'بائع' : 'Supplier' }}</span>
                            </div>

                            <div id="shop-product-modal-supplier-select-wrap" class="hidden mt-3">
                                <select id="shop-product-modal-supplier-select" class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3">
                                    <option value="">-</option>
                                </select>
                            </div>

                            <div class="mt-4 w-full h-28 md:h-32 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                <img id="shop-product-modal-supplier-image" src="" alt="product" class="w-full h-full object-cover" />
                            </div>

                            <div class="mt-4">
                                <div class="font-semibold text-gray-900 dark:text-white" id="shop-product-modal-product-name-supplier"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1" id="shop-product-modal-description-supplier"></div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-gray-900 dark:text-white font-semibold" id="shop-product-modal-supplier-usd"></div>
                                <div class="text-rose-500 dark:text-rose-400 font-semibold" id="shop-product-modal-supplier-egp"></div>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-sm">
                                <div class="text-gray-400" id="shop-product-modal-supplier-unit"></div>
                                <div class="text-gray-900 dark:text-white font-semibold" id="shop-product-modal-supplier-total"></div>
                            </div>

                            <div id="shop-product-modal-supplier-details-wrap">
                                <a id="shop-product-modal-supplier-details" href="#" class="w-full inline-flex items-center justify-center h-11 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90 !no-underline">
                                    <span>{{ $isAr ? 'المزيد من التفاصيل' : 'More details' }}</span>
                                </a>
                            </div>

                            <div class="pt-4 space-y-6">
                                <div>
                                    <label for="shop-product-modal-supplier-quantity" class="text-base font-semibold dark:text-white mb-2 block">{{ $isAr ? 'الكمية' : 'Quantity' }}</label>
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-left">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $isAr ? 'أقل كمية: 1' : 'Min. Order: 1 unit' }}</p>
                                        </div>
                                        <input id="shop-product-modal-supplier-quantity" type="number" min="1" value="1" class="w-28 h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 px-3" />
                                    </div>
                                </div>

                                <button id="shop-product-modal-supplier-add" type="button" class="w-full px-8 py-3 text-lg font-bold text-white bg-gradient-to-r from-[#F6416C] via-rose-500 to-orange-400 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                                    {{ $isAr ? 'أضف إلى السلة' : 'Add to cart' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="shop-product-modal-footer" class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex-shrink-0 hidden">
                    <button id="shop-product-modal-main-add" type="button" class="w-full px-8 py-3 text-lg font-bold text-white bg-gradient-to-r from-[#F6416C] via-rose-500 to-orange-400 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        {{ $isAr ? 'أضف إلى السلة' : 'Add to cart' }}
                    </button>
                </div>
                </div>
            </div>
        </div>
    </div>



    @stack('scripts')

    <script>
        const darkModeToggles = document.querySelectorAll('.dark-mode-toggle');
        const html = document.documentElement;
        function updateToggles(isChecked) { darkModeToggles.forEach(t => { t.checked = isChecked; }); }
        if (localStorage.getItem('darkMode') === 'enabled') { html.classList.add('dark'); updateToggles(true); } else { updateToggles(false); }
        darkModeToggles.forEach(toggle => {
            toggle.addEventListener('change', () => {
                if (toggle.checked) { html.classList.add('dark'); localStorage.setItem('darkMode', 'enabled'); updateToggles(true); }
                else { html.classList.remove('dark'); localStorage.setItem('darkMode', 'disabled'); updateToggles(false); }
            });
        });

        const shopMenuBtn = document.getElementById('shop-menu-btn');
        const shopSideMenu = document.getElementById('shop-side-menu');
        const shopOverlay = document.getElementById('shop-overlay');
        const shopCloseBtn = document.getElementById('shop-close-menu-btn');

        function shopOpenMenu() {
            if (!shopSideMenu || !shopOverlay) return;
            if (document.documentElement.dir === 'rtl') {
                shopSideMenu.classList.remove('translate-x-full');
            } else {
                shopSideMenu.classList.remove('-translate-x-full');
            }
            shopOverlay.classList.remove('hidden');
            document.body.classList.add('no-scroll');
        }

        function shopCloseMenu() {
            if (!shopSideMenu || !shopOverlay) return;
            if (document.documentElement.dir === 'rtl') {
                shopSideMenu.classList.add('translate-x-full');
            } else {
                shopSideMenu.classList.add('-translate-x-full');
            }
            shopOverlay.classList.add('hidden');
            document.body.classList.remove('no-scroll');
        }

        if (shopMenuBtn) shopMenuBtn.addEventListener('click', shopOpenMenu);
        if (shopCloseBtn) shopCloseBtn.addEventListener('click', shopCloseMenu);
        if (shopOverlay) shopOverlay.addEventListener('click', shopCloseMenu);

        const productModalOverlay = document.getElementById('shop-product-modal-overlay');
        const productModal = document.getElementById('shop-product-modal');
        const productModalClose = document.getElementById('shop-product-modal-close');
        const productModalTitle = document.getElementById('shop-product-modal-title');
        const productModalForm = document.getElementById('shop-product-modal-form');
        const productModalProductIdInput = document.getElementById('shop-product-modal-product-id-input');
        const productModalSupplierIdInput = document.getElementById('shop-product-modal-supplier-id-input');
        const productModalFactorySelect = document.getElementById('shop-product-modal-factory-select');
        const productModalSupplierSelect = document.getElementById('shop-product-modal-supplier-select');
        const productModalFactorySelectWrap = document.getElementById('shop-product-modal-factory-select-wrap');
        const productModalSupplierSelectWrap = document.getElementById('shop-product-modal-supplier-select-wrap');
        const productNameFactory = document.getElementById('shop-product-modal-product-name-factory');
        const productNameSupplier = document.getElementById('shop-product-modal-product-name-supplier');
        const productDescFactory = document.getElementById('shop-product-modal-description-factory');
        const productDescSupplier = document.getElementById('shop-product-modal-description-supplier');
        const productFactoryUsd = document.getElementById('shop-product-modal-factory-usd');
        const productFactoryEgp = document.getElementById('shop-product-modal-factory-egp');
        const productSupplierUsd = document.getElementById('shop-product-modal-supplier-usd');
        const productSupplierEgp = document.getElementById('shop-product-modal-supplier-egp');
        const productFactoryUnit = document.getElementById('shop-product-modal-factory-unit');
        const productFactoryTotal = document.getElementById('shop-product-modal-factory-total');
        const productSupplierUnit = document.getElementById('shop-product-modal-supplier-unit');
        const productSupplierTotal = document.getElementById('shop-product-modal-supplier-total');
        const productFactoryImage = document.getElementById('shop-product-modal-factory-image');
        const productSupplierImage = document.getElementById('shop-product-modal-supplier-image');
        const productModalFactoryDetails = document.getElementById('shop-product-modal-factory-details');
        const productModalSupplierDetails = document.getElementById('shop-product-modal-supplier-details');
        const productModalQuantityHidden = document.getElementById('shop-product-modal-quantity-hidden');
        const factoryQtyInput = document.getElementById('shop-product-modal-factory-quantity');
        const factoryAddBtn = document.getElementById('shop-product-modal-factory-add');
        const supplierQtyInput = document.getElementById('shop-product-modal-supplier-quantity');
        const supplierAddBtn = document.getElementById('shop-product-modal-supplier-add');
        const productModalFactorySection = document.getElementById('shop-product-modal-factory-section');
        const productModalSupplierSection = document.getElementById('shop-product-modal-supplier-section');
        const productModalGrid = document.getElementById('shop-product-modal-grid');
        const productModalFooter = document.getElementById('shop-product-modal-footer');
        const productModalMainAdd = document.getElementById('shop-product-modal-main-add');
        const productModalFactoryDetailsWrap = document.getElementById('shop-product-modal-factory-details-wrap');
        const productModalSupplierDetailsWrap = document.getElementById('shop-product-modal-supplier-details-wrap');
        const productModalTypeToggle = document.getElementById('shop-product-modal-type-toggle');
        const productModalTypeFactoryBtn = document.getElementById('shop-modal-type-factory');
        const productModalTypeSupplierBtn = document.getElementById('shop-modal-type-supplier');
        const globalSupplierWrap = document.getElementById('shop-product-modal-global-supplier-wrap');
        const globalSupplierSelect = document.getElementById('shop-product-modal-global-supplier');
        const orderSummary = document.getElementById('shop-product-modal-order-summary');
        const orderCodeEl = document.getElementById('shop-order-code');
        const orderDateEl = document.getElementById('shop-order-date');
        const orderStatusEl = document.getElementById('shop-order-status-text');
        const orderTotalEl = document.getElementById('shop-order-total');

        function setSectionDisabled(sectionEl, disabled) {
            if (!sectionEl) return;
            sectionEl.classList.toggle('opacity-60', !!disabled);
            sectionEl.classList.toggle('pointer-events-none', !!disabled);
        }

        


        const MODAL_MODE_CENTER = 'center';
        const MODAL_MODE_SHEET = 'sheet';
        const MODAL_MODE_POPUP = 'popup';
        let currentModalMode = MODAL_MODE_SHEET;
        let currentSheetType = 'factory';
        let currentFactoriesCount = 0;
        let currentVendorsCount = 0;
        let currentReadOnly = false;

        function updateSheetSupplierPickers() {
            if (currentModalMode !== MODAL_MODE_SHEET) {
                if (productModalFactorySelectWrap) productModalFactorySelectWrap.classList.add('hidden');
                if (productModalSupplierSelectWrap) productModalSupplierSelectWrap.classList.add('hidden');
                return;
            }

            if (productModalFactorySelectWrap) {
                const show = currentSheetType === 'factory' && currentFactoriesCount > 1;
                productModalFactorySelectWrap.classList.toggle('hidden', !show);
            }
            if (productModalSupplierSelectWrap) {
                const show = currentSheetType === 'vendor' && currentVendorsCount > 1;
                productModalSupplierSelectWrap.classList.toggle('hidden', !show);
            }
        }

        function setSheetType(type) {
            currentSheetType = type === 'vendor' ? 'vendor' : 'factory';
            if (productModalFactorySection) productModalFactorySection.classList.toggle('hidden', currentSheetType !== 'factory');
            if (productModalSupplierSection) productModalSupplierSection.classList.toggle('hidden', currentSheetType !== 'vendor');
            updateSheetSupplierPickers();

            if (productModalTypeFactoryBtn) {
                productModalTypeFactoryBtn.classList.toggle('bg-white', currentSheetType === 'factory');
                productModalTypeFactoryBtn.classList.toggle('dark:bg-slate-800', currentSheetType === 'factory');
                productModalTypeFactoryBtn.classList.toggle('text-gray-900', currentSheetType === 'factory');
                productModalTypeFactoryBtn.classList.toggle('dark:text-white', currentSheetType === 'factory');
                productModalTypeFactoryBtn.classList.toggle('text-gray-500', currentSheetType !== 'factory');
                productModalTypeFactoryBtn.classList.toggle('dark:text-slate-300', currentSheetType !== 'factory');
            }
            if (productModalTypeSupplierBtn) {
                productModalTypeSupplierBtn.classList.toggle('bg-white', currentSheetType === 'vendor');
                productModalTypeSupplierBtn.classList.toggle('dark:bg-slate-800', currentSheetType === 'vendor');
                productModalTypeSupplierBtn.classList.toggle('text-gray-900', currentSheetType === 'vendor');
                productModalTypeSupplierBtn.classList.toggle('dark:text-white', currentSheetType === 'vendor');
                productModalTypeSupplierBtn.classList.toggle('text-gray-500', currentSheetType !== 'vendor');
                productModalTypeSupplierBtn.classList.toggle('dark:text-slate-300', currentSheetType !== 'vendor');
            }
        }

        if (productModalTypeFactoryBtn) {
            productModalTypeFactoryBtn.addEventListener('click', () => {
                if (currentModalMode !== MODAL_MODE_SHEET) return;
                setSheetType('factory');
            });
        }

        if (productModalTypeSupplierBtn) {
            productModalTypeSupplierBtn.addEventListener('click', () => {
                if (currentModalMode !== MODAL_MODE_SHEET) return;
                setSheetType('vendor');
            });
        }

        function applyProductModalMode(mode) {
            if (!productModal) return;
            currentModalMode = MODAL_MODE_POPUP; // Keep refined popup style
            updateSheetSupplierPickers();

            // Clear previously applied classes that might conflict
            const allPossible = [
                'fixed', 'bottom-0', 'left-1/2', '-translate-x-1/2', 'translate-y-0', 'translate-y-full',
                'w-[96vw]', 'max-w-[760px]', 'max-h-[85vh]', 'md:rounded-2xl', 'rounded-t-2xl'
            ];
            allPossible.forEach(c => productModal.classList.remove(c));

            if (currentModalMode === MODAL_MODE_SHEET) {
                productModal.classList.add('fixed', 'bottom-0', 'left-1/2', '-translate-x-1/2', 'w-[96vw]', 'max-w-[760px]', 'max-h-[92vh]', 'rounded-t-2xl', 'md:rounded-2xl', 'translate-y-full');
            } else {
                productModal.classList.add('relative', 'w-full', 'md:w-[720px]', 'max-w-full', 'max-h-[85vh]', 'rounded-2xl');
            }
        }

        if (productModal) {
            // Force POPUP mode always
            applyProductModalMode(MODAL_MODE_POPUP);
        }

        function openShopProductModal(el) {
            if (!productModal || !productModalOverlay) return;

            const isRtl = document.documentElement.dir === 'rtl';
            const id = el.dataset.productId;
            const modalTitleOverride = el.dataset.modalTitle || '';
            const modalMode = el.dataset.modalMode || MODAL_MODE_SHEET;
            currentReadOnly = String(el.dataset.modalReadonly || '') === '1' || String(el.dataset.modalReadonly || '') === 'true';
            applyProductModalMode(modalMode);
            const name = isRtl ? (el.dataset.nameAr || el.dataset.nameEn || '') : (el.dataset.nameEn || el.dataset.nameAr || '');

            const image = el.dataset.image || '';
            const description = isRtl
                ? (el.dataset.descriptionAr || el.dataset.description || el.dataset.descriptionEn || '')
                : (el.dataset.descriptionEn || el.dataset.description || el.dataset.descriptionAr || '');
            const orderCode = el.dataset.orderCode || '';
            const orderDate = el.dataset.orderDate || '';
            const orderStatus = el.dataset.orderStatus || '';
            const orderTotalRaw = el.dataset.orderTotal || '';

            const suppliersRaw = el.dataset.suppliers || '';
            const pricingTiersRaw = el.dataset.pricingTiers || '';
            const factoryId = el.dataset.factoryId || '';
            const supplierId = el.dataset.supplierId || '';
            let suppliers = [];
            try {
                if (suppliersRaw) suppliers = JSON.parse(suppliersRaw) || [];
            } catch (e) {
                suppliers = [];
            }

            let pricingTiersBySupplier = {};
            try {
                if (pricingTiersRaw) pricingTiersBySupplier = JSON.parse(pricingTiersRaw) || {};
            } catch (e) {
                pricingTiersBySupplier = {};
            }

            const findTierPrice = function (supplierId, qty) {
                const q = Math.max(1, Number(qty || 1) || 1);
                const sid = supplierId !== null && typeof supplierId !== 'undefined' ? String(supplierId) : '';
                const list = pricingTiersBySupplier && pricingTiersBySupplier[sid] ? pricingTiersBySupplier[sid] : [];
                const tiers = Array.isArray(list) ? list : [];
                for (let i = 0; i < tiers.length; i++) {
                    const t = tiers[i] || {};
                    const min = Number(t.min ?? t.min_quantity ?? 0) || 0;
                    const maxVal = t.max ?? t.max_quantity;
                    const max = (maxVal === null || typeof maxVal === 'undefined' || maxVal === '') ? null : (Number(maxVal) || null);
                    if (min > 0 && q >= min && (max === null || q <= max)) {
                        const p = Number(t.price ?? t.price_per_unit ?? 0);
                        return Number.isFinite(p) && p >= 0 ? p : null;
                    }
                }
                return null;
            };

            const factories = suppliers.filter(s => (s?.type || '') === 'factory');
            const vendors = suppliers.filter(s => (s?.type || '') === 'vendor');
            const hasFactories = factories.length > 0;
            const hasVendors = vendors.length > 0;

            currentFactoriesCount = factories.length;
            currentVendorsCount = vendors.length;

            const showSingleColumn = currentModalMode === MODAL_MODE_SHEET;

            if (productModalGrid) {
                if (showSingleColumn) {
                    productModalGrid.classList.remove('md:grid-cols-2');
                    productModalGrid.classList.add('md:grid-cols-1');
                } else {
                    productModalGrid.classList.remove('md:grid-cols-1');
                    productModalGrid.classList.add('md:grid-cols-2');
                }
            }

            if (currentModalMode === MODAL_MODE_SHEET) {
                if (productModalTypeToggle) productModalTypeToggle.classList.toggle('hidden', !(hasFactories && hasVendors));
                if (globalSupplierWrap) globalSupplierWrap.classList.remove('hidden');
                if (hasFactories && !hasVendors) {
                    setSheetType('factory');
                } else if (!hasFactories && hasVendors) {
                    setSheetType('vendor');
                } else {
                    setSheetType(currentSheetType);
                }

                updateSheetSupplierPickers();
            } else {
                if (productModalTypeToggle) productModalTypeToggle.classList.add('hidden');
                if (globalSupplierWrap) globalSupplierWrap.classList.add('hidden');
                if (productModalFactorySection) productModalFactorySection.classList.remove('hidden');
                if (productModalSupplierSection) productModalSupplierSection.classList.remove('hidden');
            }

            if (productModalFooter) {
                productModalFooter.classList.toggle('hidden', !(currentModalMode === MODAL_MODE_SHEET) || currentReadOnly);
            }
            if (factoryAddBtn) factoryAddBtn.classList.toggle('hidden', currentModalMode === MODAL_MODE_SHEET || currentReadOnly);
            if (supplierAddBtn) supplierAddBtn.classList.toggle('hidden', currentModalMode === MODAL_MODE_SHEET || currentReadOnly);

            if (productModalFactoryDetailsWrap) productModalFactoryDetailsWrap.classList.toggle('hidden', currentModalMode === MODAL_MODE_SHEET);
            if (productModalSupplierDetailsWrap) productModalSupplierDetailsWrap.classList.toggle('hidden', currentModalMode === MODAL_MODE_SHEET);

            if (productModalMainAdd) {
                productModalMainAdd.onclick = function () {
                    if (currentModalMode !== MODAL_MODE_SHEET) return;
                    if (currentSheetType === 'vendor') {
                        supplierAddBtn?.click();
                        return;
                    }
                    factoryAddBtn?.click();
                };
            }
            const noFactoryTitle = isRtl ? 'لا يوجد مصنع' : 'No factory';
            const noFactoryDesc = isRtl ? 'لا يوجد عرض من المصنع لهذا المنتج حالياً.' : 'No factory offer available for this product.';
            const noVendorTitle = isRtl ? 'لا يوجد مورد' : 'No supplier';
            const noVendorDesc = isRtl ? 'لا يوجد عرض من المورد لهذا المنتج حالياً.' : 'No supplier offer available for this product.';

            if (orderSummary) {
                const hasOrderInfo = orderCode || orderDate || orderStatus || orderTotalRaw;
                if (hasOrderInfo) {
                    orderSummary.classList.remove('hidden');
                    if (orderCodeEl) {
                        orderCodeEl.textContent = orderCode || (isRtl ? 'غير متوفر' : 'N/A');
                    }
                    if (orderDateEl) {
                        orderDateEl.textContent = orderDate || '-';
                    }
                    if (orderStatusEl) {
                        const displayStatus = orderStatus || (isRtl ? 'غير محدد' : 'Unknown');
                        orderStatusEl.textContent = displayStatus;
                        let statusClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-200';
                        const st = (displayStatus || '').toLowerCase();
                        if (st.includes('delivered') || st.includes('تم')) {
                            statusClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
                        } else if (st.includes('processing') || st.includes('قيد')) {
                            statusClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
                        } else if (st.includes('cancelled') || st.includes('canceled') || st.includes('ملغي')) {
                            statusClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300';
                        }
                        orderStatusEl.className = statusClass;
                    }
                    if (orderTotalEl) {
                        const num = Number(orderTotalRaw || 0);
                        if (Number.isFinite(num) && num > 0) {
                            orderTotalEl.textContent = formatMoney(num);
                        } else {
                            orderTotalEl.textContent = '';
                        }
                    }
                } else {
                    orderSummary.classList.add('hidden');
                    if (orderCodeEl) orderCodeEl.textContent = '';
                    if (orderDateEl) orderDateEl.textContent = '';
                    if (orderStatusEl) {
                        orderStatusEl.textContent = '';
                        orderStatusEl.className = '';
                    }
                    if (orderTotalEl) orderTotalEl.textContent = '';
                }
            }

            setSectionDisabled(productModalFactorySection, !hasFactories);
            setSectionDisabled(productModalSupplierSection, !hasVendors);

            const fillSelect = function (selectEl, items) {
                if (!selectEl) return;
                selectEl.innerHTML = '<option value="">-</option>';
                items.forEach((s) => {
                    const o = document.createElement('option');
                    o.value = String(s?.id ?? '');
                    const priceNum = Number(s?.price ?? 0);
                    const priceLabel = Number.isFinite(priceNum) ? formatMoney(priceNum) : '';
                    o.textContent = `${s?.name ?? ''}${priceLabel ? ' - ' + priceLabel : ''}`;
                    selectEl.appendChild(o);
                });
            };

            const fillGlobalSupplierSelect = function () {
                if (!globalSupplierSelect) return;
                globalSupplierSelect.innerHTML = '<option value="">-</option>';
                suppliers.forEach((s) => {
                    const o = document.createElement('option');
                    o.value = String(s?.id ?? '');
                    const t = (s?.type || '') === 'factory' ? (isRtl ? 'مصنع' : 'Factory') : (isRtl ? 'مورد' : 'Supplier');
                    const basePrice = Number(s?.unit_price ?? s?.price ?? 0);
                    const priceLabel = Number.isFinite(basePrice) ? formatMoney(basePrice) : '';
                    o.textContent = `${t} - ${s?.name ?? ''}${priceLabel ? ' - ' + priceLabel : ''}`;
                    globalSupplierSelect.appendChild(o);
                });
            };

            const selectedFromList = function (items, selectedId) {
                const idStr = String(selectedId ?? '');
                const found = items.find(s => String(s?.id ?? '') === idStr);
                return found || items[0] || null;
            };

            const applyCard = function (target, supplierObj) {
                if (!supplierObj) {
                    if (target === 'factory') {
                        if (productNameFactory) productNameFactory.textContent = noFactoryTitle;
                        if (productDescFactory) productDescFactory.textContent = noFactoryDesc;
                        if (productFactoryUsd) productFactoryUsd.textContent = '';
                        if (productFactoryEgp) productFactoryEgp.textContent = '';
                        if (productFactoryUnit) productFactoryUnit.textContent = '';
                        if (productFactoryTotal) productFactoryTotal.textContent = '';
                        if (productFactoryImage) {
                            productFactoryImage.src = '';
                            productFactoryImage.classList.add('opacity-0');
                        }
                    } else {
                        if (productNameSupplier) productNameSupplier.textContent = noVendorTitle;
                        if (productDescSupplier) productDescSupplier.textContent = noVendorDesc;
                        if (productSupplierUsd) productSupplierUsd.textContent = '';
                        if (productSupplierEgp) productSupplierEgp.textContent = '';
                        if (productSupplierUnit) productSupplierUnit.textContent = '';
                        if (productSupplierTotal) productSupplierTotal.textContent = '';
                        if (productSupplierImage) {
                            productSupplierImage.src = '';
                            productSupplierImage.classList.add('opacity-0');
                        }
                    }
                    return;
                }

                const price = Number(supplierObj?.price ?? 0);
                const usd = formatUsd(price);
                const egp = formatEgp(price);
                const egpText = egp ? egp : '';

                if (target === 'factory') {
                    if (productNameFactory) productNameFactory.textContent = name;
                    if (productDescFactory) productDescFactory.textContent = description;
                    if (productFactoryUsd) productFactoryUsd.textContent = usd;
                    if (productFactoryEgp) productFactoryEgp.textContent = egpText;
                    if (productFactoryImage) {
                        productFactoryImage.src = image;
                        productFactoryImage.classList.remove('opacity-0');
                    }
                } else {
                    if (productNameSupplier) productNameSupplier.textContent = name;
                    if (productDescSupplier) productDescSupplier.textContent = description;
                    if (productSupplierUsd) productSupplierUsd.textContent = usd;
                    if (productSupplierEgp) productSupplierEgp.textContent = egpText;
                    if (productSupplierImage) {
                        productSupplierImage.src = image;
                        productSupplierImage.classList.remove('opacity-0');
                    }
                }
            };

            const updateTotalsForSide = function (side) {
                const isFactory = side === 'factory';
                const currentObj = isFactory ? currentFactory : currentVendor;
                const qty = isFactory ? (Number(factoryQtyInput?.value || factoryState.qty || 1) || 1) : (Number(supplierQtyInput?.value || supplierState.qty || 1) || 1);
                if (!currentObj) {
                    if (isFactory) {
                        if (productFactoryUnit) productFactoryUnit.textContent = '';
                        if (productFactoryTotal) productFactoryTotal.textContent = '';
                    } else {
                        if (productSupplierUnit) productSupplierUnit.textContent = '';
                        if (productSupplierTotal) productSupplierTotal.textContent = '';
                    }
                    return;
                }

                const basePrice = Number(currentObj?.unit_price ?? currentObj?.price ?? 0);
                const tierPrice = findTierPrice(currentObj?.id ?? null, qty);
                const unitPrice = tierPrice !== null ? tierPrice : (Number.isFinite(basePrice) ? basePrice : 0);
                const total = unitPrice * Math.max(1, qty);

                const unitText = (isRtl ? 'سعر القطعة: ' : 'Unit: ') + formatMoney(unitPrice);
                const totalText = (isRtl ? 'الإجمالي: ' : 'Total: ') + formatMoney(total);

                if (isFactory) {
                    if (productFactoryUnit) productFactoryUnit.textContent = unitText;
                    if (productFactoryTotal) productFactoryTotal.textContent = totalText;
                } else {
                    if (productSupplierUnit) productSupplierUnit.textContent = unitText;
                    if (productSupplierTotal) productSupplierTotal.textContent = totalText;
                }
            };

            if (productModalTitle) {
                if (modalTitleOverride) {
                    productModalTitle.textContent = modalTitleOverride;
                } else if (currentModalMode === MODAL_MODE_SHEET) {
                    productModalTitle.textContent = isRtl ? 'اختر خياراتك' : 'Select Your Options';
                } else {
                    productModalTitle.textContent = (!hasFactories && !hasVendors)
                        ? (isRtl ? 'لا توجد عروض متاحة لهذا المنتج' : 'No offers available for this product')
                        : (isRtl ? 'خيارات الطلب' : ' Ordering options');
                }
            }

            if (hasFactories) {
                fillSelect(productModalFactorySelect, factories);
            } else if (productModalFactorySelect) {
                productModalFactorySelect.innerHTML = '<option value="">' + (isRtl ? 'لا يوجد' : 'None') + '</option>';
                productModalFactorySelect.value = '';
            }

            if (hasVendors) {
                fillSelect(productModalSupplierSelect, vendors);
            } else if (productModalSupplierSelect) {
                productModalSupplierSelect.innerHTML = '<option value="">' + (isRtl ? 'لا يوجد' : 'None') + '</option>';
                productModalSupplierSelect.value = '';
            }

            const selectedFactory = selectedFromList(factories, factoryId);
            const selectedVendor = selectedFromList(vendors, supplierId);
            let currentFactory = selectedFactory;
            let currentVendor = selectedVendor;
            if (productModalFactorySelect && selectedFactory) productModalFactorySelect.value = String(selectedFactory.id ?? '');
            if (productModalSupplierSelect && selectedVendor) productModalSupplierSelect.value = String(selectedVendor.id ?? '');

            if (globalSupplierSelect) {
                fillGlobalSupplierSelect();
                const preferred = (supplierId || factoryId) ? String(supplierId || factoryId) : String((selectedVendor?.id ?? '') || (selectedFactory?.id ?? ''));
                globalSupplierSelect.value = preferred;
                globalSupplierSelect.onchange = function () {
                    const nextId = String(globalSupplierSelect.value || '');
                    const next = suppliers.find(s => String(s?.id ?? '') === nextId) || null;
                    if (!next) return;
                    if ((next?.type || '') === 'vendor') {
                        setSheetType('vendor');
                        if (productModalSupplierSelect) productModalSupplierSelect.value = String(next.id ?? '');
                        currentVendor = next;
                        applyCard('vendor', next);
                        updateSupplierAddState();
                        updateTotalsForSide('vendor');
                        return;
                    }
                    setSheetType('factory');
                    if (productModalFactorySelect) productModalFactorySelect.value = String(next.id ?? '');
                    currentFactory = next;
                    applyCard('factory', next);
                    updateFactoryAddState();
                    updateTotalsForSide('factory');
                };
            }

            applyCard('factory', selectedFactory);
            applyCard('vendor', selectedVendor);

            updateTotalsForSide('factory');
            updateTotalsForSide('vendor');

            const setMainOrderFields = function (supplierObj, qty) {
                if (productModalSupplierIdInput) productModalSupplierIdInput.value = supplierObj ? String(supplierObj.id ?? '') : '';
                const q = Math.max(1, Number(qty || 1) || 1);
                if (productModalQuantityHidden) productModalQuantityHidden.value = String(q);
            };

            const updateFactoryAddState = function () {
                if (!factoryAddBtn) return;
                const enabled = !!currentFactory;
                factoryAddBtn.disabled = !enabled;
                factoryAddBtn.classList.toggle('opacity-50', !enabled);
                factoryAddBtn.classList.toggle('cursor-not-allowed', !enabled);
            };

            const updateSupplierAddState = function () {
                if (!supplierAddBtn) return;
                const enabled = !!currentVendor;
                supplierAddBtn.disabled = !enabled;
                supplierAddBtn.classList.toggle('opacity-50', !enabled);
                supplierAddBtn.classList.toggle('cursor-not-allowed', !enabled);
            };

            updateFactoryAddState();
            updateSupplierAddState();

            if (!hasFactories && factoryAddBtn) {
                factoryAddBtn.disabled = true;
                factoryAddBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (!hasVendors && supplierAddBtn) {
                supplierAddBtn.disabled = true;
                supplierAddBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            if (productModalFactorySelect) {
                productModalFactorySelect.onchange = function () {
                    const next = selectedFromList(factories, productModalFactorySelect.value);
                    currentFactory = next;
                    applyCard('factory', next);
                    updateFactoryAddState();
                    updateTotalsForSide('factory');
                };
            }
            if (productModalSupplierSelect) {
                productModalSupplierSelect.onchange = function () {
                    const next = selectedFromList(vendors, productModalSupplierSelect.value);
                    currentVendor = next;
                    applyCard('vendor', next);
                    updateSupplierAddState();
                    updateTotalsForSide('vendor');
                };
            }

            const base = "{{ url('/products') }}";
            if (productModalFactoryDetails) {
                if (hasFactories) {
                    productModalFactoryDetails.href = `${base}/${id}?type=factory`;
                    productModalFactoryDetails.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                } else {
                    productModalFactoryDetails.href = '#';
                    productModalFactoryDetails.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                }
            }
            if (productModalSupplierDetails) {
                if (hasVendors) {
                    productModalSupplierDetails.href = `${base}/${id}?type=vendor`;
                    productModalSupplierDetails.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                } else {
                    productModalSupplierDetails.href = '#';
                    productModalSupplierDetails.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                }
            }

            const hideConfigControls = function (hide) {
                if (factoryQtyInput) {
                    const wrap = factoryQtyInput.closest('div');
                    const outer = wrap?.parentElement;
                    if (outer) outer.classList.toggle('hidden', !!hide);
                }
                if (supplierQtyInput) {
                    const wrap = supplierQtyInput.closest('div');
                    const outer = wrap?.parentElement;
                    if (outer) outer.classList.toggle('hidden', !!hide);
                }
            };

            hideConfigControls(currentReadOnly);

            let factoryState = { qty: 1 };
            let supplierState = { qty: 1 };

            const initOptionsForSide = function (side) {
                const isFactory = side === 'factory';
                const qtyInput = isFactory ? factoryQtyInput : supplierQtyInput;

                if (qtyInput) {
                    qtyInput.value = '1';
                    qtyInput.onchange = function () {
                        const q = Math.max(1, Number(qtyInput.value || 1) || 1);
                        qtyInput.value = String(q);
                        if (isFactory) factoryState.qty = q; else supplierState.qty = q;
                        updateTotalsForSide(isFactory ? 'factory' : 'vendor');
                    };
                }
            };

            initOptionsForSide('factory');
            initOptionsForSide('supplier');

            if (productModalProductIdInput) {
                productModalProductIdInput.value = String(id || '');
            }

            if (factoryAddBtn) {
                factoryAddBtn.onclick = function () {
                    if (!currentFactory) return;
                    if (currentReadOnly) return;
                    const q = Number(factoryQtyInput?.value || factoryState.qty || 1) || 1;
                    setMainOrderFields(currentFactory, q);
                    if (productModalForm) productModalForm.requestSubmit();
                };
            }

            if (supplierAddBtn) {
                supplierAddBtn.onclick = function () {
                    if (!currentVendor) return;
                    if (currentReadOnly) return;
                    const q = Number(supplierQtyInput?.value || supplierState.qty || 1) || 1;
                    setMainOrderFields(currentVendor, q);
                    if (productModalForm) productModalForm.requestSubmit();
                };
            }

            // Animation Logic
            const showRealModal = () => {
                productModalOverlay.classList.remove('hidden');
                productModal.classList.remove('hidden');
                
                productModalOverlay.classList.remove('shop-modal-anim-overlay-out');
                productModalOverlay.classList.add('shop-modal-anim-overlay-in');

                productModal.classList.remove('shop-modal-anim-pop-out', 'shop-modal-anim-sheet-out');

                if (currentModalMode === MODAL_MODE_SHEET) {
                    productModal.classList.remove('translate-y-full');
                    productModal.classList.add('translate-y-0');
                    productModal.classList.add('shop-modal-anim-sheet-in');
                } else {
                    productModal.classList.add('shop-modal-anim-pop-in');
                }
                document.body.classList.add('no-scroll');
            };

            // Simple show logic to avoid flickering
            showRealModal();
        }

        // Multi-currency Modal Logic
        const countryModal = document.getElementById('country-modal');
        const countryOverlay = document.getElementById('country-modal-overlay');
        const countrySearch = document.getElementById('country-search');
        const countryItems = document.querySelectorAll('.country-item');

        window.openCountryModal = function() {
            if (!countryModal || !countryOverlay) return;
            countryOverlay.classList.remove('hidden');
            countryModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                countryOverlay.style.opacity = '1';
                countryModal.classList.add('slide-up');
                countryModal.classList.remove('translate-y-full');
            });
            document.body.style.overflow = 'hidden';
        };

        window.closeCountryModal = function() {
            if (!countryModal || !countryOverlay) return;
            countryModal.classList.remove('slide-up');
            countryModal.classList.add('slide-down');
            countryOverlay.style.opacity = '0';
            setTimeout(() => {
                countryModal.classList.add('hidden');
                countryOverlay.classList.add('hidden');
                countryModal.classList.remove('slide-down');
                countryModal.classList.add('translate-y-full');
                document.body.style.overflow = '';
            }, 300);
        };

        if (countryOverlay) countryOverlay.addEventListener('click', window.closeCountryModal);

        if (countrySearch) {
            countrySearch.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                countryItems.forEach(item => {
                    const name = item.innerText.toLowerCase();
                    item.style.display = name.includes(term) ? 'block' : 'none';
                });
            });
        }

        // Improved Product Modal Logic
        function closeShopProductModal() {
            if (!productModal || !productModalOverlay) return;

            productModalOverlay.classList.remove('shop-modal-anim-overlay-in');
            productModalOverlay.classList.add('shop-modal-anim-overlay-out');

            if (currentModalMode === MODAL_MODE_SHEET) {
                productModal.classList.remove('shop-modal-anim-sheet-in');
                productModal.classList.add('shop-modal-anim-sheet-out');
                setTimeout(() => {
                    productModalOverlay.classList.add('hidden');
                    productModal.classList.add('hidden');
                    productModal.classList.remove('shop-modal-anim-sheet-out');
                    document.body.classList.remove('no-scroll');
                    productModal.style.transform = '';
                }, 300);
            } else {
                productModal.classList.remove('shop-modal-anim-pop-in');
                productModal.classList.add('shop-modal-anim-pop-out');
                setTimeout(() => {
                    productModalOverlay.classList.add('hidden');
                    productModal.classList.add('hidden');
                    productModal.classList.remove('shop-modal-anim-pop-out');
                    document.body.classList.remove('no-scroll');
                    productModal.style.transform = '';
                }, 200);
            }
        }

        // Remove any existing listeners if this script is re-run (unlikely in Blade, but good practice)
        document.querySelectorAll('[data-shop-product]').forEach((el) => {
            const newEl = el.cloneNode(true);
            el.parentNode.replaceChild(newEl, el);
        });

        document.querySelectorAll('[data-shop-product]').forEach((el) => {
            el.addEventListener('click', (e) => {
                if (e.target && e.target.closest('#shop-product-modal')) return;
                if (e.target && e.target.closest('a, button, input, select, textarea, label')) return;
                openShopProductModal(el);
            });
            el.classList.add('cursor-pointer');
        });

        if (productModalOverlay) {
            productModalOverlay.addEventListener('click', (e) => {
                if (e.target === productModalOverlay) closeShopProductModal();
            });
        }
        if (productModalClose) productModalClose.addEventListener('click', closeShopProductModal);

        if (productModal) {
            productModal.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            if (!productModal || !productModalOverlay) return;
            if (productModal.classList.contains('hidden')) return;
            closeShopProductModal();
        });

        if (productModalForm) {
            productModalForm.addEventListener('submit', (e) => {
                if (!productModalSupplierIdInput || !productModalSupplierIdInput.value) {
                    e.preventDefault();
                    if (productModalSupplierSelect) {
                        productModalSupplierSelect.focus();
                    }
                    return;
                }

                e.preventDefault();

                const showToast = function (message) {
                    const existing = document.getElementById('shop-cart-toast');
                    if (existing) existing.remove();

                    const toast = document.createElement('div');
                    toast.id = 'shop-cart-toast';
                    toast.className = 'fixed top-5 right-5 z-[9999] bg-green-600 text-white px-4 py-3 rounded-xl shadow-lg';
                    toast.textContent = message || 'Added to cart';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 2500);
                };

                const showError = function (message) {
                    const existing = document.getElementById('shop-cart-toast');
                    if (existing) existing.remove();

                    const toast = document.createElement('div');
                    toast.id = 'shop-cart-toast';
                    toast.className = 'fixed top-5 right-5 z-[9999] bg-rose-600 text-white px-4 py-3 rounded-xl shadow-lg';
                    toast.textContent = message || 'Failed to add to cart';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3500);
                };

                const formData = new FormData(productModalForm);
                fetch(productModalForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: formData,
                }).then(async (res) => {
                    if (res.ok) {
                        showToast('Added to cart');
                        closeShopProductModal();
                        return;
                    }

                    let payload = null;
                    try {
                        payload = await res.json();
                    } catch (err) {
                        payload = null;
                    }
                    const message = payload?.message || 'Failed to add to cart';
                    showError(message);
                }).catch(() => {
                    showError('Failed to add to cart');
                });
            });
        }
        
        const langToggleBtn = document.getElementById('shop-lang-toggle-btn');
        if (langToggleBtn) {
            langToggleBtn.addEventListener('click', () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("shop.lang.set") }}';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                const lang = document.createElement('input');
                lang.type = 'hidden';
                lang.name = 'lang';
                lang.value = '{{ $isAr ? "en" : "ar" }}';
                form.appendChild(csrf);
                form.appendChild(lang);
                document.body.appendChild(form);
                form.submit();
            });
        }

        lucide.createIcons();
    </script>
</body>
</html>
