<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->orderBy('name_en')->paginate(12);

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $base = trim(($validated['name_en'] ?? '') ?: ($validated['name_ar'] ?? ''));
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = Str::random(10);
        }

        $candidate = $slug;
        $i = 2;
        while (Category::where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $i;
            $i++;
        }
        $validated['slug'] = $candidate;

        Category::create($validated);

        return back()->with('status', 'تم إضافة الفئة بنجاح');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if (!empty($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $base = trim(($validated['name_en'] ?? '') ?: ($validated['name_ar'] ?? ''));
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = Str::random(10);
        }

        $candidate = $slug;
        $i = 2;
        while (Category::where('slug', $candidate)->where('id', '!=', $category->id)->exists()) {
            $candidate = $slug . '-' . $i;
            $i++;
        }
        $validated['slug'] = $candidate;

        $category->update($validated);

        return back()->with('status', 'تم تعديل الفئة بنجاح');
    }

    public function destroy(Category $category)
    {
        if (!empty($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return back()->with('status', 'تم حذف الفئة بنجاح');
    }
}
