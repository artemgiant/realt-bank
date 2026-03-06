<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fill NULL values before making NOT NULL
        DB::table('cities')->whereNull('state_id')->update(['state_id' => 14]);

        $fallbackRegionId = DB::table('regions')->where('state_id', 14)->value('id');
        if ($fallbackRegionId) {
            DB::table('cities')->whereNull('region_id')->update(['region_id' => $fallbackRegionId]);
        }

        Schema::table('cities', function (Blueprint $table) {
            // Drop old FK with SET NULL, recreate with RESTRICT
            $table->dropForeign(['region_id']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable(false)->change();
            $table->unsignedBigInteger('region_id')->nullable(false)->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->change();
            $table->unsignedBigInteger('region_id')->nullable()->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->nullOnDelete();
        });
    }
};
