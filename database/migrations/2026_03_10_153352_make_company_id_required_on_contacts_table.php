<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Удаляем старый FK если существует
        $this->dropForeignIfExists('contacts', 'company_id');

        Schema::table('contacts', function (Blueprint $table) {
            // Делаем колонку NOT NULL
            $table->foreignId('company_id')->nullable(false)->change();

            // Новый FK с restrictOnDelete (нельзя удалить компанию, если есть контакты)
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        $this->dropForeignIfExists('contacts', 'company_id');

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->nullOnDelete();
        });
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND TABLE_SCHEMA = DATABASE()
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table, $column]);

        if ($fk) {
            Schema::table($table, function (Blueprint $t) use ($fk) {
                $t->dropForeign($fk->CONSTRAINT_NAME);
            });
        }
    }
};
