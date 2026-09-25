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
        $this->setStatuses(['scheduled', 'completed', 'missed', 'cancelled', 'Rescheduled']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->setStatuses(['scheduled', 'completed', 'missed', 'cancelled']);
    }

    private function setStatuses(array $statuses): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $values = implode(', ', array_map(fn ($status) => "'{$status}'", $statuses));
            DB::statement("ALTER TABLE checkups MODIFY COLUMN status ENUM({$values}) DEFAULT 'scheduled'");
            return;
        }

        // The original table used title-case values. Convert them before the
        // replacement check constraint restricts status to the new values.
        DB::statement("ALTER TABLE checkups DROP CONSTRAINT IF EXISTS checkups_status_check");
        DB::table('checkups')->whereIn('status', ['Scheduled', 'Completed', 'Missed', 'Cancelled'])
            ->update(['status' => DB::raw('LOWER(status)')]);
        DB::statement("ALTER TABLE checkups ALTER COLUMN status SET DEFAULT 'scheduled'");

        $values = implode(', ', array_map(fn ($status) => "'{$status}'", $statuses));
        DB::statement("ALTER TABLE checkups ADD CONSTRAINT checkups_status_check CHECK (status IN ({$values}))");
    }
};
