<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop column if exists from failed migration (wrong type)
        if (Schema::hasColumn('zones', 'district_id')) {
            Schema::table('zones', function (Blueprint $table) {
                $table->dropColumn('district_id');
            });
        }

        Schema::table('zones', function (Blueprint $table) {
            $table->integer('district_id')->nullable()->after('city_id');
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
        });

        // Set state_id = 14 for all zones
        DB::table('zones')->whereNull('state_id')->update(['state_id' => 14]);
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
        });

        DB::table('zones')->where('state_id', 14)->update(['state_id' => null]);
    }
};
