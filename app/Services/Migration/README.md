# Сервис миграции данных: factor_dump → realt_bank

Перенос данных из старой CRM (factor_dump) в новую CRM (realt_bank).
./vendor/bin/sail artisan app:migrate-from-factor-dump --fresh --force --limit=10
---
Запустить тест  
#### ./vendor/bin/sail artisan test --filter=VerifyMigrationTest 
Запустиь тест конктеного метода
#### ./vendor/bin/sail artisan test --filter=VerifyMigrationTest::test_property_locations_match_source
Запуститься частрично миграцыю для теста    
#### ./vendor/bin/sail artisan app:migrate-from-factor-dump --fresh --force --limit=10

## Структура файлов

```
app/Services/Migration/
├── README.md                      ← этот файл
├── CleanupService.php             ← очистка таблиц перед миграцией
├── DataMigrationService.php       ← оркестратор (запускает всё по порядку)
├── Mappers/                       ← маппинг old ID → new ID
│   ├── LocationMapper.php         ← локации (города, районы, зоны, улицы)
│   ├── DictionaryMapper.php       ← справочники (тип здания, состояние, стены и т.д.)
│   ├── UserMapper.php             ← пользователи (old user_id → new user_id)
│   └── FilialMapper.php           ← филиалы (old filial_id → new office_id)
└── Migrators/                     ← собственно перенос данных
    ├── FilialMigrator.php         ← филиалы → компания + офисы
    ├── UserMigrator.php           ← пользователи → users + employees
    ├── PropertyMigrator.php       ← объекты → properties
    └── PropertyPhotoMigrator.php  ← фото объектов → property_photos

app/Console/Commands/
└── MigrateFromFactorDump.php      ← artisan-команда для запуска
```

---

## Что переносим

| Источник (factor_dump) | Назначение (realt_bank) | Фильтр | Записей |
|------------------------|------------------------|--------|---------|
| `filials` | `companies` + `company_offices` | все | ~39 |
| `users` | `users` + `employees` + роли | `deleted=0` | ~468 |
| `objects` | `properties` + `property_features` | `status IN(1,2,3)`, `rent=0`, `deleted=0` | ~6 691 |
| `object_images` | `property_photos` | только для перенесённых объектов | зависит от объектов |

### Фильтр объектов по типу (поле `status` в старой базе)

| status | Тип недвижимости | property_type_id (новая база) |
|--------|-----------------|-------------------------------|
| 1 | Квартиры | 23 (Квартира) |
| 2 | Дома | 28 (Дом) |
| 3 | Коммерция | 40 (Помещение свободного назначения) |

**Не переносим:** `rent=1` (аренда), `deleted=1` (удалённые), другие status.

---

## Описание компонентов

### CleanupService.php — Очистка перед миграцией

Очищает целевые таблицы, сохраняя пользователей и справочники.

**Что очищается (TRUNCATE):**
- `properties`, `property_photos`, `property_features`, `property_translations`, `property_documents`
- `contacts`, `contactables`, `contact_phones`
- `companies`, `company_offices`

**Что удаляется выборочно:**
- `employees` без `user_id` (DELETE WHERE user_id IS NULL)
- У оставшихся employees обнуляются `company_id`, `office_id`

**Что НЕ трогается:**
- `users` (6 записей — admin и созданные вручную)
- `employees` с `user_id` (привязанные к users)
- `dictionaries` (384 записи — справочники)
- Локации: `cities`, `districts`, `zones`, `streets`
- `roles`, `permissions`, `model_has_roles`
- `currencies`, `sources`

---

### DataMigrationService.php — Оркестратор

Управляет порядком выполнения миграции:

1. **Строит маперы** — загружает маппинги локаций и справочников
2. **FilialMigrator** — создаёт компанию и офисы
3. **UserMigrator** — переносит пользователей и сотрудников
4. **PropertyMigrator** — переносит объекты недвижимости
5. **PropertyPhotoMigrator** — переносит фото объектов

Поддерживает параметр `--only` для выборочного переноса.

---

### Mappers/ — Маппинг ID

#### LocationMapper.php
Маппинг локаций по **имени** (case-insensitive):

| Старая таблица | Новая таблица | Ключ |
|---------------|--------------|------|
| `lib_towns` | `cities` | name |
| `lib_regions` | `districts` | name + city_id |
| `lib_zones` | `zones` | name + city_id |
| `lib_streets` | `streets` | name + city_id |

Если локация не найдена — **создаётся новая** запись в новой базе.

#### DictionaryMapper.php
Маппинг старых `lib_other` записей → новые `dictionaries`:

| Старое поле (objects) | Тип в lib_other | Новый тип Dictionary | Назначение |
|----------------------|----------------|---------------------|------------|
| `type_object` | type_object | `deal_kind` | Вид сделки (Нотариальная, Переуступка, Отдел продаж) |
| `project` | project | `building_type` | Тип здания (Новострой, Хрущёвка, Сталинка...) |
| `situation` | situation | `condition` | Состояние (С ремонтом, Без ремонта...) |
| `type_height` | type_height | `heating_type` | Отопление (Централизованное, Автономное...) |
| `wall_type_g` | wall_type_g | `wall_type` | Тип стен (квартиры) |
| `wall_type_home` | wall_type_home | `wall_type` | Тип стен (дома) |
| `the_balkon` | the_balkon | `feature` | Балкон → property_features |
| `the_plase_auto` | the_plase_auto | `feature` | Парковка → property_features |
| `the_vid_na` | the_vid_na | `feature` | Вид → property_features |
| `rooms` | — (integer) | `room_count` | Кол-во комнат (по значению) |
| `bothroom` | — (integer) | `bathroom_count` | Кол-во санузлов (по значению) |
| `rent` | — (0/1) | `deal_type` | Тип сделки (Продажа/Аренда) |

**Поля без маппинга** (записываются в `notes` текстом):
- `kitchen` — Кухня
- `plan` — Планировка
- `lest` — Лестница
- `type_material` — Материал перекрытий
- `orient` — Ориентация
- `sale_off_comment` — Скидка
- `commention_moderator` — Комментарий модератора

#### UserMapper.php
Простой маппинг: `old_user_id → new_user_id`. Заполняется при миграции пользователей.

#### FilialMapper.php
Маппинг: `old_filial_id → new_company_office_id`. Хранит также `companyId`.

---

### Migrators/ — Перенос данных

#### FilialMigrator.php (шаг 1)
1. Создаёт компанию **"Factor"**
2. Переносит `filials` → `company_offices` (привязывает к компании)
3. Сохраняет маппинг в `FilialMapper`

#### UserMigrator.php (шаг 2)
Для каждого пользователя (`deleted=0`):
1. Создаёт `User` (name, email, phone, password)
2. Назначает **spatie-роль**: `role_id 1` → `super_admin`, остальные → `agent`
3. Создаёт `Employee` (привязка к company, office, position, status)
4. Сохраняет маппинг в `UserMapper`

| factor_dump.users | realt_bank.users | realt_bank.employees |
|-------------------|-----------------|---------------------|
| name + sname | name | first_name, last_name |
| parent_name | — | middle_name |
| email | email | email |
| tel | phone | phone |
| password | password | — |
| role_id | → spatie role | — |
| filial | — | office_id (через FilialMapper) |

#### PropertyMigrator.php (шаг 3)
Переносит объекты пачками (`chunk=500`).

Маппинг полей `objects` → `properties`:

| Старое поле | Новое поле | Трансформация |
|-------------|-----------|---------------|
| `status` | `property_type_id` | 1→Квартира, 2→Дом, 3→Коммерция |
| `town_id` | `city_id` | LocationMapper |
| `region_id` | `district_id` | LocationMapper |
| `zone_id` | `zone_id` | LocationMapper |
| `street_id` | `street_id` | LocationMapper |
| `number_house` | `building_number` | прямой |
| `num_flat` | `apartment_number` | прямой |
| `coords` | `latitude` + `longitude` | split по запятой |
| `user_id` | `user_id` | UserMapper |
| `type_object` | `deal_kind_id` | DictionaryMapper |
| `project` | `building_type_id` | DictionaryMapper |
| `situation` | `condition_id` | DictionaryMapper |
| `type_height` | `heating_type_id` | DictionaryMapper |
| `wall_type_g` / `wall_type_home` | `wall_type_id` | DictionaryMapper |
| `rooms` | `room_count_id` | DictionaryMapper (по значению) |
| `bothroom` | `bathroom_count_id` | DictionaryMapper (по значению) |
| `rent` | `deal_type_id` | 0→Продажа |
| `total_area` | `area_total` | прямой |
| `area_live` | `area_living` | прямой |
| `area_kitchen` | `area_kitchen` | прямой |
| `area_total_ych_t` / `area_home` | `area_land` | прямой / парсинг |
| `floor_build` | `floor` | прямой |
| `all_floors` | `floors_total` | прямой |
| `price` | `price` | прямой |
| `price_area` | `price_per_m2` | прямой |
| `rem_url` | `external_url` | прямой |
| `open` | `is_visible_to_agents` | bool |
| `data` + unmapped fields | `notes` | склейка текстом |
| `date_created` | `created_at` | прямой |
| `date_updated` | `updated_at` | прямой |
| `the_balkon`, `the_plase_auto`, `the_vid_na` | `property_features` | many-to-many |

Все объекты получают `status='active'` (переносим только не удалённые).

#### PropertyPhotoMigrator.php (шаг 4)
Переносит фото пачками (`chunk=1000`):

| factor_dump.object_images | realt_bank.property_photos |
|--------------------------|---------------------------|
| `object_id` | `property_id` (через propertyMap) |
| `img_name` | `filename` + `path` (prefix: `legacy/`) |
| `is_main` | `is_main` |
| `sort_at` | `sort_order` |

Фото без соответствующего объекта в `propertyMap` — пропускаются.

---

## Запуск

```bash
# Полная миграция с очисткой
./vendor/bin/sail artisan app:migrate-from-factor-dump --fresh --force

# Без очистки (добавить к существующим данным)
./vendor/bin/sail artisan app:migrate-from-factor-dump

# Только определённые сущности
./vendor/bin/sail artisan app:migrate-from-factor-dump --only=filials,users
./vendor/bin/sail artisan app:migrate-from-factor-dump --only=properties,photos

# С другим размером пакета
./vendor/bin/sail artisan app:migrate-from-factor-dump --chunk=200
```

### Порядок при выборочном запуске

Важно соблюдать зависимости:
1. `filials` — первыми (от них зависят employees)
2. `users` — вторыми (от них зависят properties)
3. `properties` — третьими
4. `photos` — последними (нужен propertyMap из шага 3)

---

## Верификация после миграции

```bash
./vendor/bin/sail artisan tinker --execute="
echo 'Users: ' . \App\Models\User::count();
echo 'Employees: ' . \App\Models\Employee\Employee::count();
echo 'Properties: ' . \App\Models\Property\Property::count();
echo 'Photos: ' . \App\Models\Property\PropertyPhoto::count();
echo 'Offices: ' . \App\Models\Reference\CompanyOffice::count();
echo 'Companies: ' . \App\Models\Reference\Company::count();
"
```

Ожидаемые значения:
- Properties: ~6 691
- Users: ~468 + 6 (существующие)
- Employees: ~468 + 6
- Photos: зависит от фильтрации
- Offices: ~39
- Companies: 1 (Factor)
