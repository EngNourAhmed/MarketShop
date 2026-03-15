<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRatingController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $rating = (int) $validated['rating'];

        ProductRating::query()->updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $userId,
            ],
            [
                'rating' => $rating,
            ]
        );

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

        return response()->json([
            'avg_rating' => $avgRating,
            'ratings_count' => $ratingsCount,
            'user_rating' => $rating,
            'ratings_breakdown' => $ratingsBreakdown,
        ]);
    }
}
