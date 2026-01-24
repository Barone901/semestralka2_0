<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Collection;

class BannerService
{
    /**
     * Získa aktívne bannery.
     */
    public function getActiveBanners(): Collection
    {
        return Banner::active()
            ->ordered()
            ->with('page')
            ->get();
    }
}

