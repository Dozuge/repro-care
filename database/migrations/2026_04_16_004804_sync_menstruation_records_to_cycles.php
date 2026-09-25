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
        // Do not depend on application models in migrations: models can be
        // renamed or removed long after this migration has been recorded.
        $records = DB::table('menstruation_records')->get();
        
        foreach ($records as $record) {
            $existingCycle = DB::table('cycles')->where('user_id', $record->user_id)
                ->where('period_start_date', $record->start_date)
                ->first();
            
            if (!$existingCycle) {
                DB::table('cycles')->insert([
                    'user_id' => $record->user_id,
                    'period_start_date' => $record->start_date,
                    'period_end_date' => $record->end_date,
                    'flow_intensity' => 'medium', // Default value
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove synced Cycle records
        // This is a one-time sync, so we can leave the records or add logic to identify synced ones
    }
};
