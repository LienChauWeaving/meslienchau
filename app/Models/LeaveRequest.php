<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'user_id', 'leave_type', 'start_date', 'end_date', 'reason', 'attachments', 'status', 'is_cancellation',
    ];

    // Tự động chuyển JSON từ DB thành Array khi lấy ra và ngược lại
    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class, 'formID');
    }
}