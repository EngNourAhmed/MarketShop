<?php

namespace App\Http\Controllers;

use App\Models\SpecialOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShopSpecialOrderController extends Controller
{
    public function create(Request $request)
    {
        return view('shop.special_orders.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'specs' => ['nullable', 'string', 'max:5000'],
            'reference_url' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'details' => ['nullable', 'string', 'max:5000'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images') as $file) {
                if (!$file) continue;
                $imagePaths[] = $file->store('special_orders', 'public');
            }
        }

        SpecialOrder::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'product_name' => $validated['product_name'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'color' => $validated['color'] ?? null,
            'size' => $validated['size'] ?? null,
            'material' => $validated['material'] ?? null,
            'specs' => $validated['specs'] ?? null,
            'reference_url' => $validated['reference_url'] ?? null,
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'details' => $validated['details'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('shop.special_orders.create')
            ->with('status', 'تم إرسال طلبك الخاص بنجاح، سنقوم بالتواصل معك قريبًا.');
    }
}
