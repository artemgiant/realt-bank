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
use Illuminate\Support\Str;
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
            'streets' => 0,
        ],
        'skipped' => [
            'developers' => 0,
            'complexes' => 0,
            'blocks' => 0,
            'streets' => 0,
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

            // Пропускаем пустые строки
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $this->processRow($rowNumber, $headers, $row);
        }

        return $this->result;
    }

    /**
     * Проверка на пустую строку
     */
    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }
        return true;
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
            $developer->id,
            $location['street_id'],
            $data['house'] ?? null
        );
    }

    /**
     * Поиск локации по данным строки
     * Country и State - только поиск (ошибка если не найдены)
     * District, Zone, Street - создаются если не найдены
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

        // Country - только поиск
        if (!empty($data['country'])) {
            $country = $this->findCountry($data['country']);
            if (!$country) {
                $this->addError($rowNumber, "Страна \"{$data['country']}\" не найдена");
                return null;
            }
            $result['country_id'] = $country->id;
        }

        // State - только поиск
        if (!empty($data['state'])) {
            $state = $this->findState($data['state'], $result['country_id']);
            if (!$state) {
                $this->addError($rowNumber, "Область \"{$data['state']}\" не найдена");
                return null;
            }
            $result['state_id'] = $state->id;
        }

        // City (обязательно) - только поиск
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

        // District - только поиск
        if (!empty($data['district'])) {
            $district = $this->findDistrict($data['district'], $result['city_id']);
            if (!$district) {
                $this->addError($rowNumber, "Район \"{$data['district']}\" не найден в городе \"{$data['city']}\"");
                return null;
            }
            $result['district_id'] = $district->id;
        }

        // Zone - только поиск
        if (!empty($data['zone'])) {
            $zone = $this->findZone($data['zone'], $result['city_id'], $result['district_id']);
            if (!$zone) {
                $this->addError($rowNumber, "Зона \"{$data['zone']}\" не найдена");
                return null;
            }
            $result['zone_id'] = $zone->id;
        }

        // Street - создаём если не найдена
        if (!empty($data['street'])) {
            $street = $this->findOrCreateStreet($data['street'], $result['city_id'], $result['district_id'], $result['zone_id']);
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
     * Парсинг названия с альтернативой в скобках
     * "Пересыпский (Суворовский)" => ["Пересыпский", "Суворовский"]
     */
    protected function parseNameWithAlternative(string $name): array
    {
        $names = [];

        // Основное название (до скобки)
        if (preg_match('/^([^(]+)/', $name, $matches)) {
            $names[] = trim($matches[1]);
        }

        // Альтернативное название (в скобках)
        if (preg_match('/\(([^)]+)\)/', $name, $matches)) {
            $names[] = trim($matches[1]);
        }

        // Если скобок нет - просто название
        if (empty($names)) {
            $names[] = trim($name);
        }

        return $names;
    }

    /**
     * Поиск района
     */
    protected function findDistrict(string $name, int $cityId): ?District
    {
        $key = mb_strtolower($name) . '_' . $cityId;

        if (!isset($this->cache['districts'][$key])) {
            $district = null;

            // Парсим название с альтернативой
            $names = $this->parseNameWithAlternative($name);

            // Ищем по каждому варианту названия
            foreach ($names as $searchName) {
                $district = District::where('name', 'like', "%{$searchName}%")
                    ->where('city_id', $cityId)
                    ->first();

                if ($district) {
                    break;
                }
            }

            $this->cache['districts'][$key] = $district;
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
     * Найти или создать улицу
     */
    protected function findOrCreateStreet(string $name, int $cityId, ?int $districtId, ?int $zoneId): Street
    {
        $key = mb_strtolower($name) . '_' . $cityId . '_' . $districtId . '_' . $zoneId;

        if (!isset($this->cache['streets'][$key])) {
            $query = Street::where('name', $name)
                ->where('city_id', $cityId);

            if ($districtId) {
                $query->where('district_id', $districtId);
            }

            if ($zoneId) {
                $query->where('zone_id', $zoneId);
            }

            $street = $query->first();

            if (!$street) {
                $street = Street::create([
                    'name' => $name,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'zone_id' => $zoneId,
                ]);
                $this->result['created']['streets']++;
            } else {
                $this->result['skipped']['streets']++;
            }

            $this->cache['streets'][$key] = $street;
        }

        return $this->cache['streets'][$key];
    }

    /**
     * Найти или создать застройщика
     */
    protected function findOrCreateDeveloper(string $name): Developer
    {
        $slug = Str::slug($name);
        $key = $slug;

        if (!isset($this->cache['developers'][$key])) {
            // Ищем по slug (уникальный индекс)
            $developer = Developer::where('slug', $slug)->first();

            if (!$developer) {
                $developer = Developer::create([
                    'name' => $name,
                    'slug' => $slug,
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
     * Уникальный индекс: developer_id, city_id, district_id, zone_id, slug
     */
    protected function findOrCreateComplex(
        string $name,
        int $developerId,
        int $cityId,
        ?int $districtId,
        ?int $zoneId
    ): Complex {
        $slug = Str::slug($name);
        $key = $slug . '_' . $developerId . '_' . $cityId . '_' . $districtId . '_' . $zoneId;

        if (!isset($this->cache['complexes'][$key])) {
            // Ищем по полям уникального индекса
            $complex = Complex::where('developer_id', $developerId)
                ->where('city_id', $cityId)
                ->where('district_id', $districtId)
                ->where('zone_id', $zoneId)
                ->where('slug', $slug)
                ->first();

            if (!$complex) {
                $complex = Complex::create([
                    'name' => $name,
                    'slug' => $slug,
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
     * Уникальный индекс: complex_id, street_id, slug, building_number
     */
    protected function findOrCreateBlock(
        string $name,
        int $complexId,
        int $developerId,
        ?int $streetId,
        ?string $buildingNumber
    ): Block {
        $slug = Str::slug($name);

        // Ищем по полям уникального индекса
        $block = Block::where('complex_id', $complexId)
            ->where('street_id', $streetId)
            ->where('slug', $slug)
            ->where('building_number', $buildingNumber)
            ->first();

        if (!$block) {
            $block = Block::create([
                'name' => $name,
                'slug' => $slug,
                'complex_id' => $complexId,
                'developer_id' => $developerId,
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
