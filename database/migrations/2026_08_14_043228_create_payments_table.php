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
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('invoice_id');
            $table->ulid('client_id');
            $table->bigInteger('amount');
            $table->enum('method', ['bank_transfer', 'cash', 'cheque', 'online', 'other']);
            $table->string('transaction_reference')->nullable();
            $table->timestamp('paid_at');
            $table->ulid('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('received_by')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('invoice_id');
            $table->index('client_id');
            $table->index('received_by');
            $table->index('paid_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
