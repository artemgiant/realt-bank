<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Изменение структуры таблицы контактов
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Удаляем старые поля
            $table->dropColumn([
                'name',
                'role',
                'phone',
                'phone_additional',
                'has_whatsapp',
                'has_viber',
                'has_telegram',
                'notes',
            ]);
        });

        Schema::table('contacts', function (Blueprint $table) {
            // Добавляем новые поля
            $table->string('first_name')->after('id')->comment('Имя');
            $table->string('last_name')->after('first_name')->comment('Фамилия');
            $table->string('middle_name')->nullable()->after('last_name')->comment('Отчество');

            // contact_type: owner, agent, developer
            $table->enum('contact_type', ['owner', 'agent', 'developer'])
                ->nullable()
                ->after('email')
                ->comment('Тип контакта: Владелец/Агент/Девелопер');

            $table->string('tags')->nullable()->after('contact_type')->comment('Теги через запятую');

            // Мессенджеры (ссылки)
            $table->string('telegram')->nullable()->after('tags')->comment('Ссылка Telegram');
            $table->string('viber')->nullable()->after('telegram')->comment('Ссылка Viber');
            $table->string('whatsapp')->nullable()->after('viber')->comment('Ссылка WhatsApp');

            // Документы
            $table->string('passport')->nullable()->after('whatsapp')->comment('Паспорт');
            $table->string('inn')->nullable()->after('passport')->comment('ИНН');

            // Фото и комментарий
            $table->string('photo')->nullable()->after('inn')->comment('Путь к фото');
            $table->text('comment')->nullable()->after('photo')->comment('Комментарий');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Удаляем новые поля
            $table->dropColumn([
                'first_name',
                'last_name',
                'middle_name',
                'contact_type',
                'tags',
                'telegram',
                'viber',
                'whatsapp',
                'passport',
                'inn',
                'photo',
                'comment',
            ]);
        });

        Schema::table('contacts', function (Blueprint $table) {
            // Восстанавливаем старые поля
            $table->string('name')->after('id')->comment('Имя контакта');
            $table->string('role')->nullable()->after('name')->comment('Должность');
            $table->string('phone', 50)->after('role')->comment('Основной телефон');
            $table->string('phone_additional', 50)->nullable()->after('phone')->comment('Доп. телефон');
            $table->boolean('has_whatsapp')->default(false)->after('email')->comment('WhatsApp');
            $table->boolean('has_viber')->default(false)->after('has_whatsapp')->comment('Viber');
            $table->boolean('has_telegram')->default(false)->after('has_viber')->comment('Telegram');
            $table->text('notes')->nullable()->after('has_telegram')->comment('Примечания');
        });
    }
};
