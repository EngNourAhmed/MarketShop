<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * جلب كل الصلاحيات
     */
    public function index(Request $request)
    {
        return response()->json([
            'data' => Permission::orderBy('name')->get()
        ]);
    }

    public function allUsersWithPermissions(Request $request)
    {
        $users = User::with('permissionItems:id,key,name')
            ->get(['id', 'name', 'email', 'phone', 'created_at', 'updated_at']);

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'permissions' => $user->permissionItems->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'key' => $permission->key,
                            'name' => $permission->name,
                            'pivot' => $permission->pivot ? [
                                'user_id' => $permission->pivot->user_id,
                                'permission_id' => $permission->pivot->permission_id
                            ] : null
                        ];
                    })->values()
                ];
            })
        ]);
    }

    /**
     * صلاحيات المستخدم الحالي (بعد تسجيل الدخول)
     */
    public function myPermissions(Request $request)
    {
        $user = $request->user()->load('permissionItems');
        return response()->json([
            'permissions' => $user->permissionItems ? $user->permissionItems->pluck('key') : []
        ]);
    }

    /**
     * إنشاء مستخدم جديد مع تحديد الصلاحيات
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users,phone',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:users,email'],
            'password' => 'required|confirmed|min:8',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,key'
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone ?? null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', // Default role
        ]);

        $permissionKeys = $request->input('permissions', []);
        $permissionIds = Permission::whereIn('key', $permissionKeys)->pluck('id')->all();
        $user->permissionItems()->sync($permissionIds);

        // Explicitly set role based on permissions and save
        $newRole = !empty($permissionIds) ? 'admin' : 'customer';
        $user->forceFill(['role' => $newRole])->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'تم إنشاء المستخدم بنجاح',
                'user' => $user->load('permissionItems:id,key,name')
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('status', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * حذف مستخدم معين مع صلاحياته
     */
    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->permissionItems()->detach();
        $user->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'message' => 'تم حذف المستخدم بنجاح'
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('status', 'تم حذف المستخدم بنجاح');
    }

    /**
     * تحديث مستخدم وصلاحياته
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email:rfc',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|confirmed|min:8',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,key',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->filled('password')
                ? Hash::make($request->password)
                : $user->password,
        ]);

        $permissionKeys = $request->input('permissions', []);
        $permissionIds = Permission::whereIn('key', $permissionKeys)->pluck('id')->all();
        $user->permissionItems()->sync($permissionIds);

        $newRole = !empty($permissionIds) ? 'admin' : 'customer';
        if (($user->role ?? null) !== $newRole) {
            $user->forceFill(['role' => $newRole])->save();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'تم تحديث المستخدم بنجاح',
                'user' => $user->load('permissionItems:id,key,name')
            ]);
        }

        return redirect()
            ->route('users.index')
            ->with('status', 'تم تحديث المستخدم بنجاح');
    }
}
