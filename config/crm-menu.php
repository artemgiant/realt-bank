<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu
    |--------------------------------------------------------------------------
    |
    | Головне меню в sidebar. Кожен пункт містить:
    | - name: назва пункту
    | - route: ім'я роуту (для url та визначення active)
    | - icon: шлях до іконки
    | - tabs: масив табів для header (якщо є)
    |
    */

    'sidebar' => [
        [
            'name' => 'Недвижимость',
            'route' => 'properties.index',
            'icon' => 'img/icon/side-bar/Finanse.svg',
            'tabs' => [
                ['label' => 'Объекты', 'route' => 'properties.index'],
                ['label' => 'Комплексы', 'route' => 'complexes.index'],
                ['label' => 'Девелоперы', 'route' => 'developers.index'],
//                ['label' => 'Девелоперы', 'route' => 'developers.index'],
            ],
        ],
//        [
//            'name' => 'Сделки',
//            'route' => 'deals.index',
//            'icon' => 'img/icon/side-bar/Deals.svg',
//            'tabs' => [],
//        ],
//        [
//            'name' => 'Задачи',
//            'route' => 'tasks.index',
//            'icon' => 'img/icon/side-bar/Tasks.svg',
//            'tabs' => [],
//        ],
        [
            'name' => 'Компания',
            'route' => 'employees.index',
            'icon' => 'img/icon/side-bar/Company1.svg',
            'tabs' => [
                ['label' => 'Команда', 'route' => 'employees.index'],

                ['label' => 'Компании', 'route' => 'companies.index'],

            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('CRM_MENU_CACHE', false),
        'ttl' => 3600, // 1 година
        'key' => 'crm:menu',
    ],

];
