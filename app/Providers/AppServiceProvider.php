<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerMenuComposer();
    }

    /**
     * Реєстрація View Composer для меню CRM.
     */
    protected function registerMenuComposer(): void
    {
        View::composer(['components.crm.sidebar', 'components.crm.header'], function ($view) {
            $menu = $this->getMenu();
            $currentRoute = request()->route()?->getName();

            // Знаходимо активний пункт меню
            $activeMenuItem = collect($menu)->first(function ($item) use ($currentRoute) {
                if ($item['route'] === $currentRoute) {
                    return true;
                }
                // Перевіряємо таби
                foreach ($item['tabs'] as $tab) {
                    if ($tab['route'] === $currentRoute) {
                        return true;
                    }
                }
                // Перевіряємо префікс роуту (properties.* для properties.index)
                $routePrefix = explode('.', $item['route'])[0] ?? '';
                $currentPrefix = explode('.', $currentRoute ?? '')[0] ?? '';
                return $routePrefix === $currentPrefix;
            });

            $view->with([
                'sidebarMenu' => $menu,
                'currentRoute' => $currentRoute,
                'pageTabs' => $activeMenuItem['tabs'] ?? [],
                'pageTitle' => $activeMenuItem['name'] ?? '',
            ]);
        });
    }

    /**
     * Отримання меню з кешу або конфігу.
     */
    protected function getMenu(): array
    {
        $cacheConfig = config('crm-menu.cache');

        if (!$cacheConfig['enabled']) {
            return config('crm-menu.sidebar', []);
        }

        return Cache::store('redis')->remember(
            $cacheConfig['key'],
            $cacheConfig['ttl'],
            fn () => config('crm-menu.sidebar', [])
        );
    }
}
