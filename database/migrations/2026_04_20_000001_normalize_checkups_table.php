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
        Schema::table('checkups', function (Blueprint $table) {
            // Add woman_id and midwife_id if they don't exist
            if (!Schema::hasColumn('checkups', 'woman_id')) {
                $table->unsignedBigInteger('woman_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('checkups', 'midwife_id')) {
                $table->unsignedBigInteger('midwife_id')->nullable()->after('woman_id');
            }
            // Add new specific foreign key columns only if they don't exist
            if (!Schema::hasColumn('checkups', 'scheduled_by_midwife_id')) {
                $table->unsignedBigInteger('scheduled_by_midwife_id')->nullable()->after('scheduled_by_id');
            }
            if (!Schema::hasColumn('checkups', 'scheduled_by_bhw_id')) {
                $table->unsignedBigInteger('scheduled_by_bhw_id')->nullable()->after('scheduled_by_midwife_id');
            }

            // Get existing foreign keys
            $foreignKeys = collect(Schema::getForeignKeys('checkups'))->pluck('name')->toArray();

            // Add foreign key constraints only if they don't already exist
            if (Schema::hasColumn('checkups', 'woman_id') && !in_array('checkups_woman_id_foreign', $foreignKeys)) {
                $table->foreign('woman_id')->references('id')->on('women')->onDelete('cascade');
            }
            if (Schema::hasColumn('checkups', 'midwife_id') && !in_array('checkups_midwife_id_foreign', $foreignKeys)) {
                $table->foreign('midwife_id')->references('id')->on('midwives')->onDelete('cascade');
            }
            if (Schema::hasColumn('checkups', 'scheduled_by_midwife_id') && !in_array('checkups_scheduled_by_midwife_id_foreign', $foreignKeys)) {
                $table->foreign('scheduled_by_midwife_id')->references('id')->on('midwives')->onDelete('set null');
            }
            if (Schema::hasColumn('checkups', 'scheduled_by_bhw_id') && !in_array('checkups_scheduled_by_bhw_id_foreign', $foreignKeys)) {
                $table->foreign('scheduled_by_bhw_id')->references('id')->on('bhws')->onDelete('set null');
            }

            // Get existing indexes
            $indexes = collect(Schema::getIndexes('checkups'))->pluck('name')->toArray();

            // Add indexes only if columns exist and they don't already exist
            if (Schema::hasColumn('checkups', 'woman_id') && !in_array('checkups_woman_id_index', $indexes)) {
                $table->index('woman_id');
            }
            if (Schema::hasColumn('checkups', 'midwife_id') && !in_array('checkups_midwife_id_index', $indexes)) {
                $table->index('midwife_id');
            }
        });

        // Migrate data from polymorphic columns to specific columns
        DB::table('checkups')
            ->whereIn('patient_type', ['App\\Models\\Woman', 'App\\Models\\Patient'])
            ->update(['woman_id' => DB::raw('patient_id')]);

        DB::table('checkups')
            ->where('scheduled_by_type', 'App\\Models\\Midwife')
            ->update(['scheduled_by_midwife_id' => DB::raw('scheduled_by_id')]);

        DB::table('checkups')
            ->where('scheduled_by_type', 'App\\Models\\Bhw')
            ->update(['scheduled_by_bhw_id' => DB::raw('scheduled_by_id')]);

        // Drop polymorphic columns
        Schema::table('checkups', function (Blueprint $table) {
            // Check if foreign key exists before dropping
            $foreignKeys = collect(Schema::getForeignKeys('checkups'))->pluck('name')->toArray();
            
            if (in_array('checkups_scheduled_by_id_foreign', $foreignKeys)) {
                $table->dropForeign(['scheduled_by_id']);
            }
            
            // Drop columns that exist
            $columnsToDrop = [];
            if (Schema::hasColumn('checkups', 'patient_id')) $columnsToDrop[] = 'patient_id';
            if (Schema::hasColumn('checkups', 'patient_type')) $columnsToDrop[] = 'patient_type';
            if (Schema::hasColumn('checkups', 'midwife_type')) $columnsToDrop[] = 'midwife_type';
            if (Schema::hasColumn('checkups', 'scheduled_by_type')) $columnsToDrop[] = 'scheduled_by_type';
            if (Schema::hasColumn('checkups', 'scheduled_by_id')) $columnsToDrop[] = 'scheduled_by_id';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkups', function (Blueprint $table) {
            // Re-add polymorphic columns
            $table->unsignedBigInteger('patient_id')->nullable()->after('id');
            $table->string('patient_type')->nullable()->after('patient_id');
            $table->string('midwife_type')->nullable()->after('midwife_id');
            $table->unsignedBigInteger('scheduled_by_id')->nullable()->after('scheduled_by_bhw_id');
            $table->string('scheduled_by_type')->nullable()->after('scheduled_by_id');
        });

        // Migrate data back
        DB::statement("UPDATE checkups SET patient_id = woman_id, patient_type = 'App\\\\Models\\\\Woman'");
        DB::statement("UPDATE checkups SET scheduled_by_id = scheduled_by_midwife_id, scheduled_by_type = 'App\\\\Models\\\\Midwife' WHERE scheduled_by_midwife_id IS NOT NULL");
        DB::statement("UPDATE checkups SET scheduled_by_id = scheduled_by_bhw_id, scheduled_by_type = 'App\\\\Models\\\\Bhw' WHERE scheduled_by_bhw_id IS NOT NULL");

        // Drop specific columns
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropForeign(['woman_id']);
            $table->dropForeign(['midwife_id']);
            $table->dropForeign(['scheduled_by_midwife_id']);
            $table->dropForeign(['scheduled_by_bhw_id']);
            $table->dropColumn(['woman_id', 'midwife_id', 'scheduled_by_midwife_id', 'scheduled_by_bhw_id']);
        });
    }
};
