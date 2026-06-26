<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\ApprovalLog;
use App\Models\ApproverList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaves = LeaveRequest::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

  public function store(Request $request)
    {
        $dateOnlyTypes = ['Thai sản', 'Kết hôn', 'Tang chế'];
        if (in_array($request->leave_type, $dateOnlyTypes)) {
            $request->merge([
                'start_hour' => '00',
                'start_minute' => '00',
                'end_hour' => '23',
                'end_minute' => '59',
            ]);
        }
        
        $request->merge([
            'start_date' => $request->start_date_only . ' ' . $request->start_hour . ':' . $request->start_minute . ':00',
            'end_date' => $request->end_date_only . ' ' . $request->end_hour . ':' . $request->end_minute . ':00',
        ]);
       $request->validate([
            'leave_type' => 'required|string|max:100', // Validate loại phép
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB mỗi tệp
        ]);

        $user = Auth::user();
        $start = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $end = \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s');

        // Kiểm tra trùng thời gian
        $hasOverlap = LeaveRequest::where('user_id', $user->id)
            ->whereNotIn('status', ['Rejected', 'Cancelled'])
            ->where(function ($query) use ($start, $end) {
                $query->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
            })->exists();

        if ($hasOverlap) {
            return back()->withInput()->withErrors(['start_date' => 'Thời gian nghỉ phép bị trùng lấp với một đơn khác của bạn.']);
        }

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Tạo tên file an toàn (Thời gian + Tên gốc)
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
                // Lưu vào thư mục storage/app/public/leave_attachments
                $path = $file->storeAs('leave_attachments', $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }


        $countToday = LeaveRequest::whereDate('created_at', now()->toDateString())->count();
        $sequence = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
        $department = $user->DepartmentCode ?? 'KXD';
        $workflowId = 'NSQT04' . now()->format('Ymd') . $department . $sequence;

        $leaveRequest = LeaveRequest::create([
            'id' => $workflowId,
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s'),
            'reason' => $request->reason,
            'attachments' => count($attachmentPaths) > 0 ? $attachmentPaths : null,
            'status' => 'Pending',
        ]);

        ApprovalLog::create([
            'formID' => $leaveRequest->id,
            'WorkFlowID' => $workflowId,
            'EmployeeID' => $user->id,
            'CreateTime' => now(),
            'ApproveTime' => now(),
            'Status' => 'Submitted',
            'Comment' => 'Khởi tạo đơn xin nghỉ phép',
        ]);

        $isApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                  ->where('employeeID', $user->id)
                                  ->first();

        if ($isApprover) {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->where('level', '>', $isApprover->level)
                                        ->orderBy('level', 'asc')
                                        ->first();
                                        
            // [CẬP NHẬT]: Nếu không có cấp nào cao hơn (mình là cấp cao nhất), tự chuyển đơn cho chính mình duyệt
            if (!$nextApprover) {
                $nextApprover = $isApprover;
            }
        } else {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->orderBy('level', 'asc')
                                        ->first();
        }

        if ($nextApprover) {
            ApprovalLog::create([
                'formID' => $leaveRequest->id,
                'WorkFlowID' => $workflowId,
                'EmployeeID' => $nextApprover->employeeID,
                'CreateTime' => now(),
                'Status' => 'Pending',
            ]);
        } else {
            $leaveRequest->update(['status' => 'Approved']);
        }

        return redirect()->route('leaves.index')->with('success', 'Đã gửi đơn xin nghỉ phép thành công!');
    }
public function workflow($id)
    {
        $leave = LeaveRequest::with('user')->findOrFail($id);

        $logs = ApprovalLog::where('formID', $id)
                           ->with('approver', 'actualApprover')
                           ->orderBy('id', 'asc')
                           ->get();

        $userId = Auth::id();
        $user = Auth::user();
        
        $delegators = \App\Models\User::where('delegate_id', $userId)->pluck('id')->toArray();
        $validIds = array_merge([$userId], $delegators);

        // Nếu user hiện tại đang uỷ quyền cho người khác, họ không được tự duyệt đơn được gán cho chính họ nữa
        if ($user->delegate_id) {
            $validIds = array_diff($validIds, [$userId]);
        }

        $pendingLog = $logs->whereIn('EmployeeID', $validIds)
                           ->where('Status', 'Pending')
                           ->first();

        return view('leaves.workflow', compact('leave', 'logs', 'pendingLog'));
    }
    // [THÊM MỚI]: Hàm xử lý gửi lại đơn bị từ chối
    public function resubmit(Request $request, $id)
    {
        $dateOnlyTypes = ['Thai sản', 'Kết hôn', 'Tang chế'];
        if (in_array($request->leave_type, $dateOnlyTypes)) {
            $request->merge([
                'start_hour' => '00',
                'start_minute' => '00',
                'end_hour' => '23',
                'end_minute' => '59',
            ]);
        }

        $request->merge([
            'start_date' => $request->start_date_only . ' ' . $request->start_hour . ':' . $request->start_minute . ':00',
            'end_date' => $request->end_date_only . ' ' . $request->end_hour . ':' . $request->end_minute . ':00',
        ]);
        $request->validate([
            'leave_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $start = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $end = \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s');

        // Kiểm tra trùng thời gian (ngoại trừ đơn hiện tại đang sửa)
        $hasOverlap = LeaveRequest::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['Rejected', 'Cancelled'])
            ->where(function ($query) use ($start, $end) {
                $query->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
            })->exists();

        if ($hasOverlap) {
            return back()->withInput()->withErrors(['start_date' => 'Thời gian nghỉ phép bị trùng lấp với một đơn khác của bạn.']);
        }

        $leaveRequest = LeaveRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($leaveRequest->status != 'Rejected') {
            abort(403, 'Chỉ có thể chỉnh sửa và gửi lại đơn khi bị từ chối.');
        }
        $dataToUpdate = [
            'leave_type' => $request->leave_type,
            'start_date' => \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s'),
            'reason' => $request->reason,
            'status' => 'Pending',
        ];

        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('leave_attachments', $filename, 'public');
                $attachmentPaths[] = $path;
            }
            $dataToUpdate['attachments'] = $attachmentPaths;
        }
        $leaveRequest->update($dataToUpdate);

        $user = Auth::user();

        // Lấy lại mã quy trình cũ
        $oldLog = ApprovalLog::where('formID', $leaveRequest->id)->first();
        $workflowId = $oldLog ? $oldLog->WorkFlowID : ('NSQT04' . now()->format('Ymd') . ($user->DepartmentCode ?? 'KXD') . '001');

        // Ghi log hành động gửi lại
        ApprovalLog::create([
            'formID' => $leaveRequest->id,
            'WorkFlowID' => $workflowId,
            'EmployeeID' => $user->id,
            'CreateTime' => now(),
            'ApproveTime' => now(),
            'Status' => 'Submitted',
            'Comment' => 'Cập nhật và gửi lại đơn xin nghỉ phép',
        ]);

        // Tính toán lại người phê duyệt (Luồng duyệt lại từ đầu)
        $isApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                  ->where('employeeID', $user->id)
                                  ->first();

        if ($isApprover) {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->where('level', '>', $isApprover->level)
                                        ->orderBy('level', 'asc')
                                        ->first();
            if (!$nextApprover) {
                $nextApprover = $isApprover;
            }
        } else {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->orderBy('level', 'asc')
                                        ->first();
        }

        if ($nextApprover) {
            ApprovalLog::create([
                'formID' => $leaveRequest->id,
                'WorkFlowID' => $workflowId,
                'EmployeeID' => $nextApprover->employeeID,
                'CreateTime' => now(),
                'Status' => 'Pending',
            ]);
        } else {
            $leaveRequest->update(['status' => 'Approved']);
        }

        return redirect()->back()->with('success', 'Đã cập nhật và gửi lại đơn phê duyệt thành công!');
    }

    public function requestCancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        $leaveRequest = LeaveRequest::where('user_id', Auth::id())->findOrFail($id);

        if ($leaveRequest->status != 'Approved') {
            abort(403, 'Chỉ có thể xin hủy đơn đã hoàn thành (Approved).');
        }

        $leaveRequest->update([
            'status' => 'Pending',
            'is_cancellation' => true
        ]);

        $user = Auth::user();

        // Khởi tạo lại luồng duyệt từ đầu
        $workflowId = $leaveRequest->id;

        ApprovalLog::create([
            'formID' => $leaveRequest->id,
            'WorkFlowID' => $workflowId,
            'EmployeeID' => $user->id,
            'CreateTime' => now(),
            'ApproveTime' => now(),
            'Status' => 'CancelSubmitted',
            'Comment' => 'Yêu cầu hủy đơn: ' . $request->cancel_reason,
        ]);

        // Tìm cấp duyệt đầu tiên
        $isApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                  ->where('employeeID', $user->id)
                                  ->first();

        if ($isApprover) {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->where('level', '>', $isApprover->level)
                                        ->orderBy('level', 'asc')
                                        ->first();
            if (!$nextApprover) {
                $nextApprover = $isApprover;
            }
        } else {
            $nextApprover = ApproverList::where('DepartmentCode', $user->DepartmentCode)
                                        ->orderBy('level', 'asc')
                                        ->first();
        }

        if ($nextApprover) {
            ApprovalLog::create([
                'formID' => $leaveRequest->id,
                'WorkFlowID' => $workflowId,
                'EmployeeID' => $nextApprover->employeeID,
                'CreateTime' => now(),
                'Status' => 'Pending',
            ]);
        } else {
            // Nếu không có người duyệt thì tự động cập nhật là Cancelled luôn
            $leaveRequest->update(['status' => 'Cancelled']);
        }

        return redirect()->back()->with('success', 'Đã gửi yêu cầu hủy đơn thành công! Vui lòng chờ phê duyệt.');
    }

    public function allLeaves(Request $request)
    {
        // Kiểm tra xem user có quyền HR không
        if (!\App\Models\UserPermission::where('user_id', Auth::id())->where('permission', 'HR_LEAVES')->exists()) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $query = \App\Models\LeaveRequest::with(['user', 'approvalLogs' => function($q) {
            $q->whereIn('Status', ['Confirmed', 'Confirmed_Cancel']);
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('DepartmentCode', $request->department);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $departments = \App\Models\User::whereNotNull('DepartmentCode')
            ->where('DepartmentCode', '!=', '')
            ->distinct()
            ->pluck('DepartmentCode');

        return view('leaves.all', compact('leaves', 'departments'));
    }

    public function confirmLeave(Request $request, $id)
    {
        // Kiểm tra xem user có quyền HR không
        if (!\App\Models\UserPermission::where('user_id', Auth::id())->where('permission', 'HR_LEAVES')->exists()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $leave = \App\Models\LeaveRequest::findOrFail($id);
        
        if (!in_array($leave->status, ['Approved', 'Cancelled'])) {
            abort(403, 'Chỉ có thể xác nhận đơn đã hoàn tất phê duyệt hoặc đã hủy.');
        }

        $latestLog = \App\Models\ApprovalLog::where('formID', $leave->id)->first();
        $workflowId = $latestLog ? $latestLog->WorkFlowID : $leave->id;

        $confirmStatus = $leave->status == 'Cancelled' ? 'Confirmed_Cancel' : 'Confirmed';

        // Kiểm tra xem đã có xác nhận cho trạng thái hiện tại chưa
        $isConfirmed = \App\Models\ApprovalLog::where('formID', $leave->id)
                                              ->where('Status', $confirmStatus)
                                              ->exists();
        if ($isConfirmed) {
            return redirect()->back()->with('success', 'Đơn đã được xác nhận trước đó.');
        }

        \App\Models\ApprovalLog::create([
            'formID' => $leave->id,
            'WorkFlowID' => $workflowId,
            'EmployeeID' => Auth::id(),
            'CreateTime' => now(),
            'ApproveTime' => now(),
            'Status' => $confirmStatus,
            'Comment' => $leave->status == 'Cancelled' ? 'Xác nhận đơn đã hủy' : 'Xác nhận nghỉ phép',
        ]);

        return redirect()->back()->with('success', 'Đã xác nhận nghỉ phép thành công!');
    }
}