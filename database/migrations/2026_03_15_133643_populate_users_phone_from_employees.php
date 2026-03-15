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
        // Copy phone from linked employee to user
        DB::statement("
            UPDATE users u
            INNER JOIN employees e ON e.user_id = u.id
            SET u.phone = e.phone
            WHERE e.phone IS NOT NULL AND e.phone != ''
        ");

        // For users without an employee phone, set a placeholder to avoid NOT NULL violation
        DB::statement("
            UPDATE users
            SET phone = CONCAT('+0', id)
            WHERE phone IS NULL OR phone = ''
        ");

        // Make phone NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
    }
};
