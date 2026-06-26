<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_logs';

    protected $fillable = [
        'formID', 'WorkFlowID', 'EmployeeID', 'actual_approver_id', 'CreateTime', 'ApproveTime', 'Status', 'Comment',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'EmployeeID');
    }

    public function actualApprover()
    {
        return $this->belongsTo(User::class, 'actual_approver_id');
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'formID');
    }
}