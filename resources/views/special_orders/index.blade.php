<x-layouts.app :title="'الطلبات الخاصة'">
    <div id="special-orders-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">الطلبات الخاصة</h1>

        <div
            class="rounded-2xl shadow-lg bg-white text-gray-800 border border-gray-200 dark:bg-slate-900 dark:text-white dark:border-slate-800 p-5">
            <div class="flex flex-col gap-4 mb-4">
                <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
                    <div class="flex items-center gap-2">
                        <div class="text-xl font-bold">الطلبات الخاصة</div>
                        @php($isAssignOpen = old('__modal') === 'assign-special-order')
                        <details {{ $isAssignOpen && $errors->any() ? 'open' : '' }}>
                            <summary
                                class="cursor-pointer px-3 py-1.5 text-sm font-semibold rounded-lg bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">
                                إضافة طلب جديد</summary>
                            <div class="fixed inset-0 z-40">
                                <div class="absolute inset-0 bg-black/40"
                                    onclick="this.closest('details').removeAttribute('open')"></div>
                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                    <div
                                        class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                        <div
                                            class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="font-semibold text-gray-800 dark:text-gray-100">إسناد طلبات خاصة
                                                إلى موردين</div>
                                            <button type="button"
                                                class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                onclick="this.closest('details').removeAttribute('open')">✕</button>
                                        </div>
                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                            <form method="POST" action="" class="grid grid-cols-1 gap-3"
                                                data-special-assign-form
                                                data-base-url="{{ url('admin/special-orders') }}"
                                                data-orders='@json($assignOrdersPayload ?? [])'
                                                data-suppliers='@json($assignSuppliersPayload ?? [])'>
                                                @csrf
                                                <input type="hidden" name="__modal" value="assign-special-order" />

                                                <div class="space-y-3" data-assign-rows-wrapper>
                                                    <!-- rows will be injected via JS -->
                                                </div>

                                                <div class="flex justify-between mt-2">
                                                    <button type="button"
                                                        class="px-3 py-2 rounded-lg bg-gray-100 border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-gray-200 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:hover:bg-gray-800"
                                                        data-add-assign-row>
                                                        + إضافة طلب آخر
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 rounded-lg bg-rose-500 text-white text-sm font-semibold hover:bg-rose-600">
                                                        حفظ الإسناد
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </div>

                    <div class="relative w-full lg:w-[420px]">
                        <input id="special-orders-search" type="text"
                            placeholder="ابحث بعنوان الطلب أو اسم العميل..."
                            class="w-full ps-10 pe-3 py-2 rounded-lg bg-gray-100 text-gray-900 placeholder:text-gray-500 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-400 dark:border-slate-700" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 dark:text-slate-400"></i>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('special_orders.index') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-slate-300 mb-1">من تاريخ</label>
                        <input type="date" name="from" value="{{ $from ?? request()->query('from') }}"
                            class="w-full p-2.5 rounded-lg bg-white text-gray-800 border border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-slate-300 mb-1">إلى تاريخ</label>
                        <input type="date" name="to" value="{{ $to ?? request()->query('to') }}"
                            class="w-full p-2.5 rounded-lg bg-white text-gray-800 border border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 dark:text-slate-300 mb-1">الحالة</label>
                        @php($currentStatus = (string) ($status ?? 'all'))
                        <select name="status"
                            class="w-full p-2.5 rounded-lg bg-white text-gray-800 border border-gray-300 dark:bg-slate-800 dark:text-white dark:border-slate-700">
                            <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>الكل</option>
                            @foreach ($availableStatuses ?? [] as $st)
                                <option value="{{ $st }}" {{ $currentStatus === $st ? 'selected' : '' }}>
                                    {{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">بحث</button>
                        <a href="{{ route('special_orders.index') }}"
                            class="px-4 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700">مسح</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-right">
                    <thead>
                        <tr
                            class="text-gray-600 border-b border-gray-200 dark:text-slate-300 dark:border-slate-700 text-xs font-semibold">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">العميل</th>
                            <th class="px-4 py-3">عنوان الطلب</th>
                            <th class="px-4 py-3">التفاصيل</th>
                            <th class="px-4 py-3">الميزانية</th>
                            <th class="px-4 py-3">المورد</th>
                            <th class="px-4 py-3">المنتج</th>
                            <th class="px-4 py-3">سعر الإسناد</th>
                            <th class="px-4 py-3">حالة المورد</th>
                            <th class="px-4 py-3">قرار الأدمن</th>
                            <th class="px-4 py-3">تاريخ الإنشاء</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-sm">
                        @forelse(($orders ?? []) as $order)
                            @php($assignedSupplier = $order->supplier)
                            @php($assignedProduct = $order->product)
                            @php($customerName = $order->user->name ?? '')
                            @php($customerEmail = $order->user->email ?? '')
                            @php($searchText = mb_strtolower((string) ($order->title ?? '') . ' ' . $customerName . ' ' . $customerEmail))
                            <tr data-special-order-row data-search="{{ $searchText }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                <td class="px-4 py-3 align-top">{{ $order->id }}</td>
                                <td class="px-4 py-3 align-top">
                                    <div class="font-semibold">{{ $customerName !== '' ? $customerName : '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customerEmail }}</div>
                                </td>
                                <td class="px-4 py-3 align-top">{{ $order->title }}</td>
                                <td class="px-4 py-3 align-top text-xs text-gray-700 dark:text-gray-200">
                                    @php($imgs = [])
                                    @php($imgs = is_string($order->images ?? null) ? (json_decode((string) $order->images, true) ?: []) : [])

                                    <div class="space-y-2">
                                        <div class="text-xs text-gray-600 dark:text-gray-300">
                                            {{ \Illuminate\Support\Str::limit((string) ($order->product_name ?? $order->title ?? ''), 40) }}
                                        </div>

                                        <details>
                                            <summary class="cursor-pointer inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-800 hover:bg-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700 text-xs font-semibold [&::-webkit-details-marker]:hidden">
                                                عرض التفاصيل
                                            </summary>

                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>
                                                <div class="relative mx-auto my-6 w-[95vw] max-w-4xl">
                                                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-900 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تفاصيل الطلب الخاص #{{ $order->id }}</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>

                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">معلومات المنتج</div>
                                                                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                                                        <div><span class="font-semibold">العنوان:</span> {{ $order->title ?? '-' }}</div>
                                                                        <div><span class="font-semibold">اسم المنتج:</span> {{ $order->product_name ?? '-' }}</div>
                                                                        <div><span class="font-semibold">الكمية:</span> {{ $order->quantity ?? '-' }}</div>
                                                                        <div><span class="font-semibold">اللون:</span> {{ $order->color ?? '-' }}</div>
                                                                        <div><span class="font-semibold">المقاس:</span> {{ $order->size ?? '-' }}</div>
                                                                        <div><span class="font-semibold">الخامة:</span> {{ $order->material ?? '-' }}</div>
                                                                        <div>
                                                                            <span class="font-semibold">رابط مرجعي:</span>
                                                                            @if(!empty($order->reference_url))
                                                                                <a href="{{ $order->reference_url }}" target="_blank" class="text-rose-600 dark:text-rose-400 underline break-all">{{ $order->reference_url }}</a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">تفاصيل العميل</div>
                                                                    <div class="space-y-3 text-sm text-gray-700 dark:text-gray-200">
                                                                        <div>
                                                                            <div class="font-semibold mb-1">المواصفات</div>
                                                                            <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-200">{{ $order->specs ?? '-' }}</div>
                                                                        </div>
                                                                        <div>
                                                                            <div class="font-semibold mb-1">التفاصيل</div>
                                                                            <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-200">{{ $order->details ?? '-' }}</div>
                                                                        </div>
                                                                        @if(!empty($order->admin_rejection_reason))
                                                                            <div class="rounded-lg bg-rose-50 text-rose-700 border border-rose-200 p-3 dark:bg-rose-900/20 dark:text-rose-200 dark:border-rose-900/30">
                                                                                <div class="font-semibold mb-1">سبب الرفض</div>
                                                                                <div class="whitespace-pre-wrap">{{ (string) $order->admin_rejection_reason }}</div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if(!empty($imgs))
                                                                <div class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">الصور</div>
                                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                                                        @foreach($imgs as $p)
                                                                            @php($url = route('admin.media.public', ['path' => ltrim((string) $p, '/')]))
                                                                            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white/40 dark:bg-slate-800/40">
                                                                                <a href="{{ $url }}" target="_blank" class="block">
                                                                                    <img
                                                                                        src="{{ $url }}"
                                                                                        alt="img"
                                                                                        class="w-full h-28 object-cover"
                                                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                                                    />
                                                                                    <div style="display:none" class="w-full h-28 items-center justify-center text-xs text-gray-600 dark:text-gray-300">
                                                                                        معاينة غير متاحة
                                                                                    </div>
                                                                                </a>
                                                                                <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                                                                                    <a href="{{ $url }}" target="_blank" class="text-xs font-semibold text-rose-600 dark:text-rose-400 underline break-all">فتح الصورة</a>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($order->budget !== null)
                                        {{ number_format((float) $order->budget, 2, '.', ',') }} ج.م
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($assignedSupplier)
                                        @php($isVendor = ($assignedSupplier->type ?? '') === 'vendor')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $isVendor ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/50' : 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700' }}">
                                            {{ $assignedSupplier->name ?? '-' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    {{ $assignedProduct->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($order->assigned_price !== null)
                                        {{ number_format((float) $order->assigned_price, 2, '.', ',') }} ج.م
                                    @elseif($order->budget !== null)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">من الميزانية:
                                            {{ number_format((float) $order->budget, 2, '.', ',') }} ج.م</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @php($supplierStatus = (string) ($order->status ?? ''))
                                    @php($supplierStatusLabel = $supplierStatus)
                                    @php($supplierStatusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100')
                                    @if ($supplierStatus === 'pending')
                                        @php($supplierStatusLabel = 'قيد المراجعة')
                                        @php($supplierStatusClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300')
                                    @elseif($supplierStatus === 'in_progress')
                                        @php($supplierStatusLabel = 'جاري تنفيذ الطلب')
                                        @php($supplierStatusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300')
                                    @elseif($supplierStatus === 'done')
                                        @php($supplierStatusLabel = 'تم توافر الطلب')
                                        @php($supplierStatusClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')
                                    @elseif($supplierStatus === 'manufacturing')
                                        @php($supplierStatusLabel = 'تحت التصنيع')
                                        @php($supplierStatusClass = 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300')
                                    @elseif($supplierStatus === 'shipping')
                                        @php($supplierStatusLabel = 'يتم الشحن')
                                        @php($supplierStatusClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300')
                                    @elseif($supplierStatus === 'shipped')
                                        @php($supplierStatusLabel = 'اتشحن')
                                        @php($supplierStatusClass = 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300')
                                    @elseif($supplierStatus === 'cancelled')
                                        @php($supplierStatusLabel = 'لم يتم توافر الطلب')
                                        @php($supplierStatusClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300')
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $supplierStatusClass }}">
                                        {{ $supplierStatusLabel }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    @php($adminStatus = (string) ($order->admin_status ?? ''))
                                    @php($badgeLabel = $adminStatus)
                                    @php($badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100')
                                    @if ($adminStatus === 'approved')
                                        @php($badgeLabel = 'موافقة الأدمن')
                                        @php($badgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300')
                                    @elseif($adminStatus === 'rejected')
                                        @php($badgeLabel = 'رفض الأدمن')
                                        @php($badgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300')
                                    @else
                                        @php($badgeLabel = 'بانتظار قرار الأدمن')
                                        @php($badgeClass = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300')
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    {{ optional($order->created_at)->format('Y-m-d H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-3 align-top">
                                    @php($adminStatus = (string) ($order->admin_status ?? ''))
                                    @if($adminStatus !== '')
                                        <span class="text-xs text-gray-500 dark:text-gray-300">تم اتخاذ قرار</span>
                                    @else
                                        <div class="flex flex-col gap-2">
                                            <form method="POST" action="{{ route('special_orders.approve', $order->id) }}">
                                                @csrf
                                                <button type="submit" class="w-full px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-500 text-white hover:bg-emerald-600">
                                                    موافقة
                                                </button>
                                            </form>

                                            <details>
                                                <summary class="cursor-pointer w-full px-3 py-2 rounded-lg text-xs font-semibold bg-rose-500 text-white hover:bg-rose-600 [&::-webkit-details-marker]:hidden">رفض</summary>
                                                <div class="mt-2 space-y-2">
                                                    <form method="POST" action="{{ route('special_orders.reject', $order->id) }}">
                                                        @csrf
                                                        <textarea name="rejection_reason" rows="3" class="w-full p-2 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs" placeholder="سبب الرفض (اختياري)">{{ old('rejection_reason') }}</textarea>
                                                        <button type="submit" class="w-full px-3 py-2 rounded-lg text-xs font-semibold bg-rose-600 text-white hover:bg-rose-700">
                                                            تأكيد الرفض
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12"
                                    class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-300">
                                    لا توجد طلبات خاصة حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // مودال الإسناد المتعدد
                const assignForm = document.querySelector('[data-special-assign-form]');
                if (assignForm) {
                    const baseUrl = (assignForm.getAttribute('data-base-url') || '').replace(/\/$/, '');
                    let orders = [];
                    let suppliers = [];
                    try {
                        orders = JSON.parse(assignForm.getAttribute('data-orders') || '[]') || [];
                    } catch (e) {
                        orders = [];
                    }
                    try {
                        suppliers = JSON.parse(assignForm.getAttribute('data-suppliers') || '[]') || [];
                    } catch (e) {
                        suppliers = [];
                    }

                    const rowsWrapper = assignForm.querySelector('[data-assign-rows-wrapper]');
                    const addRowBtn = assignForm.querySelector('[data-add-assign-row]');

                    function buildAssignRow() {
                        if (!rowsWrapper) return;

                        const row = document.createElement('div');
                        row.className =
                            'border border-gray-200 dark:border-gray-700 rounded-lg p-3 space-y-2 bg-gray-50 dark:bg-gray-900/40';
                        row.setAttribute('data-assign-row', '');

                        const top = document.createElement('div');
                        top.className = 'flex items-start gap-2';

                        const orderCol = document.createElement('div');
                        orderCol.className = 'flex-1';

                        const orderLabel = document.createElement('label');
                        orderLabel.className = 'block text-sm mb-1 text-gray-600 dark:text-gray-300';
                        orderLabel.textContent = 'الطلب الخاص';

                        const orderDropdown = document.createElement('div');
                        orderDropdown.className = 'relative';

                        const orderInput = document.createElement('input');
                        orderInput.type = 'text';
                        orderInput.placeholder = 'اختر طلباً أو ابحث بالعنوان أو اسم العميل...';
                        orderInput.className =
                            'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200';
                        orderInput.autocomplete = 'off';

                        const orderList = document.createElement('div');
                        orderList.className =
                            'absolute z-30 mt-1 w-full max-h-56 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg hidden';

                        const orderHidden = document.createElement('input');
                        orderHidden.type = 'hidden';
                        orderHidden.setAttribute('data-field', 'order-id');

                        let priceInput = null; // سيتم تعيينه لاحقًا

                        const orderButtons = [];
                        orders.forEach(function(o) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className =
                                'w-full text-right px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800';
                            const label = '#' + o.id + ' - ' + (o.title || '') + (o.customer ? ' (' + o
                                .customer + ')' : '');
                            btn.textContent = label;
                            btn.dataset.id = String(o.id);
                            btn.dataset.search = (label).toLowerCase();
                            btn.dataset.budget = o.budget != null ? String(o.budget) : '';
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                orderHidden.value = btn.dataset.id || '';
                                orderInput.value = label;
                                const budgetVal = Number(btn.dataset.budget || '0');
                                if (!Number.isNaN(budgetVal) && budgetVal > 0 && priceInput && !
                                    priceInput.value) {
                                    priceInput.value = String(budgetVal);
                                }
                                closeOrderList();
                            });
                            orderList.appendChild(btn);
                            orderButtons.push(btn);
                        });

                        function openOrderList() {
                            if (orderList.classList.contains('hidden')) {
                                orderList.classList.remove('hidden');
                            }
                        }

                        function closeOrderList() {
                            if (!orderList.classList.contains('hidden')) {
                                orderList.classList.add('hidden');
                            }
                        }

                        orderInput.addEventListener('focus', openOrderList);
                        orderInput.addEventListener('click', openOrderList);
                        orderInput.addEventListener('input', function() {
                            const q = (orderInput.value || '').trim().toLowerCase();
                            orderButtons.forEach(function(btn) {
                                const hay = btn.dataset.search || '';
                                btn.style.display = !q || hay.indexOf(q) !== -1 ? '' : 'none';
                            });
                            openOrderList();
                        });

                        document.addEventListener('click', function(e) {
                            if (!orderDropdown.contains(e.target)) {
                                closeOrderList();
                            }
                        });

                        orderDropdown.appendChild(orderInput);
                        orderDropdown.appendChild(orderList);

                        orderCol.appendChild(orderLabel);
                        orderCol.appendChild(orderDropdown);
                        orderCol.appendChild(orderHidden);

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'px-2 py-1 rounded-lg bg-red-500 text-white text-xs';
                        removeBtn.textContent = '×';
                        removeBtn.addEventListener('click', function() {
                            row.remove();
                        });

                        top.appendChild(orderCol);
                        top.appendChild(removeBtn);

                        const bottom = document.createElement('div');
                        bottom.className = 'flex flex-col md:flex-row gap-2';

                        const supplierCol = document.createElement('div');
                        supplierCol.className = 'flex-1';

                        const supplierLabel = document.createElement('label');
                        supplierLabel.className = 'block text-sm mb-1 text-gray-600 dark:text-gray-300';
                        supplierLabel.textContent = 'المورد';

                        const supplierDropdown = document.createElement('div');
                        supplierDropdown.className = 'relative';

                        const supplierInput = document.createElement('input');
                        supplierInput.type = 'text';
                        supplierInput.placeholder = 'اختر مورداً أو ابحث بالاسم...';
                        supplierInput.className =
                            'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200';
                        supplierInput.autocomplete = 'off';

                        const supplierList = document.createElement('div');
                        supplierList.className =
                            'absolute z-30 mt-1 w-full max-h-56 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg hidden';

                        const supplierHidden = document.createElement('input');
                        supplierHidden.type = 'hidden';
                        supplierHidden.setAttribute('data-field', 'supplier-id');

                        const supplierButtons = [];
                        suppliers.forEach(function(s) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className =
                                'w-full text-right px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800';
                            const label = ((s.type === 'vendor') ? 'Trady' : (s.name || '')) + (s.type && s
                                .type !== 'vendor' ? ' (' + s.type + ')' : '');
                            btn.textContent = label;
                            btn.dataset.id = String(s.id);
                            btn.dataset.search = (s.name + ' ' + (s.type || '')).toLowerCase();
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                supplierHidden.value = btn.dataset.id || '';
                                supplierInput.value = label;
                                closeSupplierList();
                            });
                            supplierList.appendChild(btn);
                            supplierButtons.push(btn);
                        });

                        function openSupplierList() {
                            if (supplierList.classList.contains('hidden')) {
                                supplierList.classList.remove('hidden');
                            }
                        }

                        function closeSupplierList() {
                            if (!supplierList.classList.contains('hidden')) {
                                supplierList.classList.add('hidden');
                            }
                        }

                        supplierInput.addEventListener('focus', openSupplierList);
                        supplierInput.addEventListener('click', openSupplierList);
                        supplierInput.addEventListener('input', function() {
                            const q = (supplierInput.value || '').trim().toLowerCase();
                            supplierButtons.forEach(function(btn) {
                                const hay = btn.dataset.search || '';
                                btn.style.display = !q || hay.indexOf(q) !== -1 ? '' : 'none';
                            });
                            openSupplierList();
                        });

                        document.addEventListener('click', function(e) {
                            if (!supplierDropdown.contains(e.target)) {
                                closeSupplierList();
                            }
                        });

                        supplierDropdown.appendChild(supplierInput);
                        supplierDropdown.appendChild(supplierList);

                        supplierCol.appendChild(supplierLabel);
                        supplierCol.appendChild(supplierDropdown);
                        supplierCol.appendChild(supplierHidden);

                        const priceCol = document.createElement('div');
                        priceCol.className = 'w-full md:w-40';

                        const priceLabel = document.createElement('label');
                        priceLabel.className = 'block text-sm mb-1 text-gray-600 dark:text-gray-300';
                        priceLabel.textContent = 'سعر الإسناد (ج.م)';

                        priceInput = document.createElement('input');
                        priceInput.type = 'number';
                        priceInput.step = '0.01';
                        priceInput.className =
                            'w-full p-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-800 dark:text-gray-200';
                        priceInput.setAttribute('data-field', 'price');

                        const priceHint = document.createElement('p');
                        priceHint.className = 'mt-1 text-[11px] text-gray-500 dark:text-gray-400';
                        priceHint.textContent = 'يمكنك ترك السعر فارغاً لاستخدام ميزانية العميل كسعر مبدئي.';

                        priceCol.appendChild(priceLabel);
                        priceCol.appendChild(priceInput);
                        priceCol.appendChild(priceHint);

                        bottom.appendChild(supplierCol);
                        bottom.appendChild(priceCol);

                        row.appendChild(top);
                        row.appendChild(bottom);

                        rowsWrapper.appendChild(row);
                    }

                    if (addRowBtn) {
                        addRowBtn.addEventListener('click', function() {
                            buildAssignRow();
                        });
                    }

                    // صف واحد مبدئي
                    buildAssignRow();

                    assignForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (!baseUrl) {
                            alert('حدث خطأ في الرابط، برجاء إعادة تحميل الصفحة.');
                            return;
                        }

                        const tokenInput = assignForm.querySelector('input[name="_token"]');
                        const token = tokenInput ? tokenInput.value : '';
                        if (!token) {
                            alert('تعذر العثور على رمز الحماية (CSRF).');
                            return;
                        }

                        const rows = Array.prototype.slice.call(rowsWrapper.querySelectorAll(
                            '[data-assign-row]'));
                        const payloads = [];
                        rows.forEach(function(row) {
                            const orderIdInput = row.querySelector('[data-field="order-id"]');
                            const supplierIdInput = row.querySelector('[data-field="supplier-id"]');
                            const priceInput = row.querySelector('[data-field="price"]');
                            const orderId = orderIdInput ? orderIdInput.value : '';
                            const supplierId = supplierIdInput ? supplierIdInput.value : '';
                            const priceVal = priceInput ? priceInput.value : '';

                            if (orderId && supplierId) {
                                payloads.push({
                                    orderId: orderId,
                                    supplierId: supplierId,
                                    price: priceVal
                                });
                            }
                        });

                        if (!payloads.length) {
                            alert('برجاء إضافة صف واحد على الأقل مع اختيار الطلب والمورد.');
                            return;
                        }

                        const submitBtn = assignForm.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'جاري الحفظ...';
                        }

                        (async function() {
                            for (const p of payloads) {
                                const url = baseUrl + '/' + encodeURIComponent(p.orderId) + '/assign';
                                const formData = new FormData();
                                formData.append('_token', token);
                                formData.append('supplier_id', p.supplierId);
                                if (p.price !== '') {
                                    formData.append('assigned_price', p.price);
                                }

                                await fetch(url, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                });
                            }

                            window.location.reload();
                        })().catch(function() {
                            alert('حدث خطأ أثناء حفظ الإسناد، برجاء المحاولة مرة أخرى.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.textContent = 'حفظ الإسناد';
                            }
                        });
                    });
                }

                // بحث فوري داخل جدول الطلبات الخاصة
                const searchInput = document.getElementById('special-orders-search');
                if (searchInput) {
                    const rows = Array.prototype.slice.call(document.querySelectorAll('[data-special-order-row]'));
                    const handler = function() {
                        const q = (searchInput.value || '').trim().toLowerCase();
                        rows.forEach(function(row) {
                            const hay = (row.getAttribute('data-search') || '').toLowerCase();
                            row.style.display = q === '' || hay.indexOf(q) !== -1 ? '' : 'none';
                        });
                    };

                    searchInput.addEventListener('input', handler);
                }
            });
        </script>
    </div>
</x-layouts.app>
