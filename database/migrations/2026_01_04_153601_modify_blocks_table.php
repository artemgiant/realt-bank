<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Проверка существования индекса
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Добавляем slug если не существует
        if (!Schema::hasColumn('blocks', 'slug')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->string('slug')->after('name')->default('');
            });
        }

        // 2. Добавляем developer_id если не существует
        if (!Schema::hasColumn('blocks', 'developer_id')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->foreignId('developer_id')->nullable()->after('complex_id')->constrained('developers')->nullOnDelete();
            });
        }

        // 3. Удаляем year_built если существует
        if (Schema::hasColumn('blocks', 'year_built')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropColumn('year_built');
            });
        }

        // 4. Удаляем floors_total если существует
        if (Schema::hasColumn('blocks', 'floors_total')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropColumn('floors_total');
            });
        }

        // 5. Заполняем slug для существующих записей
        DB::table('blocks')->whereNull('slug')->orWhere('slug', '')->orderBy('id')->chunk(100, function ($blocks) {
            foreach ($blocks as $block) {
                DB::table('blocks')->where('id', $block->id)->update([
                    'slug' => Str::slug($block->name) ?: 'block-' . $block->id,
                ]);
            }
        });

        // 6. Удаляем дубликаты перед созданием уникального индекса
        DB::statement('
            DELETE b1 FROM blocks b1
            INNER JOIN blocks b2
            WHERE b1.id > b2.id
            AND b1.complex_id <=> b2.complex_id
            AND b1.street_id <=> b2.street_id
            AND b1.slug <=> b2.slug
            AND b1.building_number <=> b2.building_number
        ');

        // 7. Добавляем уникальный индекс если не существует
        if (!$this->indexExists('blocks', 'blocks_complex_street_slug_building_unique')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->unique(
                    ['complex_id', 'street_id', 'slug', 'building_number'],
                    'blocks_complex_street_slug_building_unique'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем индекс если существует
        if ($this->indexExists('blocks', 'blocks_complex_street_slug_building_unique')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropUnique('blocks_complex_street_slug_building_unique');
            });
        }

        // Удаляем foreign key и поле developer_id если существует
        if (Schema::hasColumn('blocks', 'developer_id')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropForeign(['developer_id']);
                $table->dropColumn('developer_id');
            });
        }

        // Удаляем slug если существует
        if (Schema::hasColumn('blocks', 'slug')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }

        // Возвращаем старые поля если не существуют
        if (!Schema::hasColumn('blocks', 'floors_total')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->integer('floors_total')->nullable();
            });
        }

        if (!Schema::hasColumn('blocks', 'year_built')) {
            Schema::table('blocks', function (Blueprint $table) {
                $table->integer('year_built')->nullable();
            });
        }
    }
};
