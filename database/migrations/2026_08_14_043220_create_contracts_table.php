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
        Schema::create('contracts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('contract_number')->unique();
            $table->ulid('client_id');
            $table->ulid('proposal_id')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->bigInteger('value')->default(0);
            $table->text('payment_terms')->nullable();
            $table->text('scope')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->string('signed_document_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('proposal_id')->references('id')->on('proposals')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('proposal_id');
            $table->index('status');
            $table->index('end_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
