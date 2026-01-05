@extends('layouts.guest')

@section('title', 'Chính sách Bảo mật')

@section('content')
<div class="container mx-auto px-6 py-16">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-primary-blue mb-8 text-center">Chính sách Bảo mật</h1>

        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-8">
            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">1. Thu thập thông tin</h2>
                <p class="text-gray-700 leading-relaxed mb-2">
                    Hệ thống thu thập các thông tin sau khi bạn đăng ký tài khoản:
                </p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 ml-4">
                    <li>Mã sinh viên (MSSV)</li>
                    <li>Họ và tên</li>
                    <li>Email trường</li>
                    <li>Thông tin cá nhân khác (số điện thoại, ngày sinh, giới tính, khoa, lớp)</li>
                    <li>Ảnh đại diện (nếu bạn tải lên)</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">2. Sử dụng thông tin</h2>
                <p class="text-gray-700 leading-relaxed mb-2">
                    Thông tin của bạn được sử dụng để:
                </p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 ml-4">
                    <li>Xác thực và quản lý tài khoản của bạn</li>
                    <li>Kết nối bạn với các CLB và hoạt động phù hợp</li>
                    <li>Gửi thông báo về các sự kiện và hoạt động</li>
                    <li>Cải thiện chất lượng dịch vụ và trải nghiệm người dùng</li>
                    <li>Thống kê và báo cáo cho nhà trường</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">3. Bảo vệ thông tin</h2>
                <p class="text-gray-700 leading-relaxed">
                    Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn bằng các biện pháp bảo mật tiên tiến. Mật khẩu được mã hóa và không thể đọc được ngay cả bởi các quản trị viên hệ thống. Thông tin của bạn chỉ được chia sẻ với các thành viên trong CLB mà bạn tham gia, và chỉ những thông tin cần thiết.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">4. Quyền của người dùng</h2>
                <p class="text-gray-700 leading-relaxed mb-2">
                    Bạn có quyền:
                </p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 ml-4">
                    <li>Xem và chỉnh sửa thông tin cá nhân của mình</li>
                    <li>Yêu cầu xóa tài khoản (liên hệ quản trị viên)</li>
                    <li>Yêu cầu xuất dữ liệu cá nhân</li>
                    <li>Thay đổi cài đặt quyền riêng tư và thông báo</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">5. Cookie và công nghệ theo dõi</h2>
                <p class="text-gray-700 leading-relaxed">
                    Hệ thống sử dụng cookie để lưu trữ phiên đăng nhập và cài đặt người dùng. Cookie này chỉ được sử dụng cho mục đích cải thiện trải nghiệm người dùng và không được chia sẻ với bên thứ ba.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">6. Thay đổi chính sách</h2>
                <p class="text-gray-700 leading-relaxed">
                    Chúng tôi có thể cập nhật chính sách bảo mật này theo thời gian. Mọi thay đổi sẽ được thông báo trên trang web. Việc bạn tiếp tục sử dụng hệ thống sau khi có thay đổi đồng nghĩa với việc bạn chấp nhận chính sách mới.
                </p>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-primary-blue mb-4">7. Liên hệ</h2>
                <p class="text-gray-700 leading-relaxed">
                    Nếu bạn có bất kỳ câu hỏi nào về chính sách bảo mật này, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:minhluanngulac@gmail.com" class="text-primary-blue hover:underline">minhluanngulac@gmail.com</a>
                </p>
            </div>

            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

