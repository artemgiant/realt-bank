<?php

namespace App\Services\XmlExport\Contracts;

use App\Services\XmlExport\Dto\PropertyExportData;

interface XmlAdapterInterface
{
    public function getName(): string;

    public function getRootElement(): string;

    public function toArray(PropertyExportData $dto): array;

    /**
     * Validate DTO has all required fields.
     * Returns array of missing field names, empty if valid.
     *
     * @return string[]
     */
    public function validate(PropertyExportData $dto): array;

    public function generateXml(PropertyExportData $dto): string;

    /**
     * @param PropertyExportData[] $items
     */
    public function generateBatchXml(array $items): string;
}
