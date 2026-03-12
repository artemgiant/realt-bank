<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillPropertyRegionId extends Command
{
    protected $signature = 'properties:fill-region-id';

    protected $description = 'Fill region_id for properties from their city\'s region_id';

    public function handle(): int
    {
        $updated = DB::table('properties')
            ->whereNull('region_id')
            ->whereNotNull('city_id')
            ->update([
                'region_id' => DB::raw('(SELECT region_id FROM cities WHERE cities.id = properties.city_id)'),
            ]);

        $this->info("Updated {$updated} properties.");

        return Command::SUCCESS;
    }
}
