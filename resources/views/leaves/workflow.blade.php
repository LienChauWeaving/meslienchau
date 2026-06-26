@extends('layouts.app')

@section('title', 'Lưu trình phê duyệt đơn ' . $leave->id)

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-file-lines me-1 text-primary"></i> Thông tin đơn xin nghỉ phép 
        @if($leave->is_cancellation)
            <span class="badge bg-danger ms-2"><i class="fa-solid fa-ban"></i> ĐANG XIN HỦY</span>
        @endif
    </div>
    <div class="card-body">
       <div class="row">
            <div class="col-md-6 mb-2">
                <strong>Người tạo đơn:</strong> {{ $leave->user->full_name ?? 'N/A' }} 
                @if(!empty($leave->user->job_title))
                    <span class="text-muted small">({{ $leave->user->job_title }})</span>
                @endif
                <span class="badge bg-secondary ms-1">{{ $leave->user->department_name ?? $leave->user->DepartmentCode ?? '' }}</span>
            </div>
            <div class="col-md-6 mb-2">
                <strong>Loại phép:</strong> <span class="text-primary fw-bold">{{ $leave->leave_type ?? 'Không xác định' }}</span>
            </div>
            <div class="col-md-6 mb-2">
                <strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('H:i d/m/Y') }} đến {{ \Carbon\Carbon::parse($leave->end_date)->format('H:i d/m/Y') }}
            </div>
            <div class="col-md-6 mb-2">
                <strong>Trạng thái chung:</strong> 
                <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Rejected' ? 'danger' : ($leave->status == 'Cancelled' ? 'dark' : 'warning text-dark')) }}">
                    {{ $leave->status == 'Cancelled' ? 'Đã hủy (Cancelled)' : $leave->status }}
                </span>
            </div>
            <div class="col-md-12 mt-2">
                <strong>Lý do nghỉ:</strong> <em>{{ $leave->reason }}</em>
            </div>
            
            @if(!empty($leave->attachments))
            <div class="col-md-12 mt-3 p-3 bg-light rounded">
                <strong><i class="fa-solid fa-paperclip me-1"></i> Tệp đính kèm ({{ count($leave->attachments) }} tệp):</strong>
                <div class="d-flex flex-wrap mt-2">
                    @foreach($leave->attachments as $file)
                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2 mb-2">
                            <i class="fa-solid fa-download"></i> {{ basename($file) }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-route me-1 text-success"></i> Tiến trình xử lý (Workflow)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Bước</th>
                        <th>Người xử lý</th>
                        <th>Phòng ban</th>
                        <th>Thời gian nhận</th>
                        <th>Thời gian duyệt</th>
                        <th>Hành động</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                    <tr>
                        <td class="ps-3"><strong>#{{ $index + 1 }}</strong></td>
                        <td>
                            <strong>{{ $log->approver->full_name ?? 'N/A' }}</strong><br>
                            <small class="text-muted">{{ $log->approver->job_title ?? 'Chưa có chức danh' }}</small>
                            @if($log->actual_approver_id && $log->actual_approver_id != $log->EmployeeID)
                                <br><span class="badge bg-warning text-dark mt-1" style="font-size:0.7rem;"><i class="fa-solid fa-user-clock"></i> Ký thay bởi {{ $log->actualApprover->full_name ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>{{ $log->approver->department_name ?? $log->approver->DepartmentCode ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->CreateTime)->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->ApproveTime ? \Carbon\Carbon::parse($log->ApproveTime)->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @if($log->Status == 'Submitted')
                                <span class="badge bg-info text-dark"><i class="fa-solid fa-paper-plane"></i> Tạo đơn</span>
                            @elseif($log->Status == 'CancelSubmitted')
                                <span class="badge bg-dark"><i class="fa-solid fa-ban"></i> Hủy đơn</span>
                            @elseif($log->Status == 'Pending')
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i> Đang chờ</span>
                            @elseif($log->Status == 'Approved')
                                <span class="badge bg-success"><i class="fa-solid fa-check-double"></i> Chấp thuận</span>
                            @elseif($log->Status == 'Rejected')
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark"></i> Từ chối</span>
                            @elseif($log->Status == 'Confirmed')
                                <span class="badge bg-primary"><i class="fa-solid fa-clipboard-check"></i> Xác nhận duyệt</span>
                            @elseif($log->Status == 'Confirmed_Cancel')
                                <span class="badge bg-dark"><i class="fa-solid fa-clipboard-check"></i> Xác nhận hủy</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $log->Comment ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(isset($pendingLog))
<div class="card shadow-sm border-primary mb-4">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="fa-solid fa-gavel me-1"></i> Xử lý phê duyệt
    </div>
    <div class="card-body">
        <form id="approvalForm" action="{{ route('approvals.process', $pendingLog->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Ý kiến phản hồi / Lý do từ chối</label>
                <textarea id="approvalComment" name="comment" class="form-control" rows="3" placeholder="Nhập ý kiến (Bắt buộc phải nhập nếu chọn Từ chối)..."></textarea>
            </div>
            
            <input type="hidden" name="action" id="actionInput" value="">
            
            <button type="button" class="btn btn-success" onclick="submitApproval('Approved')">
                <i class="fa-solid fa-check me-1"></i> Phê duyệt đơn
            </button>
            <button type="button" class="btn btn-danger ms-2" onclick="submitApproval('Rejected')">
                <i class="fa-solid fa-xmark me-1"></i> Từ chối đơn
            </button>
        </form>
    </div>
</div>

<script>
    function submitApproval(action) {
        const comment = document.getElementById('approvalComment').value.trim();
        
        if (action === 'Rejected' && comment === '') {
            alert('Vui lòng điền Lý do từ chối vào ô ý kiến!');
            document.getElementById('approvalComment').focus();
            return;
        }
        
        document.getElementById('actionInput').value = action;
        document.getElementById('approvalForm').submit();
    }
</script>
@endif

@php
    $confirmStatusToCheck = $leave->status == 'Cancelled' ? 'Confirmed_Cancel' : 'Confirmed';
    $isConfirmed = $logs->where('Status', $confirmStatusToCheck)->isNotEmpty();
@endphp

@if(in_array($leave->status, ['Approved', 'Cancelled']) && request('source') == 'all' && !$isConfirmed)
<div class="card shadow-sm border-info mb-4">
    <div class="card-header bg-info text-white fw-bold">
        <i class="fa-solid fa-check-to-slot me-1"></i> Xác nhận nghỉ phép
    </div>
    <div class="card-body">
        <p class="text-muted">Đơn này đã được xử lý hoàn tất. Bạn có thể xác nhận thông tin của đơn này.</p>
        <form action="{{ route('leaves.confirm', $leave->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-info text-white" onclick="return confirm('Bạn có chắc chắn muốn xác nhận cho đơn này?')">
                <i class="fa-solid fa-check me-1"></i> Xác nhận
            </button>
        </form>
    </div>
</div>
@endif

@if($leave->status == 'Rejected' && Auth::check() && $leave->user_id == Auth::id())
<div class="card shadow-sm border-danger mb-4">
    <div class="card-header bg-danger text-white fw-bold">
        <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa và Gửi lại đơn
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form action="{{ route('leaves.resubmit', $leave->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Loại nghỉ phép</label>
                <select name="leave_type" id="leave_type" class="form-select" required>
                    <option value="">-- Chọn phân loại --</option>
                    <option value="Thai sản" {{ $leave->leave_type == 'Thai sản' ? 'selected' : '' }}>Thai sản</option>
                    <option value="Kết hôn" {{ $leave->leave_type == 'Kết hôn' ? 'selected' : '' }}>Kết hôn</option>
                    <option value="Tang chế" {{ $leave->leave_type == 'Tang chế' ? 'selected' : '' }}>Tang chế</option>
                    <option value="Không lương" {{ $leave->leave_type == 'Không lương' ? 'selected' : '' }}>Không lương</option>
                    <option value="Bản thân ốm" {{ $leave->leave_type == 'Bản thân ốm' ? 'selected' : '' }}>Bản thân ốm</option>
                    <option value="Con ốm" {{ $leave->leave_type == 'Con ốm' ? 'selected' : '' }}>Con ốm</option>
                    <option value="Phép năm" {{ $leave->leave_type == 'Phép năm' ? 'selected' : '' }}>Phép năm</option>
                    <option value="Khám thai" {{ $leave->leave_type == 'Khám thai' ? 'selected' : '' }}>Khám thai</option>
                    <option value="Chồng nghỉ vợ sinh" {{ $leave->leave_type == 'Chồng nghỉ vợ sinh' ? 'selected' : '' }}>Chồng nghỉ vợ sinh</option>
                    <option value="Dưỡng sức sau sinh" {{ $leave->leave_type == 'Dưỡng sức sau sinh' ? 'selected' : '' }}>Dưỡng sức sau sinh</option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Từ ngày giờ</label>
                    <div class="d-flex">
                        <input type="date" name="start_date_only" class="form-control" value="{{ \Carbon\Carbon::parse($leave->start_date)->format('Y-m-d') }}" required>
                        <div class="time-selector d-flex">
                            <select name="start_hour" class="form-select ms-2" style="width: 70px;">
                                @for($h=0; $h<24; $h++)
                                    @php $hStr = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $hStr }}" {{ \Carbon\Carbon::parse($leave->start_date)->format('H') == $hStr ? 'selected' : '' }}>{{ $hStr }}</option>
                                @endfor
                            </select>
                            <span class="align-self-center mx-1">:</span>
                            <select name="start_minute" class="form-select" style="width: 70px;">
                                @foreach(['00','15','30','45'] as $m)
                                    <option value="{{ $m }}" {{ \Carbon\Carbon::parse($leave->start_date)->format('i') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đến ngày giờ</label>
                    <div class="d-flex">
                        <input type="date" name="end_date_only" class="form-control" value="{{ \Carbon\Carbon::parse($leave->end_date)->format('Y-m-d') }}" required>
                        <div class="time-selector d-flex">
                            <select name="end_hour" class="form-select ms-2" style="width: 70px;">
                                @for($h=0; $h<24; $h++)
                                    @php $hStr = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $hStr }}" {{ \Carbon\Carbon::parse($leave->end_date)->format('H') == $hStr ? 'selected' : '' }}>{{ $hStr }}</option>
                                @endfor
                            </select>
                            <span class="align-self-center mx-1">:</span>
                            <select name="end_minute" class="form-select" style="width: 70px;">
                                @foreach(['00','15','30','45'] as $m)
                                    <option value="{{ $m }}" {{ \Carbon\Carbon::parse($leave->end_date)->format('i') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Lý do nghỉ</label>
                <textarea name="reason" class="form-control" rows="3" required>{{ $leave->reason }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-paperclip me-1"></i> Đính kèm tệp mới (Có thể chọn nhiều lần)</label>
                <input type="file" id="file_input" name="attachments[]" class="form-control" multiple>
                <ul id="file_list" class="list-group mt-2 d-none"></ul>
            </div>

            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-paper-plane me-1"></i> Cập nhật và Gửi duyệt lại</button>
        </form>
    </div>
</div>
@endif

@if($leave->status == 'Approved' && Auth::check() && $leave->user_id == Auth::id())
<div class="card shadow-sm border-dark mb-4">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="fa-solid fa-ban me-1"></i> Yêu cầu hủy đơn
    </div>
    <div class="card-body">
        <p class="text-muted">Đơn đã được duyệt. Nếu bạn muốn hủy đơn này, bạn cần ghi rõ lý do và chờ cấp quản lý phê duyệt yêu cầu hủy.</p>
        <form action="{{ route('leaves.cancel', $leave->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Lý do xin hủy đơn <span class="text-danger">*</span></label>
                <textarea name="cancel_reason" class="form-control" rows="2" placeholder="Ví dụ: Đã sắp xếp lại được công việc..." required></textarea>
            </div>
            <button type="submit" class="btn btn-dark" onclick="return confirm('Bạn có chắc chắn muốn gửi yêu cầu hủy đơn này? Quy trình phê duyệt sẽ được bắt đầu lại từ đầu.')">
                <i class="fa-solid fa-paper-plane me-1"></i> Gửi yêu cầu hủy
            </button>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const leaveType = document.getElementById('leave_type');
        const timeSelectors = document.querySelectorAll('.time-selector');
        
        function toggleTimeSelectors() {
            if(!leaveType) return;
            const dateOnlyTypes = ['Thai sản', 'Kết hôn', 'Tang chế'];
            if (dateOnlyTypes.includes(leaveType.value)) {
                timeSelectors.forEach(el => el.classList.add('d-none'));
            } else {
                timeSelectors.forEach(el => el.classList.remove('d-none'));
            }
        }
        
        if(leaveType) {
            leaveType.addEventListener('change', toggleTimeSelectors);
            toggleTimeSelectors();
        }

        // Handle multiple file additions
        const dt = new DataTransfer();
        const fileInput = document.getElementById('file_input');
        const fileList = document.getElementById('file_list');

        if(fileInput) {
            fileInput.addEventListener('change', function() {
                for (let file of this.files) {
                    // Prevent duplicate files based on name and size
                    let exists = false;
                    for (let i = 0; i < dt.items.length; i++) {
                        if (dt.items[i].getAsFile().name === file.name && dt.items[i].getAsFile().size === file.size) {
                            exists = true;
                            break;
                        }
                    }
                    if (!exists) {
                        dt.items.add(file);
                    }
                }
                this.files = dt.files;
                updateFileList();
            });
        }

        function updateFileList() {
            if(!fileList) return;
            fileList.innerHTML = '';
            if (dt.files.length > 0) {
                fileList.classList.remove('d-none');
            } else {
                fileList.classList.add('d-none');
            }
            for (let i = 0; i < dt.files.length; i++) {
                let li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center py-1';
                li.innerHTML = `<span><i class="fa-solid fa-file me-2 text-primary"></i> ${dt.files[i].name} <small class="text-muted">(${(dt.files[i].size / 1024).toFixed(1)} KB)</small></span> 
                                <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeFile(${i})"><i class="fa-solid fa-trash"></i></button>`;
                fileList.appendChild(li);
            }
        }

        window.removeFile = function(index) {
            dt.items.remove(index);
            if(fileInput) fileInput.files = dt.files;
            updateFileList();
        }
    });
</script>
@endsection