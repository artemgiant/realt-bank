/**
 * Логика фильтров для таблицы сотрудников
 */
window.EmployeeFilters = {
    // Текущие значения сортировки
    sortColumn: null,
    sortDirection: 'asc',

    /**
     * Собрать все параметры фильтра для AJAX запроса
     */
    getParams: function() {
        const params = {};

        // Поиск по имени/email/телефону
        const searchValue = $('#search-name-email-phone').val();
        if (searchValue && searchValue.trim()) {
            params.search = searchValue.trim();
        }

        // Должность
        const position = $('#position').val();
        if (position) {
            params.position_id = position;
        }

        // Статус агента
        const status = $('#statusagents').val();
        if (status) {
            params.status = status;
        }

        // Компания
        const company = $('#company').val();
        if (company) {
            params.company_id = company;
        }

        // Офис
        const office = $('#offices').val();
        if (office) {
            params.office_id = office;
        }

        // Теги (множественный выбор)
        const selectedTags = [];
        $('.multiple-menu-list input[type="checkbox"]:checked').each(function() {
            const name = $(this).attr('name');
            if (name && name !== 'complex-all' && !name.startsWith('checkbox-all')) {
                selectedTags.push(name);
            }
        });
        if (selectedTags.length > 0) {
            params.tags = selectedTags;
        }

        // Дата (DateRangePicker)
        const dateValue = $('#datapiker').val();
        if (dateValue && dateValue.trim()) {
            params.date_range = dateValue.trim();
        }

        // Сортировка
        if (this.sortColumn) {
            params.sort_column = this.sortColumn;
            params.sort_direction = this.sortDirection;
        }

        return params;
    },

    /**
     * Установить сортировку
     */
    setSort: function(column, direction) {
        this.sortColumn = column;
        this.sortDirection = direction || 'asc';
    },

    /**
     * Сбросить все фильтры
     */
    reset: function() {
        // Очистить текстовые поля
        $('#search-name-email-phone').val('');
        $('#datapiker').val('');

        // Сбросить Select2
        $('#position').val(null).trigger('change');
        $('#statusagents').val(null).trigger('change');
        $('#company').val(null).trigger('change');
        $('#offices').val(null).trigger('change');

        // Сбросить чекбоксы тегов
        $('.multiple-menu-list input[type="checkbox"]').prop('checked', false);
        $('.multiple-menu-list input[data-name="checkbox-all"]').prop('checked', true);

        // Сбросить сортировку
        this.sortColumn = null;
        this.sortDirection = 'asc';

        // Обновить счетчик
        this.updateFilterCount();
    },

    /**
     * Обновить счетчик активных фильтров
     */
    updateFilterCount: function() {
        let count = 0;
        const params = this.getParams();

        // Подсчет активных фильтров
        if (params.search) count++;
        if (params.position_id) count++;
        if (params.status) count++;
        if (params.company_id) count++;
        if (params.office_id) count++;
        if (params.tags && params.tags.length > 0) count++;
        if (params.date_range) count++;

        // Обновить бейдж или индикатор
        const $filterBtn = $('#full-filter-btn');
        if (count > 0) {
            $filterBtn.prop('disabled', false);
            // Можно добавить бейдж с количеством
        } else {
            $filterBtn.prop('disabled', true);
        }

        return count;
    },

    /**
     * Инициализация Select2 для фильтров
     */
    initSelect2: function() {
        // Инициализация Select2 для фильтров в шапке
        $('.js-example-responsive2').each(function() {
            const $select = $(this);
            const placeholder = $select.attr('id');

            let placeholderText = 'Выберите...';
            switch(placeholder) {
                case 'position': placeholderText = 'Должность'; break;
                case 'statusagents': placeholderText = 'Статус'; break;
                case 'company': placeholderText = 'Компания'; break;
                case 'offices': placeholderText = 'Офис'; break;
            }

            $select.select2({
                placeholder: placeholderText,
                allowClear: true,
                minimumResultsForSearch: -1, // Скрыть поиск
                width: '100%'
            });
        });
    },

    /**
     * Инициализация DateRangePicker
     */
    initDatePicker: function() {
        if (typeof moment !== 'undefined' && $.fn.daterangepicker) {
            $('#datapiker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD.MM.YYYY',
                    separator: ' - ',
                    applyLabel: 'Применить',
                    cancelLabel: 'Отмена',
                    fromLabel: 'От',
                    toLabel: 'До',
                    customRangeLabel: 'Выбрать период',
                    weekLabel: 'Нед',
                    daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                    firstDay: 1
                }
            });

            $('#datapiker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
                // Триггер для обновления таблицы
                $(this).trigger('filter:change');
            });

            $('#datapiker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $(this).trigger('filter:change');
            });
        }
    },

    /**
     * Инициализация multiple menu (теги)
     */
    initMultipleMenu: function() {
        // Открытие/закрытие меню
        $(document).on('click', '.multiple-menu-btn', function(e) {
            e.stopPropagation();
            const $wrapper = $(this).siblings('.multiple-menu-wrapper');
            const isOpen = $(this).attr('data-open-menu') === 'true';

            // Закрыть все другие меню
            $('.multiple-menu-btn').attr('data-open-menu', 'false');
            $('.multiple-menu-wrapper').hide();

            if (!isOpen) {
                $(this).attr('data-open-menu', 'true');
                $wrapper.show();
            }
        });

        // Закрытие при клике вне меню
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.multiple-menu').length) {
                $('.multiple-menu-btn').attr('data-open-menu', 'false');
                $('.multiple-menu-wrapper').hide();
            }
        });

        // Логика "Все" чекбокса
        $(document).on('change', '.multiple-menu-list input[data-name="checkbox-all"]', function() {
            const isChecked = $(this).is(':checked');
            $(this).closest('.multiple-menu-list')
                .find('input[type="checkbox"]')
                .not(this)
                .prop('checked', false);
        });

        // Снять "Все" при выборе конкретного тега
        $(document).on('change', '.multiple-menu-list input[type="checkbox"]:not([data-name="checkbox-all"])', function() {
            const $list = $(this).closest('.multiple-menu-list');
            const $allCheckbox = $list.find('input[data-name="checkbox-all"]');

            if ($(this).is(':checked')) {
                $allCheckbox.prop('checked', false);
            } else {
                // Если ничего не выбрано, выбрать "Все"
                const anyChecked = $list.find('input[type="checkbox"]:not([data-name="checkbox-all"]):checked').length > 0;
                if (!anyChecked) {
                    $allCheckbox.prop('checked', true);
                }
            }
        });

        // Поиск в меню
        $(document).on('input', '.multiple-menu-search', function() {
            const searchText = $(this).val().toLowerCase();
            const $items = $(this).closest('.multiple-menu-wrapper').find('.multiple-menu-item');

            $items.each(function() {
                const text = $(this).find('.my-custom-text').text().toLowerCase();
                $(this).toggle(text.includes(searchText));
            });
        });
    }
};
