<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'student_code',
        'role_id',
        'status',
        'password',
        'last_activity',
        'phone',
        'gender',
        'date_of_birth',
        'department',
        'class',
        'bio',
        'avatar',
        'two_factor_enabled',
        'devices',
        'email_notifications',
        'event_notifications',
        'club_notifications',
        'language',
        'dark_mode',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_activity'     => 'datetime',
        'two_factor_enabled' => 'boolean',
        'devices' => 'array',
        'email_notifications' => 'boolean',
        'event_notifications' => 'boolean',
        'club_notifications' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    /**
     * User thuộc một Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Lấy tên vai trò (role_name)
     */
    public function getRoleNameAttribute()
    {
        return $this->role->name ?? '—';
    }

    /**
     * Lấy label trạng thái
     */
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Hoạt động' : 'Đã khóa';
    }

    /**
     * Kiểm tra user có phải role nào không
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }
}
