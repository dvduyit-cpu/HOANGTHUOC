@include('partials.section-banner', ['bannerClass' => 'contact-hero', 'imageKey' => 'contact_hero_image', 'eyebrowKey' => 'contact_block_1_vi', 'titleKey' => 'contact_title_vi'])
<section class="contact-page contact-page-modern">
    <div class="container contact-grid">
        <div class="contact-intro">
            <p data-content-keys="contact_body_vi">{{ $site['contact_body_'.$locale] ?? '' }}</p>
        </div>
        <div class="contact-copy contact-information-card">
            @include('partials.contact-details')
        </div>
        <div class="contact-form-wrap">
            <span class="eyebrow dark" data-content-keys="contact_form_eyebrow_vi">{{ $site['contact_form_eyebrow_vi'] }}</span>
            <h2 data-content-keys="contact_form_title_vi">{{ $site['contact_form_title_vi'] }}</h2>
            <p class="contact-form-description" data-content-keys="contact_form_body_vi">{{ $site['contact_form_body_vi'] }}</p>
            @include('partials.contact-form')
        </div>
    </div>
</section>
