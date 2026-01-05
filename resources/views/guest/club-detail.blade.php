@extends('layouts.guest')

@section('title', $club->name . ' - Chi tiết CLB')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-text-light py-8">
    <div class="container mx-auto px-6">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm">
            <a href="{{ route('guest.home') }}" class="text-text-light hover:opacity-80 hover:underline">Trang chủ</a>
            <span class="mx-2 text-text-light opacity-80">/</span>
            <a href="{{ route('guest.clubs') }}" class="text-text-light hover:opacity-80 hover:underline">Danh sách CLB</a>
            <span class="mx-2 text-text-light opacity-80">/</span>
            <span class="text-text-light opacity-80">{{ $club->name }}</span>
        </nav>
    </div>
</section>

<div class="container mx-auto px-6 py-16">
    <!-- Banner CLB -->
    @if($club->banner)
        <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
            <img src="{{ asset('storage/' . $club->banner) }}" alt="{{ $club->name }}" class="w-full h-64 md:h-96 object-cover">
        </div>
    @endif

    <!-- Header CLB -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-6">
            @if($club->logo)
                <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}" class="w-32 h-32 rounded-2xl object-cover shadow-md">
            @else
                <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold shadow-md">
                    {{ strtoupper(substr($club->name ?? 'CLB', 0, 3)) }}
                </div>
            @endif
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-primary-blue mb-2">{{ $club->name }}</h1>
                <p class="text-lg text-gray-600 mb-4">Mã CLB: {{ $club->code }}</p>
                <div class="flex flex-wrap gap-3">
                    @if($club->field_display)
                        <span class="inline-block bg-accent-yellow text-primary-blue px-4 py-2 rounded-full text-sm font-semibold">
                            {{ $club->field_display }}
                        </span>
                    @endif
                    <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="bi bi-people"></i> {{ $club->members_count }} thành viên
                    </span>
                    <span class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                        Hoạt động
                    </span>
                </div>
            </div>
        </div>

        <!-- Nút Đăng nhập để tham gia -->
        @guest
            <div class="mt-6 p-4 bg-yellow-50 rounded" style="border-left: 4px solid var(--accent-yellow);">
                <p class="text-gray-700 mb-3">Bạn cần đăng nhập để tham gia CLB này.</p>
                <a href="{{ route('login') }}" class="bg-primary-blue text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition inline-block">
                    Đăng nhập để tham gia
                </a>
            </div>
        @endguest
    </div>

    <!-- Giới thiệu CLB -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold text-primary-blue mb-4">Giới thiệu</h2>
        <p class="text-gray-700 leading-relaxed">{{ $club->description ?? 'Chưa có mô tả' }}</p>
    </div>

    <!-- Mục tiêu hoạt động -->
    @if($club->activity_goals)
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-primary-blue mb-4">Mục tiêu hoạt động</h2>
            <p class="text-gray-700 leading-relaxed">{{ $club->activity_goals }}</p>
        </div>
    @endif

    <!-- Thông tin liên hệ -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-primary-blue mb-4">Thông tin liên hệ</h3>
            @if($club->email)
                <p class="text-gray-700 mb-2"><i class="bi bi-envelope"></i> {{ $club->email }}</p>
            @endif
            @if($club->phone)
                <p class="text-gray-700 mb-2"><i class="bi bi-telephone"></i> {{ $club->phone }}</p>
            @endif
            @if($club->fanpage)
                <p class="text-gray-700 mb-2"><i class="bi bi-facebook"></i> <a href="{{ $club->fanpage }}" target="_blank" class="text-primary-blue hover:underline">Fanpage</a></p>
            @endif
            @if($club->meeting_place)
                <p class="text-gray-700 mb-2"><i class="bi bi-geo-alt"></i> {{ $club->meeting_place }}</p>
            @endif
        </div>

        <!-- Ban điều hành -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-primary-blue mb-4">Chủ nhiệm</h3>
            @if($club->owner)
                <div class="flex items-center gap-3">
                    @if($club->owner->avatar)
                        <img src="{{ asset('storage/' . $club->owner->avatar) }}" alt="{{ $club->owner->name }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($club->owner->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-800">{{ $club->owner->name }}</p>
                        <p class="text-sm text-gray-600">{{ $club->owner->student_code ?? '' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sự kiện gần đây -->
    @if($recentEvents->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-primary-blue mb-6">Sự kiện gần đây</h2>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($recentEvents as $event)
                    <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                        <h3 class="font-semibold text-lg text-primary-blue mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                        </p>
                        @if($event->location)
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="bi bi-geo-alt"></i> {{ $event->location }}
                            </p>
                        @endif
                        @if($event->description)
                            <p class="text-gray-700 text-sm line-clamp-2">{{ $event->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-6 text-center">
                <a href="{{ route('guest.events') }}?club_id={{ $club->id }}" class="text-primary-blue hover:underline font-semibold">
                    Xem tất cả sự kiện →
                </a>
            </div>
        </div>
    @endif

    <!-- Nút quay lại -->
    <div class="text-center">
        <a href="{{ route('guest.clubs') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition inline-block">
            ← Quay lại danh sách CLB
        </a>
    </div>
</div>
@endsection

