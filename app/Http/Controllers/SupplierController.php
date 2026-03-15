<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::latest()->paginate(10);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($suppliers);
        }

        $vendors = Supplier::where('type', 'vendor')
            ->latest()
            ->paginate(10, ['*'], 'vendors_page');

        $factories = Supplier::where('type', 'factory')
            ->latest()
            ->paginate(10, ['*'], 'factories_page');

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'vendors' => $vendors,
            'factories' => $factories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:factory,vendor',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:suppliers,email'],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'logo' => 'nullable|string',
            'website' => 'nullable|url',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'instagram' => 'nullable|string',
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $supplierData = $validated;
        unset($supplierData['password'], $supplierData['password_confirmation']);

        $supplier = new Supplier();
        $supplier->forceFill($supplierData);
        $supplier->commission_percent = $supplierData['commission_percent'] ?? 0;
        $supplier->created_by = (string) (Auth::id() ?? 0);
        $supplier->save();

        $user = User::where('email', $supplierData['email'])->first();
        if (!$user) {
            $user = new User();
            $user->email = $supplierData['email'];
        }

        $userPhone = $supplierData['phone'] ?? null;

        if ($userPhone) {
            $phoneQuery = User::where('phone', $userPhone);
            if ($user->id) {
                $phoneQuery->where('id', '!=', $user->id);
            }

            if ($phoneQuery->exists()) {
                $userPhone = null;
            }
        }

        if (!$userPhone) {
            $userPhone = 'SUP-' . $supplier->id . '-' . Str::upper(Str::random(6));

            while (User::where('phone', $userPhone)->exists()) {
                $userPhone = 'SUP-' . $supplier->id . '-' . Str::upper(Str::random(6));
            }
        }

        $user->name = $supplierData['name'];
        $user->phone = $user->phone ?: $userPhone;
        $user->address = $user->address ?: ($supplierData['address'] ?? '');
        $user->role = 'supplier';
        $user->password = Hash::make($validated['password']);
        $user->save();

        $supplier->user_id = $user->id;
        $supplier->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($supplier, 201);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'تم إضافة المورد بنجاح');
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:factory,vendor',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'email' => ['sometimes', 'required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:suppliers,email,' . $id],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'logo' => 'nullable|string',
            'website' => 'nullable|url',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'instagram' => 'nullable|string',
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $supplierData = $validated;
        unset($supplierData['password'], $supplierData['password_confirmation']);

        $supplier->forceFill($supplierData);
        $supplier->updated_by = (string) (Auth::id() ?? 0);
        $supplier->save();

        $email = $supplierData['email'] ?? $supplier->email;

        $user = $supplier->user;
        if (!$user || ($email && $user->email !== $email)) {
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                // Use existing user with this email
                $user = $existingUser;
                $user->email = $email;
            } else {
                // Only create a new user if a password was provided
                if (!empty($validated['password'])) {
                    $user = new User();
                    $user->email = $email;
                } else {
                    $user = null;
                }
            }
        }

        if ($user) {
            $userPhone = $supplierData['phone'] ?? $supplier->phone;

            if ($userPhone) {
                $phoneQuery = User::where('phone', $userPhone);
                if ($user->id) {
                    $phoneQuery->where('id', '!=', $user->id);
                }

                if ($phoneQuery->exists()) {
                    $userPhone = null;
                }
            }

            if (!$userPhone) {
                $userPhone = 'SUP-' . $supplier->id . '-' . Str::upper(Str::random(6));

                while (User::where('phone', $userPhone)->exists()) {
                    $userPhone = 'SUP-' . $supplier->id . '-' . Str::upper(Str::random(6));
                }
            }

            $user->name = $supplierData['name'] ?? $supplier->name;
            $user->phone = $user->phone ?: $userPhone;
            $user->address = $user->address ?: ($supplierData['address'] ?? $supplier->address ?? '');
            $user->role = 'supplier';

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
            $supplier->user_id = $user->id;
            $supplier->save();
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($supplier);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'تم تحديث المورد بنجاح');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Supplier deleted successfully']);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'تم حذف المورد بنجاح');
    }
}
