<?php

namespace App\Services\XmlExport;

use App\Models\Property\Property;
use App\Services\XmlExport\Contracts\XmlAdapterInterface;
use App\Services\XmlExport\Dto\PropertyExportData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class XmlExportService
{
    /** @var array<string, XmlAdapterInterface> */
    private array $adapters = [];

    public function registerAdapter(XmlAdapterInterface $adapter): void
    {
        $this->adapters[$adapter->getName()] = $adapter;
    }

    public function adapter(string $name): XmlAdapterInterface
    {
        if (!isset($this->adapters[$name])) {
            throw new \InvalidArgumentException("XML adapter [{$name}] is not registered.");
        }

        return $this->adapters[$name];
    }

    /**
     * Generate full XML feed for an adapter and save to file.
     * Returns ['exported' => int, 'skipped' => int].
     */
    public function generateFeed(string $adapterName): array
    {
        $adapter = $this->adapter($adapterName);

        $properties = Property::active()
            ->with([
                'employee',
                'currency',
                'country',
                'state',
                'city',
                'street',
                'district',
                'zone',
                'dealType',
                'propertyType',
                'wallType',
                'condition',
                'heatingType',
                'buildingType',
                'roomCount',
                'ceilingHeight',
                'photos',
                'features',
                'translations',
            ])
            ->get();

        $dtos = $properties
            ->map(fn (Property $property) => PropertyExportData::fromModel($property))
            ->all();

        // Validate and filter
        $validDtos = [];
        $skipped = [];

        foreach ($dtos as $dto) {
            $missingFields = $adapter->validate($dto);

            if (empty($missingFields)) {
                $validDtos[] = $dto;
            } else {
                $skipped[] = [
                    'id' => $dto->id,
                    'missing' => $missingFields,
                ];
            }
        }

        // Write skipped properties log (overwrite each run)
        $this->writeSkippedLog($adapterName, $skipped);

        $xml = $adapter->generateBatchXml($validDtos);

        $path = $this->getFeedPath($adapterName);
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $xml);

        Log::info("XML feed [{$adapterName}] generated", [
            'exported' => count($validDtos),
            'skipped'  => count($skipped),
            'path'     => $path,
        ]);

        return [
            'exported' => count($validDtos),
            'skipped'  => count($skipped),
        ];
    }

    public function getFeedPath(string $adapterName): string
    {
        return storage_path("app/xml-feeds/{$adapterName}.xml");
    }

    public function getFeedContent(string $adapterName): ?string
    {
        $path = $this->getFeedPath($adapterName);

        if (!file_exists($path)) {
            return null;
        }

        return file_get_contents($path);
    }

    /**
     * Write log of skipped properties to a separate file (overwritten each run).
     */
    private function writeSkippedLog(string $adapterName, array $skipped): void
    {
        $logPath = storage_path("app/xml-feeds/{$adapterName}_skipped.log");
        $directory = dirname($logPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $lines = [];
        $lines[] = "=== XML Feed [{$adapterName}] — {$timestamp} ===";
        $lines[] = "";

        if (empty($skipped)) {
            $lines[] = "Все объекты прошли валидацию. Пропущенных нет.";
        } else {
            $lines[] = "Пропущено объектов: " . count($skipped);
            $lines[] = "";

            foreach ($skipped as $item) {
                $fields = implode(', ', $item['missing']);
                $lines[] = "Объект #{$item['id']} — отсутствуют обязательные поля: {$fields}";
            }
        }

        $lines[] = "";

        file_put_contents($logPath, implode(PHP_EOL, $lines));
    }

    /**
     * @return string[] List of registered adapter names
     */
    public function availableAdapters(): array
    {
        return array_keys($this->adapters);
    }
}
