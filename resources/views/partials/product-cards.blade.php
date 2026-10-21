@if($products->isNotEmpty())
<div class="product-slider {{ ($site['medical_products_layout'] ?? 'three') === 'single' ? 'product-slider--single' : 'product-slider--three' }}" data-product-slider role="region" aria-label="{{ $locale === 'en' ? 'Medical products' : 'Sản phẩm y khoa' }}">
<div class="product-slider-controls" hidden>
    <button type="button" data-slide-prev aria-label="{{ $locale === 'en' ? 'Previous products' : 'Sản phẩm trước' }}">←</button>
    <span data-slide-counter aria-live="polite" aria-atomic="true"></span>
    <button type="button" data-slide-next aria-label="{{ $locale === 'en' ? 'Next products' : 'Sản phẩm tiếp theo' }}">→</button>
</div>
<div class="team-products product-slider-track" tabindex="0" aria-label="{{ $locale === 'en' ? 'Swipe or use arrow keys to browse products' : 'Vuốt hoặc dùng phím mũi tên để xem sản phẩm' }}">
    @foreach($products as $product)
        @php($productName = $locale === 'en' && filled($product['name_en'] ?? '') ? $product['name_en'] : $product['name'])
        @php($productDescription = $locale === 'en' && filled($product['description_en'] ?? '') ? $product['description_en'] : ($product['description'] ?? ''))
        @php($slideImages = collect([$product['image_path'] ?? null, ...($product['gallery_paths'] ?? [])])->filter()->unique()->values())
        @foreach($slideImages->isEmpty() ? [null] : $slideImages as $slideImage)
        <article class="team-product-card">
            @if($slideImage)<a class="product-image-link" href="{{ asset('storage/'.$slideImage) }}" target="_blank" rel="noopener" aria-label="{{ $locale === 'en' ? 'View full product image: ' : 'Xem ảnh sản phẩm đầy đủ: ' }}{{ $productName }}"><img src="{{ asset('storage/'.$slideImage) }}" alt="{{ $productName }}" loading="lazy"></a><small class="product-image-hint">{{ $locale === 'en' ? 'Tap image to view full size ↗' : 'Chạm vào ảnh để xem đầy đủ ↗' }}</small>@endif
            <h3>{{ $productName }}</h3>
            @if(filled($productDescription))<p>{{ $productDescription }}</p>@endif
        </article>
        @endforeach
    @endforeach
</div>
</div>
@endif
