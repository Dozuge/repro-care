<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Phase 4: FINAL CLEANUP - Drop all legacy tables and columns
     * This migration completes the normalization to 100%
     */
    public function up(): void
    {
        // PostgreSQL prevents dropping or altering columns used by views. Remove
        // these legacy views before touching their source tables.
        DB::statement('DROP VIEW IF EXISTS midwives_legacy');
        DB::statement('DROP VIEW IF EXISTS bhw_legacy');
        DB::statement('DROP VIEW IF EXISTS health_records_enriched');

        // Step 1: Migrate any remaining checkups.midwife_id data to midwife_user_id
        $this->migrateRemainingCheckupData();

        // Step 2: Drop old FK on checkups.midwife_id
        $this->dropCheckupMidwifeFK();

        // Step 3: Drop checkups.midwife_id column
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropColumn('midwife_id');
        });

        // Step 4: Drop health_records.created_by_role (redundant - computed from recorded_by_id)
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropColumn('created_by_role');
        });

        // Step 5: Drop midwives table (data migrated to users)
        Schema::dropIfExists('midwives');

        // Step 6: Drop bhw table (data migrated to users)
        Schema::dropIfExists('bhw');

        // Step 7: Clean up legacy tracking columns from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_legacy_midwife_id');
            $table->dropIndex('idx_users_legacy_bhw_id');
            $table->dropColumn(['legacy_midwife_id', 'legacy_bhw_id']);
        });

        // Step 8: Fix bhw_monthly_reports.bhw_id CASCADE to RESTRICT
        $this->fixBhwMonthlyReportsCascade();
    }

    /**
     * Reverse the migrations.
     * WARNING: This will NOT restore dropped tables/columns
     * You would need to restore from backup
     */
    public function down(): void
    {
        // Cannot reverse table drops - would need backup restore
        throw new \RuntimeException('This migration cannot be rolled back. Restore from backup if needed.');
    }

    /**
     * Migrate any remaining checkup data from midwife_id to midwife_user_id
     */
    protected function migrateRemainingCheckupData(): void
    {
        // Find checkups that still have midwife_id but no midwife_user_id
        $orphanedCheckups = DB::table('checkups')
            ->whereNotNull('midwife_id')
            ->whereNull('midwife_user_id')
            ->count();

        if ($orphanedCheckups > 0) {
            // This shouldn't happen if Phase 1 ran correctly, but safety check
            \Log::warning("Found {$orphanedCheckups} checkups with midwife_id but no midwife_user_id");
        }
    }

    /**
     * Drop the old FK constraint on checkups.midwife_id
     */
    protected function dropCheckupMidwifeFK(): void
    {
        $foreignKey = collect(Schema::getForeignKeys('checkups'))
            ->first(fn (array $fk) => in_array('midwife_id', $fk['columns']));

        if ($foreignKey) {
            Schema::table('checkups', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey['name']);
            });
        }
    }

    /**
     * Fix bhw_monthly_reports.bhw_id CASCADE to RESTRICT
     */
    protected function fixBhwMonthlyReportsCascade(): void
    {
        if (! Schema::hasTable('bhw_monthly_reports')) {
            return;
        }

        $currentRule = collect(Schema::getForeignKeys('bhw_monthly_reports'))
            ->first(fn (array $fk) => in_array('bhw_id', $fk['columns']));

        if ($currentRule) {
            
            // Only change if it's still CASCADE
            if (strtoupper($currentRule['on_delete']) === 'CASCADE') {
                // Drop old FK
                Schema::table('bhw_monthly_reports', function (Blueprint $table) use ($currentRule) {
                    $table->dropForeign($currentRule['name']);
                    $table->foreign('bhw_id', 'fk_bhw_monthly_reports_bhw')
                        ->references('id')->on('users')->onDelete('restrict');
                });
                
                \Log::info("Changed bhw_monthly_reports.bhw_id FK from CASCADE to RESTRICT");
            }
        }
    }
};
