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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('activity_id')->nullable();
            $table->ulid('subject_id');
            $table->string('subject_type');
            $table->ulid('employee_id')->nullable();
            $table->timestamp('due_at');
            $table->enum('status', ['pending', 'done', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activities')->nullOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index('employee_id');
            $table->index('status');
            $table->index('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
