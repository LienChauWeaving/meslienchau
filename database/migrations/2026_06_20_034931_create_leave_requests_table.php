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
    Schema::create('leave_requests', function (Blueprint $table) {
        $table->string('id', 50)->primary(); // Mã string NSQT...
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('start_date');
        $table->date('end_date');
        $table->text('reason');
        $table->string('status', 50)->default('Pending'); // Pending, Approved, Rejected
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
