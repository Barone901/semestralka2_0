<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        $products = Product::with('category')
            ->latest()
            ->paginate(12);

        return view('pages.home', compact('categories', 'products'));
    }
}
