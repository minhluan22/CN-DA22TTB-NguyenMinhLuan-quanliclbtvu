<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Event;


class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'student_code',
        'description',
        'activity_goals',
        'logo',
        'owner_id',
        'status',
        'field',
        'chairman',
        'members',
        'activity',
        'club_type',
        'establishment_date',
        'banner',
        'email',
        'fanpage',
        'phone',
        'social_links',
        'meeting_place',
        'meeting_schedule',
        'approval_mode',
        'activity_approval_mode',
        'is_public',
    ];

    /**
     * Chủ nhiệm CLB (User)
     * owner_id → users.id
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Một CLB có nhiều sự kiện (Event)
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    /**
     * Map lĩnh vực: tiếng Anh => tiếng Việt
     */
    private static function getFieldMap()
    {
        return [
            'academic' => 'Học thuật',
            'sports' => 'Thể thao',
            'arts' => 'Nghệ thuật',
            'volunteer' => 'Tình nguyện',
            'social' => 'Xã hội',
            'cultural' => 'Văn hóa',
            'technology' => 'Công nghệ',
            'business' => 'Kinh doanh',
            'science' => 'Khoa học',
            'music' => 'Âm nhạc',
            'dance' => 'Khiêu vũ',
            'photography' => 'Nhiếp ảnh',
            'literature' => 'Văn học',
            'theater' => 'Sân khấu',
            'other' => 'Khác',
        ];
    }

    /**
     * Static method: Chuyển đổi lĩnh vực từ tiếng Anh sang tiếng Việt
     * Có thể dùng ở bất kỳ đâu trong hệ thống
     */
    public static function getFieldDisplay($fieldValue)
    {
        if (!$fieldValue) {
            return 'Chưa xác định';
        }

        $fieldMap = self::getFieldMap();
        $lowerValue = strtolower(trim($fieldValue));
        
        // Nếu là tiếng Anh thì map sang tiếng Việt
        if (isset($fieldMap[$lowerValue])) {
            return $fieldMap[$lowerValue];
        }
        
        // Nếu là tiếng Việt (kiểm tra trong values) thì trả về luôn
        if (in_array($fieldValue, $fieldMap, true)) {
            return $fieldValue;
        }
        
        // Không match thì trả về nguyên giá trị
        return $fieldValue;
    }

    /**
     * Static method: Chuyển đổi lĩnh vực từ tiếng Việt về tiếng Anh (để lưu vào database)
     */
    public static function getFieldValue($displayValue)
    {
        if (!$displayValue) {
            return null;
        }

        $fieldMap = self::getFieldMap();
        $lowerDisplay = strtolower(trim($displayValue));
        
        // Tìm key (tiếng Anh) tương ứng với value (tiếng Việt)
        foreach ($fieldMap as $key => $value) {
            if (strtolower(trim($value)) === $lowerDisplay) {
                return $key;
            }
        }
        
        // Nếu đã là tiếng Anh (key trong map), trả về luôn
        if (isset($fieldMap[$lowerDisplay])) {
            return $lowerDisplay;
        }
        
        // Không match thì trả về nguyên giá trị (có thể là custom value)
        return $displayValue;
    }

    /**
     * Lấy danh sách các lĩnh vực (tiếng Việt) để dùng trong dropdown
     */
    public static function getFieldOptions()
    {
        return array_values(self::getFieldMap());
    }

    /**
     * Accessor: Lấy lĩnh vực hiển thị (tiếng Việt)
     */
    public function getFieldDisplayAttribute()
    {
        return self::getFieldDisplay($this->club_type ?? $this->field);
    }
}
