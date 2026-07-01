<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'campus_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Map of roles to their permitted abilities.
     */
    public static array $rolePermissions = [
        'super_admin' => [
            'manage-campuses',
            'manage-admins',
            'manage-teachers',
            'manage-classes',
            'configure-inspections',
            'inspect-admins',
            'view-monitoring',
            'export-reports',
            'update-profile',
        ],
        'admin' => [
            'view-dashboard',
            'view-monitoring',
            'manage-campus-teachers',
            'inspect-teachers',
            'add-remarks',
            'inspect-campus',
            'view-performance',
            'export-campus-reports',
            'update-profile',
        ],
        'teacher' => [
            'view-dashboard',
            'view-profile',
            'view-scores',
            'view-own-inspections',
            'update-profile',
        ],
    ];

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = self::$rolePermissions[$this->role] ?? [];
        return in_array($permission, $permissions);
    }

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    // Relationships
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'teacher_id');
    }

    public function madeInspections()
    {
        return $this->hasMany(Inspection::class, 'inspector_id');
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class, 'teacher_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
