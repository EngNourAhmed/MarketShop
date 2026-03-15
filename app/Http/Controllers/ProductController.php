<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResponse;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPricingTier;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['suppliers' => function ($q) {
            $q->select('suppliers.id', 'suppliers.name', 'suppliers.type');
        }, 'category:id,name_ar,name_en,slug', 'pricingTiers'])->latest()->paginate(10);
        $suppliers = Supplier::query()->orderBy('name')->get(['id', 'name', 'type']);
        $categories = Category::query()->orderBy('name_en')->get(['id', 'name_ar', 'name_en', 'slug', 'icon', 'image', 'bg_color']);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(ProductResponse::Collection($products));
        }

        return view('products.index', [
            'products' => $products,
            'suppliers' => $suppliers,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'nullable|integer|exists:categories,id',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:255',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:255',
            'supplier_prices' => 'nullable|array',
            'supplier_prices.*' => 'nullable|numeric|min:0',
            'supplier_unit_prices' => 'nullable|array',
            'supplier_unit_prices.*' => 'nullable|numeric|min:0',
            'supplier_quantities' => 'nullable|array',
            'supplier_quantities.*' => 'nullable|integer|min:0',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.min_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.price_per_unit' => 'nullable|numeric|min:0',
            'supplier_pricing_tiers' => 'nullable|array',
        ]);

        if (empty($validated['description']) && (! empty($validated['description_ar']) || ! empty($validated['description_en']))) {
            $validated['description'] = ! empty($validated['description_ar']) ? $validated['description_ar'] : $validated['description_en'];
        }

        $colors = array_values(array_filter(array_map('trim', (array) $request->input('colors', [])), fn ($v) => $v !== ''));
        if (empty($colors) && ! empty($validated['color'])) {
            $colors = [(string) $validated['color']];
        }
        $sizes = array_values(array_filter(array_map('trim', (array) $request->input('sizes', [])), fn ($v) => $v !== ''));
        if (empty($sizes) && ! empty($validated['size'])) {
            $sizes = [(string) $validated['size']];
        }

        $validated['colors'] = ! empty($colors) ? $colors : null;
        $validated['color'] = ! empty($colors) ? $colors[0] : ($validated['color'] ?? null);
        $validated['sizes'] = ! empty($sizes) ? $sizes : null;
        $validated['size'] = ! empty($sizes) ? $sizes[0] : ($validated['size'] ?? null);

        $supplierPricesInput = $request->input('supplier_prices', []);
        unset($validated['supplier_prices']);

        $supplierUnitPricesInput = $request->input('supplier_unit_prices', []);
        unset($validated['supplier_unit_prices']);

        $supplierQuantitiesInput = $request->input('supplier_quantities', []);
        unset($validated['supplier_quantities']);

        $pricingTiersInput = $request->input('pricing_tiers', []);
        unset($validated['pricing_tiers']);

        $supplierPricingTiersInput = $request->input('supplier_pricing_tiers', []);
        unset($validated['supplier_pricing_tiers']);

        $basePrice = $validated['price'] ?? null;
        if ($basePrice === null || $basePrice === '') {
            $numericPrices = [];
            if (is_array($supplierPricesInput) && ! empty($supplierPricesInput)) {
                $numericPrices = array_filter($supplierPricesInput, fn ($v) => $v !== null && $v !== '' && is_numeric($v));
            }
            if (empty($numericPrices) && is_array($supplierUnitPricesInput)) {
                $numericPrices = array_filter($supplierUnitPricesInput, fn ($v) => $v !== null && $v !== '' && is_numeric($v));
            }
            if (! empty($numericPrices)) {
                $basePrice = (float) min($numericPrices);
            }
        }

        $validated['price'] = $basePrice !== null && $basePrice !== '' ? (float) $basePrice : 0.0;

        if (! isset($validated['unit_price']) || $validated['unit_price'] === null || $validated['unit_price'] === '') {
            $validated['unit_price'] = (float) $validated['price'];
        }

        if (! isset($validated['quantity']) || $validated['quantity'] === null || $validated['quantity'] === '') {
            $qtySum = 0;
            if (is_array($supplierQuantitiesInput)) {
                foreach ($supplierQuantitiesInput as $v) {
                    if ($v !== null && $v !== '' && is_numeric($v)) {
                        $qtySum += (int) $v;
                    }
                }
            }
            $validated['quantity'] = $qtySum;
        }

        $storedImages = [];
        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images', []) as $file) {
                if (! $file) {
                    continue;
                }
                $storedImages[] = $file->store('products', 'public');
            }
        } elseif ($request->hasFile('image')) {
            $storedImages[] = $request->file('image')->store('products', 'public');
        }

        if (! empty($storedImages)) {
            $validated['images'] = $storedImages;
            $validated['image'] = $storedImages[0];
        }

        $validated['featured'] = (int) $request->boolean('featured');

        $validated['name'] = $validated['name_en'];

        if (! empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            $validated['category'] = $cat?->name_en;
        }

        // return response()->json(['message' => 'Product created successfully', 'data' => $validated]);
        $product = new Product;
        $product->forceFill($validated);
        $product->slug = Str::slug($validated['name_en']).'-'.Str::random(5);
        $product->added_by = Auth::id() ?? 0;
        $product->save();

        $syncData = [];
        $supplierIdsToProcess = array_unique(array_merge(
            array_keys(is_array($supplierPricesInput) ? $supplierPricesInput : []),
            array_keys(is_array($supplierUnitPricesInput) ? $supplierUnitPricesInput : [])
        ));
        foreach ($supplierIdsToProcess as $supplierId) {
            $price = $supplierPricesInput[$supplierId] ?? null;
            $unitPrice = $supplierUnitPricesInput[$supplierId] ?? null;
            $p = null;
            if ($price !== null && $price !== '' && is_numeric($price)) {
                $p = (float) $price;
            }
            $u = null;
            if ($unitPrice !== null && $unitPrice !== '' && is_numeric($unitPrice)) {
                $u = (float) $unitPrice;
            }
            if ($p === null && $u === null) {
                continue;
            }
            $sid = (int) $supplierId;
            $q = null;
            if (is_array($supplierQuantitiesInput) && array_key_exists($supplierId, $supplierQuantitiesInput) && $supplierQuantitiesInput[$supplierId] !== null && $supplierQuantitiesInput[$supplierId] !== '' && is_numeric($supplierQuantitiesInput[$supplierId])) {
                $q = (int) $supplierQuantitiesInput[$supplierId];
            }

            $syncData[$sid] = [
                'price' => $p ?? $u ?? 0,
                'unit_price' => $u ?? $p ?? 0,
                'quantity' => $q,
            ];
        }

        if (! empty($syncData)) {
            $validSupplierIds = Supplier::whereIn('id', array_keys($syncData))->pluck('id')->all();
            $syncData = array_intersect_key($syncData, array_flip($validSupplierIds));
            $product->suppliers()->sync($syncData);
        }

        // Save per-supplier pricing tiers
        $tiersToInsert = [];
        if (is_array($supplierPricingTiersInput)) {
            foreach ($supplierPricingTiersInput as $supplierId => $rows) {
                if (! is_array($rows)) {
                    continue;
                }
                $sid = (int) $supplierId;
                if ($sid < 1) {
                    continue;
                }
                foreach ($rows as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $min = isset($row['min_quantity']) ? (int) $row['min_quantity'] : 0;
                    $maxRaw = $row['max_quantity'] ?? null;
                    $max = ($maxRaw === null || $maxRaw === '') ? null : (int) $maxRaw;
                    $ppuRaw = $row['price_per_unit'] ?? null;
                    $ppu = ($ppuRaw === null || $ppuRaw === '') ? null : (float) $ppuRaw;

                    if ($min < 1 || $ppu === null || $ppu < 0) {
                        continue;
                    }
                    if ($max !== null && $max < $min) {
                        $max = null;
                    }
                    $tiersToInsert[] = [
                        'product_id' => (int) $product->id,
                        'supplier_id' => $sid,
                        'min_quantity' => $min,
                        'max_quantity' => $max,
                        'price_per_unit' => $ppu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        } elseif (is_array($pricingTiersInput) && ! empty($pricingTiersInput)) {
            // Legacy tiers: attach to the first supplier (if any)
            $firstSupplierId = ! empty($syncData) ? (int) array_key_first($syncData) : 0;
            if ($firstSupplierId > 0) {
                foreach ($pricingTiersInput as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $min = isset($row['min_quantity']) ? (int) $row['min_quantity'] : 0;
                    $maxRaw = $row['max_quantity'] ?? null;
                    $max = ($maxRaw === null || $maxRaw === '') ? null : (int) $maxRaw;
                    $ppuRaw = $row['price_per_unit'] ?? null;
                    $ppu = ($ppuRaw === null || $ppuRaw === '') ? null : (float) $ppuRaw;

                    if ($min < 1 || $ppu === null || $ppu < 0) {
                        continue;
                    }
                    if ($max !== null && $max < $min) {
                        $max = null;
                    }
                    $tiersToInsert[] = [
                        'product_id' => (int) $product->id,
                        'supplier_id' => $firstSupplierId,
                        'min_quantity' => $min,
                        'max_quantity' => $max,
                        'price_per_unit' => $ppu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (! empty($tiersToInsert)) {
            ProductPricingTier::query()->where('product_id', $product->id)->delete();
            ProductPricingTier::query()->insert($tiersToInsert);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(ProductResponse::collection(collect([$product])));
        }

        return redirect()
            ->route('products.index')
            ->with('status', 'تم إضافة المنتج بنجاح');

    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'nullable|numeric',
            'unit_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'sku' => 'required|string|unique:products,sku,'.$id,
            'category_id' => 'nullable|integer|exists:categories,id',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'supplier_prices' => 'nullable|array',
            'supplier_prices.*' => 'nullable|numeric|min:0',
            'supplier_unit_prices' => 'nullable|array',
            'supplier_unit_prices.*' => 'nullable|numeric|min:0',
            'supplier_quantities' => 'nullable|array',
            'supplier_quantities.*' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:255',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:255',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.min_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.price_per_unit' => 'nullable|numeric|min:0',
            'supplier_pricing_tiers' => 'nullable|array',
        ]);

        if (empty($validated['description']) && (! empty($validated['description_ar']) || ! empty($validated['description_en']))) {
            $validated['description'] = ! empty($validated['description_ar']) ? $validated['description_ar'] : $validated['description_en'];
        }

        $colors = array_values(array_filter(array_map('trim', (array) $request->input('colors', [])), fn ($v) => $v !== ''));
        if (empty($colors) && ! empty($validated['color'])) {
            $colors = [(string) $validated['color']];
        }
        $sizes = array_values(array_filter(array_map('trim', (array) $request->input('sizes', [])), fn ($v) => $v !== ''));
        if (empty($sizes) && ! empty($validated['size'])) {
            $sizes = [(string) $validated['size']];
        }

        $validated['colors'] = ! empty($colors) ? $colors : null;
        $validated['color'] = ! empty($colors) ? $colors[0] : ($validated['color'] ?? null);
        $validated['sizes'] = ! empty($sizes) ? $sizes : null;
        $validated['size'] = ! empty($sizes) ? $sizes[0] : ($validated['size'] ?? null);

        $supplierPricesInput = $request->input('supplier_prices', []);
        unset($validated['supplier_prices']);

        $supplierUnitPricesInput = $request->input('supplier_unit_prices', []);
        unset($validated['supplier_unit_prices']);

        $supplierQuantitiesInput = $request->input('supplier_quantities', []);
        unset($validated['supplier_quantities']);

        $pricingTiersInput = $request->input('pricing_tiers', []);
        unset($validated['pricing_tiers']);

        $supplierPricingTiersInput = $request->input('supplier_pricing_tiers', []);
        unset($validated['supplier_pricing_tiers']);

        $basePrice = $validated['price'] ?? null;
        if ($basePrice === null || $basePrice === '') {
            $numericPrices = [];
            if (is_array($supplierPricesInput) && ! empty($supplierPricesInput)) {
                $numericPrices = array_filter($supplierPricesInput, fn ($v) => $v !== null && $v !== '' && is_numeric($v));
            }
            if (empty($numericPrices) && is_array($supplierUnitPricesInput)) {
                $numericPrices = array_filter($supplierUnitPricesInput, fn ($v) => $v !== null && $v !== '' && is_numeric($v));
            }
            if (! empty($numericPrices)) {
                $basePrice = (float) min($numericPrices);
            }
        }

        if ($basePrice === null || $basePrice === '') {
            $basePrice = $product->price;
        }

        $validated['price'] = $basePrice !== null && $basePrice !== '' ? (float) $basePrice : (float) ($product->price ?? 0.0);

        if (! isset($validated['unit_price']) || $validated['unit_price'] === null || $validated['unit_price'] === '') {
            $validated['unit_price'] = (float) ($product->unit_price ?? $validated['price']);
        }

        if (! isset($validated['quantity']) || $validated['quantity'] === null || $validated['quantity'] === '') {
            $qtySum = 0;
            if (is_array($supplierQuantitiesInput)) {
                foreach ($supplierQuantitiesInput as $v) {
                    if ($v !== null && $v !== '' && is_numeric($v)) {
                        $qtySum += (int) $v;
                    }
                }
            }
            $validated['quantity'] = $qtySum > 0 ? $qtySum : (int) ($product->quantity ?? 0);
        }

        $storedImages = [];
        if ($request->hasFile('images')) {
            foreach ((array) $request->file('images', []) as $file) {
                if (! $file) {
                    continue;
                }
                $storedImages[] = $file->store('products', 'public');
            }
        } elseif ($request->hasFile('image')) {
            $storedImages[] = $request->file('image')->store('products', 'public');
        }

        if (! empty($storedImages)) {
            $validated['images'] = $storedImages;
            $validated['image'] = $storedImages[0];
        } else {
            unset($validated['image']);
            unset($validated['images']);
        }

        $validated['featured'] = (int) $request->boolean('featured');

        $validated['name'] = $validated['name_en'];

        if (! empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            $validated['category'] = $cat?->name_en;
        } else {
            $validated['category'] = null;
        }

        $product->forceFill($validated);

        $product->slug = Str::slug($validated['name_en']).'-'.Str::random(5);

        $product->updated_by = Auth::id() ?? 0;
        $product->save();

        $syncData = [];
        $supplierIdsToProcess = array_unique(array_merge(
            array_keys(is_array($supplierPricesInput) ? $supplierPricesInput : []),
            array_keys(is_array($supplierUnitPricesInput) ? $supplierUnitPricesInput : [])
        ));
        foreach ($supplierIdsToProcess as $supplierId) {
            $price = $supplierPricesInput[$supplierId] ?? null;
            $unitPrice = $supplierUnitPricesInput[$supplierId] ?? null;
            $p = null;
            if ($price !== null && $price !== '' && is_numeric($price)) {
                $p = (float) $price;
            }
            $u = null;
            if ($unitPrice !== null && $unitPrice !== '' && is_numeric($unitPrice)) {
                $u = (float) $unitPrice;
            }
            if ($p === null && $u === null) {
                continue;
            }
            $sid = (int) $supplierId;
            $q = null;
            if (is_array($supplierQuantitiesInput) && array_key_exists($supplierId, $supplierQuantitiesInput) && $supplierQuantitiesInput[$supplierId] !== null && $supplierQuantitiesInput[$supplierId] !== '' && is_numeric($supplierQuantitiesInput[$supplierId])) {
                $q = (int) $supplierQuantitiesInput[$supplierId];
            }

            $syncData[$sid] = [
                'price' => $p ?? $u ?? 0,
                'unit_price' => $u ?? $p ?? 0,
                'quantity' => $q,
            ];
        }

        if (! empty($syncData)) {
            $validSupplierIds = Supplier::whereIn('id', array_keys($syncData))->pluck('id')->all();
            $syncData = array_intersect_key($syncData, array_flip($validSupplierIds));
            $product->suppliers()->sync($syncData);
        }

        $tiersToInsert = [];
        if (is_array($supplierPricingTiersInput)) {
            foreach ($supplierPricingTiersInput as $supplierId => $rows) {
                if (! is_array($rows)) {
                    continue;
                }
                $sid = (int) $supplierId;
                if ($sid < 1) {
                    continue;
                }
                foreach ($rows as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $min = isset($row['min_quantity']) ? (int) $row['min_quantity'] : 0;
                    $maxRaw = $row['max_quantity'] ?? null;
                    $max = ($maxRaw === null || $maxRaw === '') ? null : (int) $maxRaw;
                    $ppuRaw = $row['price_per_unit'] ?? null;
                    $ppu = ($ppuRaw === null || $ppuRaw === '') ? null : (float) $ppuRaw;

                    if ($min < 1 || $ppu === null || $ppu < 0) {
                        continue;
                    }
                    if ($max !== null && $max < $min) {
                        $max = null;
                    }
                    $tiersToInsert[] = [
                        'product_id' => (int) $product->id,
                        'supplier_id' => $sid,
                        'min_quantity' => $min,
                        'max_quantity' => $max,
                        'price_per_unit' => $ppu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        } elseif (is_array($pricingTiersInput) && ! empty($pricingTiersInput)) {
            $firstSupplierId = ! empty($syncData) ? (int) array_key_first($syncData) : 0;
            if ($firstSupplierId > 0) {
                foreach ($pricingTiersInput as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $min = isset($row['min_quantity']) ? (int) $row['min_quantity'] : 0;
                    $maxRaw = $row['max_quantity'] ?? null;
                    $max = ($maxRaw === null || $maxRaw === '') ? null : (int) $maxRaw;
                    $ppuRaw = $row['price_per_unit'] ?? null;
                    $ppu = ($ppuRaw === null || $ppuRaw === '') ? null : (float) $ppuRaw;

                    if ($min < 1 || $ppu === null || $ppu < 0) {
                        continue;
                    }
                    if ($max !== null && $max < $min) {
                        $max = null;
                    }
                    $tiersToInsert[] = [
                        'product_id' => (int) $product->id,
                        'supplier_id' => $firstSupplierId,
                        'min_quantity' => $min,
                        'max_quantity' => $max,
                        'price_per_unit' => $ppu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        ProductPricingTier::query()->where('product_id', $product->id)->delete();
        if (! empty($tiersToInsert)) {
            ProductPricingTier::query()->insert($tiersToInsert);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($product);
        }

        return redirect()
            ->route('products.index')
            ->with('status', 'تم تحديث المنتج بنجاح');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Product deleted successfully']);
        }

        return redirect()
            ->route('products.index')
            ->with('status', 'تم حذف المنتج بنجاح');
    }

    public function print($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['message' => 'Print view not implemented', 'data' => $product]);
    }

    public function pdf($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['message' => 'PDF generation not implemented', 'data' => $product]);
    }

    public function excel($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['message' => 'Excel export not implemented', 'data' => $product]);
    }

    public function csv($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['message' => 'CSV export not implemented', 'data' => $product]);
    }
}
