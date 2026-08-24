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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('lead_id')->nullable();
            $table->ulid('client_id')->nullable();
            $table->string('title');
            $table->string('service')->nullable();
            $table->bigInteger('value')->default(0);
            $table->unsignedTinyInteger('probability')->default(0);
            $table->date('expected_close_date')->nullable();
            $table->ulid('assigned_to')->nullable();
            $table->ulid('stage_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('leads')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('stage_id')->references('id')->on('pipeline_stages')->restrictOnDelete();
            
            $table->index('lead_id');
            $table->index('client_id');
            $table->index('assigned_to');
            $table->index('stage_id');
            $table->index('expected_close_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
