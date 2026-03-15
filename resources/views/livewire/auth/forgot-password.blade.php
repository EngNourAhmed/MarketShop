<x-layouts.auth>
    <div class="w-full max-w-md">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur rounded-2xl shadow-2xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">نسيت كلمة المرور</h1>
                    <p class="text-sm text-gray-500 mt-1">اكتب بريدك الإلكتروني لإرسال كود التحقق، ثم ادخل الكود وكلمة المرور الجديدة.</p>
                </div>

                <label for="dark-mode-toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="dark-mode-toggle" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-400"
                    ></div>
                </label>
            </div>

            @php($otpEmail = old('email', session('otp_email')))

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4 mb-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ $otpEmail }}"
                        placeholder="example@example.example"
                        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="example@example.example"
                        required
                        autofocus
                        class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full p-3 bg-gradient-to-r from-rose-500 to-orange-400 text-white rounded-lg font-semibold hover:opacity-95 transition-opacity"
                >
                    إرسال كود التحقق
                </button>
            </form>

            @if ($otpEmail)
                <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-4 mt-2">
                    @csrf
                    <input type="hidden" name="email" value="{{ $otpEmail }}" />

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">كود التحقق (OTP)</label>
                        <input
                            type="text"
                            name="otp"
                            inputmode="numeric"
                            pattern="[0-9]{4,6}"
                            placeholder="مثال: 123456"
                            required
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">كلمة المرور الجديدة</label>
                        <input
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">تأكيد كلمة المرور الجديدة</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            class="w-full p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full p-3 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 transition-colors"
                    >
                        تأكيد الكود وتعيين كلمة المرور
                    </button>
                </form>
            @endif

            <div class="mt-6 text-sm text-gray-600 dark:text-gray-300">
                <a href="{{ route('login') }}" class="font-semibold underline text-rose-600">الرجوع لتسجيل الدخول</a>
            </div>
        </div>
    </div>
</x-layouts.auth>
