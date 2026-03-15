<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerHomeController extends Controller
{
    public function index(Request $request)
    {
        $driver = (string) DB::connection()->getDriverName();
        $legacyFilter = (string) $request->query('filter', '');
        $type = (string) $request->query('type', 'all');
        $sort = (string) $request->query('sort', 'recent');
        $factoryId = (int) $request->query('factory_id', 0);
        $supplierId = (int) $request->query('supplier_id', 0);

        if ($legacyFilter !== '') {
            if (in_array($legacyFilter, ['factory', 'supplier'], true)) {
                $type = $legacyFilter;
            } elseif (in_array($legacyFilter, ['all', 'recent', 'reviewed'], true)) {
                $sort = $legacyFilter;
            }
        }

        if (! in_array($type, ['all', 'factory', 'supplier'], true)) {
            $type = 'all';
        }
        if (! in_array($sort, ['all', 'recent', 'reviewed'], true)) {
            $sort = 'recent';
        }

        if ($factoryId < 1) {
            $factoryId = 0;
        }
        if ($supplierId < 1) {
            $supplierId = 0;
        }

        $bestSellerProductIds = OrderItem::query()
            ->whereHas('order', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('status', 'delivered')
                        ->orWhere('status', 'Delivered')
                        ->orWhere('status', 'تم التوصيل');
                });
            })
            ->select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(12)
            ->pluck('product_id');

        $bestSellers = collect();
        if ($bestSellerProductIds->isNotEmpty()) {
            $ids = $bestSellerProductIds->map(fn ($id) => (int) $id)->values()->all();

            $bestSellers = Product::with(['suppliers' => function ($q) {
                $q->select('suppliers.id', 'suppliers.name', 'suppliers.type')
                  ->withPivot('price', 'unit_price', 'quantity');
            }])
                ->with('pricingTiers')
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->whereIn('id', $ids)
                ->when($driver === 'mysql', function ($q) use ($ids) {
                    $orderBy = implode(',', $ids);

                    return $q->orderByRaw("FIELD(id, {$orderBy})");
                }, function ($q) use ($ids) {
                    $cases = [];
                    foreach ($ids as $i => $id) {
                        $cases[] = "WHEN {$id} THEN {$i}";
                    }
                    $caseSql = 'CASE id '.implode(' ', $cases).' ELSE '.count($ids).' END';

                    return $q->orderByRaw($caseSql);
                })
                ->get();
        }

        // Fallback: show latest products when no delivered orders exist
        if ($bestSellers->isEmpty()) {
            $bestSellers = Product::with(['suppliers' => function ($q) {
                $q->select('suppliers.id', 'suppliers.name', 'suppliers.type')
                  ->withPivot('price', 'unit_price', 'quantity');
            }])
                ->with('pricingTiers')
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->latest()
                ->limit(12)
                ->get();
        }

        $homeProductsQuery = Product::with(['suppliers' => function ($q) {
            $q->select('suppliers.id', 'suppliers.name', 'suppliers.type')
              ->withPivot('price', 'unit_price', 'quantity');
        }])
            ->with('pricingTiers')
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')

            ->when($type === 'factory', function ($q) {
                return $q->whereHas('suppliers', function ($qq) {
                    $qq->where('type', 'factory');
                });
            })
            ->when($type === 'supplier', function ($q) {
                return $q->whereHas('suppliers', function ($qq) {
                    $qq->where('type', 'vendor');
                });
            })

            ->when($factoryId > 0, function ($q) use ($factoryId) {
                return $q->whereHas('suppliers', function ($qq) use ($factoryId) {
                    $qq->where('suppliers.id', $factoryId)
                        ->where('type', 'factory');
                });
            })
            ->when($supplierId > 0, function ($q) use ($supplierId) {
                return $q->whereHas('suppliers', function ($qq) use ($supplierId) {
                    $qq->where('suppliers.id', $supplierId)
                        ->where('type', 'vendor');
                });
            })

            ->when($sort === 'reviewed', function ($q) {
                return $q
                    ->whereRaw(
                        '(SELECT AVG(rating) FROM product_ratings WHERE product_ratings.product_id = products.id) >= ? AND (SELECT AVG(rating) FROM product_ratings WHERE product_ratings.product_id = products.id) <= ?',
                        [3, 5]
                    )
                    ->orderByDesc('ratings_avg_rating')
                    ->orderByDesc('ratings_count')
                    ->orderByDesc('id');
            }, function ($q) {
                return $q->latest();
            });

        $limit = $sort === 'recent' ? 5 : 24;
        $homeProducts = $homeProductsQuery
            ->limit($limit)
            ->get();

        $categories = Category::query()
            ->orderBy('name_en')
            ->get(['id', 'name_ar', 'name_en', 'slug', 'icon', 'image', 'bg_color']);

        $ads = Advertisement::query()
            ->where('status', 'active')
            ->latest()
            ->get(['id', 'title', 'image', 'link', 'status']);

        $factories = Supplier::query()
            ->where('type', 'factory')
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        $suppliers = Supplier::query()
            ->where('type', 'vendor')
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return view('shop.home', [
            'bestSellers' => $bestSellers,
            'homeProducts' => $homeProducts,
            'homeFilter' => $sort,
            'homeType' => $type,
            'homeSort' => $sort,
            'factoryId' => $factoryId,
            'supplierId' => $supplierId,
            'factories' => $factories,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'ads' => $ads,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', 'all');
        if (! in_array($type, ['all', 'products', 'categories'], true)) {
            $type = 'all';
        }

        $products = collect();
        $categories = collect();

        if ($q !== '') {
            if ($type === 'all' || $type === 'products') {
                $products = Product::with(['suppliers' => function ($q2) {
                    $q2->select('suppliers.id', 'suppliers.name', 'suppliers.type');
                }])
                    ->where(function ($query) use ($q) {
                        $query->where('name', 'like', '%'.$q.'%')
                            ->orWhere('name_ar', 'like', '%'.$q.'%')
                            ->orWhere('name_en', 'like', '%'.$q.'%')
                            ->orWhere('sku', 'like', '%'.$q.'%');
                    })
                    ->latest()
                    ->limit(24)
                    ->get();
            }

            if ($type === 'all' || $type === 'categories') {
                $categories = Category::query()
                    ->where(function ($query) use ($q) {
                        $query->where('name_ar', 'like', '%'.$q.'%')
                            ->orWhere('name_en', 'like', '%'.$q.'%')
                            ->orWhere('slug', 'like', '%'.$q.'%');
                    })
                    ->orderBy('name_en')
                    ->limit(24)
                    ->get(['id', 'name_ar', 'name_en', 'slug', 'icon', 'image', 'bg_color']);
            }
        }

        return view('shop.search.index', [
            'q' => $q,
            'type' => $type,
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
