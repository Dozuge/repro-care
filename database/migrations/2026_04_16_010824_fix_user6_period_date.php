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
        // Update MenstruationRecord for user 6
        DB::table('menstruation_records')->where('user_id', 6)
            ->where('start_date', '2026-04-10')
            ->update([
                'start_date' => '2026-04-11',
                'end_date' => '2026-04-14'
            ]);

        // Update Cycle for user 6
        DB::table('cycles')->where('user_id', 6)
            ->where('period_start_date', '2026-04-10')
            ->update([
                'period_start_date' => '2026-04-11',
                'period_end_date' => '2026-04-14'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert MenstruationRecord for user 6
        DB::table('menstruation_records')->where('user_id', 6)
            ->where('start_date', '2026-04-11')
            ->update([
                'start_date' => '2026-04-10',
                'end_date' => '2026-04-13'
            ]);

        // Revert Cycle for user 6
        DB::table('cycles')->where('user_id', 6)
            ->where('period_start_date', '2026-04-11')
            ->update([
                'period_start_date' => '2026-04-10',
                'period_end_date' => '2026-04-13'
            ]);
    }
};
