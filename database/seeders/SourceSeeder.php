<?php

namespace Database\Seeders;

use App\Models\Reference\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            // Рекомендации
            'Рекомендации клиентов',
            'Рекомендации партнёров',
            'Рекомендации друзей/знакомых',
            'Повторное обращение (старый клиент)',

            // Платная реклама
            'Google Ads (поиск)',
            'Google Ads (баннерная сеть)',
            'Facebook Ads / Instagram Ads',
            'YouTube реклама',
            'TikTok Ads',
            'Реклама в мессенджерах (Telegram, Viber)',
            'Контекстная реклама (другие сети)',
            'Баннеры / наружная реклама',
            'Радио',
            'ТВ',

            // Социальные сети (органика)
            'Instagram (органика)',
            'Facebook (органика)',
            'TikTok (органика)',
            'YouTube (органика)',
            'LinkedIn',
            'Telegram канал',
            'Viber сообщество',

            // Сайт и онлайн
            'Сайт компании (прямой заход)',
            'Лендинг под объект',
            'Блог компании',
            'Онлайн-чат на сайте',
            'Форма обратной связи',
            'CRM-виджет / лид-форма на сайте',

            // Порталы недвижимости (Украина)
            'OLX',
            'DomRia',
            'Rieltor.ua',
            'Flatfy',
            'Lun.ua',
            'Besplatka.ua',
            'Krysha.ua',

            // Порталы недвижимости (международные)
            'Realtor.com',
            'Zillow',
            'Idealista (Испания)',
            'Properstar',
            'Facebook Marketplace',

            // Офлайн
            'Офис компании (walk-in)',
            'Выставка недвижимости',
            'Семинар / мастер-класс',
            'Печатные издания (газеты, журналы)',
            'Раздатка / флаеры',
            'Инфостенды в ЖК',
            'Вывеска на объекте ("Продам"/"Сдам")',

            // Партнёры
            'Партнёр-застройщик',
            'Другое агентство недвижимости',
            'Банки / ипотечные брокеры',
            'Юридические компании',
            'Оценщики / нотариусы',

            // Прямые контакты
            'Холодный звонок',
            'Email-рассылка',
            'Личное сообщение в мессенджере',
            'Запрос по телефону',

            // Другое
            'Immobilium.io',
        ];

        foreach ($sources as $name) {
            Source::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true,
                ]
            );
        }
    }
}
