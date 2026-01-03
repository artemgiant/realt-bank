<?php

namespace App\Services\Import;

use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\State;
use App\Models\Location\Street;
use App\Models\Location\Zone;
use App\Models\Reference\Block;
use App\Models\Reference\Complex;
use App\Models\Reference\Developer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ComplexImportService
{
    /**
     * Результаты импорта
     */
    protected array $result = [
        'created' => [
            'developers' => 0,
            'complexes' => 0,
            'blocks' => 0,
        ],
        'skipped' => [
            'developers' => 0,
            'complexes' => 0,
            'blocks' => 0,
        ],
        'errors' => [],
        'total_rows' => 0,
    ];

    /**
     * Кэш для избежания повторных запросов
     */
    protected array $cache = [
        'countries' => [],
        'states' => [],
        'cities' => [],
        'districts' => [],
        'zones' => [],
        'streets' => [],
        'developers' => [],
        'complexes' => [],
    ];

    /**
     * Импорт из Excel файла
     */
    public function import(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Первая строка - заголовки
        $headers = array_shift($rows);
        $headers = array_map('trim', $headers);
        $headers = array_map('mb_strtolower', $headers);

        $this->result['total_rows'] = count($rows);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 потому что массив с 0, и пропустили заголовок
            $this->processRow($rowNumber, $headers, $row);
        }

        return $this->result;
    }

    /**
     * Обработка одной строки
     */
    protected function processRow(int $rowNumber, array $headers, array $row): void
    {
        // Преобразуем в ассоциативный массив
        $data = [];
        foreach ($headers as $i => $header) {
            $data[$header] = isset($row[$i]) ? trim($row[$i]) : '';
        }

        // Проверка обязательных полей
        if (empty($data['developer']) || empty($data['complex']) || empty($data['block'])) {
            $this->addError($rowNumber, 'Отсутствуют обязательные поля: developer, complex или block');
            return;
        }

        // 1. Находим локацию
        $location = $this->findLocation($rowNumber, $data);
        if ($location === null) {
            return; // Ошибка уже добавлена в findLocation
        }

        // 2. Находим или создаём Developer
        $developer = $this->findOrCreateDeveloper($data['developer']);

        // 3. Находим или создаём Complex
        $complex = $this->findOrCreateComplex(
            $data['complex'],
            $developer->id,
            $location['city_id'],
            $location['district_id'],
            $location['zone_id']
        );

        // 4. Находим или создаём Block
        $this->findOrCreateBlock(
            $data['block'],
            $complex->id,
            $location['street_id'],
            $data['house'] ?? null
        );
    }

    /**
     * Поиск локации по данным строки
     */
    protected function findLocation(int $rowNumber, array $data): ?array
    {
        $result = [
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'district_id' => null,
            'zone_id' => null,
            'street_id' => null,
        ];

        // Country
        if (!empty($data['country'])) {
            $country = $this->findCountry($data['country']);
            if (!$country) {
                $this->addError($rowNumber, "Страна \"{$data['country']}\" не найдена");
                return null;
            }
            $result['country_id'] = $country->id;
        }

        // State
        if (!empty($data['state'])) {
            $state = $this->findState($data['state'], $result['country_id']);
            if (!$state) {
                $this->addError($rowNumber, "Область \"{$data['state']}\" не найдена");
                return null;
            }
            $result['state_id'] = $state->id;
        }

        // City (обязательно)
        if (empty($data['city'])) {
            $this->addError($rowNumber, 'Город не указан');
            return null;
        }

        $city = $this->findCity($data['city'], $result['state_id']);
        if (!$city) {
            $this->addError($rowNumber, "Город \"{$data['city']}\" не найден");
            return null;
        }
        $result['city_id'] = $city->id;

        // District
        if (!empty($data['district'])) {
            $district = $this->findDistrict($data['district'], $result['city_id']);
            if (!$district) {
                $this->addError($rowNumber, "Район \"{$data['district']}\" не найден в городе \"{$data['city']}\"");
                return null;
            }
            $result['district_id'] = $district->id;
        }

        // Zone
        if (!empty($data['zone'])) {
            $zone = $this->findZone($data['zone'], $result['city_id'], $result['district_id']);
            if (!$zone) {
                $this->addError($rowNumber, "Зона \"{$data['zone']}\" не найдена в районе \"{$data['district']}\"");
                return null;
            }
            $result['zone_id'] = $zone->id;
        }

        // Street
        if (!empty($data['street'])) {
            $street = $this->findStreet($data['street'], $result['city_id'], $result['district_id'], $result['zone_id']);
            if (!$street) {
                $this->addError($rowNumber, "Улица \"{$data['street']}\" не найдена");
                return null;
            }
            $result['street_id'] = $street->id;
        }

        return $result;
    }

    /**
     * Поиск страны
     */
    protected function findCountry(string $name): ?Country
    {
        $key = mb_strtolower($name);

        if (!isset($this->cache['countries'][$key])) {
            $this->cache['countries'][$key] = Country::where('name', 'like', $name)->first();
        }

        return $this->cache['countries'][$key];
    }

    /**
     * Поиск области
     */
    protected function findState(string $name, ?int $countryId): ?State
    {
        $key = mb_strtolower($name) . '_' . $countryId;

        if (!isset($this->cache['states'][$key])) {
            $query = State::where('name', 'like', "%{$name}%");
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            $this->cache['states'][$key] = $query->first();
        }

        return $this->cache['states'][$key];
    }

    /**
     * Поиск города
     */
    protected function findCity(string $name, ?int $stateId): ?City
    {
        $key = mb_strtolower($name) . '_' . $stateId;

        if (!isset($this->cache['cities'][$key])) {
            $query = City::where('name', 'like', $name);
            if ($stateId) {
                $query->where('state_id', $stateId);
            }
            $this->cache['cities'][$key] = $query->first();
        }

        return $this->cache['cities'][$key];
    }

    /**
     * Поиск района
     */
    protected function findDistrict(string $name, int $cityId): ?District
    {
        $key = mb_strtolower($name) . '_' . $cityId;

        if (!isset($this->cache['districts'][$key])) {
            $this->cache['districts'][$key] = District::where('name', 'like', "%{$name}%")
                ->where('city_id', $cityId)
                ->first();
        }

        return $this->cache['districts'][$key];
    }

    /**
     * Поиск зоны
     */
    protected function findZone(string $name, int $cityId, ?int $districtId): ?Zone
    {
        $key = mb_strtolower($name) . '_' . $cityId . '_' . $districtId;

        if (!isset($this->cache['zones'][$key])) {
            $query = Zone::where('name', 'like', "%{$name}%")
                ->where('city_id', $cityId);

            if ($districtId) {
                $query->where('district_id', $districtId);
            }

            $this->cache['zones'][$key] = $query->first();
        }

        return $this->cache['zones'][$key];
    }

    /**
     * Поиск улицы
     */
    protected function findStreet(string $name, int $cityId, ?int $districtId, ?int $zoneId): ?Street
    {
        $key = mb_strtolower($name) . '_' . $cityId . '_' . $districtId . '_' . $zoneId;

        if (!isset($this->cache['streets'][$key])) {
            $query = Street::where('name', 'like', "%{$name}%")
                ->where('city_id', $cityId);

            if ($districtId) {
                $query->where('district_id', $districtId);
            }

            if ($zoneId) {
                $query->where('zone_id', $zoneId);
            }

            $this->cache['streets'][$key] = $query->first();
        }

        return $this->cache['streets'][$key];
    }

    /**
     * Найти или создать застройщика
     */
    protected function findOrCreateDeveloper(string $name): Developer
    {
        $key = mb_strtolower($name);

        if (!isset($this->cache['developers'][$key])) {
            $developer = Developer::where('name', $name)->first();

            if (!$developer) {
                $developer = Developer::create([
                    'name' => $name,
                    'is_active' => true,
                ]);
                $this->result['created']['developers']++;
            } else {
                $this->result['skipped']['developers']++;
            }

            $this->cache['developers'][$key] = $developer;
        }

        return $this->cache['developers'][$key];
    }

    /**
     * Найти или создать комплекс
     */
    protected function findOrCreateComplex(
        string $name,
        int $developerId,
        int $cityId,
        ?int $districtId,
        ?int $zoneId
    ): Complex {
        $key = mb_strtolower($name) . '_' . $developerId;

        if (!isset($this->cache['complexes'][$key])) {
            $complex = Complex::where('name', $name)
                ->where('developer_id', $developerId)
                ->first();

            if (!$complex) {
                $complex = Complex::create([
                    'name' => $name,
                    'developer_id' => $developerId,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'zone_id' => $zoneId,
                    'is_active' => true,
                ]);
                $this->result['created']['complexes']++;
            } else {
                $this->result['skipped']['complexes']++;
            }

            $this->cache['complexes'][$key] = $complex;
        }

        return $this->cache['complexes'][$key];
    }

    /**
     * Найти или создать блок
     */
    protected function findOrCreateBlock(
        string $name,
        int $complexId,
        ?int $streetId,
        ?string $buildingNumber
    ): Block {
        $block = Block::where('name', $name)
            ->where('complex_id', $complexId)
            ->first();

        if (!$block) {
            $block = Block::create([
                'name' => $name,
                'complex_id' => $complexId,
                'street_id' => $streetId,
                'building_number' => $buildingNumber,
                'is_active' => true,
            ]);
            $this->result['created']['blocks']++;
        } else {
            $this->result['skipped']['blocks']++;
        }

        return $block;
    }

    /**
     * Добавить ошибку
     */
    protected function addError(int $rowNumber, string $message): void
    {
        $this->result['errors'][] = [
            'row' => $rowNumber,
            'message' => $message,
        ];
    }
}
