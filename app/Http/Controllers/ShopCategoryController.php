<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopCategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with(['suppliers' => function ($q) {
            $q->select('suppliers.id', 'suppliers.name', 'suppliers.type');
        }])->where('category_id', $category->id)->latest()->get();

        return view('shop.category.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
