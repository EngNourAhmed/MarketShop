<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier) {
            abort(403, 'Supplier profile not found for this user.');
        }

        return view('seller.settings.index', [
            'user' => $user,
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $supplier = $user->supplier;
        if (!$supplier) {
            abort(403, 'Supplier profile not found for this user.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,strict|regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'factory_short_details' => 'nullable|string',
            'factory_long_details' => 'nullable|string',
        ]);

        $supplierData = $validated;

        $supplier->forceFill([
            'name' => $supplierData['name'],
            'email' => $supplierData['email'],
            'phone' => $supplierData['phone'] ?? $supplier->phone,
            'address' => $supplierData['address'] ?? $supplier->address,
            'city' => $supplierData['city'] ?? $supplier->city,
            'state' => $supplierData['state'] ?? $supplier->state,
            'country' => $supplierData['country'] ?? $supplier->country,
            'zip_code' => $supplierData['zip_code'] ?? $supplier->zip_code,
            'logo' => $supplierData['logo'] ?? $supplier->logo,
            'website' => $supplierData['website'] ?? $supplier->website,
            'facebook' => $supplierData['facebook'] ?? $supplier->facebook,
            'twitter' => $supplierData['twitter'] ?? $supplier->twitter,
            'instagram' => $supplierData['instagram'] ?? $supplier->instagram,
            'factory_short_details' => $supplierData['factory_short_details'] ?? $supplier->factory_short_details,
            'factory_long_details' => $supplierData['factory_long_details'] ?? $supplier->factory_long_details,
        ]);
        $supplier->save();

        $user->forceFill([
            'name' => $supplierData['name'],
            'email' => $supplierData['email'],
            'phone' => $supplierData['phone'] ?? $user->phone,
            'address' => $supplierData['address'] ?? $user->address,
        ])->save();

        return redirect()
            ->route('seller.settings.edit')
            ->with('status', 'تم تحديث إعدادات المتجر بنجاح');
    }
}
