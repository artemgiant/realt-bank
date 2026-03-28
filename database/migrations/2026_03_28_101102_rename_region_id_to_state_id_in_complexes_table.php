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
            $table->dropForeign(['region_id']);
            $table->renameColumn('region_id', 'state_id');
        });

        Schema::table('complexes', function (Blueprint $table) {
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->renameColumn('state_id', 'region_id');
        });

        Schema::table('complexes', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->nullOnDelete();
        });
    }
};
