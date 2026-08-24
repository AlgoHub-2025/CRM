<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->ulid('opportunity_id')->nullable()->after('client_id');
            // Change client_id to be nullable
            $table->ulid('client_id')->nullable()->change();
            
            $table->foreign('opportunity_id')->references('id')->on('opportunities')->restrictOnDelete();
            $table->index('opportunity_id');
        });

        // Add DB-level check constraint to ensure at least one is present
        DB::statement('ALTER TABLE proposals ADD CONSTRAINT chk_proposals_parent CHECK (client_id IS NOT NULL OR opportunity_id IS NOT NULL)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE proposals DROP CONSTRAINT chk_proposals_parent');

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['opportunity_id']);
            $table->dropIndex(['opportunity_id']);
            $table->dropColumn('opportunity_id');
            // We revert client_id back to NOT NULL, assuming it was NOT NULL before
            $table->ulid('client_id')->nullable(false)->change();
        });
    }
};
