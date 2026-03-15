<x-layouts.auth>
    <div class="w-full max-w-md">
        <div
            class="bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إنشاء حساب</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">أنشئ حسابك ثم سجّل دخولك للوصول إلى لوحة الإدارة.</p>
                </div>

                <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400">
                    </div>
                </label>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">الاسم</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">البريد
                        الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="example@example.example" required
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}" required
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">رقم الهاتف</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400" />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">كلمة المرور</label>
                    <div class="relative">
                        <input
                            id="register-password"
                            type="password"
                            name="password"
                            minlength="8"
                            required
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 pr-10" />
                        <button
                            type="button"
                            id="toggle-register-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">الحد الأدنى 8 أحرف.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">تأكيد كلمة
                        المرور</label>
                    <div class="relative">
                        <input
                            id="register-password-confirmation"
                            type="password"
                            name="password_confirmation"
                            minlength="8"
                            required
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400 pr-10" />
                        <button
                            type="button"
                            id="toggle-register-password-confirmation"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full p-3 bg-gradient-to-r from-rose-500 to-orange-400 text-white rounded-lg font-semibold hover:opacity-95 transition-opacity">
                    إنشاء الحساب
                </button>
            </form>

            <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">
                لديك حساب؟
                <a href="{{ route('login') }}" class="font-semibold underline text-rose-600">تسجيل الدخول</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupToggle(inputId, buttonId) {
                var input = document.getElementById(inputId);
                var btn = document.getElementById(buttonId);
                if (!input || !btn) return;

                btn.addEventListener('click', function () {
                    var icon = btn.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (icon) {
                            icon.setAttribute('data-lucide', 'eye-off');
                        }
                    } else {
                        input.type = 'password';
                        if (icon) {
                            icon.setAttribute('data-lucide', 'eye');
                        }
                    }

                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }
                });
            }

            setupToggle('register-password', 'toggle-register-password');
            setupToggle('register-password-confirmation', 'toggle-register-password-confirmation');
        });
    </script>
</x-layouts.auth>
