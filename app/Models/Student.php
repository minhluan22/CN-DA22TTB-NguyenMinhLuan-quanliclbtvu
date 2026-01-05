<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    // Các cột cho phép fill (tuỳ DB của bạn)
    protected $fillable = [
        'mssv',
        'name',
        'class',
        'email',
        'phone',
        // thêm các cột khác nếu bạn có
    ];

    /**
     * Một sinh viên có thể làm chủ nhiệm của nhiều câu lạc bộ
     * Quan hệ: Student 1 - n Club (owner_id)
     */
    public function clubsOwned()
    {
        return $this->hasMany(Club::class, 'owner_id');
    }
}
