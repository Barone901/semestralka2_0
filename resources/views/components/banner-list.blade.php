<div class="banner-list">
    @foreach($banners as $banner)
        <a href="{{ $banner->link_url ?? '#' }}">
            <img src="{{ asset('storage/' . $banner->image_path) }}"
                 alt="{{ $banner->title ?? 'Banner' }}">
        </a>
    @endforeach
</div>
