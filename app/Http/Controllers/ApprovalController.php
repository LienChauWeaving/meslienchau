<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\LeaveRequest;
use App\Models\ApproverList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $delegators = \App\Models\User::where('delegate_id', $userId)->pluck('id')->toArray();
        $validIds = array_merge([$userId], $delegators);

        $pendingApprovals = ApprovalLog::whereIn('EmployeeID', $validIds)
                                       ->where('Status', 'Pending')
                                       ->with('leaveRequest.user', 'approver')
                                       ->orderBy('CreateTime', 'desc')
                                       ->get();

        $processedApprovals = ApprovalLog::whereIn('actual_approver_id', [$userId])
                                         ->orWhere(function($query) use ($userId, $delegators) {
                                             $query->whereIn('EmployeeID', array_merge([$userId], $delegators))
                                                   ->whereIn('Status', ['Approved', 'Rejected']);
                                         })
                                         ->with('leaveRequest.user', 'actualApprover')
                                         ->orderBy('ApproveTime', 'desc')
                                         ->get();

        return view('approvals.index', compact('pendingApprovals', 'processedApprovals'));
    }

    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|string|max:500',
        ]);

        $userId = Auth::id();
        $delegators = \App\Models\User::where('delegate_id', $userId)->pluck('id')->toArray();
        $validIds = array_merge([$userId], $delegators);

        $log = ApprovalLog::where('id', $id)->whereIn('EmployeeID', $validIds)->firstOrFail();
        $leaveRequest = LeaveRequest::with('user')->find($log->formID);

        // Đóng log hiện tại
        $actualApproverId = ($log->EmployeeID != $userId) ? $userId : null;

        $log->update([
            'ApproveTime' => now(),
            'Status' => $request->action,
            'Comment' => $request->comment,
            'actual_approver_id' => $actualApproverId,
        ]);

        if ($request->action == 'Rejected') {
            // Nếu từ chối 
            if ($leaveRequest) {
                if ($leaveRequest->is_cancellation) {
                    $leaveRequest->update(['status' => 'Approved', 'is_cancellation' => false]);
                } else {
                    $leaveRequest->update(['status' => 'Rejected']);
                }
            }
        } else {
            // Nếu Chấp thuận -> Tìm xem có cấp duyệt tiếp theo không
            $currentApproverSetup = ApproverList::where('DepartmentCode', $leaveRequest->user->DepartmentCode)
                                                ->where('employeeID', $log->EmployeeID)
                                                ->first();

            if ($currentApproverSetup) {
                // Tìm cấp lớn hơn tiếp theo
                $nextApprover = ApproverList::where('DepartmentCode', $leaveRequest->user->DepartmentCode)
                                            ->where('level', '>', $currentApproverSetup->level)
                                            ->orderBy('level', 'asc')
                                            ->first();

                if ($nextApprover) {
                    // Tạo Pending task cho cấp tiếp theo (Trạng thái đơn vẫn là Pending)
                    ApprovalLog::create([
                        'formID' => $leaveRequest->id,
                        'WorkFlowID' => $log->WorkFlowID,
                        'EmployeeID' => $nextApprover->employeeID,
                        'CreateTime' => now(),
                        'Status' => 'Pending',
                    ]);
                } else {
                    // Đây là cấp cuối cùng -> Đơn được thông qua hoàn toàn
                    if ($leaveRequest) {
                        if ($leaveRequest->is_cancellation) {
                            $leaveRequest->update(['status' => 'Cancelled']);
                        } else {
                            $leaveRequest->update(['status' => 'Approved']);
                        }
                    }
                }
            } else {
                // Đề phòng lỗi cấu hình thì pass luôn
                if ($leaveRequest) {
                    if ($leaveRequest->is_cancellation) {
                        $leaveRequest->update(['status' => 'Cancelled']);
                    } else {
                        $leaveRequest->update(['status' => 'Approved']);
                    }
                }
            }
        }

        return redirect()->route('approvals.index')->with('success', 'Xử lý phê duyệt thành công!');
    }
}