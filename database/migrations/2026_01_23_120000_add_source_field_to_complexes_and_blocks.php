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
            $table->string('source', 50)->default('manual')->after('is_active')
                ->comment('Источник создания: manual - вручную, import - импорт');
            $table->index('source');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->string('source', 50)->default('manual')->after('is_active')
                ->comment('Источник создания: manual - вручную, import - импорт');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complexes', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};
