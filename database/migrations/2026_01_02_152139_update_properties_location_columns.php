<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Обновление колонок локации в таблице properties
     * region_id -> state_id
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Переименовываем region_id -> state_id
            if (Schema::hasColumn('properties', 'region_id') && !Schema::hasColumn('properties', 'state_id')) {
                $table->renameColumn('region_id', 'state_id');
            }
        });
    }

    /**
     * Откат миграции
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Переименовываем state_id -> region_id
            if (Schema::hasColumn('properties', 'state_id') && !Schema::hasColumn('properties', 'region_id')) {
                $table->renameColumn('state_id', 'region_id');
            }
        });
    }
};
