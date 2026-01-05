<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    /* ================= LOGIN ================= */

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'student_code' => ['required', 'string'],
            'password'     => ['required'],
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Vui lòng xác thực reCAPTCHA.',
        ]);

        // Kiểm tra reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        if ($recaptchaSecret) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            
            try {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip(),
                ]);
                
                $responseData = $response->json();
                
                if (!isset($responseData['success']) || !$responseData['success']) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'Xác thực reCAPTCHA không thành công. Vui lòng thử lại.',
                    ])->onlyInput('student_code');
                }
            } catch (\Exception $e) {
                // Nếu không kết nối được đến Google reCAPTCHA API, bỏ qua (để tránh lỗi khi dev)
                // Trong production nên log lỗi này
            }
        }

        // Kiểm tra tài khoản có tồn tại không
        $user = User::where('student_code', $request->student_code)->first();

        // Nếu tài khoản tồn tại nhưng bị khóa
        if ($user && $user->status == 0) {
            return back()->withErrors([
                'student_code' => 'Tài khoản bạn đã bị khóa. Liên hệ admin để giải quyết.',
            ])->onlyInput('student_code');
        }

        // Lấy intended_role từ request (được set bởi JavaScript)
        $intendedRole = $request->input('intended_role');
        
        // Thử đăng nhập với điều kiện status = 1 (chỉ cho phép tài khoản hoạt động)
        if (Auth::attempt([
            'student_code' => $request->student_code,
            'password' => $request->password,
            'status' => 1  // Chỉ cho phép đăng nhập nếu status = 1 (hoạt động)
        ])) {
            $user = Auth::user();
            
            // Kiểm tra lại status sau khi đăng nhập thành công (để chắc chắn)
            if ($user->status == 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'student_code' => 'Tài khoản bạn đã bị khóa. Liên hệ admin để giải quyết.',
                ])->onlyInput('student_code');
            }
            
            // Xác định role
            $isAdmin = $user->role && $user->role->name === 'Admin';
            $userRole = $isAdmin ? 'admin' : 'student';
            
            // Regenerate session để tạo session ID mới
            $request->session()->regenerate();
            
            // Đảm bảo user vẫn được lưu trong session
            Auth::login($user);
            
            // Cập nhật last_activity khi đăng nhập
            $user->update([
                'last_activity' => now()
            ]);

            /* ===== FIX CHECK QUYỀN ADMIN ===== */
            if ($isAdmin) {
                // Set cookie 'auth_role' và 'auth_user_id' để middleware biết dùng cookie name nào
                // Middleware LoadUserFromCookie sẽ load lại user từ cookie sau khi redirect
                return redirect()->route('admin.dashboard')
                    ->cookie('auth_role', 'admin', config('session.lifetime', 120) * 60, '/', null, false, false)
                    ->cookie('auth_user_id', $user->id, config('session.lifetime', 120) * 60, '/', null, false, false);
            }

            /* Mặc định: Sinh viên */
            // Set cookie 'auth_role' và 'auth_user_id' để middleware biết dùng cookie name nào
            // Middleware LoadUserFromCookie sẽ load lại user từ cookie sau khi redirect
            return redirect()->route('student.home')
                ->cookie('auth_role', 'student', config('session.lifetime', 120) * 60, '/', null, false, false)
                ->cookie('auth_user_id', $user->id, config('session.lifetime', 120) * 60, '/', null, false, false);
        }

        return back()->withErrors([
            'student_code' => 'Sai MSSV hoặc mật khẩu.',
        ])->onlyInput('student_code');
    }


    /* ================= REGISTER ================= */

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',

            'student_code'  => 'required|digits:9|unique:users',

            'email' => [
                'required',
                'email',
                'unique:users',
                'regex:/^[A-Za-z0-9]+@st\.tvu\.edu\.vn$/'
            ],

            'password'      => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'email.regex' => 'Email phải có dạng: MSSV@st.tvu.edu.vn',
            'student_code.digits' => 'MSSV phải gồm đúng 9 chữ số.',
            'student_code.unique' => 'Bạn đã có tài khoản rồi. Vui lòng đăng nhập.',
            'email.unique' => 'Email này đã được sử dụng. Bạn đã có tài khoản rồi.',
            'g-recaptcha-response.required' => 'Vui lòng xác thực reCAPTCHA.',
        ]);

        // Kiểm tra email đúng với MSSV
        if (!str_starts_with($request->email, $request->student_code)) {
            return back()->withErrors([
                'email' => 'Email phải trùng với MSSV. Ví dụ: ' . $request->student_code . '@st.tvu.edu.vn'
            ])->withInput();
        }

        // Kiểm tra reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        if ($recaptchaSecret) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            
            try {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip(),
                ]);
                
                $responseData = $response->json();
                
                if (!isset($responseData['success']) || !$responseData['success']) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'Xác thực reCAPTCHA không thành công. Vui lòng thử lại.',
                    ])->withInput();
                }
            } catch (\Exception $e) {
                // Nếu không kết nối được đến Google reCAPTCHA API, bỏ qua (để tránh lỗi khi dev)
                // Trong production nên log lỗi này
            }
        }

        // ====== Tạo tài khoản ngay (không cần OTP) ======
        User::create([
            'name'          => $request->name,
            'student_code'  => $request->student_code,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'role_id'       => 2,   // 2 = Student
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }


    /* ================= VERIFY OTP ================= */

    public function showVerifyOtpForm()
    {
        if (!session('pending_user')) {
            return redirect()->route('register')->with('error', 'Vui lòng điền thông tin đăng ký trước.');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        if (!session('pending_user') || !session('otp_code') || !session('otp_expires_at')) {
            return redirect()->route('register')->withErrors(['otp' => 'Dữ liệu không hợp lệ.']);
        }

        // Kiểm tra hết hạn
        $expiresAt = Carbon::parse(session('otp_expires_at'));
        if (Carbon::now()->greaterThan($expiresAt)) {
            session()->forget(['pending_user','otp_code','otp_expires_at','otp_sent_at','otp_attempts']);
            return redirect()->route('register')->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng đăng ký lại.']);
        }

        // Giới hạn brute-force
        $attempts = session('otp_attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['pending_user','otp_code','otp_expires_at','otp_sent_at','otp_attempts']);
            return redirect()->route('register')->withErrors(['otp' => 'Bạn đã nhập OTP sai quá nhiều lần.']);
        }

        // Kiểm tra mã OTP
        if ($request->otp != session('otp_code')) {
            session(['otp_attempts' => $attempts + 1]);
            return back()->withErrors(['otp' => 'Mã OTP không đúng.'])->withInput();
        }

        // OTP đúng → Tạo tài khoản
        $data = session('pending_user');

        User::create([
            'name'          => $data['name'],
            'student_code'  => $data['student_code'],
            'email'         => $data['email'],
            'password'      => $data['password'],

            /* === FIX TẠO USER ĐÚNG role_id === */
            'role_id'       => $data['role_id'] ?? 2,
        ]);

        session()->forget(['pending_user','otp_code','otp_expires_at','otp_sent_at','otp_attempts']);

        return redirect()->route('login')->with('success', 'Xác thực thành công! Vui lòng đăng nhập.');
    }


    /* ================= LOGOUT ================= */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Xóa tất cả cookies liên quan khi đăng xuất
        return redirect()->route('guest.home')
            ->cookie('intended_role', '', -1, '/', null, false, false)
            ->cookie('auth_user_id', '', -1, '/', null, false, false)
            ->cookie('auth_role', '', -1, '/', null, false, false);
    }

    /**
     * Đăng xuất khỏi tất cả thiết bị
     */
    public function logoutAll(Request $request)
    {
        $user = Auth::user();
        
        // Xóa tất cả sessions của user
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
        
        // Logout thiết bị hiện tại
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Xóa tất cả cookies liên quan khi đăng xuất
        return redirect()->route('login')
            ->with('success', 'Bạn đã đăng xuất khỏi tất cả thiết bị thành công.')
            ->cookie('intended_role', '', -1, '/', null, false, false)
            ->cookie('auth_user_id', '', -1, '/', null, false, false)
            ->cookie('auth_role', '', -1, '/', null, false, false);
    }
}
