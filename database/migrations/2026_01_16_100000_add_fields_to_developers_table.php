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
        Schema::table('developers', function (Blueprint $table) {
            // Add logo path
            $table->string('logo_path')->nullable()->after('description')->comment('Путь к логотипу');

            // Add year founded
            $table->year('year_founded')->nullable()->after('logo_path')->comment('Год основания');

            // Add agent notes
            $table->text('agent_notes')->nullable()->after('year_founded')->comment('Примечание для агентов');

            // Add contact relationship
            $table->foreignId('contact_id')->nullable()->after('id')
                ->constrained('contacts')
                ->nullOnDelete()
                ->comment('Контакт девелопера');

            // Add multilingual fields (JSON)
            $table->json('name_translations')->nullable()->after('name')->comment('Переводы названия');
            $table->json('description_translations')->nullable()->after('description')->comment('Переводы описания');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developers', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropColumn([
                'logo_path',
                'year_founded',
                'agent_notes',
                'contact_id',
                'name_translations',
                'description_translations'
            ]);
        });
    }
};
