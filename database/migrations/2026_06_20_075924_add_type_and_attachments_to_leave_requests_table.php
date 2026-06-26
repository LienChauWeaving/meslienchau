<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('leave_type')->nullable()->after('user_id'); // Loại phép
            $table->json('attachments')->nullable()->after('reason');   // Lưu mảng đường dẫn file
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['leave_type', 'attachments']);
        });
    }
};