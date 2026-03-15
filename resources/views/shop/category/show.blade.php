@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')
<x-shop-layouts.app :title="($isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar))">
    <div class="flex items-center justify-between mb-4">
        <div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $isAr ? ($category->name_en ?? '') : ($category->name_ar ?? '') }}</div>
        </div>
        <a href="{{ route('customer.home') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/40">
            <i data-lucide="arrow-right" class="w-5 h-5"></i>
            {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

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
            @php($suppliersForModal = ($product->suppliers ?? collect())->map(fn($s) => [
                'id' => (int) $s->id,
                'name' => (string) (($s->type ?? '') === 'vendor' ? 'Trady' : ($s->name ?? '')),
                'type' => (string) ($s->type ?? ''),
                'price' => (float) ($s->pivot->price ?? 0),
            ])->values())

            <div class="p-4 rounded-2xl bg-white border border-gray-200 shadow-sm hover:cursor-pointer dark:bg-gradient-to-b dark:from-slate-900 dark:to-slate-950 dark:border-slate-800 dark:shadow-lg" data-shop-product
                data-product-id="{{ $product->id }}"
                data-name-en="{{ $product->name_en ?? $product->name }}"
                data-name-ar="{{ $product->name_ar ?? $product->name }}"
                data-image="{{ !empty($product->image) ? asset('storage/' . $product->image) : asset('apple-touch-icon.png') }}"
                data-description="{{ $product->description ?? '' }}"
                data-description-ar="{{ $product->description_ar ?? '' }}"
                data-description-en="{{ $product->description_en ?? '' }}"
                data-color="{{ $product->color ?? '' }}"
                data-size="{{ $product->size ?? '' }}"
                data-colors="{{ collect((array) ($product->colors ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
                data-sizes="{{ collect((array) ($product->sizes ?? []))->filter(fn($v) => trim((string) $v) !== '')->implode(',') }}"
                data-suppliers='@json($suppliersForModal)'
                data-factory-id="{{ (int) ($factory->id ?? 0) ?: '' }}"
                data-supplier-id="{{ (int) ($supplier->id ?? 0) ?: '' }}"
                data-factory-name="{{ $factory->name ?? '' }}"
                data-factory-price="{{ (string) ($factory->pivot->price ?? '') }}"
                data-supplier-name="{{ $supplier->name ?? '' }}"
                data-supplier-price="{{ (string) ($supplier->pivot->price ?? '') }}">

                <div class="w-full h-24 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                    @if(!empty($product->image))
                        <img src="{{ asset('storage/' . $product->image) }}" alt="product" class="w-full h-full object-cover" />
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
                    <span class="text-slate-600">★</span>
                    <span class="text-slate-600">★</span>
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
            </div>
        @empty
            <div class="col-span-2 md:col-span-3 text-sm text-gray-500 dark:text-gray-300">لا توجد منتجات في هذه الفئة.</div>
        @endforelse
    </div>
</x-shop-layouts.app>
