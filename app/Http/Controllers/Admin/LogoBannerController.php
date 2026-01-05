<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoBannerController extends Controller
{
    /**
     * Hiển thị form cấu hình Logo – Banner
     */
    public function index()
    {
        $configs = [
            'logo' => SystemConfig::getValue('logo', ''),
            'favicon' => SystemConfig::getValue('favicon', ''),
            'banner_home' => SystemConfig::getValue('banner_home', ''),
            'banner_login' => SystemConfig::getValue('banner_login', ''),
        ];

        return view('admin.system-config.logo', compact('configs'));
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048|mimes:jpeg,jpg,png,svg',
        ]);

        try {
            // Xóa logo cũ nếu có
            $oldLogo = SystemConfig::getValue('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Upload logo mới
            $path = $request->file('logo')->store('system/logo', 'public');
            SystemConfig::setValue('logo', $path, 'logo', 'string', 'Logo website');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật logo website',
                ['old_logo' => $oldLogo],
                ['new_logo' => $path]
            );

            return redirect()->back()->with('success', 'Upload logo thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi upload: ' . $e->getMessage());
        }
    }

    /**
     * Upload favicon
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|max:512|mimes:jpeg,jpg,png,ico,svg',
        ]);

        try {
            // Xóa favicon cũ nếu có
            $oldFavicon = SystemConfig::getValue('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            // Upload favicon mới
            $path = $request->file('favicon')->store('system/favicon', 'public');
            SystemConfig::setValue('favicon', $path, 'logo', 'string', 'Favicon website');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật favicon',
                ['old_favicon' => $oldFavicon],
                ['new_favicon' => $path]
            );

            return redirect()->back()->with('success', 'Upload favicon thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi upload: ' . $e->getMessage());
        }
    }

    /**
     * Upload banner trang chủ
     */
    public function uploadBannerHome(Request $request)
    {
        $request->validate([
            'banner_home' => 'required|image|max:5120|mimes:jpeg,jpg,png',
        ]);

        try {
            // Xóa banner cũ nếu có
            $oldBanner = SystemConfig::getValue('banner_home');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }

            // Upload banner mới
            $path = $request->file('banner_home')->store('system/banner', 'public');
            SystemConfig::setValue('banner_home', $path, 'logo', 'string', 'Banner trang chủ');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật banner trang chủ',
                ['old_banner' => $oldBanner],
                ['new_banner' => $path]
            );

            return redirect()->back()->with('success', 'Upload banner trang chủ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi upload: ' . $e->getMessage());
        }
    }

    /**
     * Upload banner trang đăng nhập
     */
    public function uploadBannerLogin(Request $request)
    {
        $request->validate([
            'banner_login' => 'required|image|max:5120|mimes:jpeg,jpg,png',
        ]);

        try {
            // Xóa banner cũ nếu có
            $oldBanner = SystemConfig::getValue('banner_login');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }

            // Upload banner mới
            $path = $request->file('banner_login')->store('system/banner', 'public');
            SystemConfig::setValue('banner_login', $path, 'logo', 'string', 'Banner trang đăng nhập');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật banner trang đăng nhập',
                ['old_banner' => $oldBanner],
                ['new_banner' => $path]
            );

            return redirect()->back()->with('success', 'Upload banner trang đăng nhập thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi upload: ' . $e->getMessage());
        }
    }

    /**
     * Xóa logo/banner
     */
    public function delete(Request $request, $type)
    {
        $allowedTypes = ['logo', 'favicon', 'banner_home', 'banner_login'];
        
        if (!in_array($type, $allowedTypes)) {
            return redirect()->back()->with('error', 'Loại file không hợp lệ!');
        }

        try {
            $oldPath = SystemConfig::getValue($type);
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            SystemConfig::setValue($type, '', 'logo', 'string');

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'delete',
                'SystemConfig',
                null,
                "Xóa {$type}",
                ['old_path' => $oldPath],
                null
            );

            return redirect()->back()->with('success', 'Xóa thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
}

