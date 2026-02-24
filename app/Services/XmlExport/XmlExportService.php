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
     * Returns the number of exported properties.
     */
    public function generateFeed(string $adapterName): int
    {
        $adapter = $this->adapter($adapterName);

        $properties = Property::active()
            ->with([
                'employee',
                'currency',
                'state',
                'city',
                'street',
                'district',
                'wallType',
                'condition',
                'heatingType',
                'buildingType',
                'roomCount',
                'ceilingHeight',
                'photos',
                'features',
            ])
            ->get();

        $dtos = $properties
            ->map(fn (Property $property) => PropertyExportData::fromModel($property))
            ->all();

        $xml = $adapter->generateBatchXml($dtos);

        $path = $this->getFeedPath($adapterName);
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $xml);

        Log::info("XML feed [{$adapterName}] generated", [
            'properties_count' => count($dtos),
            'path' => $path,
        ]);

        return count($dtos);
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
     * @return string[] List of registered adapter names
     */
    public function availableAdapters(): array
    {
        return array_keys($this->adapters);
    }
}
