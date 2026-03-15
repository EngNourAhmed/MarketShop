@php($lang = session('lang', 'en'))
@php($isAr = $lang === 'ar')

<x-shop-layouts.app :title="($isAr ? 'الدفع' : 'Checkout')">
    @php($availablePoints = (int) ($availablePoints ?? 0))
    @php($applyPoints = (bool) ($applyPoints ?? false))
    @php($itemsCount = (int) (collect($items ?? [])->sum('quantity') ?? 0))
    @php($subtotal = (float) ($subtotal ?? 0))
    @php($shipping = (float) ($shipping ?? 20))
    @php($pointsRateEgp = 1)
    @php($maxPoints = max(0, (int) floor($subtotal / max(1, $pointsRateEgp))))
    @php($pointsDiscount = $applyPoints ? (min($availablePoints, $maxPoints) * $pointsRateEgp) : 0)
    @php($total = max(0, ($subtotal + $shipping) - $pointsDiscount))

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-950 px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="{{ route('shop.cart.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-slate-200 hover:text-rose-500">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    {{ $isAr ? 'السلة' : 'Cart' }}
                </a>
                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $isAr ? 'Checkout' : 'Checkout' }}</div>
                <div class="w-10"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('shop.orders.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="apply_points" id="checkout-apply-points" value="{{ $applyPoints ? 1 : 0 }}" />

            <div class="space-y-3">
                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $isAr ? 'عنوان الشحن' : 'Shipping Address' }}</div>

                <input name="full_name" value="{{ old('full_name', (string) data_get($prefill ?? [], 'name', '')) }}" placeholder="{{ $isAr ? 'الاسم بالكامل' : 'Full Name' }}" required
                       class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />

                <input name="address_line_1" value="{{ old('address_line_1', (string) data_get($prefill ?? [], 'address', '')) }}" placeholder="{{ $isAr ? 'العنوان' : 'Address Line 1' }}" required
                       class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />

                <input name="city" value="{{ old('city') }}" placeholder="{{ $isAr ? 'المدينة' : 'City' }}" required
                       class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />

                <div class="grid grid-cols-2 gap-3">
                    <input name="state" value="{{ old('state') }}" placeholder="{{ $isAr ? 'المحافظة' : 'State / Province' }}"
                           class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />

                    <input name="postal_code" value="{{ old('postal_code') }}" placeholder="{{ $isAr ? 'الرمز البريدي' : 'Postal Code' }}"
                           class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />
                </div>

                <input name="phone" value="{{ old('phone', (string) data_get($prefill ?? [], 'phone', '')) }}" placeholder="{{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}" required
                       class="w-full h-12 rounded-xl bg-gray-100 text-gray-900 placeholder-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-400 dark:border-slate-700 px-4" />
            </div>

            <div class="space-y-3">
                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $isAr ? 'طريقة التوصيل' : 'Delivery Method' }}</div>

                @php($deliverySelected = old('delivery_method', (string) ($deliveryMethod ?? 'standard')))

                <label class="block cursor-pointer">
                    <input class="peer sr-only" type="radio" name="delivery_method" value="standard" {{ $deliverySelected === 'standard' ? 'checked' : '' }} />
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white/40 dark:bg-slate-900/20 px-4 py-3 peer-checked:border-rose-500/40 peer-checked:bg-rose-500/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isAr ? 'توصيل عادي' : 'Standard Delivery' }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-300">{{ $isAr ? '5-7 أيام عمل' : '5-7 business days' }}</div>
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format(20.00) }}</div>
                        </div>
                    </div>
                </label>

                <label class="block cursor-pointer">
                    <input class="peer sr-only" type="radio" name="delivery_method" value="express" {{ $deliverySelected === 'express' ? 'checked' : '' }} />
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white/40 dark:bg-slate-900/20 px-4 py-3 peer-checked:border-rose-500/40 peer-checked:bg-rose-500/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isAr ? 'توصيل سريع' : 'Express Delivery' }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-300">{{ $isAr ? '1-2 يوم عمل' : '1-2 business days' }}</div>
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::format(50.00) }}</div>
                        </div>
                    </div>
                </label>
            </div>

            <div class="space-y-3">
                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $isAr ? 'طريقة الدفع' : 'Payment Method' }}</div>

                @php($paymentSelected = old('payment_method', 'card'))

                <label class="block cursor-pointer">
                    <input class="peer sr-only" type="radio" name="payment_method" value="card" {{ $paymentSelected === 'card' ? 'checked' : '' }} />
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white/40 dark:bg-slate-900/20 px-4 py-3 peer-checked:border-rose-500/40 peer-checked:bg-rose-500/10">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isAr ? 'بطاقة' : 'Credit Card' }}</div>
                    </div>
                </label>

                <label class="block cursor-pointer">
                    <input class="peer sr-only" type="radio" name="payment_method" value="wallet" {{ $paymentSelected === 'wallet' ? 'checked' : '' }} />
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white/40 dark:bg-slate-900/20 px-4 py-3 peer-checked:border-rose-500/40 peer-checked:bg-rose-500/10">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isAr ? 'محفظة' : 'Online Wallets' }}</div>
                    </div>
                </label>

                <label class="block cursor-pointer">
                    <input class="peer sr-only" type="radio" name="payment_method" value="cod" {{ $paymentSelected === 'cod' ? 'checked' : '' }} />
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 bg-white/40 dark:bg-slate-900/20 px-4 py-3 peer-checked:border-rose-500/40 peer-checked:bg-rose-500/10">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $isAr ? 'الدفع عند الاستلام' : 'Cash on Delivery' }}</div>
                    </div>
                </label>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-slate-800 dark:bg-slate-900/40 p-4">
                <div class="text-sm font-bold text-gray-900 dark:text-white mb-3">{{ $isAr ? 'ملخص الطلب' : 'Order Summary' }}</div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                        <span>{{ $isAr ? 'عدد المنتجات' : 'Items' }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $itemsCount }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                        <span>{{ $isAr ? 'الإجمالي الفرعي' : 'Subtotal' }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white" id="co-subtotal">{{ \App\Helpers\CurrencyHelper::format($subtotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                        <span>{{ $isAr ? 'الشحن' : 'Shipping' }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white" id="co-shipping">{{ \App\Helpers\CurrencyHelper::format($shipping) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600 dark:text-slate-300">
                        <span>{{ $isAr ? 'خصم النقاط' : 'Points Discount' }}</span>
                        <span class="font-semibold text-emerald-500" id="co-points">-{{ \App\Helpers\CurrencyHelper::format($pointsDiscount) }}</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-slate-700 pt-2 flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900 dark:text-white">{{ $isAr ? 'الإجمالي' : 'Total' }}</span>
                        <span class="text-lg font-extrabold text-rose-500" id="co-total">{{ \App\Helpers\CurrencyHelper::format($total) }}</span>
                    </div>
                </div>

                <button id="checkout-submit" type="submit" class="mt-4 w-full h-14 rounded-full bg-gradient-to-r from-[#F6416C] to-orange-400 text-white font-bold text-lg hover:opacity-95">
                    {{ $isAr ? 'تأكيد الطلب' : 'Place order' }}
                </button>

            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            (function () {
                const applyPoints = Number({{ json_encode($applyPoints ? 1 : 0) }}) === 1;
                const subtotal = Number({{ json_encode((float) $subtotal) }}) || 0;
                const availablePoints = Number({{ json_encode((int) $availablePoints) }}) || 0;
                const pointsRate = 1;
                const maxPoints = Math.max(0, Math.floor(subtotal / Math.max(1, pointsRate)));
                const maxDiscount = (Math.min(availablePoints, maxPoints) * pointsRate);

                const shippingEl = document.getElementById('co-shipping');
                const pointsEl = document.getElementById('co-points');
                const totalEl = document.getElementById('co-total');
                const applyHidden = document.getElementById('checkout-apply-points');

                const getShipping = () => {
                    const selected = document.querySelector('input[name="delivery_method"]:checked');
                    const val = selected ? String(selected.value || '') : 'standard';
                    return val === 'express' ? 50 : 20;
                };

                const render = () => {
                    const shipping = getShipping();
                    const discount = applyPoints ? maxDiscount : 0;
                    const exRate = window.exchangeRate || 1;
                    const cSymbol = window.currencySymbol || 'EGP';

                    const c_formatMoney = (n) => {
                        const converted = Number(n || 0) / exRate;
                        return converted.toLocaleString(undefined, { 
                            maximumFractionDigits: converted < 1 ? 2 : 0,
                            minimumFractionDigits: converted < 1 ? 2 : 0
                        }) + ' ' + cSymbol;
                    };

                    if (shippingEl) shippingEl.textContent = c_formatMoney(shipping);
                    if (pointsEl) pointsEl.textContent = '-' + c_formatMoney(discount);
                    if (totalEl) totalEl.textContent = c_formatMoney(Math.max(0, (subtotal + shipping) - discount));
                    if (applyHidden) applyHidden.value = applyPoints ? '1' : '0';
                };

                document.querySelectorAll('input[name="delivery_method"]').forEach((r) => r.addEventListener('change', render));
                render();
            })();
        </script>

        <script>
            (function () {
                const paymentRadio = document.querySelector('input[name="payment_method"]');
                const form = paymentRadio ? paymentRadio.closest('form') : null;
                if (!form) return;

                const submitBtn = document.getElementById('checkout-submit');
                let submitting = false;

                const disableSubmit = () => {
                    if (!submitBtn) return;
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
                };

                const autoSubmitIfOnline = (e) => {
                    const selected = document.querySelector('input[name="payment_method"]:checked');
                    const method = selected ? String(selected.value || '') : '';

                    if (method === 'card' || method === 'wallet') {
                        if (submitting) return;
                        
                        // Check form validity before auto-submitting
                        if (form.reportValidity()) {
                            submitting = true;
                            disableSubmit();
                            
                            // Show loading on the button
                            if (submitBtn) {
                                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> ' + ({{ json_encode($isAr) }} ? 'جاري التحويل...' : 'Redirecting...');
                            }
                            
                            setTimeout(() => form.submit(), 50);
                        }
                    }
                };

                document.querySelectorAll('input[name="payment_method"]').forEach((r) => {
                    r.addEventListener('change', autoSubmitIfOnline);
                    // Also trigger on click in case it's already selected
                    r.addEventListener('click', autoSubmitIfOnline);
                });
            })();
        </script>
    @endpush
</x-shop-layouts.app>
