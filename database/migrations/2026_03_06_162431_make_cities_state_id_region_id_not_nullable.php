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

        // Drop existing FK on region_id (name may vary between environments)
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'cities'
              AND COLUMN_NAME = 'region_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if ($fk) {
            Schema::table('cities', function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }

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
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'cities'
              AND COLUMN_NAME = 'region_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if ($fk) {
            Schema::table('cities', function (Blueprint $table) use ($fk) {
                $table->dropForeign($fk->CONSTRAINT_NAME);
            });
        }

        Schema::table('cities', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->change();
            $table->unsignedBigInteger('region_id')->nullable()->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->nullOnDelete();
        });
    }
};
