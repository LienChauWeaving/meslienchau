<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approver_lists', function (Blueprint $table) {
            $table->id();
            $table->string('DepartmentCode');
            $table->integer('level');
            $table->foreignId('employeeID')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approver_lists');
    }
};