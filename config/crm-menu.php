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
            'name' => 'Объекты',
            'route' => 'properties.index',
            'permission' => 'properties.view',
            'icon' => 'img/icon/side-bar/Finanse.svg',
            'tabs' => [
                ['label' => 'Объекты', 'route' => 'properties.index', 'permission' => 'properties.view'],
                ['label' => 'Комплексы', 'route' => 'complexes.index', 'permission' => 'complexes.view'],
                ['label' => 'Девелоперы', 'route' => 'developers.index', 'permission' => 'developers.view'],
            ],
        ],
        [
            'name' => 'Компания',
            'route' => 'employees.index',
            'permission' => 'employees.view',
            'icon' => 'img/icon/side-bar/Company1.svg',
            'tabs' => [
                ['label' => 'Сотрудники', 'route' => 'employees.index', 'permission' => 'employees.view'],
                ['label' => 'Компания', 'route' => 'companies.index', 'permission' => 'companies.view'],
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
