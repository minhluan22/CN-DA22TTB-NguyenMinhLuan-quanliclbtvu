@extends('layouts.guest')

@section('title', 'Giới thiệu - Hệ thống CLB')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-text-light py-12">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold mb-4">Giới thiệu về Hệ thống</h1>
        <p class="text-lg">Tìm hiểu về nền tảng quản lý CLB của chúng tôi</p>
    </div>
</section>

<div class="container mx-auto px-6 py-16">
    <div class="max-w-4xl mx-auto">

        <!-- Thống kê tổng quan -->
        <section class="bg-soft-yellow py-12 rounded-2xl mb-12">
            <div class="px-6">
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                <div class="text-4xl font-bold text-primary-blue mb-2">{{ $stats['total_clubs'] }}</div>
                <div class="text-gray-600">Câu lạc bộ</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                <div class="text-4xl font-bold text-primary-blue mb-2">{{ $stats['total_members'] }}</div>
                <div class="text-gray-600">Thành viên</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
                <div class="text-4xl font-bold text-primary-blue mb-2">{{ $stats['total_events'] }}</div>
                <div class="text-gray-600">Sự kiện</div>
            </div>
        </div>
            </div>
        </section>

        <!-- Hệ thống hoạt động ra sao -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-primary-blue mb-4">Hệ thống hoạt động ra sao?</h2>
            <div class="space-y-4 text-gray-700 leading-relaxed">
                <p>Hệ thống Quản lý Câu lạc bộ Sinh viên là nền tảng kết nối và quản lý các hoạt động của các CLB trong trường Đại học Trà Vinh. Hệ thống cung cấp đầy đủ các công cụ để:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Quản lý thông tin CLB, thành viên và hoạt động</li>
                    <li>Tổ chức và theo dõi các sự kiện, hoạt động</li>
                    <li>Tích điểm và đánh giá hoạt động của thành viên</li>
                    <li>Phê duyệt đơn đăng ký tham gia CLB</li>
                    <li>Thông báo và cập nhật thông tin cho thành viên</li>
                </ul>
            </div>
        </div>

        <!-- Lợi ích của CLB -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-primary-blue mb-4">Lợi ích của CLB đối với sinh viên</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg text-primary-blue mb-2">📚 Phát triển kỹ năng</h3>
                    <p class="text-gray-700">Tham gia các hoạt động thực tế, rèn luyện kỹ năng mềm và chuyên môn.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-primary-blue mb-2">🤝 Kết nối mạng lưới</h3>
                    <p class="text-gray-700">Gặp gỡ và kết bạn với những người có cùng sở thích và đam mê.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-primary-blue mb-2">🎯 Định hướng nghề nghiệp</h3>
                    <p class="text-gray-700">Khám phá và phát triển đam mê, định hướng con đường sự nghiệp tương lai.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-primary-blue mb-2">🏆 Xây dựng hồ sơ</h3>
                    <p class="text-gray-700">Tích lũy điểm hoạt động, chứng chỉ và thành tích để làm đẹp CV.</p>
                </div>
            </div>
        </div>

        <!-- Quy trình tham gia -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-primary-blue mb-4">Quy trình tham gia CLB</h2>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center font-bold text-primary-blue">1</div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Đăng ký tài khoản</h3>
                        <p class="text-gray-700">Tạo tài khoản sinh viên trên hệ thống với MSSV và email trường.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center font-bold text-primary-blue">2</div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Khám phá CLB</h3>
                        <p class="text-gray-700">Xem danh sách các CLB đang hoạt động và tìm CLB phù hợp với bạn.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center font-bold text-primary-blue">3</div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Gửi đơn đăng ký</h3>
                        <p class="text-gray-700">Nộp đơn đăng ký tham gia CLB mà bạn quan tâm.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center font-bold text-primary-blue">4</div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Chờ phê duyệt</h3>
                        <p class="text-gray-700">Ban điều hành CLB sẽ xem xét và phê duyệt đơn của bạn.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-accent-yellow rounded-full flex items-center justify-center font-bold text-primary-blue">5</div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Tham gia hoạt động</h3>
                        <p class="text-gray-700">Sau khi được duyệt, bạn có thể tham gia các sự kiện và hoạt động của CLB.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

