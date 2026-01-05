@extends('layouts.guest')

@section('title', 'Danh sách Sự kiện')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-text-light py-12">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold mb-4">Danh sách Sự kiện</h1>
        <p class="text-lg">Khám phá các sự kiện và hoạt động đang diễn ra</p>
    </div>
</section>

<div class="container mx-auto px-6 py-16">
    <!-- Bộ lọc -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ route('guest.events') }}" class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lọc theo CLB</label>
                <select name="club_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    <option value="">Tất cả CLB</option>
                    @foreach($clubs as $clubOption)
                        <option value="{{ $clubOption->id }}" {{ request()->club_id == $clubOption->id ? 'selected' : '' }}>{{ $clubOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lọc theo thời gian</label>
                <select name="time_filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="upcoming" {{ request()->time_filter === 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="past" {{ request()->time_filter === 'past' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-blue text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách sự kiện -->
    <section class="bg-soft-yellow py-16 rounded-2xl mb-8">
        <div class="px-6">
    @if($events->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6">
                    @if($event->club && $event->club->logo)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $event->club->logo) }}" alt="{{ $event->club->name }}" class="w-full h-32 rounded-lg object-cover">
                        </div>
                    @endif
                    <h3 class="text-xl font-semibold text-primary-blue mb-3">{{ $event->title }}</h3>
                    @if($event->club)
                        <p class="text-sm text-gray-600 mb-2 font-semibold">{{ $event->club->name }}</p>
                    @endif
                    <p class="text-gray-600 text-sm mb-2">
                        <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                    </p>
                    @if($event->location)
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="bi bi-geo-alt"></i> {{ $event->location }}
                        </p>
                    @endif
                    @if($event->description)
                        <p class="text-gray-700 text-sm mb-4 line-clamp-3">{{ $event->description }}</p>
                    @endif
                    @guest
                        <div class="p-3 bg-yellow-50 rounded text-sm text-gray-700" style="border-left: 4px solid var(--accent-yellow);">
                            <a href="{{ route('login') }}" class="text-primary-blue hover:underline font-semibold">Đăng nhập</a> để đăng ký tham gia
                        </div>
                    @endguest
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-2xl shadow">
            <p class="text-gray-500 text-lg">Không có sự kiện nào.</p>
        </div>
    @endif
        </div>
    </section>
</div>
@endsection

