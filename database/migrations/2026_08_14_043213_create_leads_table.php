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
        Schema::create('leads', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->ulid('company_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->string('industry')->nullable();
            $table->ulid('source_id')->nullable();
            $table->string('interested_service')->nullable();
            $table->bigInteger('estimated_budget')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->ulid('assigned_to')->nullable();
            $table->ulid('status_id');
            $table->text('description')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('source_id')->references('id')->on('lead_sources')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('pipeline_stages')->restrictOnDelete();
            
            $table->index('company_id');
            $table->index('assigned_to');
            $table->index('status_id');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
