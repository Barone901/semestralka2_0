<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sluzba pre pracu s kategoriami produktov.
 */
class CategoryService
{
    /**
     * Ziska vsetky hlavne kategorie s detskymi kategoriami.
     */
    public function getParentCategories(): Collection
    {
        return Category::parents()
            ->ordered()
            ->with(['children' => fn ($q) => $q->ordered()])
            ->get();
    }

    /**
     * Ziska vsetky ID kategorie vratane jej potomkov.
     */
    public function getCategoryWithChildrenIds(Category $category): array
    {
        return $category->getAllCategoryIds();
    }
}
