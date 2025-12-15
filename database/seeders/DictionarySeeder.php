<?php

namespace Database\Seeders;

use App\Models\Reference\Dictionary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dictionaries = [
            // Тип угоди
            Dictionary::TYPE_DEAL_TYPE => [
                'Продаж квартир',
                'Продаж будинків',
                'Продаж землі',
                'Продаж комерції',
                'Оренда квартир',
                'Оренда будинків',
                'Оренда землі',
                'Оренда комерції',
            ],

            // Вид угоди
            Dictionary::TYPE_DEAL_KIND => [
                'Нотаріальна',
                'Переуступка',
                'Розстрочка',
                'Іпотека',
            ],

            // Тип будівлі
            Dictionary::TYPE_BUILDING_TYPE => [
                'Новобуд',
                'Старий фонд',
                'Сталінка',
                'Хрущовка',
                'Чешка',
                'Панельний',
            ],

            // Тип нерухомості
            Dictionary::TYPE_PROPERTY_TYPE => [
                'Квартира',
                'Будинок',
                'Таунхаус',
                'Дуплекс',
                'Пентхаус',
                'Комерція',
                'Офіс',
                'Магазин',
                'Склад',
                'Земельна ділянка',
            ],

            // Стан
            Dictionary::TYPE_CONDITION => [
                'Без ремонту',
                'Потребує ремонту',
                'Косметичний ремонт',
                'Хороший стан',
                'Євроремонт',
                'Дизайнерський ремонт',
                'Від забудовника',
            ],

            // Тип стін
            Dictionary::TYPE_WALL_TYPE => [
                'Цегла',
                'Панель',
                'Моноліт',
                'Газоблок',
                'Піноблок',
                'Керамоблок',
                'Дерево',
                'Каркасний',
            ],

            // Опалення
            Dictionary::TYPE_HEATING_TYPE => [
                'Централізоване',
                'Автономне газове',
                'Автономне електричне',
                'Індивідуальне',
                'Твердопаливний котел',
                'Тепловий насос',
                'Без опалення',
            ],

            // Кількість кімнат
            Dictionary::TYPE_ROOM_COUNT => [
                ['name' => '1 кімната', 'value' => '1'],
                ['name' => '2 кімнати', 'value' => '2'],
                ['name' => '3 кімнати', 'value' => '3'],
                ['name' => '4 кімнати', 'value' => '4'],
                ['name' => '5+ кімнат', 'value' => '5+'],
            ],

            // Кількість ванних
            Dictionary::TYPE_BATHROOM_COUNT => [
                ['name' => '1 ванна', 'value' => '1'],
                ['name' => '2 ванні', 'value' => '2'],
                ['name' => '3+ ванні', 'value' => '3+'],
            ],

            // Висота стелі
            Dictionary::TYPE_CEILING_HEIGHT => [
                ['name' => 'До 2.5м', 'value' => '2.5'],
                ['name' => '2.5 - 2.7м', 'value' => '2.7'],
                ['name' => '2.7 - 3м', 'value' => '3.0'],
                ['name' => '3 - 3.5м', 'value' => '3.5'],
                ['name' => 'Вище 3.5м', 'value' => '4.0'],
            ],

            // Особливості
            Dictionary::TYPE_FEATURE => [
                'Від посередника',
                'Держпрограми',
                'Меблі',
                'Побутова техніка',
                'Кондиціонер',
                'Балкон',
                'Лоджія',
                'Тераса',
                'Гараж',
                'Паркінг',
                'Підвал',
                'Горище',
                'Охорона',
                'Консьєрж',
                'Ліфт',
                'Дитячий майданчик',
                'Закрита територія',
                'Відеоспостереження',
                'Басейн',
                'Сауна',
                'Камін',
                'Тепла підлога',
                'Панорамні вікна',
                'Вид на море',
                'Вид на парк',
                'Перший поверх',
                'Останній поверх',
                'Дворівнева',
            ],

            // Теги контактів
            Dictionary::TYPE_CONTACT_TAG => [
                'Власник',
                'Посередник',
                'Агент',
                'Забудовник',
                'VIP',
                'Проблемний',
                'Постійний клієнт',
            ],
        ];

        foreach ($dictionaries as $type => $items) {
            $sortOrder = 0;

            foreach ($items as $item) {
                $sortOrder += 10;

                // Якщо елемент - масив з name та value
                if (is_array($item)) {
                    $name = $item['name'];
                    $value = $item['value'] ?? null;
                } else {
                    $name = $item;
                    $value = null;
                }

                Dictionary::updateOrCreate(
                    [
                        'type' => $type,
                        'slug' => Str::slug($name),
                    ],
                    [
                        'type' => $type,
                        'name' => $name,
                        'value' => $value,
                        'slug' => Str::slug($name),
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
