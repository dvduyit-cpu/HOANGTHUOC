@php
    $email = $site['company_email'] ?? 'phongnhansu@hoangthuocmedical.com';
    $phone = $site['company_phone'] ?? '';
    $address = $site['company_address_'.$locale] ?? '';
@endphp
<footer class="footer">
    <div class="container footer-grid">
        <div><a class="brand brand-logo footer-brand" href="{{ route('home') }}"><img class="company-logo" src="{{ !empty($site['site_logo']) ? asset('storage/'.$site['site_logo']) : asset('images/hoang-thuoc-logo.png') }}" alt="Hoang Thuoc Medical"></a><strong class="footer-brand-name">{{ $site['company_name'] }}</strong>@if(!empty($site['footer_description_'.$locale]))<p>{{ $site['footer_description_'.$locale] }}</p>@endif</div>
        <div>
            <strong>{{ __('Kết nối cùng chúng tôi') }}</strong>
            <div class="footer-contact-list">
                @if($email)<a class="footer-contact-row" href="mailto:{{ $email }}"><x-admin-icon name="mail" size="18" /><span>{{ $email }}</span></a>@endif
                @if($phone)<a class="footer-contact-row" href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"><x-admin-icon name="phone" size="18" /><span>{{ $phone }}</span></a>@endif
            </div>
            <div class="footer-contact-row footer-company"><x-admin-icon name="building" size="18" /><div>{{ $site['company_legal_name'] ?? '' }}@if(!empty($site['company_tax_id']))<br>{{ __('Mã số thuế:') }} {{ $site['company_tax_id'] }}@endif</div></div>
        </div>
        <div><strong>{{ __('Địa chỉ') }}</strong>
            @if($address)<a class="footer-contact-row footer-address" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($address) }}" target="_blank" rel="noopener noreferrer"><x-admin-icon name="pin" size="18" /><span>{{ $address }}<small class="footer-map-label">{{ __('Xem trên bản đồ ↗') }}</small></span></a>@endif
        </div>
    </div>
    <div class="container footer-bottom">© {{ date('Y') }} {{ $site['company_name'] ?? 'Hoang Thuoc Medical' }}. {{ __('Thông tin ứng viên được bảo mật.') }}<br>@include('partials.developer-credit')</div>
</footer>
