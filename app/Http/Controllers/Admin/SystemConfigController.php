<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SystemConfigController extends Controller
{
    /**
     * Hiển thị form cấu hình website
     */
    public function website()
    {
        $configs = [
            'website_name' => SystemConfig::getValue('website_name', 'Hệ thống quản lý CLB'),
            'website_description' => SystemConfig::getValue('website_description', ''),
            'website_keywords' => SystemConfig::getValue('website_keywords', ''),
            'website_author' => SystemConfig::getValue('website_author', ''),
            'website_email' => SystemConfig::getValue('website_email', ''),
            'website_phone' => SystemConfig::getValue('website_phone', ''),
            'website_address' => SystemConfig::getValue('website_address', ''),
            'website_footer' => SystemConfig::getValue('website_footer', ''),
        ];

        return view('admin.system-config.website', compact('configs'));
    }

    /**
     * Lưu cấu hình website
     */
    public function updateWebsite(Request $request)
    {
        $request->validate([
            'website_name' => 'required|string|max:255',
            'website_description' => 'nullable|string',
            'website_keywords' => 'nullable|string',
            'website_author' => 'nullable|string|max:255',
            'website_email' => 'nullable|email|max:255',
            'website_phone' => 'nullable|string|max:20',
            'website_address' => 'nullable|string',
            'website_footer' => 'nullable|string',
        ]);

        try {
            $oldValues = [];
            $newValues = [];

            foreach ($request->only([
                'website_name', 'website_description', 'website_keywords',
                'website_author', 'website_email', 'website_phone',
                'website_address', 'website_footer'
            ]) as $key => $value) {
                $oldValue = SystemConfig::getValue($key);
                $oldValues[$key] = $oldValue;
                
                SystemConfig::setValue($key, $value, 'website', 'string');
                $newValues[$key] = $value;
            }

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật cấu hình website',
                $oldValues,
                $newValues
            );

            return redirect()->back()->with('success', 'Cập nhật cấu hình website thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form cấu hình email
     */
    public function email()
    {
        $configs = [
            'mail_mailer' => SystemConfig::getValue('mail_mailer', config('mail.default')),
            'mail_host' => SystemConfig::getValue('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => SystemConfig::getValue('mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => SystemConfig::getValue('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => SystemConfig::getValue('mail_password', config('mail.mailers.smtp.password')),
            'mail_encryption' => SystemConfig::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => SystemConfig::getValue('mail_from_address', config('mail.from.address')),
            'mail_from_name' => SystemConfig::getValue('mail_from_name', config('mail.from.name')),
        ];

        return view('admin.system-config.email', compact('configs'));
    }

    /**
     * Lưu cấu hình email
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string|in:smtp,mailgun,ses,postmark,log,array',
            'mail_host' => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_mailer,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            $oldValues = [];
            $newValues = [];

            foreach ($request->only([
                'mail_mailer', 'mail_host', 'mail_port',
                'mail_username', 'mail_password', 'mail_encryption',
                'mail_from_address', 'mail_from_name'
            ]) as $key => $value) {
                $oldValue = SystemConfig::getValue($key);
                $oldValues[$key] = $oldValue ? '***' : null; // Ẩn password trong log
                
                SystemConfig::setValue($key, $value, 'email', 'string');
                $newValues[$key] = $key === 'mail_password' ? '***' : $value;
            }

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật cấu hình email',
                $oldValues,
                $newValues
            );

            return redirect()->back()->with('success', 'Cập nhật cấu hình email thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * Test gửi email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Lấy cấu hình email từ SystemConfig
            $mailConfig = [
                'mail_mailer' => SystemConfig::getValue('mail_mailer', config('mail.default')),
                'mail_host' => SystemConfig::getValue('mail_host', config('mail.mailers.smtp.host')),
                'mail_port' => SystemConfig::getValue('mail_port', config('mail.mailers.smtp.port')),
                'mail_username' => SystemConfig::getValue('mail_username', config('mail.mailers.smtp.username')),
                'mail_password' => SystemConfig::getValue('mail_password', config('mail.mailers.smtp.password')),
                'mail_encryption' => SystemConfig::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
                'mail_from_address' => SystemConfig::getValue('mail_from_address', config('mail.from.address')),
                'mail_from_name' => SystemConfig::getValue('mail_from_name', config('mail.from.name')),
            ];

            // Cập nhật config tạm thời
            config([
                'mail.default' => $mailConfig['mail_mailer'],
                'mail.mailers.smtp.host' => $mailConfig['mail_host'],
                'mail.mailers.smtp.port' => $mailConfig['mail_port'],
                'mail.mailers.smtp.username' => $mailConfig['mail_username'],
                'mail.mailers.smtp.password' => $mailConfig['mail_password'],
                'mail.mailers.smtp.encryption' => $mailConfig['mail_encryption'],
                'mail.from.address' => $mailConfig['mail_from_address'],
                'mail.from.name' => $mailConfig['mail_from_name'],
            ]);

            // Gửi email test
            Mail::raw('Đây là email test từ hệ thống quản lý CLB. Nếu bạn nhận được email này, cấu hình email đã hoạt động đúng.', function ($message) use ($request, $mailConfig) {
                $message->to($request->test_email)
                    ->subject('Test Email - Hệ thống quản lý CLB');
            });

            return redirect()->back()->with('success', "Đã gửi email test đến {$request->test_email}. Vui lòng kiểm tra hộp thư!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi gửi email test: ' . $e->getMessage());
        }
    }
}

