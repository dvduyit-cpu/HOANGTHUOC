<?php

return [
    'trang-chu' => ['root' => '.home-premium', 'blocks' => [
        'banner' => ['name' => 'Banner đầu trang', 'selector' => '.hp-hero', 'keys' => ['home_hero_', 'home_eyebrow_', 'home_title_', 'home_body_', 'home_block_1_']],
        'opportunities' => ['name' => 'Thông báo tuyển dụng', 'selector' => '.hp-opportunity', 'keys' => []],
        'values' => ['name' => 'Chuẩn mực y khoa', 'selector' => '#cung-dong-hanh', 'keys' => ['home_intro_', 'home_value_', 'home_process_image', 'home_standards_', 'home_block_2_', 'home_block_3_', 'home_block_4_', 'home_block_5_', 'home_block_6_', 'home_block_7_', 'home_block_8_']],
        'culture' => ['name' => 'Con người & văn hóa', 'selector' => '.hp-culture', 'keys' => ['culture_', 'home_block_9_', 'home_block_10_', 'home_block_11_', 'home_block_12_']],
        'process' => ['name' => 'Hành trình ứng tuyển', 'selector' => '.hp-process', 'keys' => ['home_block_13_', 'home_block_14_', 'home_block_15_', 'home_block_16_', 'home_block_17_', 'home_block_18_', 'home_block_19_', 'home_block_20_', 'home_block_21_']],
        'news' => ['name' => 'Câu chuyện & cập nhật', 'selector' => '.hp-updates', 'keys' => ['home_updates_', 'home_block_22_', 'home_block_23_']],
        'call-to-action' => ['name' => 'Lời mời đồng hành', 'selector' => '.hp-final', 'keys' => ['home_block_24_', 'home_block_25_', 'home_block_26_']],
    ]],
    'gioi-thieu' => ['root' => 'main', 'blocks' => [
        'banner' => ['name' => 'Banner giới thiệu', 'selector' => '.page-hero-about', 'keys' => ['about_hero_', 'about_eyebrow_', 'about_title_', 'about_block_1_']],
        'story' => ['name' => 'Câu chuyện về chúng tôi', 'selector' => '.about-story', 'keys' => ['about_story_', 'about_body_', 'about_block_2_', 'about_block_3_']],
        'values' => ['name' => 'Giá trị cốt lõi', 'selector' => '.values', 'keys' => ['about_block_4_', 'about_block_5_', 'about_block_6_', 'about_block_7_', 'about_block_8_', 'about_block_9_', 'about_block_10_', 'about_block_11_', 'about_block_12_', 'about_block_13_']],
        'team' => ['name' => 'Lời mời gặp gỡ đội ngũ', 'selector' => '.team-invite', 'keys' => ['about_team_', 'about_block_14_', 'about_block_15_']],
        'products' => ['name' => 'Giải pháp y khoa', 'selector' => '.about-products', 'keys' => ['medical_products_', 'team_block_10_', 'team_block_11_', 'team_block_12_']],
    ]],
    'doi-ngu' => ['root' => 'main', 'blocks' => [
        'banner' => ['name' => 'Banner đội ngũ', 'selector' => '.page-hero', 'keys' => ['team_block_1_', 'team_block_2_']],
        'story' => ['name' => 'Niềm tin & sứ mệnh', 'selector' => '.team-story', 'keys' => ['team_portrait_', 'team_block_3_', 'team_block_4_', 'team_block_5_', 'team_block_6_', 'team_block_7_']],
        'people' => ['name' => 'Con người Hoàng Thuộc', 'selector' => '.team-group', 'keys' => ['team_group_']],
        'journey' => ['name' => 'Hành trình phát triển', 'selector' => '.journey', 'keys' => ['team_stage_', 'team_block_8_', 'team_block_9_']],
    ]],
    'tuyen-dung' => ['root' => 'main', 'blocks' => [
        'banner' => ['name' => 'Banner tuyển dụng', 'selector' => '.careers-hero', 'keys' => ['careers_hero_', 'careers_eyebrow_', 'careers_block_1_']],
        'intro' => ['name' => 'Lời giới thiệu', 'selector' => '.careers-intro', 'keys' => ['careers_block_2_', 'careers_block_3_']],
        'jobs' => ['name' => 'Danh sách việc làm', 'selector' => '.positions-section', 'keys' => ['careers_block_4_', 'careers_block_5_']],
    ]],
    'tin-tuc' => ['root' => 'main', 'blocks' => [
        'banner' => ['name' => 'Banner tin tức', 'selector' => '.news-hero', 'keys' => ['news_hero_', 'news_block_1_', 'news_block_2_']],
        'intro' => ['name' => 'Lời giới thiệu', 'selector' => '.news-intro', 'keys' => ['news_block_3_']],
        'news' => ['name' => 'Danh sách bản tin', 'selector' => '.news-editorial-grid', 'keys' => []],
    ]],
    'lien-he' => ['root' => 'main', 'blocks' => [
        'banner' => ['name' => 'Banner liên hệ', 'selector' => '.contact-hero', 'keys' => ['contact_hero_', 'contact_title_', 'contact_block_1_']],
        'contact' => ['name' => 'Thông tin & form liên hệ', 'selector' => '.contact-page', 'keys' => ['contact_body_', 'contact_block_2_', 'contact_form_']],
    ]],
];
