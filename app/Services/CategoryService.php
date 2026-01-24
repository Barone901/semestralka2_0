<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Získa všetky hlavné kategórie s deťmi.
     */
    public function getParentCategories(): Collection
    {
        return Category::parents()
            ->ordered()
            ->with(['children' => fn ($q) => $q->ordered()])
            ->get();
    }

    /**
     * Získa kategóriu so všetkými ID (vrátane detí).
     */
    public function getCategoryWithChildrenIds(Category $category): array
    {
        return $category->getAllCategoryIds();
    }
}

