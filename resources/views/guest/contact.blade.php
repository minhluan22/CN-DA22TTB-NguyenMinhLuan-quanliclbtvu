@extends('layouts.guest')

@section('title', 'Liên hệ - Hệ thống CLB')

@section('content')
<!-- Hero Section -->
<section class="hero-section text-text-light py-12">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold mb-4">Liên hệ & Hỗ trợ</h1>
        <p class="text-lg">Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
    </div>
</section>

<div class="container mx-auto px-6 py-16">
    <div class="max-w-4xl mx-auto">

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Thông tin liên hệ -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-primary-blue mb-6">Thông tin liên hệ</h2>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-envelope text-primary-blue"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                            <a href="mailto:minhluanngulac@gmail.com" class="text-primary-blue hover:underline">minhluanngulac@gmail.com</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-telephone text-primary-blue"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Hotline</h3>
                            <a href="tel:0123456789" class="text-primary-blue hover:underline">0123 456 789</a>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-geo-alt text-primary-blue"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Địa chỉ</h3>
                            <p class="text-gray-700">Trường Đại học Trà Vinh</p>
                            <p class="text-gray-600 text-sm">126 Nguyễn Thiện Thành, Khóm 4, Phường 5, TP. Trà Vinh</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-clock text-primary-blue"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Giờ làm việc</h3>
                            <p class="text-gray-700">Thứ 2 - Thứ 6: 7:30 - 17:00</p>
                            <p class="text-gray-700">Thứ 7: 7:30 - 12:00</p>
                            <p class="text-gray-600 text-sm">Nghỉ Chủ nhật và các ngày lễ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-primary-blue mb-6">Gửi tin nhắn</h2>
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('guest.contact.submit') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung <span class="text-red-500">*</span></label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent">{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary-blue text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        <i class="bi bi-send"></i> Gửi tin nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

