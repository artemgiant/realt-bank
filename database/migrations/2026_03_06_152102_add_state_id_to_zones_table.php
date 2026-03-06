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
        Schema::table('zones', function (Blueprint $table) {
            if (!Schema::hasColumn('zones', 'state_id')) {
                $table->foreignId('state_id')->nullable()->after('city_id')->constrained()->nullOnDelete()->comment('Регион (область)');
            }
            if (Schema::hasColumn('zones', 'district_id')) {
                $table->dropColumn('district_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            if (!Schema::hasColumn('zones', 'district_id')) {
                $table->unsignedInteger('district_id')->nullable()->after('city_id')->comment('Район');
            }
            if (Schema::hasColumn('zones', 'state_id')) {
                $table->dropForeign(['state_id']);
                $table->dropColumn('state_id');
            }
        });
    }
};
