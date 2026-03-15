<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingResponse;
use Illuminate\Http\Request;
use App\Models\Shipping;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = Shipping::with('order')->get();

        return response()->json($shippings);
        return response()->json(ShippingResponse::collection($shippings));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'company_id' => 'required|integer',
            'shipping_method_id' => 'required|string',
            'address' => 'required|string',
            'carrier' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $shipping = new Shipping();
        $shipping->forceFill($validated);
        $shipping->save();

        return response()->json($shipping, 201);
    }

    public function show($id)
    {
        $shipping = Shipping::with('order')->findOrFail($id);
        return response()->json($shipping);
    }

    public function update(Request $request, $id)
    {
        $shipping = Shipping::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|exists:orders,id',
            'company_id' => 'sometimes|integer',
            'shipping_method_id' => 'sometimes|string',
            'address' => 'sometimes|string',
            'carrier' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'status' => 'sometimes|string',
        ]);

        $shipping->forceFill($validated);
        $shipping->save();

        return response()->json($shipping);
    }

    public function destroy($id)
    {
        $shipping = Shipping::findOrFail($id);
        $shipping->delete();

        return response()->json(['message' => 'Shipping deleted successfully']);
    }
}
