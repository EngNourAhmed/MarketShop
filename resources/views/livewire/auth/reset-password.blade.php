<x-layouts.auth>
    <div class="w-full max-w-md">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إعادة تعيين كلمة المرور</h1>
                    <p class="text-sm text-gray-500 mt-1">أدخل كلمة المرور الجديدة.</p>
                </div>

                <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"
                    ></div>
                </label>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ request('email') }}"
                        placeholder="example@example.example"
                        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="example@example.example"
                        required
                        autocomplete="email"
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">كلمة المرور الجديدة</label>
                    <input
                        type="password"
                        name="password"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                    />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">تأكيد كلمة المرور</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        minlength="8"
                        required
                        autocomplete="new-password"
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full p-3 bg-gradient-to-r from-rose-500 to-orange-400 text-white rounded-lg font-semibold hover:opacity-95 transition-opacity"
                >
                    حفظ كلمة المرور
                </button>
            </form>
        </div>
    </div>
</x-layouts.auth>
