<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (!$this->indexExists('developers', 'developers_slug_unique')) {
            Schema::table('developers', function (Blueprint $table) {
                $table->unique('slug', 'developers_slug_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('developers', 'developers_slug_unique')) {
            Schema::table('developers', function (Blueprint $table) {
                $table->dropUnique('developers_slug_unique');
            });
        }
    }
};
