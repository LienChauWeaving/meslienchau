@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100 border-primary" style="border-top: 4px solid #0d6efd;">
            <div class="card-body">
                <h4 class="card-title text-primary"><i class="fa-solid fa-users me-2"></i> Nhân sự</h4>
                <div class="list-group list-group-flush mt-3">
                    <a href="{{ route('leaves.index') }}" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-calendar-alt text-secondary me-2"></i> Đơn xin nghỉ phép</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </a>
                    
                    @if(\App\Models\UserPermission::where('user_id', Auth::id())->where('permission', 'HR_LEAVES')->exists())
                    <a href="{{ route('leaves.all') }}" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-file-lines text-secondary me-2"></i> Tất cả đơn nghỉ phép</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </a>
                    @endif
                    
                    @if(Auth::check() && !Auth::guard('admin')->check())
                    @php
                        $delegators = \App\Models\User::where('delegate_id', Auth::id())->pluck('id')->toArray();
                        $validIds = array_merge([Auth::id()], $delegators);
                        $isApprover = \App\Models\ApproverList::whereIn('employeeID', $validIds)->exists() || count($delegators) > 0;
                    @endphp
                    @if($isApprover)
                        @php
                            $pendingCount = \App\Models\ApprovalLog::whereIn('EmployeeID', $validIds)
                                                                   ->where('Status', 'Pending')
                                                                   ->count();
                        @endphp
                        <a href="{{ route('approvals.index') }}" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-user-check text-secondary me-2"></i> Phê duyệt đơn</span>
                            @if($pendingCount > 0)
                                <span class="badge bg-danger rounded-pill">{{ $pendingCount }}</span>
                            @else
                                <i class="fa-solid fa-chevron-right text-muted"></i>
                            @endif
                        </a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100 border-success" style="border-top: 4px solid #198754;">
            <div class="card-body">
                <h4 class="card-title text-success"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Kế toán</h4>
                <div class="list-group list-group-flush mt-3">
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center text-muted">
                        <span><i class="fa-solid fa-receipt text-secondary me-2"></i> Thanh toán (Sắp ra mắt)</span>
                        <i class="fa-solid fa-lock"></i>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center text-muted">
                        <span><i class="fa-solid fa-money-bill-wave text-secondary me-2"></i> Tạm ứng (Sắp ra mắt)</span>
                        <i class="fa-solid fa-lock"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::guard('admin')->check())
    <div class="col-md-6 mb-3">
        <div class="card shadow-sm h-100 border-info" style="border-top: 4px solid #0dcaf0;">
            <div class="card-body">
                <h4 class="card-title text-info"><i class="fa-solid fa-user-shield me-2"></i> Không gian Admin</h4>
                <div class="list-group list-group-flush mt-3">
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-users text-secondary me-2"></i> Quản lý Users</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.approvers.index') }}" class="list-group-item list-group-item-action border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user-shield text-secondary me-2"></i> Cấu hình phê duyệt</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(Auth::check() && !Auth::guard('admin')->check())
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-dark text-white shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Tổng ngày nghỉ của bạn</h5>
                <h2>0 <small class="fs-6">ngày</small></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white"><i class="fa-solid fa-clock-rotate-left me-1"></i> Đơn xin nghỉ phép gần đây</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Từ ngày</th>
                    <th>Đến ngày</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLeaves ?? [] as $leave)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('leaves.workflow', $leave->id) }}'">
                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                    <td>{{ $leave->reason }}</td>
                    <td>
                        <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Rejected' ? 'danger' : ($leave->status == 'Cancelled' ? 'dark' : 'warning text-dark')) }}">
                            {{ $leave->status == 'Cancelled' ? 'Đã hủy' : $leave->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection