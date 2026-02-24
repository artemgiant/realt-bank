# XML Export Service

Система генерации XML-фидов для экспорта объектов недвижимости на площадки (DIM.RIA, OLX и др.).

---

## Структура сервиса

```
app/Services/XmlExport/
├── Contracts/
│   └── XmlAdapterInterface.php        # Интерфейс адаптера
├── Dto/
│   └── PropertyExportData.php         # DTO — данные объекта для экспорта
├── Adapters/
│   ├── AbstractXmlAdapter.php         # Базовый класс адаптеров
│   └── DimRiaAdapter.php              # Адаптер DIM.RIA
├── Mappings/
│   └── DimRiaMappings.php             # Маппинг значений Dictionary → DIM.RIA
├── Providers/
│   └── XmlExportServiceProvider.php   # Регистрация в контейнере Laravel
├── XmlExportService.php               # Главный сервис (точка входа)
└── README.md                          # Этот файл

Связанные файлы вне папки сервиса:
├── app/Console/Commands/GenerateXmlFeedCommand.php   # Artisan-команда
├── app/Http/Controllers/XmlFeedController.php        # Контроллер для отдачи XML
├── bootstrap/providers.php                           # Регистрация провайдера
└── routes/web.php                                    # Маршрут /xml-feed/{adapter}
```

---

## Как это работает (поток данных)

```
1. Artisan-команда или Job
   │  xml:generate dim_ria
   ▼
2. XmlExportService::generateFeed('dim_ria')
   │  Загружает все Property со статусом 'active'
   │  с eager-load всех нужных связей
   ▼
3. PropertyExportData::fromModel($property)
   │  Каждый Property → DTO (чистые данные без Eloquent)
   │  Извлекает: employee->email/phone, state->name,
   │  city->name, photos->url, features->name, etc.
   ▼
4. DimRiaAdapter::toArray($dto)
   │  DTO → массив в формате DIM.RIA
   │  Подставляет константы (Продажа, Квартира, улица...)
   │  Маппит значения через DimRiaMappings
   │  Собирает features → да/нет поля
   ▼
5. AbstractXmlAdapter::filterEmpty() → ArrayToXml::convert()
   │  Убирает null/пустые значения
   │  Конвертирует массив в XML через spatie/array-to-xml
   ▼
6. Файл storage/app/xml-feeds/dim_ria.xml
   │  Один файл на площадку, перезаписывается
   ▼
7. GET /xml-feed/dim_ria
   XmlFeedController читает файл и отдаёт Content-Type: application/xml
```

---

## Детальное описание каждого файла

---

### `Contracts/XmlAdapterInterface.php`

**Что это:** Интерфейс, который обязан реализовать каждый адаптер площадки.

**Методы:**

| Метод | Возвращает | Описание |
|-------|-----------|----------|
| `getName()` | `string` | Уникальный идентификатор адаптера (`'dim_ria'`, `'olx'`). Используется как ключ для регистрации и для имени файла фида |
| `getRootElement()` | `string` | Корневой XML-тег для одного объекта (`'realty'` у DIM.RIA) |
| `toArray(PropertyExportData $dto)` | `array` | Главный метод — преобразует DTO в массив нужного формата. Каждый адаптер реализует свою логику маппинга |
| `generateXml(PropertyExportData $dto)` | `string` | XML-строка для одного объекта (вызывает `toArray` → `filterEmpty` → `ArrayToXml`) |
| `generateBatchXml(array $items)` | `string` | XML-строка для списка объектов (все объекты в одном XML-документе) |

**Зачем нужен:** Гарантирует, что любой новый адаптер (OLX, другие) будет иметь одинаковый набор методов. `XmlExportService` работает только с интерфейсом, не знает про конкретные адаптеры.

---

### `Dto/PropertyExportData.php`

**Что это:** Data Transfer Object — чистый объект с данными одного объекта недвижимости. Не зависит от Eloquent, не зависит от площадки.

**Зачем нужен:** Отделяет данные от логики Eloquent. Адаптеры работают с DTO, а не с моделью напрямую. Это позволяет:
- Тестировать адаптеры без базы данных
- Менять структуру модели Property не ломая адаптеры
- Чётко видеть какие данные нужны для экспорта

**Поля:**

| Группа | Поле | Тип | Откуда берётся |
|--------|------|-----|----------------|
| Идентификация | `id` | `int` | `$property->id` |
| Риелтор | `email` | `?string` | `$property->employee->email` |
| Риелтор | `phone` | `?string` | `$property->employee->phone` |
| Локация | `stateName` | `?string` | `$property->state->name` |
| Локация | `cityName` | `?string` | `$property->city->name` |
| Локация | `districtName` | `?string` | `$property->district->name` |
| Локация | `streetName` | `?string` | `$property->street->name` |
| Локация | `buildingNumber` | `?string` | `$property->building_number` |
| Локация | `apartmentNumber` | `?string` | `$property->apartment_number` |
| Локация | `latitude` | `?float` | `$property->latitude` |
| Локация | `longitude` | `?float` | `$property->longitude` |
| Справочники | `wallTypeName` | `?string` | `$property->wallType->name` (Dictionary) |
| Справочники | `buildingTypeName` | `?string` | `$property->buildingType->name` (Dictionary) |
| Справочники | `conditionName` | `?string` | `$property->condition->name` (Dictionary) |
| Справочники | `heatingTypeName` | `?string` | `$property->heatingType->name` (Dictionary) |
| Справочники | `roomCountValue` | `?string` | `$property->roomCount->value` (Dictionary, поле value: "1", "2"...) |
| Справочники | `ceilingHeightValue` | `?string` | `$property->ceilingHeight->value` (Dictionary, поле value: "2.5", "3.0"...) |
| Характеристики | `areaTotal` | `?float` | `$property->area_total` |
| Характеристики | `areaLiving` | `?float` | `$property->area_living` |
| Характеристики | `areaKitchen` | `?float` | `$property->area_kitchen` |
| Характеристики | `floor` | `?int` | `$property->floor` |
| Характеристики | `floorsTotal` | `?int` | `$property->floors_total` |
| Характеристики | `yearBuilt` | `?int` | `$property->year_built` |
| Цена | `price` | `?float` | `$property->price` |
| Цена | `currencySymbol` | `?string` | `$property->currency->symbol` ("$", "₴", "€") |
| Медиа | `youtubeUrl` | `?string` | `$property->youtube_url` |
| Медиа | `tiktokUrl` | `?string` | `$property->tiktok_url` |
| Медиа | `photoUrls` | `array` | `$property->photos->pluck('url')` (публичные URL фото) |
| Особенности | `featureNames` | `array` | `$property->features->pluck('name')` (названия из Dictionary) |

**Статический метод `fromModel(Property $property)`** — создаёт DTO из Eloquent-модели. Ожидает что связи загружены через eager-load.

---

### `Adapters/AbstractXmlAdapter.php`

**Что это:** Абстрактный базовый класс для всех адаптеров. Реализует общую логику, которая одинакова для всех площадок.

**Что делает:**

- **`generateXml($dto)`** — генерация XML для одного объекта:
  1. Вызывает `toArray($dto)` (реализуется в конкретном адаптере)
  2. Пропускает через `filterEmpty()` (убирает null и пустые строки)
  3. Конвертирует в XML через `ArrayToXml::convert()` с кодировкой UTF-8

- **`generateBatchXml($items)`** — генерация XML для списка объектов:
  1. Каждый DTO → `toArray()` → `filterEmpty()`
  2. Оборачивает в корневой тег `<feed>` с вложенными `<realty>` (или другой тег)
  3. Результат: один XML-документ со всеми объектами

- **`filterEmpty($data)`** — рекурсивно обходит массив и убирает:
  - `null` значения
  - Пустые строки `''`
  - Пустые вложенные массивы (после фильтрации)

**Зачем нужен:** Конкретные адаптеры (DimRia, OLX) реализуют только `toArray()` — формат конкретной площадки. Вся остальная логика (фильтрация, конвертация в XML) наследуется.

---

### `Adapters/DimRiaAdapter.php`

**Что это:** Конкретный адаптер для площадки DIM.RIA. Знает XML-структуру DIM.RIA и маппит данные из DTO в нужный формат.

**Конфигурация:**
- `getName()` → `'dim_ria'`
- `getRootElement()` → `'realty'`

**Метод `toArray($dto)` — формирует массив для XML:**

```
Корневой уровень (вне <characteristics>):
├── advert_type        = "Продажа"           (константа)
├── realty_type         = "Квартира"          (константа)
├── realty_sale_type    = 1                   (константа — вторичная)
├── email              ← dto->email          (Employee)
├── phone              ← dto->phone          (Employee)
├── local_realty_id    ← dto->id
├── photos_urls > loc  ← dto->photoUrls      (массив URL)
├── youtube_link       ← dto->youtubeUrl
├── tiktok_link        ← dto->tiktokUrl
├── state              ← dto->stateName      (eager-load)
├── city               ← dto->cityName       (eager-load)
├── district           ← dto->districtName   (eager-load)
├── street             ← dto->streetName     (eager-load)
├── street_type        = "улица"             (константа)
├── building_number    ← dto->buildingNumber
├── show_building_no   = 1                   (константа)
├── show_flat_no       = 1                   (константа)
├── flat_number_str    ← dto->apartmentNumber
├── longitude          ← dto->longitude
├── latitude           ← dto->latitude
├── radius_location    = "да"                (константа)
│
└── <characteristics>
    ├── wall_type      ← DimRiaMappings::mapWallType()
    ├── build_year     ← dto->yearBuilt
    ├── parking        ← DimRiaMappings::mapParking()
    ├── rooms_count    ← dto->roomCountValue
    ├── total_area     ← dto->areaTotal
    ├── living_area    ← dto->areaLiving
    ├── kitchen_area   ← dto->areaKitchen
    ├── floors         ← dto->floorsTotal
    ├── floor          ← dto->floor
    ├── flat_state     ← DimRiaMappings::mapCondition()
    ├── ceiling_height ← dto->ceilingHeightValue
    ├── building_type  ← DimRiaMappings::mapBuildingType()
    ├── price_type     = "за объект"          (константа)
    ├── price          ← (int) dto->price
    ├── currency       ← DimRiaMappings::mapCurrency()
    ├── offer_type     = "от посредника"      (константа)
    ├── [heating fields]  ← DimRiaMappings::mapHeatingFields() → "да"
    └── [feature fields]  ← DimRiaMappings::mapFeatures() → "да"
```

Массив `characteristics` собирается через `array_merge()` из трёх частей:
1. Основные поля (wall_type, rooms_count, price, etc.)
2. Поля отопления (individual_electricity, without_heating, etc.) — `"да"` если совпадает
3. Поля features (balcony_loggia, utp_with_garage, etc.) — `"да"` если feature есть у объекта

---

### `Mappings/DimRiaMappings.php`

**Что это:** Файл маппинга значений из нашей БД (Dictionary) в строки, которые ожидает DIM.RIA. Содержит константы-массивы и статические методы.

**Константы:**

| Константа | Что маппит | Пример |
|-----------|-----------|--------|
| `WALL_TYPE_MAP` | Dictionary(wall_type) name → DIM.RIA wall_type | `'Кирпич' → 'кирпич'`, `'Газоблок' → 'газобетон'` |
| `CONDITION_MAP` | Dictionary(condition) name → DIM.RIA flat_state | `'С ремонтом' → 'евроремонт'`, `'Без ремонта' → 'требует ремонта...'` |
| `BUILDING_TYPE_MAP` | Dictionary(building_type) name → DIM.RIA building_type | `'Хрущевка' → 'хрущевка'`, `'Новострой' → 'новый фонд'` |
| `HEATING_TYPE_MAP` | Dictionary(heating_type) name → XML field name | `'Индивидуальное электрическое отопление' → 'individual_electricity'` |
| `FEATURE_MAP` | Dictionary(feature) name → XML field name | `'Балкон' → 'balcony_loggia'`, `'Гараж' → 'utp_with_garage'` |
| `PARKING_MAP` | Feature name → DIM.RIA parking value | `'Паркинг' → 'наземный паркинг'`, `'Гараж' → 'гараж'` |
| `CURRENCY_MAP` | Symbol из Currency → DIM.RIA currency | `'₴' → 'грн'`, `'$' → '$'` |

**Статические методы:**

| Метод | Вход | Выход | Описание |
|-------|------|-------|----------|
| `mapWallType(?string $name)` | `'Кирпич'` | `'кирпич'` | Маппинг типа стен |
| `mapCondition(?string $name)` | `'С ремонтом'` | `'евроремонт'` | Маппинг состояния |
| `mapBuildingType(?string $name)` | `'Хрущевка'` | `'хрущевка'` | Маппинг типа дома |
| `mapCurrency(?string $symbol)` | `'₴'` | `'грн'` | Маппинг валюты |
| `mapParking(array $featureNames)` | `['Паркинг', 'Балкон']` | `'наземный паркинг'` | Определяет тип паркинга из features |
| `mapHeatingFields(?string $name)` | `'Без отопления'` | `['without_heating' => 'да']` | Маппинг отопления в XML-поле |
| `mapFeatures(array $featureNames)` | `['Балкон', 'Гараж']` | `['balcony_loggia' => 'да', 'utp_with_garage' => 'да']` | Маппинг features → да/нет поля |

**Как добавить маппинг для новой площадки:** создать `OlxMappings.php` в этой же папке с аналогичной структурой.

---

### `XmlExportService.php`

**Что это:** Главный сервис — единая точка входа для всего XML-экспорта. Зарегистрирован как singleton в контейнере Laravel.

**Методы:**

| Метод | Описание |
|-------|----------|
| `registerAdapter(XmlAdapterInterface $adapter)` | Регистрирует адаптер в реестре. Вызывается из ServiceProvider при старте приложения |
| `adapter(string $name)` | Возвращает адаптер по имени. Бросает `InvalidArgumentException` если не найден |
| `generateFeed(string $adapterName): int` | **Главный метод.** Загружает все active-объекты → создаёт DTO → генерирует XML → сохраняет в файл. Возвращает кол-во экспортированных объектов |
| `getFeedPath(string $adapterName): string` | Путь к файлу фида: `storage/app/xml-feeds/{name}.xml` |
| `getFeedContent(string $adapterName): ?string` | Читает содержимое файла фида. Возвращает `null` если файл не существует |
| `availableAdapters(): array` | Список имён зарегистрированных адаптеров (`['dim_ria']`) |

**Как работает `generateFeed()`:**

```php
// 1. Загрузка данных с eager-load
$properties = Property::active()->with([
    'employee', 'currency', 'state', 'city', 'street', 'district',
    'wallType', 'condition', 'heatingType', 'buildingType',
    'roomCount', 'ceilingHeight', 'photos', 'features',
])->get();

// 2. Property → DTO
$dtos = $properties->map(fn ($p) => PropertyExportData::fromModel($p))->all();

// 3. DTO → XML (через адаптер)
$xml = $adapter->generateBatchXml($dtos);

// 4. Сохранение в файл
file_put_contents(storage_path('app/xml-feeds/dim_ria.xml'), $xml);
```

---

### `Providers/XmlExportServiceProvider.php`

**Что это:** Laravel Service Provider — регистрирует `XmlExportService` в IoC-контейнере.

**Что делает:**
1. Создаёт `XmlExportService` как **singleton** (один экземпляр на всё приложение)
2. Регистрирует адаптер `DimRiaAdapter` в сервисе

**Зарегистрирован в:** `bootstrap/providers.php`

**Как добавить новый адаптер:** добавить строку в `register()`:
```php
$service->registerAdapter(new OlxAdapter());
```

---

### `app/Console/Commands/GenerateXmlFeedCommand.php`

**Что это:** Artisan-команда для генерации XML-фида.

**Использование:**
```bash
./vendor/bin/sail artisan xml:generate dim_ria
```

**Что делает:**
1. Получает имя адаптера из аргумента
2. Проверяет что адаптер зарегистрирован
3. Вызывает `XmlExportService::generateFeed()`
4. Выводит кол-во объектов и путь к файлу

**Вывод при успехе:**
```
Generating XML feed [dim_ria]...
Done! Exported 42 properties.
File: /var/www/html/storage/app/xml-feeds/dim_ria.xml
```

---

### `app/Http/Controllers/XmlFeedController.php`

**Что это:** Контроллер для отдачи XML-фида по HTTP.

**Маршрут:** `GET /xml-feed/{adapter}` — публичный, без авторизации

**Что делает:**
1. Проверяет что адаптер зарегистрирован → 404 если нет
2. Читает файл фида → 404 если файл ещё не сгенерирован
3. Отдаёт содержимое с `Content-Type: application/xml; charset=UTF-8`

**Примеры:**
- `GET /xml-feed/dim_ria` → XML-документ со всеми объектами
- `GET /xml-feed/olx` → 404 (если адаптер не зарегистрирован)
- `GET /xml-feed/dim_ria` (до первой генерации) → 404 "Feed has not been generated yet"

---

## Модель Property — связи для экспорта

Property загружается с eager-load. Вот откуда берётся каждое поле:

| Связь | Тип | Модель | Что извлекаем |
|-------|-----|--------|---------------|
| `employee` | BelongsTo | Employee | `->email`, `->phone` (данные риелтора) |
| `currency` | BelongsTo | Currency | `->symbol` ($, ₴, €) |
| `state` | BelongsTo | State | `->name` (область) |
| `city` | BelongsTo | City | `->name` (город) |
| `district` | BelongsTo | District | `->name` (район) |
| `street` | BelongsTo | Street | `->name` (улица) |
| `wallType` | BelongsTo | Dictionary (wall_type) | `->name` → маппинг |
| `condition` | BelongsTo | Dictionary (condition) | `->name` → маппинг |
| `heatingType` | BelongsTo | Dictionary (heating_type) | `->name` → маппинг |
| `buildingType` | BelongsTo | Dictionary (building_type) | `->name` → маппинг |
| `roomCount` | BelongsTo | Dictionary (room_count) | `->value` ("1", "2", "3"...) |
| `ceilingHeight` | BelongsTo | Dictionary (ceiling_height) | `->value` ("2.5", "3.0"...) |
| `photos` | HasMany | PropertyPhoto | `->url` (accessor, публичный URL) |
| `features` | BelongsToMany | Dictionary (feature) | `->name` → маппинг в да/нет поля |

---

## Константы DIM.RIA

Эти значения всегда фиксированы в адаптере, не берутся из БД:

| XML-поле | Значение | Пояснение |
|----------|----------|-----------|
| `advert_type` | `"Продажа"` | Тип операции |
| `realty_type` | `"Квартира"` | Тип недвижимости |
| `realty_sale_type` | `1` | Вторичная |
| `street_type` | `"улица"` | Тип улицы |
| `show_building_no` | `1` | Показывать номер дома |
| `show_flat_no` | `1` | Показывать номер квартиры |
| `radius_location` | `"да"` | Зона вместо точных координат |
| `price_type` | `"за объект"` | Цена за весь объект |
| `offer_type` | `"от посредника"` | Тип предложения |

---

## Как добавить новую площадку

1. Создать адаптер `app/Services/XmlExport/Adapters/OlxAdapter.php`:
   - `extends AbstractXmlAdapter`
   - Реализовать `getName()`, `getRootElement()`, `toArray()`

2. При необходимости создать `app/Services/XmlExport/Mappings/OlxMappings.php`

3. Зарегистрировать в `XmlExportServiceProvider::register()`:
   ```php
   $service->registerAdapter(new OlxAdapter());
   ```

4. Готово — команда `xml:generate olx` и маршрут `/xml-feed/olx` заработают автоматически

---

## Генерация и хранение

| Параметр | Значение |
|----------|----------|
| Путь к файлу | `storage/app/xml-feeds/{adapter_name}.xml` |
| Формат | Один XML-файл на площадку |
| Обновление | Файл перезаписывается при каждой генерации |
| Artisan-команда | `./vendor/bin/sail artisan xml:generate dim_ria` |
| HTTP-маршрут | `GET /xml-feed/dim_ria` (публичный, без auth) |
| Логирование | `Log::info()` при каждой генерации (кол-во объектов, путь) |
