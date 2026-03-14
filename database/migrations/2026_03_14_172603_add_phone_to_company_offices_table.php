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
        // 1. Добавляем поле phone (nullable, чтобы не сломать существующие записи)
        Schema::table('company_offices', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('office_number');
        });

        // 2. Заполняем существующие офисы — берём основной телефон из первого контакта компании
        $offices = DB::table('company_offices')->whereNull('phone')->whereNull('deleted_at')->get();

        foreach ($offices as $office) {
            $contactId = DB::table('contactables')
                ->where('contactable_type', 'App\\Models\\Reference\\Company')
                ->where('contactable_id', $office->company_id)
                ->value('contact_id');

            if ($contactId) {
                $phone = DB::table('contact_phones')
                    ->where('contact_id', $contactId)
                    ->orderByDesc('is_primary')
                    ->value('phone');

                if ($phone) {
                    DB::table('company_offices')
                        ->where('id', $office->id)
                        ->update(['phone' => $phone]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_offices', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
