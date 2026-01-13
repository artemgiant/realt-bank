"use strict";

/**
 * Автозаполнение полей при выборе типа здания
 * Высота потолка, Год постройки, Отопление
 */

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const buildingTypeSelect = document.getElementById('building_type_id');
        const ceilingHeightSelect = document.getElementById('ceiling_height_id');
        const yearBuiltSelect = document.getElementById('year_built');
        const heatingTypeSelect = document.getElementById('heating_type_id');

        if (!buildingTypeSelect) return;

        // Маппінг типів будинків на значення полів
        // Ключ - назва типу будинку (lowercase для порівняння)
        // Значення - об'єкт з текстами для пошуку в селектах
        const buildingTypeMapping = {
            // Старий фонд - висока стеля, до 1949, без опалення
            'бельгийка': {
                ceilingHeight: '3.1 – 3.5',
                yearBuilt: 'до 1949',
                heatingType: null
            },
            'старый фонд': {
                ceilingHeight: '3.1 – 3.5',
                yearBuilt: 'до 1949',
                heatingType: null
            },
            // Радянська забудова - стандартна стеля, 1950-1989, централізоване опалення
            'чешка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'хрущёвка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'хрущевка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'московка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'гостинка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'сотовый': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'харьковка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'югославка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'общежитие': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            },
            'малосемейка': {
                ceilingHeight: '2.5 – 2.7',
                yearBuilt: '1950-1989',
                heatingType: 'централизованное'
            }
        };

        // Обробник зміни типу будинку (Select2)
        if (typeof $ !== 'undefined') {
            $(buildingTypeSelect).on('select2:select', function (e) {
                handleBuildingTypeChange(e.params.data.text);
            });

            $(buildingTypeSelect).on('select2:clear', function (e) {
                // При очищенні - нічого не робимо (не очищаємо інші поля)
            });
        }

        /**
         * Обробка зміни типу будинку
         */
        function handleBuildingTypeChange(selectedText) {
            const text = (selectedText || '').trim().toLowerCase();

            // Сброс комплекса и блока при изменении типа здания
            // Если это не программное изменение (например, при выборе комплекса сам тип меняется на новострой)
            // Но здесь мы просто сбрасываем, если пользователь сам меняет
            const complexSelect = document.getElementById('complex_id');
            const blockSelect = document.getElementById('block_id');

            if (complexSelect && $(complexSelect).val()) {
                $(complexSelect).val(null).trigger('change');
            }
            if (blockSelect && $(blockSelect).val()) {
                $(blockSelect).val(null).trigger('change');
            }


            // Шукаємо маппінг для вибраного типу
            const mapping = buildingTypeMapping[text];

            if (!mapping) {
                // Тип не в списку - нічого не робимо
                return;
            }

            // Встановлюємо висоту потолка
            if (mapping.ceilingHeight && ceilingHeightSelect) {
                setSelectValueByText(ceilingHeightSelect, mapping.ceilingHeight);
            }

            // Встановлюємо рік побудови
            if (mapping.yearBuilt && yearBuiltSelect) {
                setSelectValueByText(yearBuiltSelect, mapping.yearBuilt);
            }

            // Встановлюємо опалення
            if (mapping.heatingType && heatingTypeSelect) {
                setSelectValueByText(heatingTypeSelect, mapping.heatingType);
            }
        }

        /**
         * Встановити значення Select2 по тексту опції
         */
        function setSelectValueByText(selectElement, searchText) {
            const searchLower = searchText.toLowerCase();

            // Шукаємо опцію по тексту
            const options = selectElement.options;
            let foundValue = null;

            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].text.trim().toLowerCase();
                if (optionText.includes(searchLower)) {
                    foundValue = options[i].value;
                    break;
                }
            }

            if (foundValue && typeof $ !== 'undefined') {
                // Встановлюємо значення через Select2 API
                $(selectElement).val(foundValue).trigger('change');
            }
        }
    });
})();
