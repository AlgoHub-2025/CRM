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
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->ulid('client_id');
            $table->ulid('contract_id')->nullable();
            $table->ulid('project_manager_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->bigInteger('budget')->default(0);
            $table->string('technology')->nullable();
            $table->enum('status', ['not_started', 'planning', 'development', 'testing', 'client_review', 'revision', 'completed', 'maintenance', 'cancelled'])->default('not_started');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->foreign('project_manager_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('contract_id');
            $table->index('project_manager_id');
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
        Schema::dropIfExists('projects');
    }
};
