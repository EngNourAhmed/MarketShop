<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Ramsey\Collection\Collection;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('permissionItems:id,key,name')->latest()->paginate(10);
        $permissions = Permission::orderBy('name')->get(['id', 'key', 'name']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(UserResponse::collection($users), 200);
        }

        return view('users.index', [
            'users' => $users,
            'permissions' => $permissions,
        ]);
    }

    // public function test()
    // {
    //     return response()->json(['ok' => true]);
    // }

    // public function index()
    // {
    //     $users = User::all();
    //     // return response()->json(UserResponse::collection(collect([$users])));

    //     return response()->json(UserResponse::collection($users), 200);
    // }

    // public function register(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'     => 'required|string|max:255',
    //         'email'    => 'required|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //         'phone'    => 'nullable|string|max:20|unique:users',
    //     ]);

    //     $validated['password'] = Hash::make($validated['password']);

    //     $user = User::create($validated);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Registered successfully',
    //         'token'   => $token,
    //         'user'    => $user
    //     ], 201);
    // }



    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required|string|min:6'
    //     ]);

    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         throw ValidationException::withMessages([
    //             'email' => ['Invalid credentials.']
    //         ]);
    //     }

    //     $user = $request->user();

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'success' => true,
    //         'token'   => $token,
    //         'user'    => $user
    //     ]);
    // }

    // public function logout(Request $request)
    // {
    //     $user = $request->user();
    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
    //     }

    //     $token = $user->currentAccessToken();
    //     if ($token) {
    //         $token->delete();
    //     }

    //     return response()->json(['success' => true]);
    // }

    // public function refresh(Request $request)
    // {
    //     $user = $request->user();
    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
    //     }

    //     $currentToken = $user->currentAccessToken();
    //     $newToken = $user->createToken('auth_token')->plainTextToken;

    //     if ($currentToken) {
    //         $currentToken->delete();
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'token' => $newToken,
    //         'user' => $user,
    //     ]);
    // }

    // public function userRegister(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //         'phone' => 'nullable|string|max:20|unique:users',
    //         'address' => 'nullable|string|max:255',
    //         'image' => 'nullable|string',
    //         'gender' => 'nullable|string',
    //         'birthday' => 'nullable|string',
    //         'role' => 'nullable|string',
    //     ]);

    //     $user = new User();
    //     $user->forceFill($validated);
    //     $user->password = Hash::make($request->password);
    //     $user->save();

    //     return response()->json($user, 201);
    // }

    // public function show($id)
    // {
    //     $user = User::findOrFail($id);
    //     return response()->json($user);
    // }

    // public function update(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     $validated = $request->validate([
    //         'name' => 'sometimes|required|string|max:255',
    //         'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
    //         'password' => 'sometimes|nullable|string|min:8',
    //         'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
    //         'address' => 'nullable|string|max:255',
    //         'image' => 'nullable|string',
    //         'gender' => 'nullable|string',
    //         'birthday' => 'nullable|string',
    //         'role' => 'nullable|string',
    //     ]);

    //     if (isset($validated['password'])) {
    //         $validated['password'] = Hash::make($validated['password']);
    //     }

    //     $user->forceFill($validated);
    //     $user->save();

    //     return response()->json($user);
    // }

    // public function destroy($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();

    //     return response()->json(['message' => 'User deleted successfully']);
    // }

    // public function print($id)
    // {
    //     $user = User::findOrFail($id);
    //     return response()->json(['message' => 'Print view not implemented', 'data' => $user]);
    // }

    // public function pdf($id)
    // {
    //     $user = User::findOrFail($id);
    //     return response()->json(['message' => 'PDF generation not implemented', 'data' => $user]);
    // }

    // public function excel($id)
    // {
    //     $user = User::findOrFail($id);
    //     return response()->json(['message' => 'Excel export not implemented', 'data' => $user]);
    // }

    // public function csv($id)
    // {
    //     $user = User::findOrFail($id);
    //     return response()->json(['message' => 'CSV export not implemented', 'data' => $user]);
    // }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone'    => 'required|string|max:20|unique:users,phone',
            'address'  => 'nullable|string|max:255',
            'image'    => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $validated['role'] = 'customer';

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/'],
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $user = $request->user();

        if ($user) {
            $hasAnyPermission = $user->permissionItems()->exists();
            $desiredRole = $hasAnyPermission ? 'admin' : 'customer';
            if (($user->role ?? null) !== $desiredRole) {
                $user->forceFill(['role' => $desiredRole])->save();
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    
}
