/**
 * Работа с тегами фильтров (Комплексы)
 * Объект доступен через window.ComplexTags
 */
window.ComplexTags = {

    // Селектор контейнера тегов
    containerSelector: '.filter-tags',

    // SVG иконка закрытия для тегов
    closeIconSvg: '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"></path>' +
        '<path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"></path>' +
        '</svg>',

    // Конфигурация чекбокс-фильтров
    checkboxFilters: [
        { name: 'object_type_id[]', label: 'Тип объекта' },
        { name: 'year_built[]', label: 'Год сдачи' },
        { name: 'condition_id[]', label: 'Состояние' },
        { name: 'wall_type_id[]', label: 'Тип стен' },
        { name: 'heating_type_id[]', label: 'Отопление' },
        { name: 'features[]', label: 'Особенности' }
    ],

    // Конфигурация range-фильтров (от/до)
    rangeFilters: [
        { nameFrom: 'price_from', nameTo: 'price_to', label: 'Цена', idFrom: 'price_from', idTo: 'price_to' },
        { nameFrom: 'area_from', nameTo: 'area_to', label: 'Площадь' },
        { nameFrom: 'floors_from', nameTo: 'floors_to', label: 'Этажность' },
        { nameFrom: 'price_per_m2_from', nameTo: 'price_per_m2_to', label: 'Цена за м²' }
    ],

    // Конфигурация select-фильтров
    selectFilters: [
        { id: 'category_id', label: 'Категория' },
        { id: 'developer_id', label: 'Девелопер' },
        { id: 'housing_class_id', label: 'Класс недвижимости' }
    ],

    // Конфигурация текстовых фильтров поиска
    searchFilters: [
        { name: 'search_id', label: 'ID' }
    ],

    // Создание HTML тега
    create: function(text, filterType, filterValue) {
        return '<div class="badge rounded-pill" data-filter-type="' + filterType + '" data-filter-value="' + filterValue + '">' +
            text +
            '<button type="button" aria-label="Close">' + this.closeIconSvg + '</button>' +
            '</div>';
    },

    // Обновление тегов для всех фильтров
    update: function() {
        var self = this;
        var $container = $(this.containerSelector);
        $container.empty();

        // ========== Чекбокс-фильтры ==========
        this.checkboxFilters.forEach(function(filter) {
            $('[name="' + filter.name + '"]:checked').each(function() {
                var $checkbox = $(this);
                var value = $checkbox.val();
                var text = $checkbox.closest('label').find('.my-custom-text').text();
                $container.append(self.create(text, 'checkbox', filter.name + '|' + value));
            });
        });

        // ========== Range-фильтры (от/до) ==========
        this.rangeFilters.forEach(function(filter) {
            var $inputFrom = filter.idFrom
                ? $('#' + filter.idFrom)
                : $('[name="' + filter.nameFrom + '"]');
            var $inputTo = filter.idTo
                ? $('#' + filter.idTo)
                : $('[name="' + filter.nameTo + '"]');

            var valueFrom = $inputFrom.val();
            var valueTo = $inputTo.val();

            if (valueFrom || valueTo) {
                var text = filter.label + ': ';
                if (valueFrom && valueTo) {
                    text += 'от ' + valueFrom + ' до ' + valueTo;
                } else if (valueFrom) {
                    text += 'от ' + valueFrom;
                } else {
                    text += 'до ' + valueTo;
                }
                $container.append(self.create(text, 'range', filter.nameFrom + '|' + filter.nameTo));
            }
        });

        // ========== Select-фильтры ==========
        this.selectFilters.forEach(function(filter) {
            var $select = $('#' + filter.id);
            var value = $select.val();
            if (value) {
                var text = $select.find('option:selected').text();
                $container.append(self.create(filter.label + ': ' + text, 'select', filter.id));
            }
        });

        // ========== Текстовые фильтры поиска ==========
        this.searchFilters.forEach(function(filter) {
            var $input = $('[name="' + filter.name + '"]');
            var value = $input.val();
            if (value) {
                $container.append(self.create(filter.label + ': ' + value, 'search', filter.name));
            }
        });

        // ========== Фильтр локации ==========
        var locationType = $('#lfType').val();
        var locationId = $('#lfId').val();
        if (locationType && locationId) {
            var locationTag = $('#lfLocationTag').text() || 'Локация выбрана';
            $container.append(self.create(locationTag, 'location', 'location'));
        }
    },

    // Удаление тега и очистка соответствующего фильтра
    remove: function(filterType, filterValue) {
        switch (filterType) {
            case 'checkbox':
                // Формат: "name|value"
                var checkboxParts = filterValue.split('|');
                var checkboxName = checkboxParts[0];
                var checkboxValue = checkboxParts[1];
                $('[name="' + checkboxName + '"][value="' + checkboxValue + '"]').prop('checked', false);
                break;

            case 'range':
                // Формат: "nameFrom|nameTo"
                var rangeParts = filterValue.split('|');
                var nameFrom = rangeParts[0];
                var nameTo = rangeParts[1];
                // Проверяем есть ли id или используем name
                var $fromById = $('#' + nameFrom);
                var $toById = $('#' + nameTo);
                if ($fromById.length) {
                    $fromById.val('');
                } else {
                    $('[name="' + nameFrom + '"]').val('');
                }
                if ($toById.length) {
                    $toById.val('');
                } else {
                    $('[name="' + nameTo + '"]').val('');
                }
                break;

            case 'select':
                // Формат: "selectId"
                $('#' + filterValue).val('').trigger('change');
                break;

            case 'search':
                // Формат: "inputName"
                $('[name="' + filterValue + '"]').val('');
                break;

            case 'location':
                // Сбрасываем локацию
                $('#lfType').val('');
                $('#lfId').val('');
                $('#lfDetails').val('');
                $('#lfLocationTag').empty();
                // Также сбрасываем UI фильтра локации если есть
                if (window.LocationFilter && window.LocationFilter.reset) {
                    window.LocationFilter.reset();
                }
                break;
        }
    }
};
