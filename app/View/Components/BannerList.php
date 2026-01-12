<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BannerList extends Component
{
    public function __construct(
        public array $banners = [],
        public string $position = 'home'
    ) {}

    public function render()
    {
        return view('components.banner-list');
    }
}
