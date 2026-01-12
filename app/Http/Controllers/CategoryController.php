<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        // Ak klikneš na parent kategóriu, zobraz aj produkty z jej detí
        $categoryIds = [$category->id, ...$category->children()->pluck('id')->all()];

        $products = Product::with('category')
            ->whereIn('category_id', $categoryIds)
            ->latest()
            ->paginate(12);

        return view('pages.home', compact('categories', 'products', 'category'));
    }
}
