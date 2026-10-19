<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::putMany([
            'company_name' => 'Hoang Thuoc Medical',
            'company_legal_name' => 'CÔNG TY CỔ PHẦN THƯƠNG MẠI VÀ DỊCH VỤ HOÀNG THUỘC',
            'company_email' => 'phongnhansu@hoangthuocmedical.com',
            'company_phone' => '0939 481 678',
            'company_tax_id' => '1801823649',
            'company_address_vi' => '672/12 Mậu Thân, Phường Cái Khế, Thành Phố Cần Thơ, Việt Nam',
            'company_address_en' => '672/12 Mau Than Street, Cai Khe Ward, Can Tho City, Vietnam',
        ]);
    }
}
