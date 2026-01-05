<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\Violation;
use App\Models\Regulation;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Bắt đầu tạo dữ liệu thực tế từ Trường Đại học Trà Vinh...');

        // 1. Tạo tài khoản sinh viên với MSSV theo cấu trúc
        $this->createStudents();

        // 2. Tạo các CLB thực tế từ TVU
        $this->createRealClubs();

        // 3. Tạo thành viên CLB
        $this->assignClubMembers();

        // 4. Tạo hoạt động thực tế
        $this->createRealActivities();

        // 5. Tạo nội quy chung
        $this->createRegulations();

        // 6. Tạo vi phạm mẫu
        $this->createSampleViolations();

        $this->command->info('✅ Hoàn thành tạo dữ liệu thực tế!');
    }

    /**
     * Tạo tài khoản sinh viên với MSSV theo cấu trúc: 11 01 YY NNN
     * 11: Mã trường Đại học Trà Vinh
     * 01: Hệ đào tạo Đại học chính quy
     * YY: Khóa tuyển sinh (20-25 tương ứng 2020-2025)
     * NNN: Số thứ tự (001-200)
     */
    private function createStudents()
    {
        $this->command->info('📚 Tạo tài khoản sinh viên...');

        $departments = [
            'Khoa Công nghệ Thông tin',
            'Khoa Kỹ thuật và Công nghệ',
            'Khoa Nông nghiệp và Thủy sản',
            'Khoa Kinh tế - Luật',
            'Khoa Ngoại ngữ',
            'Khoa Sư phạm',
            'Khoa Y Dược',
            'Khoa Khoa học Xã hội và Nhân văn'
        ];

        $classes = ['DH', 'CD', 'TC'];
        $genders = ['male', 'female'];
        $added = 0;

        // Tạo sinh viên từ khóa 20 đến 25 (2020-2025)
        for ($year = 20; $year <= 25; $year++) {
            $academicYear = 2000 + $year;
            $studentCount = $year <= 22 ? 150 : ($year <= 24 ? 100 : 50); // Khóa cũ nhiều hơn

            for ($num = 1; $num <= $studentCount; $num++) {
                $mssv = '11' . '01' . str_pad($year, 2, '0', STR_PAD_LEFT) . str_pad($num, 3, '0', STR_PAD_LEFT);
                
                // Kiểm tra xem đã tồn tại chưa
                $exists = User::where('student_code', $mssv)->exists();
                if ($exists) continue;

                $gender = $genders[array_rand($genders)];
                $firstName = $this->getRandomVietnameseName($gender);
                $lastName = $this->getRandomVietnameseLastName();
                $fullName = $lastName . ' ' . $firstName;

                // Tạo email theo format: MSSV@st.tvu.edu.vn
                $email = $mssv . '@st.tvu.edu.vn';

                User::create([
                    'name' => $fullName,
                    'student_code' => $mssv,
                    'email' => $email,
                    'password' => Hash::make('123456'), // Mật khẩu mặc định
                    'role_id' => 2, // Student
                    'status' => 1,
                    'gender' => $gender,
                    'department' => $departments[array_rand($departments)],
                    'class' => $classes[array_rand($classes)] . $year . rand(1, 5),
                    'date_of_birth' => Carbon::now()->subYears(rand(18, 23))->subMonths(rand(0, 11)),
                    'phone' => '0' . rand(3, 9) . rand(10000000, 99999999),
                    'created_at' => Carbon::create($academicYear, 9, 1)->addDays(rand(0, 30)),
                    'updated_at' => now(),
                ]);
                $added++;
            }
        }

        $this->command->info("✅ Đã tạo {$added} tài khoản sinh viên.");
    }

    /**
     * Tạo các CLB thực tế từ Trường Đại học Trà Vinh
     */
    private function createRealClubs()
    {
        $this->command->info('🏢 Tạo các Câu lạc bộ thực tế...');

        $clubs = [
            [
                'name' => 'CLB Hành trình sinh viên',
                'code' => 'CLB-HTSV',
                'field' => 'Tình nguyện',
                'club_type' => 'volunteer',
                'description' => 'CLB Hành trình sinh viên là nơi tập hợp các bạn sinh viên có tinh thần tình nguyện, tham gia các hoạt động xã hội, chiến dịch Mùa hè xanh, và các hoạt động cộng đồng.',
                'email' => 'clbhanhtrinhsinhvientvu@gmail.com',
                'phone' => '0294000001',
                'fanpage' => 'https://www.facebook.com/clbhanhtrinhsinhvientvu',
            ],
            [
                'name' => 'CLB Đờn ca tài tử',
                'code' => 'CLB-DCTT',
                'field' => 'Nghệ thuật',
                'club_type' => 'arts',
                'description' => 'CLB Đờn ca tài tử bảo tồn và phát triển loại hình nghệ thuật truyền thống Nam Bộ, tổ chức các buổi biểu diễn và giao lưu.',
                'email' => 'doncataitucailuongtvu@gmail.com',
                'phone' => '0295001880',
            ],
            [
                'name' => 'CLB Việc làm sinh viên TVU',
                'code' => 'CLB-VLTV',
                'field' => 'Kinh tế',
                'club_type' => 'academic',
                'description' => 'CLB hỗ trợ sinh viên tìm kiếm việc làm, kỹ năng phỏng vấn, viết CV và định hướng nghề nghiệp.',
                'phone' => '0985070884',
            ],
            [
                'name' => 'CLB Môi trường TVU',
                'code' => 'CLB-MT',
                'field' => 'Môi trường',
                'club_type' => 'volunteer',
                'description' => 'CLB Môi trường hoạt động vì môi trường xanh, tổ chức các hoạt động bảo vệ môi trường, trồng cây, thu gom rác thải.',
                'email' => 'caulacbomoitruongtvu@gmail.com',
                'fanpage' => 'https://www.facebook.com/TVU.ENVIRONMENTALCLUB/',
            ],
            [
                'name' => 'CLB Vovinam TVU',
                'code' => 'CLB-VOVINAM',
                'field' => 'Thể thao',
                'club_type' => 'sports',
                'description' => 'CLB Vovinam rèn luyện võ thuật, sức khỏe và tinh thần thượng võ cho sinh viên.',
                'phone' => '0356305066',
            ],
            [
                'name' => 'CLB Taekwondo TVU',
                'code' => 'CLB-TKD',
                'field' => 'Thể thao',
                'club_type' => 'sports',
                'description' => 'CLB Taekwondo phát triển môn võ Taekwondo, tham gia các giải đấu và rèn luyện thể chất.',
                'phone' => '0907014543',
            ],
            [
                'name' => 'CLB Nghiên cứu Khoa học Sinh viên',
                'code' => 'CLB-NCKH',
                'field' => 'Khoa học',
                'club_type' => 'academic',
                'description' => 'CLB khuyến khích sinh viên tham gia nghiên cứu khoa học, tổ chức các hội thảo và cuộc thi nghiên cứu.',
                'phone' => '0982174485',
            ],
            [
                'name' => 'CLB One Health TVU',
                'code' => 'CLB-ONEHEALTH',
                'field' => 'Y tế',
                'club_type' => 'academic',
                'description' => 'CLB One Health tập trung vào sức khỏe con người, động vật và môi trường, tổ chức các hoạt động tư vấn sức khỏe.',
                'fanpage' => 'https://www.facebook.com/tvu.oh',
            ],
            [
                'name' => 'CLB Khởi nghiệp TVU',
                'code' => 'CLB-KHOINGHIEP',
                'field' => 'Kinh doanh',
                'club_type' => 'academic',
                'description' => 'CLB Khởi nghiệp hỗ trợ sinh viên phát triển ý tưởng khởi nghiệp, kết nối nhà đầu tư và mentor.',
                'email' => 'caulacbokhoinghieptvu@gmail.com',
                'phone' => '0392136845',
            ],
            [
                'name' => 'CLB Hiến máu Tình nguyện',
                'code' => 'CLB-HIENMAU',
                'field' => 'Y tế',
                'club_type' => 'volunteer',
                'description' => 'CLB tổ chức các đợt hiến máu tình nguyện, tuyên truyền về hiến máu cứu người.',
                'phone' => '0868485899',
            ],
            [
                'name' => 'CLB Tình nguyện Thanh niên TVU',
                'code' => 'CLB-TNTN',
                'field' => 'Tình nguyện',
                'club_type' => 'volunteer',
                'description' => 'CLB tổ chức các hoạt động tình nguyện, hỗ trợ cộng đồng, các chiến dịch xã hội.',
                'fanpage' => 'https://www.facebook.com/CLBTNTNTVU/',
            ],
            [
                'name' => 'CLB Tin học TVU',
                'code' => 'CLB-TINHOC',
                'field' => 'Công nghệ',
                'club_type' => 'academic',
                'description' => 'CLB Tin học phát triển kỹ năng lập trình, tổ chức các cuộc thi lập trình và workshop công nghệ.',
                'phone' => '0948728349',
            ],
            [
                'name' => 'CLB Sáng tạo TVU',
                'code' => 'CLB-SANGTAO',
                'field' => 'Sáng tạo',
                'club_type' => 'arts',
                'description' => 'CLB Sáng tạo khuyến khích sinh viên phát triển ý tưởng sáng tạo, đổi mới và khởi nghiệp.',
                'email' => 'clbsangtao2206@gmail.com',
                'fanpage' => 'https://www.facebook.com/CLB-Sáng-Tạo-108938357160687/',
            ],
            [
                'name' => 'CLB Sinh viên 5 tốt',
                'code' => 'CLB-SV5TOT',
                'field' => 'Học tập',
                'club_type' => 'academic',
                'description' => 'CLB tập hợp các sinh viên đạt danh hiệu 5 tốt, tổ chức các hoạt động học thuật và rèn luyện.',
                'phone' => '0948728349',
            ],
            [
                'name' => 'CLB Tiếng Anh Cộng đồng',
                'code' => 'CLB-TA',
                'field' => 'Ngoại ngữ',
                'club_type' => 'academic',
                'description' => 'CLB Tiếng Anh tạo môi trường giao tiếp tiếng Anh, tổ chức các buổi speaking club và workshop.',
                'email' => 'clbtakc.tvu@gmail.com',
                'phone' => '0347260992',
            ],
            [
                'name' => 'CLB English Speaking Club (ESC)',
                'code' => 'CLB-ESC',
                'field' => 'Ngoại ngữ',
                'club_type' => 'academic',
                'description' => 'CLB English Speaking Club nâng cao kỹ năng giao tiếp tiếng Anh cho sinh viên.',
                'phone' => '0339897979',
                'email' => 'khauhoanganh@st.tvu.edu.vn',
            ],
            [
                'name' => 'CLB Nghệ thuật Khmer',
                'code' => 'CLB-KHMER',
                'field' => 'Nghệ thuật',
                'club_type' => 'arts',
                'description' => 'CLB bảo tồn và phát triển nghệ thuật Khmer, tổ chức các buổi biểu diễn văn hóa.',
                'phone' => '01683209245',
            ],
            [
                'name' => 'CLB Kỹ năng sống',
                'code' => 'CLB-KNS',
                'field' => 'Kỹ năng',
                'club_type' => 'academic',
                'description' => 'CLB Kỹ năng sống trang bị các kỹ năng mềm, kỹ năng giao tiếp, làm việc nhóm cho sinh viên.',
                'phone' => '0868305349',
            ],
            [
                'name' => 'CLB Nghệ thuật Biểu diễn',
                'code' => 'CLB-NTBD',
                'field' => 'Nghệ thuật',
                'club_type' => 'arts',
                'description' => 'CLB Nghệ thuật Biểu diễn tổ chức các buổi biểu diễn văn nghệ, sân khấu và nghệ thuật.',
                'phone' => '0779891465',
                'fanpage' => 'https://www.facebook.com/groups/1470333643187362/',
            ],
            [
                'name' => 'CLB Truyền thông TVU',
                'code' => 'CLB-TT',
                'field' => 'Truyền thông',
                'club_type' => 'arts',
                'description' => 'CLB Truyền thông đào tạo kỹ năng truyền thông, quay phim, chụp ảnh và sản xuất nội dung.',
                'phone' => '09131046946',
                'fanpage' => 'https://www.facebook.com/truyenthongtvu/',
            ],
            [
                'name' => 'CLB Social Media TVU',
                'code' => 'CLB-SM',
                'field' => 'Truyền thông',
                'club_type' => 'arts',
                'description' => 'CLB Social Media quản lý các kênh truyền thông của trường, sản xuất nội dung video, TikTok.',
                'fanpage' => 'https://www.facebook.com/tvusmc',
                'youtube' => 'https://www.youtube.com/channel/UCexPR91TtxVBUxVa-a0R6YQ',
            ],
            [
                'name' => 'CLB Logistics và Thương mại điện tử',
                'code' => 'CLB-LOGISTICS',
                'field' => 'Kinh tế',
                'club_type' => 'academic',
                'description' => 'CLB Logistics và Thương mại điện tử phát triển kỹ năng trong lĩnh vực logistics và e-commerce.',
                'fanpage' => 'https://www.facebook.com/CLB-SV-Logistics-và-Thương-mại-điện-tử-TVU-100083270562262/',
            ],
            [
                'name' => 'CLB TVU Runner',
                'code' => 'CLB-RUNNER',
                'field' => 'Thể thao',
                'club_type' => 'sports',
                'description' => 'CLB TVU Runner khuyến khích chạy bộ, rèn luyện sức khỏe và tham gia các giải chạy.',
            ],
            [
                'name' => 'CLB Lập trình ITHUB',
                'code' => 'CLB-ITHUB',
                'field' => 'Công nghệ',
                'club_type' => 'academic',
                'description' => 'CLB Lập trình ITHUB phát triển kỹ năng lập trình, tổ chức các cuộc thi và workshop công nghệ.',
            ],
            [
                'name' => 'CLB Kinh doanh Online',
                'code' => 'CLB-KDONLINE',
                'field' => 'Kinh doanh',
                'club_type' => 'academic',
                'description' => 'CLB Kinh doanh Online đào tạo kỹ năng kinh doanh online, marketing và bán hàng trên mạng xã hội.',
            ],
        ];

        $added = 0;
        $users = User::where('role_id', 2)->get();

        foreach ($clubs as $clubData) {
            // Kiểm tra xem CLB đã tồn tại chưa (theo code hoặc slug)
            $slug = \Str::slug($clubData['name']);
            $exists = Club::where('code', $clubData['code'])
                ->orWhere('slug', $slug)
                ->exists();
            if ($exists) {
                $this->command->info("CLB {$clubData['name']} đã tồn tại, bỏ qua.");
                continue;
            }

            // Chọn ngẫu nhiên một sinh viên làm chủ nhiệm
            $owner = $users->random();

            Club::create([
                'name' => $clubData['name'],
                'code' => $clubData['code'],
                'slug' => $slug,
                'field' => $clubData['field'],
                'club_type' => $clubData['club_type'],
                'description' => $clubData['description'],
                'owner_id' => $owner->id,
                'status' => 'active',
                'email' => $clubData['email'] ?? null,
                'phone' => $clubData['phone'] ?? null,
                'fanpage' => $clubData['fanpage'] ?? null,
                'establishment_date' => Carbon::now()->subYears(rand(1, 10))->subMonths(rand(0, 11)),
                'approval_mode' => 'manual',
                'activity_approval_mode' => 'chairman',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo thành viên chủ nhiệm
            DB::table('club_members')->insert([
                'club_id' => Club::where('code', $clubData['code'])->first()->id,
                'user_id' => $owner->id,
                'position' => 'chairman',
                'status' => 'approved',
                'joined_date' => Carbon::now()->subYears(rand(1, 3)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $added++;
        }

        $this->command->info("✅ Đã tạo {$added} Câu lạc bộ thực tế.");
    }

    /**
     * Gán thành viên vào các CLB
     */
    private function assignClubMembers()
    {
        $this->command->info('👥 Gán thành viên vào CLB...');

        $clubs = Club::where('status', 'active')->get();
        $users = User::where('role_id', 2)->get();

        $added = 0;
        foreach ($clubs as $club) {
            // Mỗi CLB có 10-30 thành viên
            $memberCount = rand(10, 30);
            $selectedUsers = $users->shuffle()->take(min($memberCount, $users->count()));

            foreach ($selectedUsers as $index => $user) {
                $exists = DB::table('club_members')
                    ->where('club_id', $club->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$exists) {
                    $position = $index === 0 ? 'chairman' : ($index === 1 ? 'vice_chairman' : 'member');
                    
                    DB::table('club_members')->insert([
                        'club_id' => $club->id,
                        'user_id' => $user->id,
                        'position' => $position,
                        'status' => 'approved',
                        'joined_date' => Carbon::now()->subDays(rand(1, 365)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $added++;
                }
            }
        }

        $this->command->info("✅ Đã gán {$added} thành viên vào CLB.");
    }

    /**
     * Tạo hoạt động thực tế dựa trên các CLB
     */
    private function createRealActivities()
    {
        $this->command->info('📅 Tạo hoạt động thực tế...');

        $clubs = Club::where('status', 'active')->get();
        $users = User::where('role_id', 2)->get();

        $activities = [
            ['title' => 'Lễ tổng kết Chiến dịch Mùa hè xanh 2025', 'type' => 'volunteer', 'location' => 'Phòng B51.209, khu I'],
            ['title' => 'Tập huấn kỹ năng sơ cứu ban đầu và kỹ năng lều trại', 'type' => 'volunteer', 'location' => 'Khuôn viên Trường Đại học Trà Vinh'],
            ['title' => 'Công bố kết quả xét chọn thành viên ưu tú học kỳ 2', 'type' => 'academic', 'location' => 'Hội trường lớn'],
            ['title' => 'Giải đấu Vovinam mở rộng', 'type' => 'sports', 'location' => 'Nhà thi đấu TVU'],
            ['title' => 'Giải Taekwondo sinh viên', 'type' => 'sports', 'location' => 'Nhà thi đấu TVU'],
            ['title' => 'Hội thảo Nghiên cứu Khoa học Sinh viên', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Chiến dịch Hiến máu Tình nguyện', 'type' => 'volunteer', 'location' => 'Sân trường'],
            ['title' => 'Ngày hội Môi trường Xanh', 'type' => 'volunteer', 'location' => 'Khuôn viên trường'],
            ['title' => 'Workshop Khởi nghiệp và Đổi mới sáng tạo', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Cuộc thi Lập trình ITHUB', 'type' => 'academic', 'location' => 'Phòng máy tính'],
            ['title' => 'English Speaking Day', 'type' => 'academic', 'location' => 'Phòng học ngoại ngữ'],
            ['title' => 'Biểu diễn Đờn ca tài tử', 'type' => 'arts', 'location' => 'Sân khấu trường'],
            ['title' => 'Festival Nghệ thuật Khmer', 'type' => 'arts', 'location' => 'Sân khấu trường'],
            ['title' => 'Workshop Kỹ năng sống', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Đêm văn nghệ Sinh viên', 'type' => 'arts', 'location' => 'Sân khấu trường'],
            ['title' => 'Hội thảo Truyền thông và Marketing', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Chiến dịch Chạy bộ TVU Runner', 'type' => 'sports', 'location' => 'Sân vận động'],
            ['title' => 'Hội thảo Logistics và Thương mại điện tử', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Workshop Kinh doanh Online', 'type' => 'academic', 'location' => 'Phòng hội thảo'],
            ['title' => 'Ngày hội Việc làm Sinh viên', 'type' => 'academic', 'location' => 'Hội trường lớn'],
        ];

        $added = 0;
        foreach ($activities as $activity) {
            // Tìm CLB phù hợp
            $club = $clubs->filter(function($c) use ($activity) {
                return str_contains(strtolower($c->field), strtolower($activity['type'])) ||
                       str_contains(strtolower($c->club_type), strtolower($activity['type']));
            })->first() ?? $clubs->random();

            $creator = $users->random();
            
            // Tạo thời gian trong 12 tháng qua và tương lai
            $startAt = Carbon::now()->subMonths(rand(0, 11))->addDays(rand(-30, 60));
            $endAt = $startAt->copy()->addHours(rand(2, 8));
            
            $status = 'upcoming';
            if ($startAt->isPast() && $endAt->isPast()) {
                $status = rand(0, 10) < 1 ? 'cancelled' : 'finished';
            } elseif ($startAt->isPast() && $endAt->isFuture()) {
                $status = 'ongoing';
            }

            $approvalStatus = rand(0, 10) < 2 ? 'pending' : (rand(0, 10) < 1 ? 'rejected' : 'approved');
            
            Event::create([
                'title' => $activity['title'],
                'club_id' => $club->id,
                'description' => 'Hoạt động được tổ chức bởi ' . $club->name . '. ' . $activity['title'] . ' là một hoạt động ý nghĩa và bổ ích cho sinh viên.',
                'activity_type' => $this->mapActivityType($activity['type']),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'location' => $activity['location'],
                'status' => $status,
                'approval_status' => $approvalStatus,
                'created_by' => $creator->id,
                'expected_participants' => rand(30, 200),
                'expected_budget' => rand(1000000, 10000000),
                'goal' => 'Mục tiêu của hoạt động: Tạo môi trường học tập, rèn luyện và phát triển kỹ năng cho sinh viên.',
                'created_at' => $startAt->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("✅ Đã tạo {$added} hoạt động thực tế.");
    }

    /**
     * Tạo nội quy chung
     */
    private function createRegulations()
    {
        $this->command->info('📋 Tạo nội quy chung...');

        $regulations = [
            [
                'code' => 'NQ-001',
                'title' => 'Nội quy tham gia hoạt động CLB',
                'content' => 'Thành viên tham gia hoạt động phải có mặt đúng giờ, tham gia đầy đủ và tích cực. Vắng mặt không lý do sẽ bị xử lý kỷ luật.',
                'severity' => 'medium',
            ],
            [
                'code' => 'NQ-002',
                'title' => 'Nội quy về trang phục và tác phong',
                'content' => 'Thành viên tham gia hoạt động phải mặc trang phục phù hợp, lịch sự. Không được mặc quần áo phản cảm hoặc không phù hợp với môi trường học đường.',
                'severity' => 'light',
            ],
            [
                'code' => 'NQ-003',
                'title' => 'Nội quy về ứng xử trong CLB',
                'content' => 'Thành viên phải tôn trọng lẫn nhau, không được có hành vi bạo lực, xúc phạm hoặc phân biệt đối xử. Vi phạm sẽ bị xử lý nghiêm khắc.',
                'severity' => 'serious',
            ],
            [
                'code' => 'NQ-004',
                'title' => 'Nội quy về tài sản CLB',
                'content' => 'Thành viên phải bảo quản và sử dụng đúng mục đích tài sản của CLB. Làm mất hoặc hư hỏng tài sản phải bồi thường.',
                'severity' => 'medium',
            ],
            [
                'code' => 'NQ-005',
                'title' => 'Nội quy về đóng góp và tham gia',
                'content' => 'Thành viên phải tích cực tham gia các hoạt động của CLB. Thành viên không tham gia hoạt động trong 3 tháng liên tiếp sẽ bị cảnh cáo.',
                'severity' => 'light',
            ],
        ];

        $existingCount = Regulation::count();
        $added = 0;

        foreach ($regulations as $index => $reg) {
            $exists = Regulation::where('code', $reg['code'])->exists();
            if ($exists) continue;

            Regulation::create([
                'code' => $reg['code'],
                'title' => $reg['title'],
                'content' => $reg['content'],
                'scope' => 'all_clubs',
                'club_id' => null,
                'severity' => $reg['severity'],
                'status' => 'active',
                'issued_date' => Carbon::now()->subMonths(rand(6, 24)),
                'created_by' => 1, // Admin
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("✅ Đã tạo {$added} nội quy chung.");
    }

    /**
     * Tạo vi phạm mẫu
     */
    private function createSampleViolations()
    {
        $this->command->info('⚠️ Tạo vi phạm mẫu...');

        $clubs = Club::where('status', 'active')->get();
        $regulations = Regulation::where('status', 'active')->get();
        $clubMembers = DB::table('club_members')
            ->where('status', 'approved')
            ->get();

        if ($regulations->isEmpty() || $clubMembers->isEmpty()) {
            $this->command->warn('Không có nội quy hoặc thành viên để tạo vi phạm.');
            return;
        }

        $added = 0;
        $violationCount = 15; // Tạo 15 vi phạm mẫu

        for ($i = 0; $i < $violationCount; $i++) {
            $member = $clubMembers->random();
            $club = $clubs->find($member->club_id);
            $regulation = $regulations->random();
            $user = User::find($member->user_id);
            
            if (!$club || !$user) continue;

            $severities = ['light', 'medium', 'serious'];
            $statuses = ['pending', 'processed', 'monitoring'];
            $severity = $severities[array_rand($severities)];
            $status = $statuses[array_rand($statuses)];
            
            $disciplineType = null;
            $processedBy = null;
            $processedAt = null;
            
            if ($status === 'processed') {
                $disciplineTypes = ['warning', 'reprimand', 'suspension'];
                $disciplineType = $disciplineTypes[array_rand($disciplineTypes)];
                $processedBy = 1; // Admin
                $processedAt = Carbon::now()->subDays(rand(1, 30));
            }

            Violation::create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'regulation_id' => $regulation->id,
                'description' => $user->name . ' đã vi phạm nội quy: ' . $regulation->title . '. ' . $this->getViolationDescription($severity),
                'severity' => $severity,
                'violation_date' => Carbon::now()->subDays(rand(1, 180)),
                'recorded_by' => $club->owner_id ?? 1,
                'status' => $status,
                'discipline_type' => $disciplineType,
                'discipline_reason' => $disciplineType ? 'Vi phạm nội quy của CLB, cần xử lý kỷ luật để đảm bảo kỷ cương.' : null,
                'discipline_period_start' => $disciplineType ? Carbon::now()->subDays(rand(1, 30)) : null,
                'discipline_period_end' => $disciplineType ? Carbon::now()->addDays(rand(30, 90)) : null,
                'processed_by' => $processedBy,
                'processed_at' => $processedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command->info("✅ Đã tạo {$added} vi phạm mẫu.");
    }

    /**
     * Helper functions
     */
    private function getRandomVietnameseName($gender)
    {
        $maleNames = ['Anh', 'Bình', 'Cường', 'Dũng', 'Đức', 'Hùng', 'Khoa', 'Long', 'Minh', 'Nam', 'Phong', 'Quang', 'Sơn', 'Thành', 'Tuấn', 'Việt'];
        $femaleNames = ['An', 'Bích', 'Chi', 'Dung', 'Hà', 'Hương', 'Lan', 'Linh', 'Mai', 'Nga', 'Phương', 'Quỳnh', 'Thảo', 'Trang', 'Uyên', 'Yến'];
        
        return $gender === 'male' 
            ? $maleNames[array_rand($maleNames)]
            : $femaleNames[array_rand($femaleNames)];
    }

    private function getRandomVietnameseLastName()
    {
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
        return $lastNames[array_rand($lastNames)];
    }

    private function mapActivityType($type)
    {
        $mapping = [
            'volunteer' => 'volunteer',
            'sports' => 'other',
            'academic' => 'academic',
            'arts' => 'arts',
        ];
        return $mapping[$type] ?? 'other';
    }

    private function getViolationDescription($severity)
    {
        $descriptions = [
            'light' => 'Vắng mặt không báo trước 1 lần.',
            'medium' => 'Vắng mặt không báo trước nhiều lần hoặc vi phạm nội quy về trang phục.',
            'serious' => 'Có hành vi xúc phạm, bạo lực hoặc vi phạm nghiêm trọng khác.',
        ];
        return $descriptions[$severity] ?? 'Vi phạm nội quy của CLB.';
    }
}

