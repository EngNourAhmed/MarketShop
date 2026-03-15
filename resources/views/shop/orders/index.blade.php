@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

@push('head')
<style>
    @keyframes shopDotsPulse {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.35; }
        40% { transform: scale(1); opacity: 1; }
    }

    @keyframes shopDotsDrift {
        0% { transform: translateX(0); opacity: 0.85; }
        100% { transform: translateX(-4px); opacity: 0.55; }
    }

    @keyframes shopInlineDots {
        0%, 80%, 100% { transform: translateY(0); opacity: 0.35; }
        40% { transform: translateY(-1px); opacity: 1; }
    }

    @keyframes shopPackageDrop {
        0% { transform: translateY(-2px); opacity: 0.7; }
        100% { transform: translateY(2px); opacity: 1; }
    }

    @keyframes shopWheelSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes shopTruckBob {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-1px); }
    }

    .shop-status-track {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        width: 56px;
        height: 34px;
        background: transparent;
        border: 0;
    }

    .shop-status-track.is-gif {
        width: 120px;
        height: 120px;
    }

    .dark .shop-status-track {
        background: transparent;
        border-color: transparent;
    }

    .shop-status-road {
        display: none;
    }

    .shop-status-truck {
        width: 34px;
        height: 34px;
        color: rgba(244, 63, 94, 1);
        filter: drop-shadow(0 10px 16px rgba(0,0,0,0.14));
        position: relative;
        z-index: 2;
    }

    .shop-status-truck.is-pending {
        animation: shopTruckBob 900ms ease-in-out infinite;
    }

    .shop-status-truck .shop-wheel {
        transform-origin: center;
    }

    .shop-status-truck.is-pending .shop-wheel {
        animation: shopWheelSpin 700ms linear infinite;
    }

    .shop-status-gif {
        width: 120px;
        height: 120px;
        object-fit: contain;
        filter: drop-shadow(0 10px 16px rgba(0,0,0,0.14));
        position: relative;
        z-index: 2;
    }

    .dark .shop-status-gif {
        filter: drop-shadow(0 10px 18px rgba(0,0,0,0.35));
    }

    .dark .shop-status-truck {
        color: rgba(251, 113, 133, 1);
        filter: drop-shadow(0 8px 14px rgba(0,0,0,0.35));
    }

    .shop-status-dots {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        gap: 4px;
        height: 34px;
        z-index: 1;
        animation: shopDotsDrift 900ms ease-in-out infinite alternate;
    }

    .shop-status-dots > span {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
        background: rgba(244, 63, 94, 1);
        animation: shopDotsPulse 1s ease-in-out infinite;
    }

    .dark .shop-status-dots > span {
        background: rgba(251, 113, 133, 1);
    }

    .shop-status-dots > span:nth-child(2) { animation-delay: 120ms; }
    .shop-status-dots > span:nth-child(3) { animation-delay: 240ms; }

    .shop-inline-dots {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-inline-start: 6px;
        vertical-align: middle;
    }

    .shop-inline-dots > span {
        width: 4px;
        height: 4px;
        border-radius: 9999px;
        background: currentColor;
        opacity: 0.6;
        animation: shopInlineDots 900ms ease-in-out infinite;
    }

    .shop-inline-dots > span:nth-child(2) { animation-delay: 120ms; }
    .shop-inline-dots > span:nth-child(3) { animation-delay: 240ms; }

    .shop-status-package {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        background: rgba(34,197,94,1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.10);
    }

    .shop-status-package.is-dropping {
        animation: shopPackageDrop 600ms ease-in-out infinite alternate;
    }
</style>
@endpush

<x-shop-layouts.app :title="($isAr ? 'طلباتي' : 'Your orders')">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'طلباتي' : 'Your orders' }}</div>
                <i data-lucide="clipboard-list" class="w-5 h-5 text-rose-500"></i>
            </div>
        </div>

        <form method="GET" action="{{ route('shop.orders.index') }}" class="flex flex-col md:flex-row gap-3">
            @php($typeVal = (string) ($type ?? 'orders'))
            <div class="relative flex-1">
                <div class="absolute inset-y-0 {{ $isAr ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ $isAr ? 'بحث...' : 'Search...' }}"
                    class="w-full h-11 rounded-2xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 {{ $isAr ? 'pr-10 pl-4' : 'pl-10 pr-4' }}" />
            </div>

            <select name="type" class="h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700">
                <option value="orders" {{ $typeVal === 'orders' ? 'selected' : '' }}>{{ $isAr ? 'أوردرات' : 'Orders' }}</option>
                <option value="special" {{ $typeVal === 'special' ? 'selected' : '' }}>{{ $isAr ? 'طلبات خاصة' : 'Special orders' }}</option>
            </select>

            <select name="status" class="h-11 rounded-2xl bg-gray-100 text-gray-900 border border-gray-200 px-4 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700">
                @php($statusVal = (string) ($status ?? 'all'))
                <option value="all" {{ $statusVal === 'all' ? 'selected' : '' }}>{{ $isAr ? 'الكل' : 'All' }}</option>
                <option value="قيد التنفيذ" {{ $statusVal === 'قيد التنفيذ' ? 'selected' : '' }}>{{ $isAr ? 'قيد التنفيذ' : 'Processing' }}</option>
                <option value="delivered" {{ $statusVal === 'delivered' ? 'selected' : '' }}>{{ $isAr ? 'تم التوصيل' : 'Delivered' }}</option>
                <option value="cancelled" {{ $statusVal === 'cancelled' ? 'selected' : '' }}>{{ $isAr ? 'ملغي' : 'Cancelled' }}</option>
            </select>

            <button type="submit" class="h-11 px-5 rounded-2xl bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-semibold hover:opacity-90">
                {{ $isAr ? 'بحث' : 'Search' }}
            </button>
        </form>

        @php($safeTrans = fn($val) => is_array($t = __($val)) ? $val : $t)
        <div class="space-y-3">
            @if(($type ?? 'orders') === 'special')
@forelse(($specialOrders ?? []) as $order)
    @php($imgList = $order->images ? json_decode((string) $order->images, true) : [])
    @php($firstImg = is_array($imgList) && !empty($imgList) ? (string) $imgList[0] : '')
    @php($imgUrl = $firstImg !== '' ? \App\Helpers\CurrencyHelper::imageUrl($firstImg) : '')
    @php($statusText = (string) ($order->status ?? ''))
    @php($statusLower = mb_strtolower($statusText))
    @php($isSpecialDelivered = $statusLower === 'approved' || str_contains($statusLower, 'تم') || str_contains($statusLower, 'مقبول'))
    @php($isSpecialPending = $statusLower === 'pending' || str_contains($statusLower, 'قيد'))
    @php($specialStatusKey = $isSpecialDelivered ? 'delivered' : ($isSpecialPending ? 'pending' : (($statusLower === 'rejected' || str_contains($statusLower, 'مرفوض')) ? 'cancelled' : 'other')))
    
    @php($badge = 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-300')
    @if($isSpecialDelivered)
        @php($badge = 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-200')
    @elseif($isSpecialPending)
        @php($badge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-200')
    @elseif($statusLower === 'rejected' || str_contains($statusLower, 'مرفوض'))
        @php($badge = 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-200')
    @endif

    <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
        <div class="flex items-start gap-3">
            <div class="flex-1 min-w-0 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="font-bold text-gray-900 dark:text-white truncate">{{ (string) ($order->title ?? ($isAr ? 'طلب خاص' : 'Special order')) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-300 mt-1 truncate">{{ (string) ($order->product_name ?? '') }}</div>

                <div class="mt-3 w-20 h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700 flex items-center justify-center">
                    @if($imgUrl !== '')
                        <img src="{{ $imgUrl }}" alt="special order" class="w-full h-full object-cover" />
                    @else
                        <i data-lucide="image" class="w-7 h-7 text-gray-400"></i>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                    <span>{{ $safeTrans($statusText) ?: ($isAr ? 'غير محدد' : 'Unknown') }}</span>
                    @if($isSpecialPending)
                        <span class="shop-inline-dots" aria-hidden="true"><span></span><span></span><span></span></span>
                    @endif
                </div>
                                <div class="mt-2 flex justify-end">
                                    <div class="shop-status-track {{ ($isSpecialPending || $isSpecialDelivered) ? 'is-gif' : '' }}" aria-hidden="true">
                                        @if($isSpecialPending)
                                            <img src="{{ asset('delivery.gif') }}" alt="delivery" class="shop-status-gif" />
                                        @elseif($isSpecialDelivered)
                                            <img src="{{ asset('recieve.gif') }}" alt="delivered" class="shop-status-gif" />
                                        @else
                                            <div class="shop-status-package" style="background: rgba(148,163,184,1);"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-300">
                            <div>{{ $isAr ? 'التاريخ:' : 'Date:' }} {{ optional($order->created_at)->format('Y-m-d') ?? '-' }}</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) ($order->budget ?? 0)) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-white border border-gray-200 p-6 text-sm text-gray-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                        {{ $isAr ? 'لا توجد طلبات خاصة.' : 'No special orders found.' }}
                    </div>
                @endforelse
            @else
                @forelse(($orders ?? []) as $order)
                @php($items = $order->items ?? collect())
                @php($firstItem = $items->first())
                @php($product = $firstItem?->product)
                @php($title = $product ? ($isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name)) : (($isAr ? 'اوردر' : 'Order') . ' ' . ($order->order_code ?? '')))
                @php($imgUrl = \App\Helpers\CurrencyHelper::imageUrl($product->image ?? null))
                @php($factory = $product?->suppliers?->firstWhere('type', 'factory'))
                @php($vendor = $product?->suppliers?->firstWhere('type', 'vendor'))
                @php($suppliersForModal = ($product?->suppliers ?? collect())->map(fn($s) => [
                    'id' => (int) $s->id,
                    'name' => (string) (($s->type ?? '') === 'vendor' ? 'Trady' : ($s->name ?? '')),
                    'type' => (string) ($s->type ?? ''),
                    'price' => (float) ($s->pivot->price ?? 0),
                ])->values())
                @php($statusText = (string) ($order->status ?? ''))
                @php($statusLower = mb_strtolower($statusText))
                @php($orderDate = optional($order->created_at)->format('Y-m-d H:i') ?? '')
                @php($orderTotal = (float) ($order->total ?? 0))
                @php($isDelivered = $statusLower === 'delivered' || str_contains($statusLower, 'تم'))
                @php($isPending = $statusLower === 'pending' || $statusLower === 'processing' || str_contains($statusLower, 'قيد'))
                @php($statusKey = $isDelivered ? 'delivered' : ($isPending ? 'pending' : (($statusLower === 'cancelled' || $statusLower === 'canceled' || str_contains($statusLower, 'ملغي')) ? 'cancelled' : 'other')))
                @php($statusLabel = $safeTrans($statusText) ?: ($isAr ? 'غير محدد' : 'Unknown'))
                @php($badge = 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-300')
                @if($statusLower === 'delivered' || str_contains($statusLower, 'تم'))
                    @php($badge = 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-200')
                @elseif($statusLower === 'processing' || str_contains($statusLower, 'قيد'))
                    @php($badge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-200')
                @elseif($statusLower === 'cancelled' || $statusLower === 'canceled' || str_contains($statusLower, 'ملغي'))
                    @php($badge = 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-200')
                @endif

                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-bold text-gray-900 dark:text-white truncate">{{ $title }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-300 mt-1 truncate">{{ $order->order_code }}</div>

                                    <div class="mt-3 w-20 h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                        @if($imgUrl !== '')
                                            <img src="{{ $imgUrl }}" alt="order" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i data-lucide="image" class="w-7 h-7 text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                        <span>{{ $statusLabel }}</span>
                                        @if($isPending)
                                            <span class="shop-inline-dots" aria-hidden="true"><span></span><span></span><span></span></span>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex justify-end">
                                        <div class="shop-status-track {{ ($isPending || $isDelivered) ? 'is-gif' : '' }}" aria-hidden="true">
                                            @if($isPending)
                                                <img src="{{ asset('delivery.gif') }}" alt="delivery" class="shop-status-gif" />
                                            @elseif($isDelivered)
                                                <img src="{{ asset('recieve.gif') }}" alt="delivered" class="shop-status-gif" />
                                            @else
                                                <svg class="shop-status-truck" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <linearGradient id="shopTruckGrad" x1="8" y1="12" x2="58" y2="52" gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="currentColor" stop-opacity="1"/>
                                                            <stop offset="1" stop-color="currentColor" stop-opacity="0.75"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <path d="M10 20C10 16.6863 12.6863 14 16 14H38C41.3137 14 44 16.6863 44 20V38H10V20Z" stroke="url(#shopTruckGrad)" stroke-width="4.5" stroke-linejoin="round"/>
                                                    <path d="M44 24H51.5L57 31V38H44V24Z" stroke="url(#shopTruckGrad)" stroke-width="4.5" stroke-linejoin="round"/>
                                                    <path d="M16 38H57" stroke="url(#shopTruckGrad)" stroke-width="4.5" stroke-linecap="round"/>
                                                    <g>
                                                        <circle cx="22" cy="46" r="6" stroke="url(#shopTruckGrad)" stroke-width="4.5"/>
                                                        <circle cx="22" cy="46" r="1.5" fill="currentColor" fill-opacity="0.85"/>
                                                    </g>
                                                    <g>
                                                        <circle cx="50" cy="46" r="6" stroke="url(#shopTruckGrad)" stroke-width="4.5"/>
                                                        <circle cx="50" cy="46" r="1.5" fill="currentColor" fill-opacity="0.85"/>
                                                    </g>
                                                    <path d="M18 14H30" stroke="url(#shopTruckGrad)" stroke-width="4.5" stroke-linecap="round"/>
                                                </svg>

                                                <div class="shop-status-package" style="background: rgba(148,163,184,1);"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 flex items-center gap-1 text-sm">
                                <span class="text-yellow-400">★</span>
                                <span class="text-yellow-400">★</span>
                                <span class="text-yellow-400">★</span>
                                <span class="text-gray-300 dark:text-slate-600">★</span>
                                <span class="text-gray-300 dark:text-slate-600">★</span>
                            </div>

                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-300">
                                <div>{{ $isAr ? 'التاريخ:' : 'Date:' }} {{ optional($order->created_at)->format('Y-m-d') ?? '-' }}</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format((float) ($order->total ?? 0)) }}</div>
                            </div>

                            @if($items->isNotEmpty())
                                <div class="mt-3 border-t border-gray-100 dark:border-slate-800 pt-3">
                                    <div class="text-xs font-semibold text-gray-700 dark:text-slate-200 mb-2">
                                        {{ $isAr ? 'تفاصيل المنتجات' : 'Order items' }}
                                    </div>
                                    <div class="space-y-1">
                                        @foreach($items as $item)
                                            @php($p = $item->product)
                                            @php($lineName = $p ? ($isAr ? ($p->name_ar ?? $p->name) : ($p->name_en ?? $p->name)) : ($isAr ? 'منتج' : 'Product'))
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex-1 min-w-0">
                                                    <div class="truncate text-gray-800 dark:text-slate-100">{{ $lineName }}</div>
                                                    <div class="text-[11px] text-gray-500 dark:text-slate-400">
                                                        {{ $isAr ? 'الكمية:' : 'Qty:' }} {{ (int) ($item->quantity ?? 1) }}
                                                        ·
                                                        {{ $isAr ? 'سعر الوحدة:' : 'Unit:' }} {{ \App\Helpers\CurrencyHelper::format((float) ($item->unit_price ?? 0)) }}
                                                    </div>
                                                </div>
                                                <div class="ms-2 text-[11px] font-semibold text-gray-900 dark:text-white">
                                                    {{ \App\Helpers\CurrencyHelper::format((float) ($item->total ?? 0)) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="mt-3 flex justify-end items-center gap-4">
                                <a href="{{ route('shop.orders.invoice', $order->id) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white flex items-center gap-1">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                    {{ $isAr ? 'الفاتورة' : 'Invoice' }}
                                </a>

                              
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-white border border-gray-200 p-6 text-sm text-gray-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                    {{ $isAr ? 'لا توجد أوردرات.' : 'No orders found.' }}
                </div>
            @endforelse
            @endif
        </div>
    </div>
</x-shop-layouts.app>
