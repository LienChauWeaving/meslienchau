<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('delegate_id')->nullable()->after('job_title');
        });

        Schema::table('approval_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('actual_approver_id')->nullable()->after('EmployeeID');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('delegate_id');
        });

        Schema::table('approval_logs', function (Blueprint $table) {
            $table->dropColumn('actual_approver_id');
        });
    }
};
