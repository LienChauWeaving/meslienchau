@extends('layouts.app')

@section('title', 'Tạo đơn xin nghỉ phép')

@section('content')
<div class="card shadow-sm w-100">
    <div class="card-header bg-white"><i class="fa-solid fa-pen-to-square me-1"></i> Điền thông tin</div>
    <div class="card-body">
        <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Loại nghỉ phép</label>
                <select name="leave_type" id="leave_type" class="form-select @error('leave_type') is-invalid @enderror" required>
                    <option value="">-- Chọn phân loại --</option>
                    <option value="Thai sản" {{ old('leave_type') == 'Thai sản' ? 'selected' : '' }}>Thai sản</option>
                    <option value="Kết hôn" {{ old('leave_type') == 'Kết hôn' ? 'selected' : '' }}>Kết hôn</option>
                    <option value="Tang chế" {{ old('leave_type') == 'Tang chế' ? 'selected' : '' }}>Tang chế</option>
                    <option value="Không lương" {{ old('leave_type') == 'Không lương' ? 'selected' : '' }}>Không lương</option>
                    <option value="Bản thân ốm" {{ old('leave_type') == 'Bản thân ốm' ? 'selected' : '' }}>Bản thân ốm</option>
                    <option value="Con ốm" {{ old('leave_type') == 'Con ốm' ? 'selected' : '' }}>Con ốm</option>
                    <option value="Phép năm" {{ old('leave_type') == 'Phép năm' ? 'selected' : '' }}>Phép năm</option>
                    <option value="Khám thai" {{ old('leave_type') == 'Khám thai' ? 'selected' : '' }}>Khám thai</option>
                    <option value="Chồng nghỉ vợ sinh" {{ old('leave_type') == 'Chồng nghỉ vợ sinh' ? 'selected' : '' }}>Chồng nghỉ vợ sinh</option>
                    <option value="Dưỡng sức sau sinh" {{ old('leave_type') == 'Dưỡng sức sau sinh' ? 'selected' : '' }}>Dưỡng sức sau sinh</option>
                </select>
                @error('leave_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Từ ngày giờ</label>
                    <div class="d-flex">
                        <input type="date" name="start_date_only" class="form-control" value="{{ old('start_date_only') }}" required>
                        <div class="time-selector d-flex">
                            <select name="start_hour" class="form-select ms-2" style="width: 70px;">
                                @for($h=0; $h<24; $h++)
                                    @php $hStr = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $hStr }}" {{ old('start_hour', '08') == $hStr ? 'selected' : '' }}>{{ $hStr }}</option>
                                @endfor
                            </select>
                            <span class="align-self-center mx-1">:</span>
                            <select name="start_minute" class="form-select" style="width: 70px;">
                                @foreach(['00','15','30','45'] as $m)
                                    <option value="{{ $m }}" {{ old('start_minute', '00') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đến ngày giờ</label>
                    <div class="d-flex">
                        <input type="date" name="end_date_only" class="form-control" value="{{ old('end_date_only') }}" required>
                        <div class="time-selector d-flex">
                            <select name="end_hour" class="form-select ms-2" style="width: 70px;">
                                @for($h=0; $h<24; $h++)
                                    @php $hStr = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $hStr }}" {{ old('end_hour', '17') == $hStr ? 'selected' : '' }}>{{ $hStr }}</option>
                                @endfor
                            </select>
                            <span class="align-self-center mx-1">:</span>
                            <select name="end_minute" class="form-select" style="width: 70px;">
                                @foreach(['00','15','30','45'] as $m)
                                    <option value="{{ $m }}" {{ old('end_minute', '00') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Lý do</label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason') }}</textarea>
                @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa-solid fa-paperclip me-1"></i> Tệp đính kèm (Có thể chọn nhiều lần)</label>
                <input type="file" id="file_input" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror" multiple>
                @error('attachments.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="form-text">Cho phép tải lên nhiều tệp, dung lượng tối đa 10MB/tệp.</div>
                
                <ul id="file_list" class="list-group mt-2 d-none"></ul>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Gửi đơn</button>
            <a href="{{ route('leaves.index') }}" class="btn btn-light ms-2">Hủy</a>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const leaveType = document.getElementById('leave_type');
        const timeSelectors = document.querySelectorAll('.time-selector');
        
        function toggleTimeSelectors() {
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

        function updateFileList() {
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
            fileInput.files = dt.files;
            updateFileList();
        }
    });
</script>
@endsection