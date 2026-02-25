<?php

namespace App\Services\XmlExport\Providers;

use App\Services\XmlExport\Adapters\DimRiaAdapter;
use App\Services\XmlExport\Adapters\RemAdapter;
use App\Services\XmlExport\XmlExportService;
use Illuminate\Support\ServiceProvider;

class XmlExportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(XmlExportService::class, function () {
            $service = new XmlExportService();
            $service->registerAdapter(new DimRiaAdapter());
            $service->registerAdapter(new RemAdapter());

            return $service;
        });
    }
}
