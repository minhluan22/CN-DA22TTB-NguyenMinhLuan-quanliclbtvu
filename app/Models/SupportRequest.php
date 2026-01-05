<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportRequest extends Model
{
    protected $fillable = [
        'user_id',
        'club_id',
        'sender_type',
        'name',
        'email',
        'student_code',
        'subject',
        'content',
        'status',
        'priority',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Người gửi (User - nếu đã đăng nhập)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * CLB liên quan (nếu là chairman)
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Admin phản hồi
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Lấy tên người gửi
     */
    public function getSenderNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->name ?? 'Khách';
    }

    /**
     * Lấy email người gửi
     */
    public function getSenderEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
        return $this->email;
    }

    /**
     * Lấy label trạng thái
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'open' => 'Mở',
            'in_progress' => 'Đang xử lý',
            'resolved' => 'Đã giải quyết',
            'closed' => 'Đã đóng',
            default => 'Chưa xác định',
        };
    }

    /**
     * Lấy label mức độ ưu tiên
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            default => 'Trung bình',
        };
    }
}
