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
        // Earlier legacy migrations temporarily remove the unified users table,
        // but the child-care schema already references it. Recreate the base
        // table here so this and subsequent migrations have a stable FK target.
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role')->default('user');
                $table->string('status')->default('approved');
                $table->string('rejection_reason')->nullable();
                $table->string('address')->nullable();
                $table->string('barangay')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('gender')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('phone')->nullable();
                $table->string('profile_image')->nullable();
                $table->string('assigned_barangay')->nullable();
                $table->text('assigned_barangays')->nullable();
                $table->string('certification_number')->nullable();
                $table->date('certification_date')->nullable();
                $table->string('license_number')->nullable();
                $table->string('specialization')->nullable();
                $table->date('license_expiry')->nullable();
                $table->string('feeding_method')->nullable();
                $table->string('family_planning_method')->nullable();
                $table->string('vitamins')->nullable();
                $table->json('medical_history')->nullable();
                $table->date('term_start')->nullable();
                $table->date('term_end')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('child_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('child_records')->onDelete('cascade');
            $table->date('checkup_date');
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('head_circumference', 5, 2)->nullable();
            $table->text('developmental_milestones')->nullable();
            $table->json('vaccinations_given')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('conducted_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('child_id');
            $table->index('checkup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_checkups');
    }
};
