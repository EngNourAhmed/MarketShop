<x-shop-layouts.app :title="'Account'">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 md:p-6 shadow">
        @if(session('status'))
            <div class="mb-4 p-3 rounded-xl bg-green-50 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-700 border border-red-200">
                <div class="font-semibold mb-1">حدثت أخطاء أثناء الحفظ</div>
                <ul class="list-disc ps-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden text-gray-700 dark:text-gray-200 font-bold">
                @if(!empty($user->image))
                    <img src="{{ asset('storage/' . $user->image) }}" alt="avatar" class="w-full h-full object-cover" />
                @else
                    {{ $user?->initials() ?? '' }}
                @endif
            </div>
            <div class="flex-1">
                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
            <form method="POST" action="{{ route('shop.account.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf

                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Name</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                </div>

                <div>
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Phone</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                </div>

                <div>
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Gender</label>
                    <input name="gender" value="{{ old('gender', $user->gender) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Address</label>
                    <input name="address" value="{{ old('address', $user->address) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                </div>

                <div>
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Birthday</label>
                    <input name="birthday" value="{{ old('birthday', $user->birthday) }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                </div>

                <div>
                    <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">Profile Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-500 text-white font-semibold hover:bg-rose-600">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Save
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    Logout
                </button>
            </form>
            <a href="{{ route('customer.home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                Back to Home
            </a>
        </div>
    </div>
</x-shop-layouts.app>
