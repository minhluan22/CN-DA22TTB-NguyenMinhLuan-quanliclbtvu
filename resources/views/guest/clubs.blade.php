@extends('layouts.guest')

@section('title', 'Danh sách Câu lạc bộ')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-text-light py-12">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold mb-4">Danh sách Câu lạc bộ</h1>
        <p class="text-lg">Khám phá tất cả các CLB đang hoạt động tại trường</p>
    </div>
</section>

<div class="container mx-auto px-6 py-16">
    <!-- Tìm kiếm và Lọc -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <form method="GET" action="{{ route('guest.clubs') }}" class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tìm kiếm theo tên CLB</label>
                <input type="text" name="search" value="{{ request()->search }}" placeholder="Nhập tên CLB..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lọc theo lĩnh vực</label>
                <select name="field" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    <option value="">Tất cả lĩnh vực</option>
                    @foreach($fields as $field)
                        <option value="{{ $field }}" {{ request()->field === $field ? 'selected' : '' }}>{{ $field }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-blue text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách CLB -->
    <section class="bg-soft-yellow py-16 rounded-2xl mb-8">
        <div class="px-6">
    @if($clubs->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($clubs as $club)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition p-6">
                    <div class="flex items-start gap-4 mb-4">
                        @if($club->logo)
                            <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}" class="w-20 h-20 rounded-lg object-cover">
                        @else
                            <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                                {{ strtoupper(substr($club->name ?? 'CLB', 0, 3)) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-primary-blue mb-1">{{ $club->name }}</h3>
                            <p class="text-sm text-gray-500 mb-2">Mã: {{ $club->code }}</p>
                            @if($club->field_display)
                                <span class="inline-block bg-accent-yellow text-primary-blue px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $club->field_display }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $club->description ?? 'Chưa có mô tả' }}</p>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="bi bi-people"></i>
                            <span>{{ $club->members_count ?? 0 }} thành viên</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $club->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $club->status === 'active' ? 'Hoạt động' : 'Tạm ngưng' }}
                        </span>
                    </div>
                    
                    <a href="{{ route('guest.club-detail', $club->id) }}" class="block w-full text-center bg-primary-blue text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
                        Xem chi tiết
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-2xl shadow">
            <p class="text-gray-500 text-lg">Không tìm thấy CLB nào.</p>
            <a href="{{ route('guest.clubs') }}" class="text-primary-blue hover:underline mt-4 inline-block">
                Xem tất cả CLB
            </a>
        </div>
    @endif
        </div>
    </section>
</div>
@endsection

