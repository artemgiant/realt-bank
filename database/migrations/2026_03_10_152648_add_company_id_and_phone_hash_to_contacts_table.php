<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')
                ->comment('Компания контакта')
                ->constrained('companies')->nullOnDelete();

            $table->string('phone_hash', 64)->nullable()->unique()->after('comment')
                ->comment('Уникальный хеш: sha256(id + все телефоны) — защита от дублей');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'phone_hash']);
        });
    }
};
