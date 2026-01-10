<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update currency exchange rates from NBU';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currencyService): int
    {
        $this->info('Fetching exchange rates from NBU...');

        $currencyService->updateRates();

        $this->info('Rates updated successfully.');

        return 0;
    }
}
