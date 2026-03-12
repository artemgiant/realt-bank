<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('city_id')->constrained('regions')->nullOnDelete();
        });

        // Fill region_id from city's region_id
        DB::table('complexes')
            ->whereNull('region_id')
            ->whereNotNull('city_id')
            ->update([
                'region_id' => DB::raw('(SELECT region_id FROM cities WHERE cities.id = complexes.city_id)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
