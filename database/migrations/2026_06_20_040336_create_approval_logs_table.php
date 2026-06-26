<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->string('formID', 50); // Liên kết với bảng leave_requests (id kiểu string)
            $table->string('WorkFlowID')->nullable();
            $table->unsignedBigInteger('EmployeeID'); // ID người thực hiện phê duyệt
            $table->timestamp('CreateTime')->nullable();
            $table->timestamp('ApproveTime')->nullable();
            $table->string('Status');
            $table->text('Comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};