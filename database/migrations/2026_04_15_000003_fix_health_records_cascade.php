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
     * Phase 3: Fix unsafe CASCADE DELETE on health records
     * Changes to RESTRICT to prevent accidental medical history loss
     */
    public function up(): void
    {
        // Check current FK rules to avoid duplicate operations
        // Laravel reads the driver-specific system catalog for us. DATABASE() is
        // MySQL-only and fails on PostgreSQL.
        $fkRules = collect(Schema::getForeignKeys('health_records'))
            ->filter(fn (array $fk) => in_array($fk['name'], [
                'health_records_user_id_foreign',
                'fk_health_records_user',
                'health_records_recorded_by_id_foreign',
                'fk_health_records_recorded_by',
            ]));

        $userFk = $fkRules->first(fn (array $fk) => in_array($fk['name'], ['health_records_user_id_foreign', 'fk_health_records_user']));
        $recordedByFk = $fkRules->first(fn (array $fk) => in_array($fk['name'], ['health_records_recorded_by_id_foreign', 'fk_health_records_recorded_by']));
        $userFkExists = $userFk !== null;
        $recordedByFkExists = $recordedByFk !== null;

        // Step 1: Change user_id FK from CASCADE to RESTRICT (if needed)
        if ($userFkExists) {
            // Only change if it's still CASCADE
            if (strtoupper($userFk['on_delete']) === 'CASCADE') {
                Schema::table('health_records', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });

                Schema::table('health_records', function (Blueprint $table) {
                    $table->foreign('user_id', 'fk_health_records_user')
                        ->references('id')->on('users')
                        ->onDelete('restrict');
                });
            }
        }

        // Step 2: Change recorded_by_id FK from CASCADE to SET NULL (if needed)
        if ($recordedByFkExists) {
            // Only change if it's still CASCADE
            if (strtoupper($recordedByFk['on_delete']) === 'CASCADE') {
                Schema::table('health_records', function (Blueprint $table) {
                    $table->dropForeign(['recorded_by_id']);
                });

                // Make column nullable first
                Schema::table('health_records', function (Blueprint $table) {
                    $table->unsignedBigInteger('recorded_by_id')->nullable()->change();
                });

                Schema::table('health_records', function (Blueprint $table) {
                    $table->foreign('recorded_by_id', 'fk_health_records_recorded_by')
                        ->references('id')->on('users')
                        ->onDelete('set null');
                });
            }
        } elseif (!$recordedByFkExists) {
            // No FK exists, create one with SET NULL
            // First make column nullable
            Schema::table('health_records', function (Blueprint $table) {
                $table->unsignedBigInteger('recorded_by_id')->nullable()->change();
            });

            // Then add FK
            try {
                Schema::table('health_records', function (Blueprint $table) {
                    $table->foreign('recorded_by_id', 'fk_health_records_recorded_by')
                        ->references('id')->on('users')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // FK might already exist with different name, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore CASCADE for recorded_by_id
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign('fk_health_records_recorded_by');
            $table->foreign('recorded_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // Restore CASCADE for user_id
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign('fk_health_records_user');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }
};
