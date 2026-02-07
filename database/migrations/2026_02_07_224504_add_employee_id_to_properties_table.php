<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('user_id')->constrained('employees')->nullOnDelete();
        });

        // Заполнить employee_id из user_id для существующих записей
        DB::table('properties')
            ->whereNotNull('user_id')
            ->whereNull('employee_id')
            ->update([
                'employee_id' => DB::raw('(SELECT id FROM employees WHERE employees.user_id = properties.user_id LIMIT 1)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
