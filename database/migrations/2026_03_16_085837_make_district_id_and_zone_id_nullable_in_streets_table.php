<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->change();
            $table->unsignedBigInteger('zone_id')->nullable()->change();
        });

        DB::statement('
            UPDATE streets
            SET district_id = NULL
            WHERE district_id IS NOT NULL
              AND deleted_at IS NULL
              AND district_id NOT IN (SELECT id FROM districts)
        ');
    }

    public function down(): void
    {
        Schema::table('streets', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
            $table->unsignedBigInteger('zone_id')->nullable(false)->change();
        });
    }
};
