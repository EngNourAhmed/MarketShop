<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComment;
use App\Models\ProductLike;
use App\Models\ProductShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductInteractionController extends Controller
{
    public function like(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reaction' => 'nullable|string|max:20',
        ]);

        $reaction = (string) ($validated['reaction'] ?? 'like');
        $allowed = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
        if (!in_array($reaction, $allowed, true)) {
            $reaction = 'like';
        }

        $userId = Auth::id();
        $existing = ProductLike::query()
            ->where('product_id', $product->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ((string) ($existing->reaction ?? 'like') === $reaction) {
                $existing->delete();
                $userReaction = null;
            } else {
                $existing->reaction = $reaction;
                $existing->save();
                $userReaction = $reaction;
            }
        } else {
            ProductLike::query()->create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'reaction' => $reaction,
            ]);
            $userReaction = $reaction;
        }

        $likesCount = ProductLike::query()->where('product_id', $product->id)->count();

        $reactionCounts = ProductLike::query()
            ->where('product_id', $product->id)
            ->selectRaw('reaction, COUNT(*) as aggregate')
            ->groupBy('reaction')
            ->pluck('aggregate', 'reaction')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $topReactions = collect($reactionCounts)
            ->sortDesc()
            ->keys()
            ->take(3)
            ->values()
            ->toArray();

        return response()->json([
            'likes_count' => $likesCount,
            'user_reaction' => $userReaction,
            'reaction_counts' => $reactionCounts,
            'top_reactions' => $topReactions,
        ]);
    }

    public function comment(Request $request, Product $product)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $text = (string) ($validated['body'] ?? '');

        $comment = ProductComment::query()->create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'body' => $text,
        ]);

        $comment->load('user:id,name');

        $commentsCount = ProductComment::query()->where('product_id', $product->id)->count();

        return response()->json([
            'comments_count' => $commentsCount,
            'comment' => [
                'id' => $comment->id,
                'body' => (string) ($comment->body ?? ''),
                'user_name' => (string) ($comment->user->name ?? ''),
                'created_at' => optional($comment->created_at)->toIso8601String(),
            ],
        ], 201);
    }

    public function share(Request $request, Product $product)
    {
        ProductShare::query()->create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
        ]);

        $sharesCount = ProductShare::query()->where('product_id', $product->id)->count();

        return response()->json([
            'shares_count' => $sharesCount,
        ]);
    }
}
