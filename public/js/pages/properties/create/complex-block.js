/**
 * Модуль для работы с комплексами и блоками
 * Select2 AJAX + автозаполнение локации
 */

(function() {
    'use strict';

    // Конфигурация
    const CONFIG = {
        urls: {
            searchComplexes: '/complexes/search',
            searchBlocks: '/blocks/search',
        },
        selectors: {
            complex: '#complex_id',
            block: '#block_id',
            locationInput: '.location-search-input',
            houseNumber: '#number-house',
            // Hidden inputs для локации
            streetId: 'input[name="street_id"]',
            streetName: 'input[name="street_name"]',
            zoneId: 'input[name="zone_id"]',
            zoneName: 'input[name="zone_name"]',
            districtId: 'input[name="district_id"]',
            districtName: 'input[name="district_name"]',
            cityId: 'input[name="city_id"]',
            cityName: 'input[name="city_name"]',
        },
        select2: {
            language: 'ru',
            placeholder: 'Начните вводить название...',
            minimumInputLength: 0,
        }
    };

    /**
     * Инициализация Select2 для комплексов
     */
    function initComplexSelect() {
        const $complex = $(CONFIG.selectors.complex);

        if (!$complex.length) return;

        $complex.select2({
            ...CONFIG.select2,
            placeholder: 'Выберите комплекс',
            ajax: {
                url: CONFIG.urls.searchComplexes,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results,
                        pagination: data.pagination,
                    };
                },
                cache: true,
            },
            templateResult: formatComplexResult,
            templateSelection: formatComplexSelection,
        });

        // При изменении комплекса - сбросить блок
        $complex.on('change', function() {
            resetBlockSelect();
            const complexId = $(this).val();
            if (complexId) {
                enableBlockSelect();
            } else {
                disableBlockSelect();
            }
        });
    }

    /**
     * Форматирование результата комплекса в dropdown
     */
    function formatComplexResult(complex) {
        if (complex.loading) {
            return $('<span>Загрузка...</span>');
        }

        const $container = $(`
            <div class="select2-result-complex">
                <div class="complex-name">${complex.text}</div>
                <small class="complex-developer text-muted">${complex.developer_name || ''}</small>
            </div>
        `);

        return $container;
    }

    /**
     * Форматирование выбранного комплекса
     */
    function formatComplexSelection(complex) {
        if (!complex.id) return complex.text;

        if (complex.developer_name) {
            return complex.text + ', ' + complex.developer_name + '';
        }
        return complex.text || complex.id;
    }

    /**
     * Инициализация Select2 для блоков
     */
    function initBlockSelect() {
        const $block = $(CONFIG.selectors.block);

        if (!$block.length) return;

        $block.select2({
            ...CONFIG.select2,
            placeholder: 'Сначала выберите комплекс',
            ajax: {
                url: CONFIG.urls.searchBlocks,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1,
                        complex_id: $(CONFIG.selectors.complex).val(),
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results,
                        pagination: data.pagination,
                    };
                },
                cache: true,
            },
            templateResult: formatBlockResult,
            templateSelection: formatBlockSelection,
        });

        // При выборе блока - заполнить локацию
        $block.on('select2:select', function(e) {
            const data = e.params.data;
            fillLocationFromBlock(data);
        });

        // При очистке блока - очистить локацию (опционально)
        $block.on('select2:clear', function() {
            // clearLocationFields(); // Раскомментировать если нужно очищать
        });

        // Изначально заблокирован
        disableBlockSelect();
    }

    /**
     * Форматирование результата блока в dropdown
     */
    function formatBlockResult(block) {
        if (block.loading) {
            return $('<span>Загрузка...</span>');
        }

        let address = '';
        if (block.street_name) {
            address = block.street_name;
            if (block.building_number) {
                address += ', ' + block.building_number;
            }
        }

        const $container = $(`
            <div class="select2-result-block">
                <div class="block-name">${block.text}</div>
                ${address ? `<small class="block-address text-muted">${address}</small>` : ''}
            </div>
        `);

        return $container;
    }

    /**
     * Форматирование выбранного блока
     */
    function formatBlockSelection(block) {
        return block.text || block.id;
    }

    /**
     * Заполнение полей локации из данных блока
     */
    function fillLocationFromBlock(blockData) {
        // Формируем полный адрес: улица, зона, район, город
        const addressParts = [];
        if (blockData.street_name) addressParts.push(blockData.street_name);
        if (blockData.zone_name) addressParts.push(blockData.zone_name);
        if (blockData.district_name) addressParts.push(blockData.district_name);
        if (blockData.city_name) addressParts.push(blockData.city_name);

        const fullAddress = addressParts.join(', ');

        // Заполняем поле поиска локации (видимое)
        const $locationInput = $(CONFIG.selectors.locationInput);
        if ($locationInput.length && fullAddress) {
            $locationInput.val(fullAddress);
        }

        // Заполняем номер дома
        const $houseNumber = $(CONFIG.selectors.houseNumber);
        if ($houseNumber.length && blockData.building_number) {
            $houseNumber.val(blockData.building_number);
        }

        // Заполняем hidden inputs
        $(CONFIG.selectors.streetId).val(blockData.street_id || '');
        $(CONFIG.selectors.streetName).val(blockData.street_name || '');
        $(CONFIG.selectors.zoneId).val(blockData.zone_id || '');
        $(CONFIG.selectors.zoneName).val(blockData.zone_name || '');
        $(CONFIG.selectors.districtId).val(blockData.district_id || '');
        $(CONFIG.selectors.districtName).val(blockData.district_name || '');
        $(CONFIG.selectors.cityId).val(blockData.city_id || '');
        $(CONFIG.selectors.cityName).val(blockData.city_name || '');

        // Показываем кнопку очистки в поле локации
        const $clearBtn = $locationInput.siblings('.location-search-clear');
        if ($clearBtn.length && blockData.street_name) {
            $clearBtn.show();
        }
    }

    /**
     * Очистка полей локации
     */
    function clearLocationFields() {
        $(CONFIG.selectors.locationInput).val('');
        $(CONFIG.selectors.houseNumber).val('');
        $(CONFIG.selectors.streetId).val('');
        $(CONFIG.selectors.streetName).val('');
        $(CONFIG.selectors.zoneId).val('');
        $(CONFIG.selectors.zoneName).val('');
        $(CONFIG.selectors.districtId).val('');
        $(CONFIG.selectors.districtName).val('');
        $(CONFIG.selectors.cityId).val('');
        $(CONFIG.selectors.cityName).val('');
    }

    /**
     * Сброс select блока
     */
    function resetBlockSelect() {
        const $block = $(CONFIG.selectors.block);
        $block.val(null).trigger('change');
    }

    /**
     * Активация select блока
     */
    function enableBlockSelect() {
        const $block = $(CONFIG.selectors.block);
        $block.prop('disabled', false);
        // Обновляем placeholder
        $block.data('select2').$container.find('.select2-selection__placeholder').text('Выберите секцию/корпус');
    }

    /**
     * Блокировка select блока
     */
    function disableBlockSelect() {
        const $block = $(CONFIG.selectors.block);
        $block.prop('disabled', true);
    }

    /**
     * Инициализация модуля
     */
    function init() {
        initComplexSelect();
        initBlockSelect();
    }

    // Запуск при готовности DOM
    $(document).ready(init);

})();
