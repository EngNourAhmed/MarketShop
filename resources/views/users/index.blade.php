<x-layouts.app :title="'المستخدمين'">
    <div id="users-page" class="page-content">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">المستخدمين</h1>

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

        @if(empty($permissions) || (is_countable($permissions) && count($permissions) === 0))
            <div class="mb-4 p-4 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-200">
                لا توجد صلاحيات في النظام. شغّل Seeders لإنشاء الصلاحيات (PermissionSeeder).
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-4">
                <form method="POST" action="{{ route('users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                        <input name="name" value="{{ old('name') }}" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بريد الكتروني</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                        <input name="phone" value="{{ old('phone') }}" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كلمة المرور</label>
                        <input type="password" name="password" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                    </div>

                    <div class="md:col-span-2">
                        <div class="text-sm mb-2 text-gray-600 dark:text-gray-300">الصلاحيات</div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach(($permissions ?? []) as $p)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                    <input type="checkbox" name="permissions[]" value="{{ $p->key }}" class="rounded border-gray-300" {{ in_array($p->key, old('permissions', []), true) ? 'checked' : '' }} />
                                    <span>{{ $p->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-500 text-white font-semibold hover:bg-rose-600">إضافة مستخدم</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-4 flex flex-col md:flex-row gap-3 items-center">
            <div class="relative flex-1 w-full">
                <input
                    type="text"
                    id="user-search"
                    placeholder="ابحث في المستخدمين..."
                    class="w-full p-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-rose-400"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">الكود</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">الاسم</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">بريد الكتروني</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">تليفون</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">الصلاحيه</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">الاجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="user-table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach(($users ?? []) as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="p-4 text-gray-800 dark:text-gray-200">#{{ $user->id }}</td>
                                <td class="p-4 text-gray-800 dark:text-gray-200 font-medium">{{ $user->name }}</td>
                                <td class="p-4 text-gray-800 dark:text-gray-200">{{ $user->email }}</td>
                                <td class="p-4 text-gray-800 dark:text-gray-200">{{ $user->phone }}</td>
                                <td class="p-4 text-gray-800 dark:text-gray-200">
                                    <div class="flex flex-wrap gap-2">
                                        @if(($user->role ?? null) === 'admin')
                                            <span class="px-2 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-700">Admin</span>
                                        @endif

                                        @if($user->permissionItems && $user->permissionItems->count() > 0)
                                            @foreach($user->permissionItems as $perm)
                                                <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $perm->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">بدون صلاحيات</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-gray-800 dark:text-gray-200 align-top">
                                    <div class="flex flex-col gap-2">
                                        <details>
                                            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40 [&::-webkit-details-marker]:hidden">تعديل</summary>
                                            <div class="fixed inset-0 z-40">
                                                <div class="absolute inset-0 bg-black/40" onclick="this.closest('details').removeAttribute('open')"></div>

                                                <div class="relative mx-auto my-6 w-[95vw] max-w-2xl">
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                                        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">تعديل المستخدم والصلاحيات</div>
                                                            <button type="button" class="px-2 py-1 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="this.closest('details').removeAttribute('open')">✕</button>
                                                        </div>

                                                        <div class="p-4 max-h-[75vh] overflow-auto">
                                                            <form method="POST" action="{{ route('users.permissions.update', $user->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="hidden" name="user_id" value="{{ $user->id }}" />

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">الاسم</label>
                                                        <input name="name" value="{{ $user->name }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">بريد الكتروني</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" placeholder="example@example.example" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="example@example.example" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تليفون</label>
                                                        <input name="phone" value="{{ $user->phone }}" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" required />
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">كلمة المرور (اختياري)</label>
                                                        <input type="password" name="password" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm mb-1 text-gray-600 dark:text-gray-300">تأكيد كلمة المرور</label>
                                                        <input type="password" name="password_confirmation" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200" />
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <div class="text-sm mb-2 text-gray-600 dark:text-gray-300">الصلاحيات</div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 p-2">
                                                            @foreach(($permissions ?? []) as $p)
                                                                <label class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-200 leading-5">
                                                                    <input type="checkbox" name="permissions[]" value="{{ $p->key }}" class="mt-1 rounded border-gray-300" {{ $user->permissionItems && $user->permissionItems->contains('key', $p->key) ? 'checked' : '' }} />
                                                                    <span class="break-words">{{ $p->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="md:col-span-2 flex justify-end">
                                                        <button type="submit" class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">حفظ</button>
                                                    </div>
                                                </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </details>

                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirm('حذف المستخدم؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                @if(method_exists(($users ?? null), 'links'))
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>