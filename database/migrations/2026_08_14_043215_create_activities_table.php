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
        Schema::create('activities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('subject_id');
            $table->string('subject_type');
            $table->ulid('employee_id')->nullable();
            $table->enum('type', ['call', 'whatsapp', 'email', 'meeting', 'video', 'sms', 'note']);
            $table->timestamp('occurred_at');
            $table->string('subject_line')->nullable();
            $table->text('description')->nullable();
            $table->text('result')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index('employee_id');
            $table->index('occurred_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
