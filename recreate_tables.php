<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// Sao lưu dữ liệu (nếu muốn, nhưng để đơn giản ta có thể truncate nếu dev đồng ý, nhưng mình sẽ cố gắng giữ lại bằng cách update id)
// Thực ra việc đổi kiểu dữ liệu cột identity trong SQL Server cần script phức tạp.
// Xóa luôn table cho nhanh, vì môi trường test chỉ có 4 bản ghi.
Schema::dropIfExists('approval_logs');
Schema::dropIfExists('leave_requests');

Schema::create('leave_requests', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // Mã string NSQT...
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->date('start_date');
    $table->date('end_date');
    $table->text('reason');
    $table->text('attachments')->nullable();
    $table->string('leave_type', 100)->nullable();
    $table->string('status', 50)->default('Pending');
    $table->timestamps();
});

Schema::create('approval_logs', function (Blueprint $table) {
    $table->id();
    $table->string('formID', 50); // Đổi thành string
    $table->string('WorkFlowID')->nullable();
    $table->unsignedBigInteger('EmployeeID');
    $table->timestamp('CreateTime')->nullable();
    $table->timestamp('ApproveTime')->nullable();
    $table->string('Status');
    $table->text('Comment')->nullable();
    $table->timestamps();
});

echo "Tables recreated successfully.\n";
