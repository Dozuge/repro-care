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
        // PostgreSQL aborts the whole transaction after an invalid ALTER TABLE;
        // check metadata first instead of relying on try/catch around DDL.
        if (Schema::hasTable('users')) {
            $legacyColumns = ['feeding_method_id', 'family_planning_method_id', 'vitamins_id'];
            $foreignKeys = collect(Schema::getForeignKeys('users'))
                ->filter(fn (array $foreign) => ! empty(array_intersect($legacyColumns, $foreign['columns'])));

            foreach ($foreignKeys as $foreign) {
                Schema::table('users', function (Blueprint $table) use ($foreign) {
                    $table->dropForeign($foreign['name']);
                });
            }

            $columnsToDrop = array_values(array_filter(
                $legacyColumns,
                fn (string $column) => Schema::hasColumn('users', $column)
            ));

            if ($columnsToDrop) {
                Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
        }

        // Step 2: Drop unused normalization tables in correct order (respecting foreign keys)
        Schema::dropIfExists('quiz_answers'); // Must drop before quiz_questions
        Schema::dropIfExists('quiz_questions'); // Has foreign key to learning_materials
        Schema::dropIfExists('user_barangays'); // Has foreign key to users
        Schema::dropIfExists('bhw_monthly_report_filters'); // Has foreign key to bhw_monthly_reports
        Schema::dropIfExists('lookup_values'); // No foreign keys
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be easily rolled back due to data loss
        throw new \Exception('This migration cannot be rolled back. Restore from database backup if needed.');
    }
};
