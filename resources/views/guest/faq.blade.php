@extends('layouts.guest')

@section('title', 'Câu hỏi thường gặp - FAQ')

@section('content')
<div class="container mx-auto px-6 py-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl font-bold text-primary-blue mb-8 text-center">Câu hỏi thường gặp (FAQ)</h1>

        <div class="space-y-6">
            <!-- FAQ Item 1 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Làm sao gia nhập CLB?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Để gia nhập CLB, bạn cần:
                </p>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 ml-4 mt-2">
                    <li>Đăng ký tài khoản trên hệ thống với MSSV và email trường</li>
                    <li>Xem danh sách CLB và chọn CLB bạn muốn tham gia</li>
                    <li>Nhấn nút "Đăng ký tham gia" và điền đơn đăng ký</li>
                    <li>Chờ ban điều hành CLB phê duyệt đơn của bạn</li>
                    <li>Sau khi được duyệt, bạn sẽ nhận thông báo và chính thức trở thành thành viên</li>
                </ol>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">CLB có phí không?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Hầu hết các CLB trong trường đều miễn phí tham gia. Tuy nhiên, một số CLB có thể yêu cầu đóng phí hoạt động tùy thuộc vào tính chất và quy mô hoạt động của CLB đó. Thông tin về phí (nếu có) sẽ được thông báo rõ ràng khi bạn đăng ký tham gia.
                </p>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Khi nào được mở CLB mới?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Sinh viên có thể đề xuất thành lập CLB mới bất kỳ lúc nào thông qua chức năng "Đề xuất CLB mới" trên hệ thống. Đơn đề nghị sẽ được gửi đến Ban quản trị để xem xét. CLB mới sẽ được mở sau khi đơn đề nghị được phê duyệt và đáp ứng đủ các yêu cầu về thành viên và mục tiêu hoạt động.
                </p>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Làm sao để tích điểm hoạt động?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Điểm hoạt động được tích lũy khi bạn tham gia các sự kiện và hoạt động do CLB tổ chức. Sau khi tham gia và hoàn thành sự kiện, Chủ nhiệm CLB sẽ xác nhận sự tham gia của bạn và hệ thống sẽ tự động cộng điểm vào tài khoản của bạn. Bạn có thể xem số điểm của mình trong phần "Hồ sơ cá nhân".
                </p>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Tôi có thể tham gia nhiều CLB cùng lúc không?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Có, bạn hoàn toàn có thể tham gia nhiều CLB cùng lúc. Tuy nhiên, hãy đảm bảo rằng bạn có đủ thời gian và năng lượng để tham gia đầy đủ các hoạt động của các CLB mà bạn đã tham gia.
                </p>
            </div>

            <!-- FAQ Item 6 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Làm sao để rời khỏi CLB?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Nếu bạn muốn rời khỏi một CLB, bạn có thể vào trang chi tiết CLB và nhấn nút "Rời khỏi CLB". Lưu ý rằng sau khi rời khỏi, bạn sẽ mất quyền truy cập vào các thông tin nội bộ và không thể tham gia các hoạt động của CLB đó nữa.
                </p>
            </div>

            <!-- FAQ Item 7 -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-primary-blue mb-3">Tôi quên mật khẩu, làm sao lấy lại?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Trên trang đăng nhập, bạn có thể nhấn vào liên kết "Quên mật khẩu" để yêu cầu hệ thống gửi email hướng dẫn đặt lại mật khẩu. Bạn cần nhập email đã đăng ký, hệ thống sẽ gửi link reset mật khẩu vào email của bạn.
                </p>
            </div>
        </div>

        <!-- Liên hệ thêm -->
        <div class="mt-12 rounded-2xl p-8 text-center" style="background-color: var(--soft-yellow);">
            <h3 class="text-xl font-bold text-primary-blue mb-3">Không tìm thấy câu trả lời?</h3>
            <p class="text-gray-700 mb-4">Nếu bạn còn thắc mắc, đừng ngại liên hệ với chúng tôi!</p>
            <a href="{{ route('guest.contact') }}" class="bg-primary-blue text-white px-8 py-3 rounded-lg font-semibold hover:opacity-90 transition inline-block">
                Liên hệ hỗ trợ
            </a>
        </div>
    </div>
</div>
@endsection

