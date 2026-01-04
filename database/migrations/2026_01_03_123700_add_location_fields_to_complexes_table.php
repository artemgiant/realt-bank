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
        // Сначала удалим колонки если есть (с неправильным типом)
        Schema::table('complexes', function (Blueprint $table) {
            if (Schema::hasColumn('complexes', 'city_id')) {
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('complexes', 'district_id')) {
                $table->dropColumn('district_id');
            }
            if (Schema::hasColumn('complexes', 'zone_id')) {
                $table->dropColumn('zone_id');
            }
        });

        // Создаём колонки с правильным типом (int signed, как в cities/districts/zones)
        Schema::table('complexes', function (Blueprint $table) {
            $table->integer('city_id')->nullable()->after('developer_id');
            $table->integer('district_id')->nullable()->after('city_id');
            $table->integer('zone_id')->nullable()->after('district_id');

            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('zone_id')->references('id')->on('zones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['zone_id', 'district_id', 'city_id']);
        });
    }
};
