@if($products->isNotEmpty())
<div class="team-products">
    @foreach($products as $product)
        @php($productName = $locale === 'en' && filled($product['name_en'] ?? '') ? $product['name_en'] : $product['name'])
        @php($productDescription = $locale === 'en' && filled($product['description_en'] ?? '') ? $product['description_en'] : ($product['description'] ?? ''))
        <article class="team-product-card">
            @if(!empty($product['image_path']))<a class="product-image-link" href="{{ asset('storage/'.$product['image_path']) }}" target="_blank" rel="noopener" aria-label="{{ $locale === 'en' ? 'View full product image: ' : 'Xem ảnh sản phẩm đầy đủ: ' }}{{ $productName }}"><img src="{{ asset('storage/'.$product['image_path']) }}" alt="{{ $productName }}" loading="lazy"></a><small class="product-image-hint">{{ $locale === 'en' ? 'Tap image to view full size ↗' : 'Chạm vào ảnh để xem đầy đủ ↗' }}</small>@endif
            <h3>{{ $productName }}</h3>
            @if(filled($productDescription))<p>{{ $productDescription }}</p>@endif
        </article>
    @endforeach
</div>
@endif
