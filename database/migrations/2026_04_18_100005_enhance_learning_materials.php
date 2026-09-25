<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_materials', 'category')) {
                $table->enum('category', [
                    'general', 'nutrition', 'warning-signs',
                    'family-planning', 'postpartum', 'pregnancy-guide'
                ])->default('general')->after('image');
            }
            if (!Schema::hasColumn('learning_materials', 'video_url')) {
                $table->string('video_url')->nullable()->after('link_url');
            }
            if (!Schema::hasColumn('learning_materials', 'quiz_data')) {
                $table->json('quiz_data')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('learning_materials', 'week_number')) {
                $table->unsignedTinyInteger('week_number')->nullable()->after('quiz_data');
            }
        });

        $this->setMaterialTypes(['article', 'link', 'file', 'video', 'quiz']);
    }

    public function down(): void
    {
        Schema::table('learning_materials', function (Blueprint $table) {
            $table->dropColumn(['category', 'video_url', 'quiz_data', 'week_number']);
        });
        $this->setMaterialTypes(['article', 'link', 'file']);
    }

    /**
     * Laravel implements enum columns as CHECK constraints on PostgreSQL, not
     * MySQL ENUMs. Update that constraint without issuing MySQL MODIFY SQL.
     */
    private function setMaterialTypes(array $types): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $values = implode(',', array_map(fn ($type) => "'{$type}'", $types));
            DB::statement("ALTER TABLE learning_materials MODIFY COLUMN material_type ENUM({$values}) DEFAULT 'article'");
            return;
        }

        $values = implode(', ', array_map(fn ($type) => "'{$type}'", $types));
        DB::statement('ALTER TABLE learning_materials DROP CONSTRAINT IF EXISTS learning_materials_material_type_check');
        DB::statement("ALTER TABLE learning_materials ADD CONSTRAINT learning_materials_material_type_check CHECK (material_type IN ({$values}))");
    }
};
