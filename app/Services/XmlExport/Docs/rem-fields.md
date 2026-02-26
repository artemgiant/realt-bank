# REM.ua XML Feed - Описание полей

Формат: Yandex Realty Feed

## Структура документа

```xml
<?xml version="1.0" encoding="utf-8"?>
<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">
    <generation-date>13.02.2019 17:00</generation-date>
    <offer internal-id="45">...</offer>
    <offer internal-id="46">...</offer>
</realty-feed>
```

- `generation-date` — дата создания фида, формат `d.m.Y H:i:s`
- `offer` — одно объявление, атрибут `internal-id` обязателен и уникален

---

## Основные

| Тег | Обязательный | Тип | Описание | Допустимые значения |
|-----|:---:|-----|-----------|---------------------|
| `type` | * | string | Тип сделки | `продажа`, `аренда` |
| `property-type` | * | string | Тип недвижимости | `жилая` |
| `category` | * | string | Категория | `квартира`, `торговые помещения`, `коммерция`, `дом`, `участок` |
| `url` | * | string | URL объявления | |
| `title` | * | string | Заголовок | |
| `description` | * | string | Описание | |
| `creation-date` | * | datetime | Дата создания | Формат: `d.m.Y H:i:s` |
| `update-time` | * | datetime | Дата обновления | Формат: `d.m.Y H:i:s` |
| `is_premium` | * | boolean | Эксклюзив | `true`, `false` |

## Локация (`<location>`)

| Тег | Обязательный | Тип | Описание |
|-----|:---:|-----|-----------|
| `country` | * | string | Страна |
| `region` | * | string | Область |
| `locality-name` | * | string | Город |
| `district` | * | string | Район |
| `sub-locality-name` | * | string | Микрорайон |
| `address` | * | string | Улица и номер дома. Формат: `улица, номер дома` |
| `apartment` | * | string | Номер квартиры |
| `longitude` | | float | Долгота |
| `latitude` | | float | Широта |

## Агент (`<sales-agent>`)

| Тег | Обязательный | Тип | Описание |
|-----|:---:|-----|-----------|
| `name` | * | string | ФИО агента |
| `phone` | * | string | Телефон (1 уникальный номер) |
| `id` | * | string | Внутренний ID риелтора |

## Цена (`<price>`)

| Тег | Обязательный | Тип | Описание | Допустимые значения |
|-----|:---:|-----|-----------|---------------------|
| `value` | * | integer | Цена | Целое неотрицательное |
| `currency` | * | string | Валюта | `UAH`, `USD` |

## Площадь

| Тег | Обязательный | Тип | Описание |
|-----|:---:|-----|-----------|
| `area.value` | * | float | Общая площадь, м² |
| `living-space.value` | | float | Жилая площадь, м² |
| `kitchen-space.value` | | float | Площадь кухни, м² |
| `lot-area.value` | | float | Площадь участка, м² |

## Параметры

| Тег | Обязательный | Тип | Описание |
|-----|:---:|-----|-----------|
| `rooms` | * | integer | Количество комнат |
| `rooms-type` | | string | Тип комнат |
| `floor` | * | integer | Этаж |
| `floor_count` | * | integer | Этажность |
| `quality` | | string | Состояние объекта |

## Медиа

| Тег | Обязательный | Тип | Описание |
|-----|:---:|-----|-----------|
| `image` | * | string | Фото (jpg, jpeg). Может быть несколько тегов |
| `video-review` | | string | Видео с YouTube |
