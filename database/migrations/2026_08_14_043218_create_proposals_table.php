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
        Schema::create('proposals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('proposal_number')->unique();
            $table->ulid('client_id');
            $table->string('project_title');
            $table->enum('status', ['draft', 'sent', 'viewed', 'negotiation', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total')->default(0);
            $table->string('currency')->default('USD');
            $table->date('valid_until')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            
            $table->index('client_id');
            $table->index('status');
            $table->index('valid_until');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
