@extends('layouts.guest')

@section('title', 'Trang chủ - Hệ thống CLB')

@section('content')
<!-- HERO SECTION -->
<section class="hero-section text-text-light py-20 relative overflow-hidden">
    <div class="container mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl font-bold mb-4">Hệ thống quản lý Câu lạc bộ Sinh viên</h2>
        <p class="text-lg mb-6">Khám phá – Tham gia – Phát triển kỹ năng cùng cộng đồng sinh viên năng động</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('guest.clubs') }}" class="bg-accent-yellow text-primary-blue px-6 py-3 rounded-full font-semibold shadow-lg hover:scale-105 transition">Khám phá CLB</a>
            <a href="{{ route('login') }}" class="bg-white text-primary-blue px-6 py-3 rounded-full font-semibold shadow-lg hover:scale-105 transition">Đăng nhập</a>
        </div>
    </div>
</section>

<!-- BANNER PROMOTION SECTION -->
<section class="banner-promo-section py-16 relative overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="banner-promo-card relative rounded-2xl overflow-hidden shadow-2xl">
            <div class="banner-promo-background"></div>
            <div class="banner-promo-content relative z-10 p-8 md:p-12 text-center md:text-left">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="banner-promo-text">
                        <h3 class="text-3xl md:text-4xl font-bold text-white mb-4">KẾT NỐI ĐAM MÊ - TỎA SÁNG TÀI NĂNG</h3>
                        <p class="text-lg text-white mb-6 opacity-90">Hệ thống quản lý câu lạc bộ - Đại Học Trà Vinh</p>
                        <a href="{{ route('register') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:scale-105 transition transform">
                            ĐĂNG KÝ THAM GIA NGAY
                        </a>
                    </div>
                    <div class="banner-promo-image hidden md:block">
                        <div class="banner-promo-logo w-32 h-32 mx-auto md:mx-0 bg-white rounded-full flex items-center justify-center shadow-lg">
                            <div class="text-6xl">🎓</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GIỚI THIỆU -->
<section class="py-16">
    <div class="container mx-auto px-6 grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
            <h3 class="text-xl font-bold text-primary-blue mb-2">Kết nối cộng đồng</h3>
            <p>Tham gia các CLB phù hợp với sở thích và chuyên ngành của bạn.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
            <h3 class="text-xl font-bold text-primary-blue mb-2">Hoạt động phong phú</h3>
            <p>Tổ chức sự kiện, workshop, cuộc thi và tình nguyện.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
            <h3 class="text-xl font-bold text-primary-blue mb-2">Phát triển bản thân</h3>
            <p>Rèn luyện kỹ năng mềm và xây dựng hồ sơ cá nhân nổi bật.</p>
        </div>
    </div>
</section>

<!-- DANH SÁCH CLB -->
<section id="clubs" class="bg-soft-yellow py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-primary-blue mb-4">Câu lạc bộ nổi bật</h2>
        <p class="text-center text-gray-600 mb-10">Khám phá các CLB đang hoạt động sôi nổi tại trường</p>

        @if($featuredClubs->count() > 0)
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                @foreach($featuredClubs as $club)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6">
                        @if($club->logo)
                            <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}" class="w-20 h-20 rounded-lg mb-4 object-cover">
                        @else
                            <div class="w-20 h-20 rounded-lg mb-4 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ strtoupper(substr($club->name ?? 'CLB', 0, 3)) }}
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold text-primary-blue mb-2">{{ $club->name }}</h3>
                        <p class="text-gray-600 mb-3 text-sm line-clamp-2">{{ $club->description ?? 'Chưa có mô tả' }}</p>
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-block bg-accent-yellow text-primary-blue px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $club->field_display ?? 'Khác' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="bi bi-people"></i> {{ $club->members_count ?? 0 }} thành viên
                            </span>
                        </div>
                        <a href="{{ route('guest.club-detail', $club->id) }}" class="text-primary-blue font-semibold hover:underline inline-block mt-2">
                            Xem chi tiết →
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center">
                <a href="{{ route('guest.clubs') }}" class="bg-primary-blue text-white px-8 py-3 rounded-full font-semibold hover:opacity-90 transition inline-block">
                    Xem tất cả CLB
                </a>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Chưa có CLB nào.</p>
            </div>
        @endif
    </div>
</section>

<!-- HOẠT ĐỘNG -->
<section id="activities" class="py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center text-primary-blue mb-4">Hoạt động sắp diễn ra</h2>
        <p class="text-center text-gray-600 mb-10">Các sự kiện và hoạt động thú vị đang chờ bạn tham gia</p>

        @if($upcomingEvents->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($upcomingEvents as $event)
                    <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="flex items-start gap-4">
                            @if($event->club && $event->club->logo)
                                <img src="{{ asset('storage/' . $event->club->logo) }}" alt="{{ $event->club->name }}" class="w-16 h-16 rounded-lg object-cover">
                            @endif
                            <div class="flex-1">
                                <h4 class="font-semibold text-lg text-primary-blue mb-2">{{ $event->title }}</h4>
                                <p class="text-gray-500 text-sm mb-2">
                                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                </p>
                                @if($event->location)
                                    <p class="text-gray-500 text-sm mb-2">
                                        <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                    </p>
                                @endif
                                @if($event->club)
                                    <p class="text-gray-600 text-sm font-semibold">{{ $event->club->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center">
                <a href="{{ route('guest.events') }}" class="bg-primary-blue text-white px-8 py-3 rounded-full font-semibold hover:opacity-90 transition inline-block">
                    Xem tất cả sự kiện
                </a>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">Chưa có sự kiện nào sắp diễn ra.</p>
            </div>
        @endif
    </div>
</section>
@endsection
