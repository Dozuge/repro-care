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
     * Phase 1: Prepare for midwives/bhw merge into users
     * Adds legacy tracking columns and new FK to checkups
     */
    public function up(): void
    {
        // Step 1: Add legacy tracking columns to users (if not exists)
        if (!Schema::hasColumn('users', 'legacy_midwife_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('legacy_midwife_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('users', 'legacy_bhw_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('legacy_bhw_id')->nullable();
            });
        }

        // Schema::hasIndex works with MySQL and PostgreSQL; SHOW INDEX does not.
        if (!Schema::hasIndex('users', 'idx_users_legacy_midwife_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('legacy_midwife_id', 'idx_users_legacy_midwife_id');
            });
        }
        if (!Schema::hasIndex('users', 'idx_users_legacy_bhw_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('legacy_bhw_id', 'idx_users_legacy_bhw_id');
            });
        }

        // Step 2: Add soft delete support (if not exists)
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Step 3: Migrate midwives to users (if they don't exist)
        $this->migrateMidwivesToUsers();

        // Step 4: Migrate bhw to users (if they don't exist)
        $this->migrateBhwToUsers();

        // Step 5: Add new FK column to checkups (if not exists)
        if (!Schema::hasColumn('checkups', 'midwife_user_id')) {
            Schema::table('checkups', function (Blueprint $table) {
                $table->unsignedBigInteger('midwife_user_id')->nullable();
                
                $table->foreign('midwife_user_id', 'fk_checkups_midwife_user')
                    ->references('id')->on('users');
                
                $table->index('midwife_user_id', 'idx_checkups_midwife_user_id');
            });

            // Step 6: Populate new FK from migrated data
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("
                    UPDATE checkups c
                    SET midwife_user_id = u.id
                    FROM midwives m
                    INNER JOIN users u ON u.legacy_midwife_id = m.id
                    WHERE c.midwife_id = m.id
                ");
            } else {
                DB::statement("
                    UPDATE checkups c
                    INNER JOIN midwives m ON c.midwife_id = m.id
                    INNER JOIN users u ON u.legacy_midwife_id = m.id
                    SET c.midwife_user_id = u.id
                ");
            }
        }

        // Step 7: Create legacy views for backward compatibility
        $this->createMidwivesLegacyView();
        $this->createBhwLegacyView();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop views
        DB::statement('DROP VIEW IF EXISTS bhw_legacy');
        DB::statement('DROP VIEW IF EXISTS midwives_legacy');

        // Remove new FK from checkups
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropForeign('fk_checkups_midwife_user');
            $table->dropIndex('idx_checkups_midwife_user_id');
            $table->dropColumn('midwife_user_id');
        });

        // Remove legacy tracking from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_legacy_midwife_id');
            $table->dropIndex('idx_users_legacy_bhw_id');
            $table->dropColumn(['legacy_midwife_id', 'legacy_bhw_id', 'deleted_at']);
        });
    }

    /**
     * Migrate midwife records to users table
     */
    protected function migrateMidwivesToUsers(): void
    {
        $migrated = DB::table('midwives')->count();
        
        if ($migrated === 0) {
            return;
        }

        // Insert midwives that don't already exist in users
        DB::statement("
            INSERT INTO users (name, email, password, role, address, barangay, contact_number, created_at, updated_at)
            SELECT 
                m.name,
                m.email,
                CONCAT('\$2y\$10\$TEMP', MD5(m.email)),
                'midwife',
                m.address,
                m.barangay,
                m.contact,
                m.created_at,
                m.updated_at
            FROM midwives m
            WHERE m.email NOT IN (SELECT email FROM users WHERE role = 'midwife')
        ");

        // Link midwives to their user records
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                UPDATE users u
                SET legacy_midwife_id = m.id
                FROM midwives m
                WHERE m.email = u.email
                  AND u.role = 'midwife'
                  AND u.legacy_midwife_id IS NULL
            ");
        } else {
            DB::statement("
                UPDATE users u
                INNER JOIN midwives m ON m.email = u.email AND u.role = 'midwife'
                SET u.legacy_midwife_id = m.id
                WHERE u.legacy_midwife_id IS NULL
            ");
        }
    }

    /**
     * Migrate bhw records to users table
     */
    protected function migrateBhwToUsers(): void
    {
        $migrated = DB::table('bhw')->count();
        
        if ($migrated === 0) {
            return;
        }

        // Insert bhw that don't already exist in users
        DB::statement("
            INSERT INTO users (name, email, password, role, address, barangay, contact_number, created_at, updated_at)
            SELECT 
                b.name,
                b.email,
                CONCAT('\$2y\$10\$TEMP', MD5(b.email)),
                'bhw',
                b.address,
                b.barangay,
                b.contact,
                b.created_at,
                b.updated_at
            FROM bhw b
            WHERE b.email NOT IN (SELECT email FROM users WHERE role = 'bhw')
        ");

        // Link bhw to their user records
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                UPDATE users u
                SET legacy_bhw_id = b.id
                FROM bhw b
                WHERE b.email = u.email
                  AND u.role = 'bhw'
                  AND u.legacy_bhw_id IS NULL
            ");
        } else {
            DB::statement("
                UPDATE users u
                INNER JOIN bhw b ON b.email = u.email AND u.role = 'bhw'
                SET u.legacy_bhw_id = b.id
                WHERE u.legacy_bhw_id IS NULL
            ");
        }
    }

    /**
     * Create legacy view for midwives
     */
    protected function createMidwivesLegacyView(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW midwives_legacy AS
            SELECT 
                u.legacy_midwife_id AS id,
                u.name,
                u.email,
                u.contact_number AS contact,
                u.address,
                u.barangay,
                u.created_at,
                u.updated_at
            FROM users u
            WHERE u.role = 'midwife' AND u.legacy_midwife_id IS NOT NULL
        ");
    }

    /**
     * Create legacy view for bhw
     */
    protected function createBhwLegacyView(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW bhw_legacy AS
            SELECT 
                u.legacy_bhw_id AS id,
                u.name,
                u.email,
                u.contact_number AS contact,
                u.address,
                u.barangay,
                u.created_at,
                u.updated_at
            FROM users u
            WHERE u.role = 'bhw' AND u.legacy_bhw_id IS NOT NULL
        ");
    }
};
