<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\SiteSetting;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'company_name' => 'Hoang Thuoc Medical', 'company_legal_name' => 'CÔNG TY CỔ PHẦN THƯƠNG MẠI VÀ DỊCH VỤ HOÀNG THUỘC', 'company_email' => 'phongnhansu@hoangthuocmedical.com', 'company_phone' => '0939 481 678', 'company_tax_id' => '1801823649',
            'company_address_vi' => '672/12 Mậu Thân, Phường Cái Khế, Thành Phố Cần Thơ, Việt Nam', 'company_address_en' => '672/12 Mau Than Street, Cai Khe Ward, Can Tho City, Vietnam',
            'footer_description_vi' => 'Kiến tạo môi trường làm việc nhân văn, nơi chuyên môn và sự tận tâm cùng phát triển.', 'footer_description_en' => 'Building a people-first workplace where expertise and care grow together.',
            'home_eyebrow_vi' => 'HOANG THUOC MEDICAL CAREERS', 'home_eyebrow_en' => 'HOANG THUOC MEDICAL CAREERS',
            'home_title_vi' => 'Cùng kiến tạo những giá trị chăm sóc bền vững', 'home_title_en' => 'Build a meaningful future in healthcare',
            'home_body_vi' => 'Hãy tìm một công việc có ý nghĩa và phát triển sự nghiệp của bạn cùng đội ngũ Hoang Thuoc Medical.', 'home_body_en' => 'Find meaningful work and grow your career with the Hoang Thuoc Medical team.',
            'home_intro_title_vi' => 'Nơi năng lực chuyên môn gặp gỡ sự tử tế và tinh thần đổi mới.', 'home_intro_title_en' => 'Where professional excellence meets care and innovation.',
            'home_intro_body_vi' => 'Chúng tôi tôn trọng sự khác biệt, nuôi dưỡng tinh thần học hỏi và trao quyền để mỗi thành viên tạo ra ảnh hưởng tích cực.', 'home_intro_body_en' => 'We respect differences, nurture learning and empower every member to create positive impact.',
            'culture_eyebrow_vi' => 'CUỘC SỐNG TẠI HOANG THUOC MEDICAL', 'culture_eyebrow_en' => 'LIFE AT HOANG THUOC MEDICAL',
            'culture_title_vi' => 'Chúng tôi tin rằng con người là trọng tâm.', 'culture_title_en' => 'We believe people are at the heart of everything.',
            'culture_body_vi' => 'Một hành trình nghề nghiệp tốt được tạo nên từ đội ngũ cùng chí hướng, sự tin cậy và những trải nghiệm đáng tự hào mỗi ngày.', 'culture_body_en' => 'A rewarding career journey is built through shared purpose, trust and meaningful experiences every day.',
            'about_eyebrow_vi' => 'VỀ HOANG THUOC MEDICAL', 'about_eyebrow_en' => 'ABOUT HOANG THUOC MEDICAL',
            'about_title_vi' => 'Chăm sóc sức khỏe bằng cả chuyên môn và trái tim.', 'about_title_en' => 'Healthcare powered by expertise and compassion.',
            'about_body_vi' => 'Hoang Thuoc Medical hướng đến một hệ sinh thái y tế hiện đại, gần gũi và lấy con người làm trung tâm. Mỗi thành viên đóng góp bằng chuyên môn, tinh thần trách nhiệm và sự tận tâm với cộng đồng.',
            'about_body_en' => 'Hoang Thuoc Medical is building a modern, approachable healthcare ecosystem with people at its heart. Every team member contributes expertise, responsibility and care for the community.',
            'about_story_title_vi' => 'Trở thành người bạn đồng hành tin cậy trong hành trình chăm sóc sức khỏe.', 'about_story_title_en' => 'A trusted companion in every healthcare journey.',
            'contact_title_vi' => 'Liên hệ bộ phận tuyển dụng', 'contact_title_en' => 'Contact our recruitment team',
            'contact_body_vi' => 'Gửi câu hỏi cho chúng tôi. Bộ phận nhân sự sẽ phản hồi qua email hoặc điện thoại.', 'contact_body_en' => 'Send us your question. Our HR team will respond by email or phone.',
        ];
        $settings = array_replace($settings, config('medical-content'), config('english-content'));
        foreach ($settings as $key => $value) SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);

        News::firstOrCreate(['slug' => 'van-hoa-lay-con-nguoi-lam-trung-tam'], ['title_vi'=>'Hành trình xây dựng môi trường làm việc lấy con người làm trung tâm','title_en'=>'Building a people-first workplace','excerpt_vi'=>'Khám phá cách mỗi ý tưởng nhỏ được lắng nghe, nuôi dưỡng và trở thành giá trị lớn cho tập thể.','excerpt_en'=>'Discover how small ideas are heard, nurtured and grown into shared value.','content_vi'=>'Tại Hoang Thuoc Medical, chúng tôi tin rằng sự phát triển bền vững bắt đầu từ con người.','content_en'=>'At Hoang Thuoc Medical, we believe sustainable growth begins with people.','status'=>'published','published_at'=>now()]);
        News::firstOrCreate(['slug' => 'hoc-hoi-lien-tuc'], ['title_vi'=>'Học hỏi liên tục: nền tảng để mỗi cá nhân đi xa hơn','title_en'=>'Continuous learning helps every person go further','excerpt_vi'=>'Văn hóa học hỏi giúp đội ngũ không ngừng phát triển chuyên môn và kỹ năng.','excerpt_en'=>'A learning culture helps our team grow in expertise and capability.','content_vi'=>'Chúng tôi tạo điều kiện để mỗi thành viên học hỏi từ công việc, đồng nghiệp và các chương trình phát triển.','content_en'=>'We create opportunities for every member to learn from work, colleagues and development programs.','status'=>'published','published_at'=>now()->subDay()]);
        News::firstOrCreate(['slug' => 'hanh-trinh-cham-soc-suc-khoe'], ['title_vi'=>'Cùng tạo nên trải nghiệm chăm sóc sức khỏe tốt hơn mỗi ngày','title_en'=>'Creating better healthcare experiences every day','excerpt_vi'=>'Tinh thần hợp tác liên ngành giúp chúng tôi lắng nghe và cải tiến từng điểm chạm với khách hàng.','excerpt_en'=>'Cross-functional collaboration helps us listen and improve every customer touchpoint.','content_vi'=>'Mỗi cải tiến nhỏ đều bắt đầu bằng một cuộc trao đổi cởi mở và quyết tâm phục vụ tốt hơn.','content_en'=>'Every small improvement starts with an open conversation and a commitment to serve better.','status'=>'published','published_at'=>now()->subDays(3)]);

        $admin = User::query()->first();
        if ($admin) {
            $jobs = [
                ['slug'=>'chuyen-vien-tu-van-duoc','title'=>'Chuyên viên Tư vấn Dược','title_en'=>'Pharmaceutical Consultant','location'=>'Quận 3, TP. Hồ Chí Minh','location_en'=>'District 3, Ho Chi Minh City','employment_type'=>'full-time','salary'=>'12 – 18 triệu','salary_en'=>'VND 12 – 18 million','summary'=>'Tư vấn sản phẩm và đồng hành cùng khách hàng trên hành trình chăm sóc sức khỏe.','summary_en'=>'Advise on products and support customers throughout their healthcare journey.','description'=>'Tư vấn thông tin sản phẩm theo nhu cầu của khách hàng.\nPhối hợp với đội ngũ chuyên môn để nâng cao trải nghiệm dịch vụ.','description_en'=>'Provide product information tailored to customer needs.\nWork with clinical teams to improve service experiences.','requirements'=>'Tốt nghiệp Dược hoặc ngành liên quan.\nGiao tiếp rõ ràng, tận tâm với khách hàng.','requirements_en'=>'Degree in Pharmacy or a related field.\nClear communication and a customer-first mindset.','benefits'=>'Lương thưởng cạnh tranh, đào tạo chuyên môn và bảo hiểm theo quy định.','benefits_en'=>'Competitive compensation, professional training and statutory insurance.'],
                ['slug'=>'dieu-duong-phong-kham','title'=>'Điều dưỡng Phòng khám','title_en'=>'Clinic Nurse','location'=>'Thủ Đức, TP. Hồ Chí Minh','location_en'=>'Thu Duc City, Ho Chi Minh City','employment_type'=>'full-time','salary'=>'Thỏa thuận','salary_en'=>'Negotiable','summary'=>'Chăm sóc người bệnh bằng chuyên môn vững vàng và thái độ tận tâm.','summary_en'=>'Care for patients with strong expertise and genuine compassion.','description'=>'Hỗ trợ bác sĩ trong hoạt động khám và chăm sóc người bệnh.\nTheo dõi, hướng dẫn và ghi nhận thông tin điều dưỡng.','description_en'=>'Support physicians during consultations and patient care.\nMonitor, guide and document nursing information.','requirements'=>'Có chứng chỉ hành nghề điều dưỡng.\nCó tinh thần hợp tác và trách nhiệm.','requirements_en'=>'Valid nursing practice certificate.\nCollaborative and responsible mindset.','benefits'=>'Môi trường chuyên nghiệp, ca làm linh hoạt và cơ hội học hỏi liên tục.','benefits_en'=>'Professional environment, flexible shifts and continuous learning opportunities.'],
                ['slug'=>'nhan-vien-kinh-doanh-thiet-bi-y-te','title'=>'Nhân viên Kinh doanh Thiết bị Y tế','title_en'=>'Medical Equipment Sales Executive','location'=>'Bình Thạnh, TP. Hồ Chí Minh','location_en'=>'Binh Thanh District, Ho Chi Minh City','employment_type'=>'full-time','salary'=>'15 – 25 triệu + thưởng','salary_en'=>'VND 15 – 25 million + bonus','summary'=>'Phát triển khách hàng và mang giải pháp thiết bị y tế phù hợp đến các đối tác.','summary_en'=>'Develop customer relationships and bring relevant medical equipment solutions to partners.','description'=>'Tìm hiểu nhu cầu, giới thiệu giải pháp và duy trì quan hệ đối tác.\nPhối hợp với đội ngũ kỹ thuật trong quá trình tư vấn.','description_en'=>'Understand needs, introduce solutions and maintain partner relationships.\nWork with technical teams throughout the advisory process.','requirements'=>'Có kinh nghiệm kinh doanh B2B là lợi thế.\nChủ động, yêu thích môi trường năng động.','requirements_en'=>'B2B sales experience is an advantage.\nProactive and comfortable in a dynamic environment.','benefits'=>'Thu nhập theo năng lực, thưởng hiệu quả và lộ trình phát triển rõ ràng.','benefits_en'=>'Performance-based income, incentives and a clear career path.'],
            ];
            foreach ($jobs as $job) {
                Job::firstOrCreate(['slug' => $job['slug']], $job + ['status'=>'published','published_at'=>now(),'created_by'=>$admin->id,'contact_email'=>'phongnhansu@hoangthuocmedical.com','expires_at'=>now()->addMonths(3)]);
            }
        }
    }
}
