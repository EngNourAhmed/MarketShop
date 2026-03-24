@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'السلة' : 'Cart')">
    @php($availablePoints = (int) ($availablePoints ?? 0))
    @php($itemsCount = (int) (collect($items ?? [])->sum('quantity') ?? 0))

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-950 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $isAr ? 'السلة' : 'Cart' }}</div>
                    @if($itemsCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[22px] h-6 px-2 rounded-full text-xs font-semibold bg-rose-500 text-white">
                            {{ $itemsCount > 99 ? '99+' : $itemsCount }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('customer.home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-slate-200 hover:text-rose-500">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    {{ $isAr ? 'رجوع' : 'Back' }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if(($items ?? collect())->isEmpty())
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 text-sm text-gray-500 dark:text-gray-300">
                {{ $isAr ? 'السلة فارغة.' : 'Your cart is empty.' }}
            </div>
        @else
            @php($subtotal = (float) ($items ?? collect())->sum(fn ($i) => (float) ($i->total_after_all ?? $i->total ?? 0)))
            @php($shipping = 20.0)
            @php($pointsRateEgp = 1)
            @php($maxPoints = max(0, (int) floor($subtotal / max(1, $pointsRateEgp))))
            @php($pointsDiscount = min($availablePoints, $maxPoints) * $pointsRateEgp)

            <div class="space-y-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isAr ? 'مشترياتك' : 'Your Items' }}</div>

                <div class="space-y-3">
                    @foreach($items as $item)
                        @php($product = $item->product)
                        @php($supplierName = optional($item->supplier)->name)
                        @php($title = $isAr ? ($product->name_ar ?? $product->name) : ($product->name_en ?? $product->name))
                        @php($imageUrl = \App\Helpers\CurrencyHelper::imageUrl($product->image ?? null))

                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-900/40 p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-16 rounded-2xl overflow-hidden bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex-shrink-0">
                                    <img src="{{ $imageUrl }}" alt="product" class="w-full h-full object-cover" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</div>
                                    @if(!empty($item->color))
                                        <div class="text-xs text-gray-500 dark:text-slate-300 mt-1">{{ $isAr ? 'اللون:' : 'Color:' }} {{ $item->color }}</div>
                                    @endif
                                    @if(!empty($supplierName))
                                        <div class="text-xs text-gray-500 dark:text-slate-300 mt-1">{{ $isAr ? 'المورد:' : 'Supplier:' }} {{ $supplierName }}</div>
                                    @endif
                                    <div class="mt-2 flex items-center gap-2">
                                        <form method="POST" action="{{ route('shop.cart.update', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delta" value="-1" />
                                            <button type="submit" class="h-7 w-7 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-700 dark:text-white text-sm">
                                                -
                                            </button>
                                        </form>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ (int) $item->quantity }}</div>
                                        <form method="POST" action="{{ route('shop.cart.update', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="delta" value="1" />
                                            <button type="submit" class="h-7 w-7 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-gray-700 dark:text-white text-sm">
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ \App\Helpers\CurrencyHelper::format((float) ($item->total_after_all ?? $item->total ?? 0)) }}
                                    </div>

                                    <form method="POST" action="{{ route('shop.cart.destroy', $item->id) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 w-9 rounded-xl border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 text-gray-700 dark:text-slate-200">
                                            <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-900/40 p-4">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                            <span>{{ $isAr ? 'الإجمالي الفرعي' : 'Subtotal' }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white" id="cart-subtotal">{{ \App\Helpers\CurrencyHelper::format($subtotal) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                            <span>{{ $isAr ? 'الشحن' : 'Shipping' }}</span>
                            <span class="font-semibold text-gray-900 dark:text-white" id="cart-shipping">{{ \App\Helpers\CurrencyHelper::format($shipping) }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-gray-600 dark:text-slate-300">
                                <i data-lucide="badge-percent" class="w-4 h-4 text-rose-500"></i>
                                <span>{{ $isAr ? 'تطبيق خصم النقاط' : 'Apply Points Discount' }}</span>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input id="cart-apply-points" type="checkbox" class="sr-only" {{ $availablePoints > 0 ? '' : 'disabled' }}>
                                <span class="w-12 h-7 rounded-full bg-gray-200 dark:bg-slate-700 transition"></span>
                                <span id="cart-apply-points-knob" class="absolute left-1 top-1 w-5 h-5 rounded-full bg-white transition"></span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-slate-400">
                            <span></span>
                            <span>{{ $isAr ? 'متاح' : 'You have' }} {{ $availablePoints }} {{ $isAr ? 'نقطة' : 'points available' }}</span>
                        </div>

                        <div class="border-t border-gray-200 dark:border-slate-700 pt-3">
                            <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                                <span>{{ $isAr ? 'خصم النقاط' : 'Points Discount' }}</span>
                                <span class="font-semibold text-emerald-500" id="cart-points-discount">-{{ \App\Helpers\CurrencyHelper::format($pointsDiscount) }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-base font-bold text-gray-900 dark:text-white">{{ $isAr ? 'الإجمالي' : 'Total' }}</span>
                            <span class="text-lg font-extrabold text-rose-500" id="cart-total"></span>
                        </div>
                    </div>
                </div>

                <form id="cart-checkout-form" method="GET" action="{{ route('shop.checkout.index') }}" class="pt-2">
                    <input type="hidden" name="apply_points" id="cart-apply-points-hidden" value="0" />
                    <button type="submit" class="w-full h-14 rounded-full bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-bold text-lg hover:opacity-95">
                        {{ $isAr ? 'إتمام الشراء' : 'Checkout' }}
                    </button>
                </form>
            </div>

            @push('scripts')
                <script>
                    (function () {
                        const subtotal = Number({{ json_encode((float) $subtotal) }}) || 0;
                        const shipping = Number({{ json_encode((float) $shipping) }}) || 0;
                        const maxDiscount = Number({{ json_encode((float) $pointsDiscount) }}) || 0;

                        const applyEl = document.getElementById('cart-apply-points');
                        const hiddenEl = document.getElementById('cart-apply-points-hidden');
                        const discountEl = document.getElementById('cart-points-discount');
                        const totalEl = document.getElementById('cart-total');
                        const knob = document.getElementById('cart-apply-points-knob');

                        const render = () => {
                            const apply = !!(applyEl && applyEl.checked);
                            const discount = apply ? maxDiscount : 0;
                            
                            const c_formatMoney = (n) => {
                                if (typeof window.formatMoney === 'function') {
                                    return window.formatMoney(n);
                                }
                                // Basic fallback if global helper is missing
                                const rate = window.exchangeRate || 1;
                                const symbol = window.currencySymbol || 'EGP';
                                const divided = (Number(n || 0)) / rate;
                                const formatted = divided.toLocaleString(undefined, { 
                                    maximumFractionDigits: divided < 1 ? 2 : 0,
                                    minimumFractionDigits: divided < 1 ? 2 : 0
                                });
                                return window.isAr ? formatted + ' ' + symbol : symbol + ' ' + formatted;
                            };

                            if (discountEl) discountEl.textContent = '-' + c_formatMoney(discount);
                            if (totalEl) totalEl.textContent = c_formatMoney(Math.max(0, (subtotal + shipping) - discount));
                            if (hiddenEl) hiddenEl.value = apply ? '1' : '0';

                            if (applyEl && knob) {
                                const on = applyEl.checked;
                                knob.style.transform = on ? 'translateX(20px)' : 'translateX(0px)';
                                applyEl.nextElementSibling?.classList.toggle('bg-rose-500', on);
                                applyEl.nextElementSibling?.classList.toggle('bg-gray-200', !on);
                            }
                        };

                        if (applyEl) applyEl.addEventListener('change', render);
                        render();
                    })();
                </script>
            @endpush

        @endif
    </div>
</x-shop-layouts.app>
