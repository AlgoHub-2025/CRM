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
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('ticket_id');
            $table->enum('sender_type', ['employee', 'client']);
            $table->ulid('sender_id');
            $table->ulid('logged_by_employee_id')->nullable()->comment('Employee who logged the message on behalf of client');
            $table->text('message');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->cascadeOnDelete();
            
            $table->index('ticket_id');
            $table->index(['sender_type', 'sender_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
