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
        // Inspect foreign keys through Laravel rather than MySQL's DATABASE()
        // catalog function, which PostgreSQL does not implement.
        foreach (Schema::getTableListing(null, false) as $tableName) {
            $constraints = collect(Schema::getForeignKeys($tableName))
                ->filter(fn (array $foreign) => $foreign['foreign_table'] === 'users');

            foreach ($constraints as $constraint) {
                Schema::table($tableName, function (Blueprint $table) use ($constraint) {
                    $table->dropForeign($constraint['name']);
                });
            }
        }

        // Drop the users table
        Schema::dropIfExists('users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore from backup
        DB::statement("CREATE TABLE users AS SELECT * FROM users_backup");

        // Re-add foreign keys if needed
        Schema::table('midwife_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('bhw_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
