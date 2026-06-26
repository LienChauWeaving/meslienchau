@extends('layouts.app')

@section('title', 'Danh sách đơn cần phê duyệt')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white pt-3 pb-0">
        <ul class="nav nav-tabs card-header-tabs" id="approvalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                    <i class="fa-solid fa-tasks me-1"></i> Đang chờ xử lý
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Lịch sử duyệt
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="approvalTabsContent">
            <!-- Tab Đang chờ xử lý -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã quy trình</th>
                            <th>Nhân viên</th>
                            <th>Phòng ban</th>
                            <th>Thời gian nghỉ</th>
                            <th>Lý do</th>
                            <th>Ngày gửi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingApprovals as $log)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('leaves.workflow', $log->leaveRequest->id) }}'" class="align-middle">
                            <td>
                                <code>{{ $log->WorkFlowID }}</code>
                                @if($log->leaveRequest->is_cancellation)
                                    <span class="badge bg-danger ms-1"><i class="fa-solid fa-ban"></i> Xin Hủy</span>
                                @endif
                                @if($log->EmployeeID != Auth::id())
                                    <br><span class="badge bg-warning text-dark mt-1" style="font-size:0.7rem;"><i class="fa-solid fa-user-clock"></i> Uỷ quyền từ {{ $log->approver->full_name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $log->leaveRequest->user->full_name }}</strong><br>
                                <small class="text-muted">{{ $log->leaveRequest->user->job_title ?? 'Chưa có chức danh' }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $log->leaveRequest->user->department_name ?? $log->leaveRequest->user->DepartmentCode ?? 'Chưa gán' }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($log->leaveRequest->start_date)->format('H:i d/m/Y') }} 
                                đến 
                                {{ \Carbon\Carbon::parse($log->leaveRequest->end_date)->format('H:i d/m/Y') }}
                            </td>
                            <td>{{ $log->leaveRequest->reason }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->CreateTime)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Hiện tại không có đơn nào cần bạn phê duyệt.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tab Lịch sử duyệt -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã quy trình</th>
                            <th>Nhân viên</th>
                            <th>Phòng ban</th>
                            <th>Thời gian nghỉ</th>
                            <th>Quyết định</th>
                            <th>Ngày duyệt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processedApprovals as $log)
                        <tr style="cursor: pointer;" onclick="window.location='{{ route('leaves.workflow', $log->leaveRequest->id) }}'" class="align-middle">
                            <td>
                                <code>{{ $log->WorkFlowID }}</code>
                                @if($log->leaveRequest->is_cancellation)
                                    <span class="badge bg-danger ms-1"><i class="fa-solid fa-ban"></i> Xin Hủy</span>
                                @endif
                                @if($log->actual_approver_id == Auth::id() && $log->EmployeeID != Auth::id())
                                    <br><span class="badge bg-warning text-dark mt-1" style="font-size:0.7rem;"><i class="fa-solid fa-user-clock"></i> Đã duyệt thay cho {{ $log->approver->full_name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $log->leaveRequest->user->full_name }}</strong><br>
                                <small class="text-muted">{{ $log->leaveRequest->user->job_title ?? 'Chưa có chức danh' }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $log->leaveRequest->user->department_name ?? $log->leaveRequest->user->DepartmentCode ?? 'Chưa gán' }}</span></td>
                            <td>
                                {{ \Carbon\Carbon::parse($log->leaveRequest->start_date)->format('H:i d/m/Y') }} 
                                đến 
                                {{ \Carbon\Carbon::parse($log->leaveRequest->end_date)->format('H:i d/m/Y') }}
                            </td>
                            <td>
                                @if($log->Status == 'Approved')
                                    <span class="badge bg-success"><i class="fa-solid fa-check"></i> Đã duyệt</span>
                                @elseif($log->Status == 'Rejected')
                                    <span class="badge bg-danger"><i class="fa-solid fa-xmark"></i> Từ chối</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->Status }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($log->ApproveTime)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có lịch sử phê duyệt.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection