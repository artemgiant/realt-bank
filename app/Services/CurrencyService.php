<?php

namespace App\Services;

use App\Models\Reference\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Обновление курсов валют с НБУ
     */
    public function updateRates(): void
    {
        try {
            // Получаем курсы с НБУ (в формате JSON)
            $response = Http::get('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json');

            if ($response->successful()) {
                $rates = $response->json();

                // Преобразуем массив для быстрого поиска по коду валюты
                $ratesByCode = [];
                foreach ($rates as $rateData) {
                    if (isset($rateData['cc'], $rateData['rate'])) {
                        $ratesByCode[$rateData['cc']] = $rateData['rate'];
                    }
                }

                $currencies = Currency::all();

                foreach ($currencies as $currency) {
                    // Базовая валюта UAH всегда 1
                    if ($currency->code === 'UAH') {
                        $currency->update(['rate' => 1.0000]);
                        continue;
                    }

                    // Если нашли курс для валюты
                    if (isset($ratesByCode[$currency->code])) {
                        // НБУ дает курс единицы валюты к гривне (напр. 1 USD = 41.50 UAH)
                        // Мы храним курс именно так: сколько гривен стоит 1 единица этой валюты
                        $rate = $ratesByCode[$currency->code];
                        $currency->update(['rate' => $rate]);
                    }
                }

                Log::info('Currency rates updated successfully.');
            } else {
                Log::error('Failed to fetch currency rates from NBU.', ['status' => $response->status()]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating currency rates: ' . $e->getMessage());
        }
    }
}
