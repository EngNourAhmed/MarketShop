<x-layouts.app :title="'إعدادات المتجر'">
    <div id="seller-settings-page" class="page-content max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">إعدادات المتجر</h1>

        @if(session('status'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                <div class="font-semibold mb-2">حدثت أخطاء أثناء الحفظ</div>
                <ul class="list-disc ps-6 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.settings.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">اسم المتجر</label>
                <input name="name" value="{{ old('name', $supplier->name) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email ?? $user->email) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">التليفون</label>
                <input name="phone" value="{{ old('phone', $supplier->phone ?? $user->phone) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">العنوان</label>
                <input name="address" value="{{ old('address', $supplier->address ?? $user->address) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المدينة</label>
                <input name="city" value="{{ old('city', $supplier->city) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">المحافظة / الولاية</label>
                <input name="state" value="{{ old('state', $supplier->state) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الدولة</label>
                <input name="country" value="{{ old('country', $supplier->country) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الرمز البريدي</label>
                <input name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">شعار المتجر (رابط صورة)</label>
                <input name="logo" value="{{ old('logo', $supplier->logo) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الموقع الإلكتروني</label>
                <input name="website" value="{{ old('website', $supplier->website) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">فيسبوك</label>
                <input name="facebook" value="{{ old('facebook', $supplier->facebook) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div>
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تويتر</label>
                <input name="twitter" value="{{ old('twitter', $supplier->twitter) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">إنستجرام</label>
                <input name="instagram" value="{{ old('instagram', $supplier->instagram) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تفاصيل المصنع (مختصر)</label>
                <textarea name="factory_short_details" rows="3" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('factory_short_details', $supplier->factory_short_details) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تفاصيل المصنع (تفصيلي)</label>
                <textarea name="factory_long_details" rows="5" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ old('factory_long_details', $supplier->factory_long_details) }}</textarea>
            </div>

            <div class="md:col-span-2 flex justify-end mt-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</x-layouts.app>
