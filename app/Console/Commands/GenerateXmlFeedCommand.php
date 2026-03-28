<?php

namespace App\Console\Commands;

use App\Services\XmlExport\XmlExportService;
use Illuminate\Console\Command;

class GenerateXmlFeedCommand extends Command
{
    protected $signature = 'xml:generate {adapter : Adapter name (e.g. dim_ria)} {--skip-validation : Skip validation and export all properties}';

//php artisan xml:generate dim_ria

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
            $skipValidation = (bool) $this->option('skip-validation');

            if ($skipValidation) {
                $this->warn('Validation is disabled — all properties will be exported.');
            }

            $result = $service->generateFeed($adapterName, $skipValidation);
            $path = $service->getFeedPath($adapterName);

            $this->info("Done! Exported {$result['exported']} properties.");

            if ($result['skipped'] > 0) {
                $logPath = storage_path("app/xml-feeds/{$adapterName}_skipped.log");
                $this->warn("Skipped {$result['skipped']} properties (missing required fields).");
                $this->warn("Details: {$logPath}");
            }

            $this->info("File: {$path}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
