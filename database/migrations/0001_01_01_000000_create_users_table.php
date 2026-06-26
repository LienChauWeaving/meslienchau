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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique();
        $table->string('password');
        $table->string('full_name');
        $table->string('email')->nullable();
        $table->integer('role_id')->default(2); // 1: Admin, 2: Employee
        $table->boolean('status')->default(1);
        $table->string('DepartmentCode')->nullable();
        $table->timestamps();
    });

    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
