$(document).ready(function() {

    // URL для AJAX запросов
    const ajaxUrl = '/properties/ajax-data';

    // ========== Debounce функция ==========
    // Задержка перед выполнением запроса после окончания ввода
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Задержка в миллисекундах (600ms)
    const DEBOUNCE_DELAY = 600;

    // Инициализация DataTables с Server-Side Processing
    const table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        searching: false, // Отключаем встроенный поиск (используем свои фильтры)
        ordering: false,
        paging: true,
        pageLength: 10,
        pagingType: 'simple_numbers',
        info: true,

        // AJAX конфигурация
        ajax: {
            url: ajaxUrl,
            type: 'GET',
            data: function(d) {
                // Добавляем параметры фильтров из формы
                const filterForm = $('#filter-form');

                // Основные фильтры из хедера
                d.deal_type_id = filterForm.find('#deal_type_id').val();
                d.price_from = filterForm.find('#price_from').val();
                d.price_to = filterForm.find('#price_to').val();
                d.currency_id = filterForm.find('#currency_id').val();

                // Расширенные фильтры
                d.status = filterForm.find('#status').val();
                d.area_from = filterForm.find('[name="area_from"]').val();
                d.area_to = filterForm.find('[name="area_to"]').val();
                d.area_living_from = filterForm.find('[name="area_living_from"]').val();
                d.area_living_to = filterForm.find('[name="area_living_to"]').val();
                d.area_kitchen_from = filterForm.find('[name="area_kitchen_from"]').val();
                d.area_kitchen_to = filterForm.find('[name="area_kitchen_to"]').val();
                d.area_land_from = filterForm.find('[name="area_land_from"]').val();
                d.area_land_to = filterForm.find('[name="area_land_to"]').val();
                d.floor_from = filterForm.find('[name="floor_from"]').val();
                d.floor_to = filterForm.find('[name="floor_to"]').val();
                d.floors_total_from = filterForm.find('[name="floors_total_from"]').val();
                d.floors_total_to = filterForm.find('[name="floors_total_to"]').val();
                d.price_per_m2_from = filterForm.find('[name="price_per_m2_from"]').val();
                d.price_per_m2_to = filterForm.find('[name="price_per_m2_to"]').val();

                // Множественные чекбоксы
                d.property_type_id = getCheckedValues('[name="property_type_id[]"]');
                d.condition_id = getCheckedValues('[name="condition_id[]"]');
                d.building_type_id = getCheckedValues('[name="building_type_id[]"]');
                d.year_built = getCheckedValues('[name="year_built[]"]');
                d.wall_type_id = getCheckedValues('[name="wall_type_id[]"]');
                d.room_count_id = getCheckedValues('[name="room_count_id[]"]');
                d.heating_type_id = getCheckedValues('[name="heating_type_id[]"]');
                d.bathroom_count_id = getCheckedValues('[name="bathroom_count_id[]"]');
                d.ceiling_height_id = getCheckedValues('[name="ceiling_height_id[]"]');
                d.features = getCheckedValues('[name="features[]"]');
                d.developer_id = getCheckedValues('[name="developer_id[]"]');

                // Поиск
                d.search_id = filterForm.find('[name="search_id"]').val();
                d.contact_search = filterForm.find('[name="contact_search"]').val();

                // Даты
                d.created_from = filterForm.find('[name="created_from"]').val();
                d.created_to = filterForm.find('[name="created_to"]').val();

                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
            }
        },

        // Определение колонок
        columns: [
            {
                data: 'checkbox',
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper checkBox">' +
                        '<label class="my-custom-input">' +
                        '<input type="checkbox" class="row-checkbox" value="' + data + '">' +
                        '<span class="my-custom-box"></span>' +
                        '</label></div>';
                }
            },
            {
                data: 'location',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper location">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'deal_type',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper type">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'area',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper area">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'condition',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper condition">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'floor',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper floor">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'photo',
                orderable: false,
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper photo">' +
                        (data !== '-' ? data : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'price',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper price">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            },
            {
                data: 'contact',
                render: function(data, type, row) {
                    return '<div class="tbody-wrapper contact">' +
                        (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
                        '</div>';
                }
            }
        ],

        // Языковые настройки
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Загрузка...</span></div>',
            lengthMenu: 'Показать _MENU_ записей',
            zeroRecords: '<div class="text-center py-4"><div class="text-muted"><p class="mb-2">Объекты не найдены</p></div></div>',
            info: 'Показано _START_ - _END_ из _TOTAL_ записей',
            infoEmpty: 'Записи отсутствуют',
            infoFiltered: '(отфильтровано из _MAX_ записей)',
            search: 'Поиск:',
            paginate: {
                first: 'Первая',
                last: 'Последняя',
                next: 'Следующая',
                previous: 'Предыдущая'
            }
        },

        // Отключаем сортировку по умолчанию
        order: [[7, 'desc']], // Сортировка по цене по умолчанию

        // Callback после отрисовки
        drawCallback: function(settings) {
            // Обновляем информацию о количестве
            const info = table.page.info();
            $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');

            // Сбрасываем чекбокс "выбрать все"
            $('#select-all-checkbox').prop('checked', false);

            // Обновляем счетчик фильтров после каждой перезагрузки
            updateFilterCounter();
        }
    });

    // ========== Вспомогательные функции ==========

    // Получение значений отмеченных чекбоксов
    function getCheckedValues(selector) {
        const values = [];
        $(selector + ':checked').each(function() {
            values.push($(this).val());
        });
        return values.length > 0 ? values : null;
    }

    // Функция перезагрузки таблицы с debounce для текстовых полей
    const debouncedReload = debounce(function() {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    // Debounce для обновления тегов
    const debouncedUpdateTags = debounce(function() {
        updateFilterTags();
    }, DEBOUNCE_DELAY);

    // Функция мгновенной перезагрузки (для select и checkbox)
    function reloadTable() {
        table.ajax.reload();
    }

    // ========== Теги фильтров ==========

    // SVG иконка закрытия для тегов
    const closeIconSvg = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"></path>' +
        '<path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"></path>' +
        '</svg>';

    // Создание HTML тега
    function createTag(text, filterType, filterValue) {
        return '<div class="badge rounded-pill" data-filter-type="' + filterType + '" data-filter-value="' + filterValue + '">' +
            text +
            '<button type="button" aria-label="Close">' + closeIconSvg + '</button>' +
            '</div>';
    }

    // Конфигурация чекбокс-фильтров
    const checkboxFilters = [
        { name: 'property_type_id[]', label: 'Тип недвижимости' },
        { name: 'condition_id[]', label: 'Состояние' },
        { name: 'building_type_id[]', label: 'Тип здания' },
        { name: 'year_built[]', label: 'Год постройки' },
        { name: 'wall_type_id[]', label: 'Тип стен' },
        { name: 'room_count_id[]', label: 'Кол-во комнат' },
        { name: 'heating_type_id[]', label: 'Отопление' },
        { name: 'bathroom_count_id[]', label: 'Ванные комнаты' },
        { name: 'ceiling_height_id[]', label: 'Высота потолков' },
        { name: 'features[]', label: 'Дополнительно' },
        { name: 'developer_id[]', label: 'Девелопер' }
    ];

    // Конфигурация range-фильтров (от/до)
    const rangeFilters = [
        { nameFrom: 'price_from', nameTo: 'price_to', label: 'Цена', idFrom: 'price_from', idTo: 'price_to' },
        { nameFrom: 'area_from', nameTo: 'area_to', label: 'Площадь общая' },
        { nameFrom: 'area_living_from', nameTo: 'area_living_to', label: 'Площадь жилая' },
        { nameFrom: 'area_kitchen_from', nameTo: 'area_kitchen_to', label: 'Площадь кухни' },
        { nameFrom: 'area_land_from', nameTo: 'area_land_to', label: 'Площадь участка' },
        { nameFrom: 'floor_from', nameTo: 'floor_to', label: 'Этаж' },
        { nameFrom: 'floors_total_from', nameTo: 'floors_total_to', label: 'Этажность' },
        { nameFrom: 'price_per_m2_from', nameTo: 'price_per_m2_to', label: 'Цена за м²' }
    ];

    // Конфигурация select-фильтров
    const selectFilters = [
        { id: 'deal_type_id', label: 'Тип сделки' },
        { id: 'status', label: 'Объекты' }
    ];

    // Конфигурация текстовых фильтров поиска
    const searchFilters = [
        { name: 'search_id', label: 'ID' },
        { name: 'contact_search', label: 'Контакт' }
    ];

    // Обновление тегов для всех фильтров
    function updateFilterTags() {
        const $tagsContainer = $('.filter-tags');
        $tagsContainer.empty();

        // ========== Чекбокс-фильтры ==========
        checkboxFilters.forEach(function(filter) {
            $('[name="' + filter.name + '"]:checked').each(function() {
                const $checkbox = $(this);
                const value = $checkbox.val();
                const text = $checkbox.closest('label').find('.my-custom-text').text();
                $tagsContainer.append(createTag(text, 'checkbox', filter.name + '|' + value));
            });
        });

        // ========== Range-фильтры (от/до) ==========
        rangeFilters.forEach(function(filter) {
            const $inputFrom = filter.idFrom
                ? $('#' + filter.idFrom)
                : $('[name="' + filter.nameFrom + '"]');
            const $inputTo = filter.idTo
                ? $('#' + filter.idTo)
                : $('[name="' + filter.nameTo + '"]');

            const valueFrom = $inputFrom.val();
            const valueTo = $inputTo.val();

            if (valueFrom || valueTo) {
                let text = filter.label + ': ';
                if (valueFrom && valueTo) {
                    text += 'от ' + valueFrom + ' до ' + valueTo;
                } else if (valueFrom) {
                    text += 'от ' + valueFrom;
                } else {
                    text += 'до ' + valueTo;
                }
                $tagsContainer.append(createTag(text, 'range', filter.nameFrom + '|' + filter.nameTo));
            }
        });

        // ========== Select-фильтры ==========
        selectFilters.forEach(function(filter) {
            const $select = $('#' + filter.id);
            const value = $select.val();
            if (value) {
                const text = $select.find('option:selected').text();
                $tagsContainer.append(createTag(filter.label + ': ' + text, 'select', filter.id));
            }
        });

        // ========== Текстовые фильтры поиска ==========
        searchFilters.forEach(function(filter) {
            const $input = $('[name="' + filter.name + '"]');
            const value = $input.val();
            if (value) {
                $tagsContainer.append(createTag(filter.label + ': ' + value, 'search', filter.name));
            }
        });

        // ========== Фильтр дат ==========
        const dateFrom = $('#created_from').val();
        const dateTo = $('#created_to').val();
        if (dateFrom || dateTo) {
            let text = 'Дата: ';
            if (dateFrom && dateTo) {
                text += dateFrom + ' - ' + dateTo;
            } else if (dateFrom) {
                text += 'с ' + dateFrom;
            } else {
                text += 'до ' + dateTo;
            }
            $tagsContainer.append(createTag(text, 'date', 'created_from|created_to'));
        }
    }

    // Обработчик клика на кнопку удаления тега
    $(document).on('click', '.filter-tags .badge button', function() {
        const $tag = $(this).closest('.badge');
        const filterType = $tag.data('filter-type');
        const filterValue = $tag.data('filter-value');

        switch (filterType) {
            case 'checkbox':
                // Формат: "name|value"
                const [checkboxName, checkboxValue] = filterValue.split('|');
                $('[name="' + checkboxName + '"][value="' + checkboxValue + '"]').prop('checked', false);
                break;

            case 'range':
                // Формат: "nameFrom|nameTo"
                const [nameFrom, nameTo] = filterValue.split('|');
                // Проверяем есть ли id или используем name
                const $fromById = $('#' + nameFrom);
                const $toById = $('#' + nameTo);
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

            case 'date':
                // Формат: "created_from|created_to"
                $('#created_from').val('');
                $('#created_to').val('');
                $('#datapiker1').val('');
                break;
        }

        // Обновляем теги
        updateFilterTags();

        // Перезагружаем таблицу
        reloadTable();
    });

    // ========== Обработчики событий ==========

    // Выбрать все / снять все
    $('#select-all-checkbox').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#example tbody').on('change', '.row-checkbox', function() {
        const allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $('#select-all-checkbox').prop('checked', allChecked);
    });

    // Применение фильтров при отправке формы
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // ========== Автоматическая фильтрация ==========

    // Текстовые поля с debounce (цена, площадь, этаж и т.д.)
    const textInputSelectors = [
        '#price_from',
        '#price_to',
        '[name="area_from"]',
        '[name="area_to"]',
        '[name="area_living_from"]',
        '[name="area_living_to"]',
        '[name="area_kitchen_from"]',
        '[name="area_kitchen_to"]',
        '[name="area_land_from"]',
        '[name="area_land_to"]',
        '[name="floor_from"]',
        '[name="floor_to"]',
        '[name="floors_total_from"]',
        '[name="floors_total_to"]',
        '[name="price_per_m2_from"]',
        '[name="price_per_m2_to"]',
        '[name="search_id"]',
        '[name="contact_search"]'
    ].join(', ');

    // Обработчик ввода для текстовых полей (с задержкой)
    $('#filter-form').on('input', textInputSelectors, function() {
        debouncedReload();
        debouncedUpdateTags();
    });

    // Select поля - мгновенная реакция
    $('#filter-form').on('change', '#deal_type_id, #currency_id, #status, #full-filter-currency', function() {
        reloadTable();
        updateFilterTags();
    });

    // Чекбоксы фильтров - мгновенная реакция
    const checkboxSelectors = [
        '[name="property_type_id[]"]',
        '[name="condition_id[]"]',
        '[name="building_type_id[]"]',
        '[name="year_built[]"]',
        '[name="wall_type_id[]"]',
        '[name="room_count_id[]"]',
        '[name="heating_type_id[]"]',
        '[name="bathroom_count_id[]"]',
        '[name="ceiling_height_id[]"]',
        '[name="features[]"]',
        '[name="developer_id[]"]'
    ].join(', ');

    $('#filter-form').on('change', checkboxSelectors, function() {
        reloadTable();
        updateFilterTags();
    });

    // Daterangepicker - фильтрация после выбора дат
    $('#datapiker1').on('apply.daterangepicker', function(ev, picker) {
        $('#created_from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#created_to').val(picker.endDate.format('YYYY-MM-DD'));
        reloadTable();
        updateFilterTags();
    });

    $('#datapiker1').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#created_from').val('');
        $('#created_to').val('');
        reloadTable();
        updateFilterTags();
    });

    // ========== Кнопки поиска (для совместимости) ==========

    // Кнопка поиска по ID
    $('#search-id-btn').on('click', function() {
        reloadTable();
        updateFilterTags();
    });

    // Кнопка поиска по контакту
    $('#search-contact-btn').on('click', function() {
        reloadTable();
        updateFilterTags();
    });

    // Enter в поле поиска по ID
    $('#search_id').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            updateFilterTags();
        }
    });

    // Enter в поле поиска по контакту
    $('#contact_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            updateFilterTags();
        }
    });

    // ========== Сброс фильтров ==========

    // Кнопка сброса в счетчике фильтров
    $('#delete-params-on-filter').on('click', function(e) {
        e.preventDefault();
        resetFilters();
    });

    // Кнопка "Сбросить" в расширенном фильтре
    $('#reset-filters-btn').on('click', function(e) {
        e.preventDefault();
        resetFilters();
    });

    // Функция сброса всех фильтров
    function resetFilters() {
        // Сбрасываем все поля формы
        $('#filter-form')[0].reset();

        // Сбрасываем select2 если используется
        $('.js-example-responsive2, .js-example-responsive3').val('').trigger('change');

        // Снимаем все чекбоксы
        $('#filter-form input[type="checkbox"]').prop('checked', false);

        // Очищаем скрытые поля дат
        $('#created_from').val('');
        $('#created_to').val('');

        // Очищаем теги
        updateFilterTags();

        // Перезагружаем таблицу
        table.ajax.reload();

        // Скрываем счетчик фильтров
        $('.full-filter-counter').hide();
    }

    // ========== Счетчик активных фильтров ==========

    // Обновление счетчика активных фильтров
    function updateFilterCounter() {
        let count = 0;

        // Считаем заполненные текстовые поля
        $('#filter-form input[type="text"]').each(function() {
            if ($(this).val() && $(this).attr('name') !== 'search-additionally' && $(this).attr('name') !== 'search-developer') {
                count++;
            }
        });

        // Считаем скрытые поля дат
        if ($('#created_from').val()) count++;
        if ($('#created_to').val()) count++;

        // Считаем выбранные select (кроме валюты по умолчанию)
        $('#filter-form select').each(function() {
            const val = $(this).val();
            const name = $(this).attr('name') || $(this).attr('id');
            // Пропускаем валюту если она первая по умолчанию
            if (val && name !== 'currency_id' && name !== 'full-filter-currency') {
                count++;
            }
        });

        // Считаем отмеченные чекбоксы фильтров (не select-all)
        $('#filter-form input[type="checkbox"]:checked').each(function() {
            const name = $(this).attr('name');
            if (name && name !== 'select-all-checkbox' && !$(this).hasClass('row-checkbox')) {
                count++;
            }
        });

        // Обновляем отображение
        if (count > 0) {
            $('.full-filter-counter span').text(count);
            $('.full-filter-counter').show();
        } else {
            $('.full-filter-counter').hide();
        }
    }

    // ========== Инициализация ==========

    // Инициализация тегов при загрузке страницы (если есть выбранные фильтры)
    updateFilterTags();

    // Инициализация тултипов
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
