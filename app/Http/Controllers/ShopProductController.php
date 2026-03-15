<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductComment;
use App\Models\ProductLike;
use App\Models\ProductRating;
use App\Models\ProductShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopProductController extends Controller
{
    public function show(Request $request, $id)
    {
        $product = Product::with([
            'category:id,name_ar,name_en,slug',
            'comments' => function ($q) {
                $q->with(['user:id,name'])->orderBy('created_at', 'desc');
            },
            'pricingTiers',
            'suppliers' => function ($q) {
                $q->select('suppliers.id', 'suppliers.name', 'suppliers.type', 'suppliers.logo', 'suppliers.factory_short_details', 'suppliers.factory_long_details');
            },
        ])->findOrFail($id);

        $recent = (array) $request->session()->get('recently_viewed_products', []);
        $recent = array_values(array_filter(array_map('intval', $recent)));
        $recent = array_values(array_filter($recent, fn ($pid) => $pid !== (int) $product->id));
        array_unshift($recent, (int) $product->id);
        $recent = array_slice($recent, 0, 12);
        $request->session()->put('recently_viewed_products', $recent);

        $supplierType = $request->query('type');
        if (! in_array($supplierType, ['factory', 'vendor'], true)) {
            $supplierType = null;
        }

        $similarProducts = collect();
        if (! empty($product->category_id)) {
            $similarProducts = Product::with(['pricingTiers', 'suppliers' => function ($q) {
                $q->select('suppliers.id', 'suppliers.name', 'suppliers.type', 'suppliers.logo');
            }])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->latest()
                ->limit(8)
                ->get();
        }

        $ordersCount = (int) (OrderItem::query()
            ->where('product_id', $product->id)
            ->sum('quantity') ?? 0);

        $likesCount = (int) ProductLike::query()->where('product_id', $product->id)->count();
        $commentsCount = (int) ProductComment::query()->where('product_id', $product->id)->count();
        $sharesCount = (int) ProductShare::query()->where('product_id', $product->id)->count();

        $reactionCounts = ProductLike::query()
            ->where('product_id', $product->id)
            ->selectRaw('reaction, COUNT(*) as aggregate')
            ->groupBy('reaction')
            ->pluck('aggregate', 'reaction')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $avgRating = (float) (ProductRating::query()->where('product_id', $product->id)->avg('rating') ?? 0);
        $ratingsCount = (int) ProductRating::query()->where('product_id', $product->id)->count();

        $ratingsBreakdownRaw = ProductRating::query()
            ->where('product_id', $product->id)
            ->selectRaw('rating, COUNT(*) as aggregate')
            ->groupBy('rating')
            ->pluck('aggregate', 'rating')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $ratingsBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingsBreakdown[$i] = (int) ($ratingsBreakdownRaw[$i] ?? 0);
        }

        $topReactions = collect($reactionCounts)
            ->sortDesc()
            ->keys()
            ->take(3)
            ->values()
            ->toArray();

        $userReaction = null;
        $userRating = null;
        if (Auth::check()) {
            $userReaction = ProductLike::query()
                ->where('product_id', $product->id)
                ->where('user_id', Auth::id())
                ->value('reaction');

            $userRating = ProductRating::query()
                ->where('product_id', $product->id)
                ->where('user_id', Auth::id())
                ->value('rating');
        }

        $recentComments = ProductComment::query()
            ->with(['user:id,name'])
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->get();

        // Prepare Gallery Items
        $galleryItems = collect();
        if ($product->image) {
            $galleryItems->push($product->image);
        }
        if (!empty($product->images) && is_array($product->images)) {
            $galleryItems = $galleryItems->merge($product->images);
        }
        $galleryItems = $galleryItems->unique()->filter();

        return view('shop.product.show', [
            'product' => $product,
            'galleryItems' => $galleryItems,
            'similarProducts' => $similarProducts,
            'supplierType' => $supplierType,
            'ordersCount' => $ordersCount,
            'avgRating' => $avgRating,
            'ratingsCount' => $ratingsCount,
            'ratingsBreakdown' => $ratingsBreakdown,
            'userRating' => $userRating,
            'userReaction' => $userReaction,
            'likesCount' => $likesCount,
            'commentsCount' => $commentsCount,
            'sharesCount' => $sharesCount,
            'reactionCounts' => $reactionCounts,
            'topReactions' => $topReactions,
            'recentComments' => $recentComments,
        ]);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to rate products.');
        }

        $product = Product::findOrFail($id);
        
        ProductRating::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $request->rating,
                'review' => $request->comment,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Thank you for your rating!');
    }
}
