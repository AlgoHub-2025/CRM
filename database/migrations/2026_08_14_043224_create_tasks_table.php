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
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('project_id');
            $table->ulid('milestone_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->ulid('assigned_to')->nullable();
            $table->string('priority')->default('medium');
            $table->date('deadline')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'review', 'completed', 'blocked'])->default('todo');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('milestone_id')->references('id')->on('milestones')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('project_id');
            $table->index('milestone_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('deadline');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
