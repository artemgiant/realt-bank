<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Set timestamps for all districts
        DB::table('districts')
            ->whereNull('created_at')
            ->update(['created_at' => now(), 'updated_at' => now()]);

        // Drop legacy columns
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn(['id_1', 'id_2', 'dom_ria']);
        });
    }

    public function down(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->integer('id_1')->nullable();
            $table->integer('id_2')->nullable();
            $table->string('dom_ria')->nullable();
        });
    }
};
