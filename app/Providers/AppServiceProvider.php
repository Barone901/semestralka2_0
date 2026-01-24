<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Policies\BannerPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\View\Components\Layouts\DefaultLayout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrácia Blade komponentov
        Blade::component('layouts.default-layout', DefaultLayout::class);

        // Registrácia policies
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Banner::class, BannerPolicy::class);

        // Super admin má prístup ku všetkému
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
