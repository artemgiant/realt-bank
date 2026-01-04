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
        Schema::table('complexes', function (Blueprint $table) {
            // Уникальный индекс
            $table->unique(
                ['developer_id', 'city_id', 'district_id', 'zone_id', 'slug'],
                'complexes_developer_city_district_zone_slug_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropUnique('complexes_developer_city_district_zone_slug_unique');
        });
    }
};
