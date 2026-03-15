<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingCompanyResponse;
use App\Models\ShippingCompany;

use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    public function index(Request $request)
    {

        //return response()->json('Shipping Company');
        $shippingCompanies = ShippingCompany::all();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($shippingCompanies);
        }

        return view('shipping.index', [
            'shippingCompanies' => $shippingCompanies,
        ]);
    }

    public function show($id)
    {
        $shippingCompany = \App\Models\ShippingCompany::find($id);
        return response()->json($shippingCompany);
    }

    public function store(Request $request)

    {

        //return response()->json($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:shipping_company,email'],
            'status' => 'required|string|in:active,inactive,pending,blocked,suspended',
            'code' => 'nullable|string|max:255',
        ]);


        $shippingCompany = ShippingCompany::create($validatedData);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($shippingCompany);
        }

        return redirect()
            ->route('shipping.index')
            ->with('status', 'تم إضافة شركة الشحن بنجاح');
    }

    public function update(Request $request, $id)
    {
        $shippingCompany = ShippingCompany::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => ['required', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'unique:shipping_company,email,' . $shippingCompany->id],
            'status' => 'required|string|in:active,inactive,pending,blocked,suspended',
            'code' => 'nullable|string|max:255',
        ]);

        $shippingCompany->update($validatedData);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($shippingCompany);
        }

        return redirect()
            ->route('shipping.index')
            ->with('status', 'تم تحديث شركة الشحن بنجاح');
    }

    // public function destroy($id)
    // {
    //     $shippingCompany = \App\Models\ShippingCompany::find($id);
    //     $shippingCompany->delete();
    //     return response()->json($shippingCompany);
    // }

    public function destroy($id)
    {
        $shippingCompany = ShippingCompany::findOrFail($id);
        $shippingCompany->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Shipping Company deleted successfully']);
        }

        return redirect()
            ->route('shipping.index')
            ->with('status', 'تم حذف شركة الشحن بنجاح');
    }
}
