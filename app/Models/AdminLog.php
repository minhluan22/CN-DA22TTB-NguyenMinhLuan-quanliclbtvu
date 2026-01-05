<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Quan hệ với Admin
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Helper để tạo log
     */
    public static function createLog($adminId, $action, $modelType, $modelId = null, $description = null, $oldData = null, $newData = null)
    {
        return self::create([
            'admin_id' => $adminId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Lấy tên hành động dạng tiếng Việt
     */
    public function getActionNameAttribute()
    {
        return match($this->action) {
            'create' => 'Thêm mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'approve' => 'Phê duyệt',
            'reject' => 'Từ chối',
            'enable' => 'Kích hoạt',
            'disable' => 'Vô hiệu hóa',
            'backup' => 'Sao lưu',
            'restore' => 'Khôi phục',
            default => $this->action,
        };
    }

    /**
     * Lấy tên model dạng tiếng Việt
     */
    public function getModelNameAttribute()
    {
        return match($this->model_type) {
            'User' => 'Tài khoản',
            'Club' => 'Câu lạc bộ',
            'Activity' => 'Hoạt động',
            'Event' => 'Sự kiện',
            'Regulation' => 'Nội quy',
            'Violation' => 'Vi phạm',
            'SystemConfig' => 'Cấu hình hệ thống',
            'Notification' => 'Thông báo',
            default => $this->model_type,
        };
    }
}
