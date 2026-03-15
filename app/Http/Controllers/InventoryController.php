<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventory = Inventory::with('product')->latest()->paginate(10);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($inventory);
        }

        return view('inventory.index', [
            'inventory' => $inventory,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'unit' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'code' => 'required|string|max:255|unique:inventories,code',
            'serial' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $inventory = new Inventory();
        $inventory->forceFill($validated);
        $inventory->save();

        return response()->json($inventory, 201);
    }

    public function show($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json($inventory);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer',
            'unit' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'code' => 'sometimes|string|max:255|unique:inventories,code,' . $id,
            'serial' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:255',
        ]);

        $inventory->forceFill($validated);
        $inventory->save();

        return response()->json($inventory);
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json(['message' => 'Inventory item deleted successfully']);
    }

    public function print($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $inventory]);
    }

    public function pdf($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $inventory]);
    }

    public function excel($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $inventory]);
    }

    public function csv($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $inventory]);
    }
}
