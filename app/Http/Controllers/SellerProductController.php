<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerProductController extends Controller
{
    protected function currentSupplier()
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $supplier = $user->supplier;
        if (! $supplier) {
            abort(403, 'Supplier profile not found for this user.');
        }

        return $supplier;
    }

    public function index(Request $request)
    {
        $supplier = $this->currentSupplier();

        $q = trim((string) $request->query('q', ''));

        $productsQuery = Product::with(['suppliers', 'category', 'pricingTiers'])
            ->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('suppliers.id', $supplier->id);
            })
            ->latest();

        if ($q !== '') {
            $productsQuery->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('name_ar', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        $products = $productsQuery->paginate(12);

        $categories = Category::query()
            ->orderBy('name_en')
            ->get(['id', 'name_ar', 'name_en']);

        return view('seller.products.index', [
            'products' => $products,
            'categories' => $categories,
            'supplier' => $supplier,
            'q' => $q,
        ]);
    }

    public function store(Request $request)
    {
        $supplier = $this->currentSupplier();

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'nullable|integer|exists:categories,id',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'color' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:255',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.min_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.price_per_unit' => 'nullable|numeric|min:0',
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

        $pricingTiersInput = $request->input('pricing_tiers', []);
        unset($validated['pricing_tiers']);

        if (! isset($validated['unit_price']) || $validated['unit_price'] === null || $validated['unit_price'] === '') {
            $validated['unit_price'] = (float) ($validated['price'] ?? 0);
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

        $product = new Product;
        $product->forceFill($validated);
        $product->slug = Str::slug($validated['name_en']).'-'.Str::random(5);
        $product->added_by = Auth::id() ?? 0;
        $product->save();

        $price = (float) ($validated['price'] ?? 0);
        $product->suppliers()->sync([
            (int) $supplier->id => [
                'price' => $price,
                'unit_price' => (float) ($validated['unit_price'] ?? $price),
                'quantity' => array_key_exists('quantity', $validated) ? (int) $validated['quantity'] : null,
            ],
        ]);

        $tiersToCreate = [];
        if (is_array($pricingTiersInput)) {
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
                $tiersToCreate[] = [
                    'product_id' => (int) $product->id,
                    'supplier_id' => (int) $supplier->id,
                    'min_quantity' => $min,
                    'max_quantity' => $max,
                    'price_per_unit' => $ppu,
                ];
            }
        }

        if (! empty($tiersToCreate)) {
            ProductPricingTier::query()
                ->where('product_id', $product->id)
                ->where('supplier_id', (int) $supplier->id)
                ->delete();
            ProductPricingTier::query()->insert($tiersToCreate);
        }

        return redirect()
            ->route('seller.products.index')
            ->with('status', 'تم إضافة المنتج بنجاح');
    }

    public function update(Request $request, $id)
    {
        $supplier = $this->currentSupplier();

        $product = Product::where('id', (int) $id)
            ->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('suppliers.id', $supplier->id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'category_id' => 'nullable|integer|exists:categories,id',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'color' => 'nullable|string|max:255',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string|max:255',
            'pricing_tiers' => 'nullable|array',
            'pricing_tiers.*.min_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'pricing_tiers.*.price_per_unit' => 'nullable|numeric|min:0',
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

        $pricingTiersInput = $request->input('pricing_tiers', []);
        unset($validated['pricing_tiers']);

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

        $newPrice = (float) ($validated['price'] ?? 0);

        // حدِّث سعر هذا المورد فقط في Pivot بدون لمس باقي الموردين
        $product->suppliers()->syncWithoutDetaching([
            (int) $supplier->id => [
                'price' => $newPrice,
                'unit_price' => (float) ($validated['unit_price'] ?? $newPrice),
                'quantity' => array_key_exists('quantity', $validated) ? (int) $validated['quantity'] : null,
            ],
        ]);

        $tiersToCreate = [];
        if (is_array($pricingTiersInput)) {
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
                $tiersToCreate[] = [
                    'product_id' => (int) $product->id,
                    'supplier_id' => (int) $supplier->id,
                    'min_quantity' => $min,
                    'max_quantity' => $max,
                    'price_per_unit' => $ppu,
                ];
            }
        }

        ProductPricingTier::query()
            ->where('product_id', $product->id)
            ->where('supplier_id', (int) $supplier->id)
            ->delete();
        if (! empty($tiersToCreate)) {
            ProductPricingTier::query()->insert($tiersToCreate);
        }

        return redirect()
            ->route('seller.products.index')
            ->with('status', 'تم تحديث المنتج بنجاح');
    }

    public function destroy($id)
    {
        $supplier = $this->currentSupplier();

        $product = Product::where('id', (int) $id)
            ->whereHas('suppliers', function ($query) use ($supplier) {
                $query->where('suppliers.id', $supplier->id);
            })
            ->firstOrFail();

        $product->suppliers()->detach($supplier->id);

        if ($product->suppliers()->count() === 0) {
            $product->delete();
        }

        return redirect()
            ->route('seller.products.index')
            ->with('status', 'تم حذف المنتج بنجاح');
    }
}
