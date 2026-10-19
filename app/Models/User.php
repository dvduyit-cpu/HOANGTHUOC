<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'permissions',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canPublishJobs(): bool
    {
        return $this->hasPermission('jobs');
    }

    public const PERMISSIONS = [
        'jobs' => 'Quản lý tin tuyển dụng',
        'news' => 'Quản lý tin tức, bài viết',
        'content' => 'Chỉnh sửa nội dung website',
        'applications' => 'Xem, tải CV và xử lý hồ sơ ứng viên',
    ];

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active || ! array_key_exists($permission, self::PERMISSIONS)) {
            return false;
        }

        return $this->isAdmin() || ($this->role === 'editor'
            && in_array($permission, $this->permissions ?? ['jobs', 'news', 'content'], true));
    }
}
