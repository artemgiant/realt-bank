<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Отключаем проверку foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ========== 1. Переименовываем lib_* таблицы ==========
        if (Schema::hasTable('lib_towns') && !Schema::hasTable('cities')) {
            Schema::rename('lib_towns', 'cities');
        }

        if (Schema::hasTable('lib_regions') && !Schema::hasTable('districts')) {
            Schema::rename('lib_regions', 'districts');
        }

        if (Schema::hasTable('lib_zones') && !Schema::hasTable('zones')) {
            Schema::rename('lib_zones', 'zones');
        }

        if (Schema::hasTable('lib_streets') && !Schema::hasTable('streets')) {
            Schema::rename('lib_streets', 'streets');
        }

        // Включаем проверку foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ========== 2. Обновляем структуру cities ==========
        if (Schema::hasTable('cities')) {
            Schema::table('cities', function (Blueprint $table) {
                if (!Schema::hasColumn('cities', 'type')) {
                    $table->string('type', 20)->default('city')->after('name');
                }
                if (!Schema::hasColumn('cities', 'state_id')) {
                    $table->unsignedBigInteger('state_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('cities', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }
                if (!Schema::hasColumn('cities', 'created_at')) {
                    $table->timestamps();
                }
            });

            // Конвертируем deleted -> deleted_at
            if (Schema::hasColumn('cities', 'deleted')) {
                DB::statement("UPDATE cities SET deleted_at = NOW() WHERE deleted = 1");
                Schema::table('cities', function (Blueprint $table) {
                    $table->dropColumn('deleted');
                });
            }
        }

        // ========== 3. Обновляем структуру districts ==========
        if (Schema::hasTable('districts')) {
            Schema::table('districts', function (Blueprint $table) {
                if (Schema::hasColumn('districts', 'town_id')) {
                    $table->renameColumn('town_id', 'city_id');
                }
                if (!Schema::hasColumn('districts', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }
                if (!Schema::hasColumn('districts', 'created_at')) {
                    $table->timestamps();
                }
            });

            if (Schema::hasColumn('districts', 'deleted')) {
                DB::statement("UPDATE districts SET deleted_at = NOW() WHERE deleted = 1");
                Schema::table('districts', function (Blueprint $table) {
                    $table->dropColumn('deleted');
                });
            }
        }

        // ========== 4. Обновляем структуру zones ==========
        if (Schema::hasTable('zones')) {
            Schema::table('zones', function (Blueprint $table) {
                if (Schema::hasColumn('zones', 'town_id')) {
                    $table->renameColumn('town_id', 'city_id');
                }
                if (Schema::hasColumn('zones', 'region_id')) {
                    $table->renameColumn('region_id', 'district_id');
                }
                if (!Schema::hasColumn('zones', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }
                if (!Schema::hasColumn('zones', 'created_at')) {
                    $table->timestamps();
                }
            });

            if (Schema::hasColumn('zones', 'deleted')) {
                DB::statement("UPDATE zones SET deleted_at = NOW() WHERE deleted = 1");
                Schema::table('zones', function (Blueprint $table) {
                    $table->dropColumn('deleted');
                });
            }
        }

        // ========== 5. Обновляем структуру streets ==========
        if (Schema::hasTable('streets')) {
            Schema::table('streets', function (Blueprint $table) {
                if (Schema::hasColumn('streets', 'town_id')) {
                    $table->renameColumn('town_id', 'city_id');
                }
                if (Schema::hasColumn('streets', 'region_id')) {
                    $table->renameColumn('region_id', 'district_id');
                }
                if (!Schema::hasColumn('streets', 'deleted_at')) {
                    $table->timestamp('deleted_at')->nullable();
                }
                if (!Schema::hasColumn('streets', 'created_at')) {
                    $table->timestamps();
                }
            });

            if (Schema::hasColumn('streets', 'deleted')) {
                DB::statement("UPDATE streets SET deleted_at = NOW() WHERE deleted = 1");
                Schema::table('streets', function (Blueprint $table) {
                    $table->dropColumn('deleted');
                });
            }
        }
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Откат структуры streets
        if (Schema::hasTable('streets')) {
            Schema::table('streets', function (Blueprint $table) {
                if (Schema::hasColumn('streets', 'city_id')) {
                    $table->renameColumn('city_id', 'town_id');
                }
                if (Schema::hasColumn('streets', 'district_id')) {
                    $table->renameColumn('district_id', 'region_id');
                }
            });
            Schema::rename('streets', 'lib_streets');
        }

        // Откат структуры zones
        if (Schema::hasTable('zones')) {
            Schema::table('zones', function (Blueprint $table) {
                if (Schema::hasColumn('zones', 'city_id')) {
                    $table->renameColumn('city_id', 'town_id');
                }
                if (Schema::hasColumn('zones', 'district_id')) {
                    $table->renameColumn('district_id', 'region_id');
                }
            });
            Schema::rename('zones', 'lib_zones');
        }

        // Откат структуры districts
        if (Schema::hasTable('districts')) {
            Schema::table('districts', function (Blueprint $table) {
                if (Schema::hasColumn('districts', 'city_id')) {
                    $table->renameColumn('city_id', 'town_id');
                }
            });
            Schema::rename('districts', 'lib_regions');
        }

        // Откат cities
        if (Schema::hasTable('cities')) {
            Schema::rename('cities', 'lib_towns');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
