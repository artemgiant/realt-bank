"use strict";

/**
 * Фильтрация типов недвижимости по выбранному типу сделки.
 *
 * Использует window.dealTypePropertyTypeMap — объект {deal_type_id: [property_type_id, ...]}
 * который передаётся из Blade-шаблона.
 *
 * Обёрнуто в $(document).ready() чтобы гарантировать запуск после
 * page-create.js (type="module"), который инициализирует Select2.
 */
$(document).ready(function () {
    var map = window.dealTypePropertyTypeMap || {};
    var $dealType = $('#deal_type_id');
    var $propertyType = $('#property_type_id');

    if (!$dealType.length || !$propertyType.length) return;

    // Сохраняем все опции при загрузке страницы
    var allOptions = [];
    $propertyType.find('option').each(function () {
        allOptions.push({
            value: $(this).val(),
            text: $(this).text()
        });
    });

    function filterPropertyTypes(dealTypeId, keepSelected) {
        var allowedIds = map[dealTypeId];
        var currentVal = $propertyType.val();

        // Уничтожаем Select2 перед изменением опций
        if ($propertyType.hasClass('select2-hidden-accessible')) {
            $propertyType.select2('destroy');
        }

        // Очищаем и заполняем опции
        $propertyType.empty();

        if (!allowedIds) {
            // Нет маппинга — показываем все опции
            allOptions.forEach(function (opt) {
                $propertyType.append(new Option(opt.text, opt.value));
            });
        } else {
            // Пустая опция-плейсхолдер
            $propertyType.append(new Option('', ''));

            allOptions.forEach(function (opt) {
                if (opt.value === '') return;
                var id = parseInt(opt.value);
                if (allowedIds.indexOf(id) !== -1) {
                    $propertyType.append(new Option(opt.text, opt.value));
                }
            });
        }

        // Определяем значение для выбора
        var newVal = '';
        if (keepSelected && currentVal) {
            // Проверяем, что текущее значение есть в отфильтрованных
            if ($propertyType.find('option[value="' + currentVal + '"]').length) {
                newVal = currentVal;
            }
        }

        // Если только одна опция (не считая пустой) — выбираем автоматически
        var realOptions = $propertyType.find('option').filter(function () {
            return $(this).val() !== '';
        });
        if (realOptions.length === 1) {
            newVal = realOptions.first().val();
        }

        $propertyType.val(newVal);

        // Переинициализируем Select2
        $propertyType.select2({
            width: 'resolve',
            placeholder: 'Выбрать',
        });
    }

    // При смене типа сделки
    $dealType.on('change', function () {
        filterPropertyTypes($(this).val(), false);
    });

    // Инициализация: если тип сделки уже выбран (edit-страница или autosave)
    var initialDealType = $dealType.val();
    if (initialDealType) {
        filterPropertyTypes(initialDealType, true);
    }
});
