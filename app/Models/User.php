<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

  protected $fillable = [
    'username', 'password', 'full_name', 'email', 'role_id', 'status', 'DepartmentCode', 'department_name', 'job_title', 'delegate_id'
];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_id');
    }

    public function delegators()
    {
        return $this->hasMany(User::class, 'delegate_id');
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }
}