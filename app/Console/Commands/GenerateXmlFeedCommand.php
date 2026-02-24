<?php

namespace App\Console\Commands;

use App\Services\XmlExport\XmlExportService;
use Illuminate\Console\Command;

class GenerateXmlFeedCommand extends Command
{
    protected $signature = 'xml:generate {adapter : Adapter name (e.g. dim_ria)}';

    protected $description = 'Generate XML feed for a platform';

    public function handle(XmlExportService $service): int
    {
        $adapterName = $this->argument('adapter');

        $available = $service->availableAdapters();

        if (!in_array($adapterName, $available)) {
            $this->error("Adapter [{$adapterName}] not found. Available: " . implode(', ', $available));

            return self::FAILURE;
        }

        $this->info("Generating XML feed [{$adapterName}]...");

        try {
            $count = $service->generateFeed($adapterName);
            $path = $service->getFeedPath($adapterName);

            $this->info("Done! Exported {$count} properties.");
            $this->info("File: {$path}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
