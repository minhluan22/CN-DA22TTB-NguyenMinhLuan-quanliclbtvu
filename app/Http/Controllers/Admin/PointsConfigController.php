<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class PointsConfigController extends Controller
{
    /**
     * Hiển thị form cấu hình điểm hoạt động
     */
    public function index()
    {
        $configs = [
            'points_attend_event' => SystemConfig::getValue('points_attend_event', 10),
            'points_organize_event' => SystemConfig::getValue('points_organize_event', 20),
            'points_regular_reward' => SystemConfig::getValue('points_regular_reward', 5),
            'points_violation_deduct' => SystemConfig::getValue('points_violation_deduct', -10),
            'points_limit_semester' => SystemConfig::getValue('points_limit_semester', 100),
            'points_limit_year' => SystemConfig::getValue('points_limit_year', 200),
        ];

        return view('admin.system-config.points', compact('configs'));
    }

    /**
     * Lưu cấu hình điểm hoạt động
     */
    public function update(Request $request)
    {
        $request->validate([
            'points_attend_event' => 'required|integer|min:0|max:100',
            'points_organize_event' => 'required|integer|min:0|max:100',
            'points_regular_reward' => 'required|integer|min:0|max:50',
            'points_violation_deduct' => 'required|integer|min:-100|max:0',
            'points_limit_semester' => 'required|integer|min:0|max:1000',
            'points_limit_year' => 'required|integer|min:0|max:2000',
        ], [
            'points_attend_event.required' => 'Điểm tham gia sự kiện là bắt buộc',
            'points_attend_event.integer' => 'Điểm tham gia sự kiện phải là số nguyên',
            'points_attend_event.min' => 'Điểm tham gia sự kiện phải >= 0',
            'points_attend_event.max' => 'Điểm tham gia sự kiện phải <= 100',
            'points_organize_event.required' => 'Điểm tổ chức hoạt động là bắt buộc',
            'points_organize_event.integer' => 'Điểm tổ chức hoạt động phải là số nguyên',
            'points_organize_event.min' => 'Điểm tổ chức hoạt động phải >= 0',
            'points_organize_event.max' => 'Điểm tổ chức hoạt động phải <= 100',
            'points_regular_reward.required' => 'Điểm thưởng thường xuyên là bắt buộc',
            'points_regular_reward.integer' => 'Điểm thưởng thường xuyên phải là số nguyên',
            'points_regular_reward.min' => 'Điểm thưởng thường xuyên phải >= 0',
            'points_regular_reward.max' => 'Điểm thưởng thường xuyên phải <= 50',
            'points_violation_deduct.required' => 'Điểm trừ vi phạm là bắt buộc',
            'points_violation_deduct.integer' => 'Điểm trừ vi phạm phải là số nguyên',
            'points_violation_deduct.min' => 'Điểm trừ vi phạm phải >= -100',
            'points_violation_deduct.max' => 'Điểm trừ vi phạm phải <= 0',
            'points_limit_semester.required' => 'Giới hạn điểm theo học kỳ là bắt buộc',
            'points_limit_semester.integer' => 'Giới hạn điểm theo học kỳ phải là số nguyên',
            'points_limit_semester.min' => 'Giới hạn điểm theo học kỳ phải >= 0',
            'points_limit_semester.max' => 'Giới hạn điểm theo học kỳ phải <= 1000',
            'points_limit_year.required' => 'Giới hạn điểm theo năm học là bắt buộc',
            'points_limit_year.integer' => 'Giới hạn điểm theo năm học phải là số nguyên',
            'points_limit_year.min' => 'Giới hạn điểm theo năm học phải >= 0',
            'points_limit_year.max' => 'Giới hạn điểm theo năm học phải <= 2000',
        ]);

        try {
            $oldValues = [];
            $newValues = [];

            foreach ($request->only([
                'points_attend_event', 'points_organize_event', 'points_regular_reward',
                'points_violation_deduct', 'points_limit_semester', 'points_limit_year'
            ]) as $key => $value) {
                $oldValue = SystemConfig::getValue($key);
                $oldValues[$key] = $oldValue;
                
                SystemConfig::setValue($key, $value, 'points', 'integer');
                $newValues[$key] = $value;
            }

            // Ghi log
            AdminLog::createLog(
                auth()->id(),
                'update',
                'SystemConfig',
                null,
                'Cập nhật cấu hình điểm hoạt động',
                $oldValues,
                $newValues
            );

            return redirect()->back()->with('success', 'Cập nhật cấu hình điểm hoạt động thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }
}

