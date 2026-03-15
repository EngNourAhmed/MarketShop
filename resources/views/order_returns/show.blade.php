<x-layouts.app :title="'تفاصيل طلب الإرجاع'">
    <div class="page-content space-y-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">تفاصيل طلب الإرجاع</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-1 space-y-4">
                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-lg font-bold mb-3 text-gray-800 dark:text-white">بيانات المستخدم</div>
                    @php($customer = $return->customer ?? $return->order?->customer)
                    @if($customer)
                        <div class="space-y-1 text-sm text-gray-700 dark:text-slate-200">
                            <div><span class="font-semibold">الاسم:</span> {{ $customer->name }}</div>
                            <div><span class="font-semibold">الهاتف:</span> {{ $customer->phone ?? '-' }}</div>
                            <div><span class="font-semibold">البريد:</span> {{ $customer->email ?? '-' }}</div>
                            <div><span class="font-semibold">العنوان:</span> {{ $customer->address ?? '-' }}</div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 dark:text-slate-300">لا يوجد بيانات للعميل.</div>
                    @endif
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-lg font-bold mb-3 text-gray-800 dark:text-white">بيانات الاوردر</div>
                    @if($return->order)
                        <div class="space-y-1 text-sm text-gray-700 dark:text-slate-200">
                            <div><span class="font-semibold">الكود:</span> {{ $return->order->order_code }}</div>
                            <div><span class="font-semibold">التاريخ:</span> {{ optional($return->order->created_at)->format('Y-m-d') ?? '-' }}</div>
                            <div><span class="font-semibold">الإجمالي:</span> {{ number_format((float) ($return->order->total ?? 0), 0, '.', ',') }} ج.م</div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500 dark:text-slate-300">لا يوجد بيانات للاوردر.</div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-lg font-bold text-gray-800 dark:text-white">تفاصيل طلب الإرجاع</div>
                        @php($status = $return->status)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($status === 'approved') bg-emerald-500/20 text-emerald-300
                            @elseif($status === 'rejected') bg-red-500/20 text-red-300
                            @else bg-amber-500/20 text-amber-300 @endif">
                            @if($status === 'approved')
                                مقبول
                            @elseif($status === 'rejected')
                                مرفوض
                            @else
                                قيد المراجعة
                            @endif
                        </span>
                    </div>

                    <div class="space-y-3 text-sm text-gray-700 dark:text-slate-200">
                        <div>
                            <div class="font-semibold mb-1">سبب الإرجاع</div>
                            <div class="leading-relaxed">{{ $return->reason }}</div>
                        </div>

                        @php($imgs = (array) ($return->images ?? []))
                        @if(!empty($imgs))
                            <div>
                                <div class="font-semibold mb-1">الصور المرفوعة</div>
                                <div class="grid grid-cols-3 gap-2 mt-1">
                                    @foreach($imgs as $img)
                                        <div class="h-20 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                                            <img src="{{ asset('storage/' . $img) }}" alt="return image" class="w-full h-full object-cover" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($return->admin_note))
                            <div>
                                <div class="font-semibold mb-1">رد الإدارة الحالي</div>
                                <div class="leading-relaxed">{{ $return->admin_note }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 p-4 shadow-sm dark:bg-slate-900 dark:border-slate-800">
                    <div class="text-lg font-bold mb-3 text-gray-800 dark:text-white">تحديث الحالة والرد على المستخدم</div>
                    <form method="POST" action="{{ route('order_returns.update', $return->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الحالة</label>
                            <select name="status" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                <option value="pending" {{ $return->status === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="approved" {{ $return->status === 'approved' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ $return->status === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">رسالة للمستخدم</label>
                            <textarea name="admin_note" rows="4" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('admin_note', $return->admin_note) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">حفظ التحديث</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
