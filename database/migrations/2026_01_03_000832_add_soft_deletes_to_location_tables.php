<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // States
        if (!Schema::hasColumn('states', 'deleted_at')) {
            Schema::table('states', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Developers
        if (!Schema::hasColumn('developers', 'deleted_at')) {
            Schema::table('developers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Complexes
        if (!Schema::hasColumn('complexes', 'deleted_at')) {
            Schema::table('complexes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('developers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('complexes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
