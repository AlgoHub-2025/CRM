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
        Schema::create('clients', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->ulid('primary_contact_id')->nullable();
            $table->ulid('converted_from_opportunity_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            $table->foreign('primary_contact_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('converted_from_opportunity_id')->references('id')->on('opportunities')->nullOnDelete();
            
            $table->index('company_id');
            $table->index('status');
            $table->index('created_at');
        });
        
        Schema::table('opportunities', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });
        Schema::dropIfExists('clients');
    }
};
