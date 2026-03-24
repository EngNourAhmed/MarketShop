@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'بحث' : 'Search')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'نتائج البحث' : 'Search results' }}</div>
        </div>

        <form method="GET" action="{{ route('shop.search') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 {{ $isAr ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ $isAr ? 'ابحث عن منتج أو فئة...' : 'Search products or categories...' }}"
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 {{ $isAr ? 'pr-10 pl-4' : 'pl-10 pr-4' }}" />
            </div>

            <select name="type" class="h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700">
                @php($typeVal = (string) ($type ?? 'all'))
                <option value="all" {{ $typeVal === 'all' ? 'selected' : '' }}>{{ $isAr ? 'الكل' : 'All' }}</option>
                <option value="products" {{ $typeVal === 'products' ? 'selected' : '' }}>{{ $isAr ? 'منتجات' : 'Products' }}</option>
                <option value="categories" {{ $typeVal === 'categories' ? 'selected' : '' }}>{{ $isAr ? 'فئات' : 'Categories' }}</option>
            </select>

            <button type="submit" class="h-11 px-5 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90">
                {{ $isAr ? 'بحث' : 'Search' }}
            </button>
        </form>

        <div class="space-y-3">
            @if(($type ?? 'all') === 'all' || ($type ?? 'all') === 'categories')
                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $isAr ? 'الفئات' : 'Categories' }}</div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse(($categories ?? []) as $cat)
                        <a href="{{ route('shop.categories.show', $cat->slug) }}" class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm hover:bg-gray-50 dark:bg-slate-900 dark:border-slate-800 dark:hover:bg-slate-800/60">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center dark:bg-slate-800/60 dark:border-slate-700">
                                    <i data-lucide="grid-2x2" class="w-6 h-6 text-gray-500 dark:text-slate-300"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $isAr ? ($cat->name_ar ?? $cat->name_en) : ($cat->name_en ?? $cat->name_ar) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300 truncate">{{ $isAr ? ($cat->name_en ?? '') : ($cat->name_ar ?? '') }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 md:col-span-3 text-sm text-gray-500 dark:text-gray-300">{{ $isAr ? 'لا توجد فئات.' : 'No categories found.' }}</div>
                    @endforelse
                </div>
            @endif

            @if(($type ?? 'all') === 'all' || ($type ?? 'all') === 'products')
                <div class="text-lg font-bold text-gray-900 dark:text-white mt-2">{{ $isAr ? 'المنتجات' : 'Products' }}</div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse(($products ?? []) as $product)
                        @php($factory = ($product->suppliers ?? collect())->firstWhere('type', 'factory'))
                        @php($supplier = ($product->suppliers ?? collect())->firstWhere('type', 'vendor'))
                        @php($factoryPrice = (float) ($factory->pivot->price ?? 0))
                        @php($supplierPrice = (float) ($supplier->pivot->price ?? 0))
                        @php($usdRate = 50)
                        @php($factoryUsd = (int) round($factoryPrice / $usdRate))
                        @php($supplierUsd = (int) round($supplierPrice / $usdRate))
                        @php($factoryPriceK = $factoryPrice >= 1000 ? (string) round($factoryPrice / 1000) . 'k' : number_format($factoryPrice, 0, '.', ','))
                        @php($supplierPriceK = $supplierPrice >= 1000 ? (string) round($supplierPrice / 1000) . 'k' : number_format($supplierPrice, 0, '.', ','))

                        <a href="{{ route('shop.products.show', $product->id) }}" class="p-4 rounded-2xl bg-white border border-gray-200 shadow-sm hover:bg-gray-50 dark:bg-slate-900 dark:border-slate-800 dark:hover:bg-slate-800/60">
                            <div class="w-full h-24 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                @if(!empty($product->image))
                                    <img src="{{ \App\Helpers\CurrencyHelper::imageUrl($product->image) }}" alt="product" class="w-full h-full object-cover" />
                                @else
                                    <img src="{{ asset('apple-touch-icon.png') }}" alt="default" class="w-full h-full object-cover" />
                                @endif
                            </div>

                            <div class="mt-3 text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name) }}
                            </div>

                            <div class="mt-2 flex items-center gap-1 text-sm">
                                <span class="text-yellow-400">★</span>
                                <span class="text-yellow-400">★</span>
                                <span class="text-yellow-400">★</span>
                                <span class="text-gray-300 dark:text-slate-600">★</span>
                                <span class="text-gray-300 dark:text-slate-600">★</span>
                            </div>

                            <div class="mt-3 space-y-2 text-xs text-gray-700 dark:text-slate-200">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0">
                                        <div class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المصنع:' : 'Factory:' }}</div>
                                        <div class="text-gray-900 dark:text-slate-200">{{ $factoryUsd }}$</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center justify-end">
                                            <i data-lucide="arrow-right-left" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                                        </div>
                                        <div class="text-gray-900 dark:text-slate-200">{{ $factoryPriceK }} {{ $isAr ? 'ج.م' : 'EGP' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0">
                                        <div class="text-gray-500 dark:text-slate-400">{{ $isAr ? 'المورد:' : 'Supplier:' }}</div>
                                        <div class="text-gray-900 dark:text-slate-200">{{ $supplierUsd }}$</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex items-center justify-end">
                                            <i data-lucide="arrow-right-left" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                                        </div>
                                        <div class="text-gray-900 dark:text-slate-200">{{ $supplierPriceK }} {{ $isAr ? 'ج.م' : 'EGP' }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-2 md:col-span-3 text-sm text-gray-500 dark:text-gray-300">{{ $isAr ? 'لا توجد منتجات.' : 'No products found.' }}</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-shop-layouts.app>
