$(document).ready(function() {

    // URL для AJAX запросов
    const ajaxUrl = '/properties/ajax-data';

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

    // Обновление таблицы при изменении select в хедере
    $('#deal_type_id, #currency_id').on('change', function() {
        table.ajax.reload();
    });

    // Кнопка "Применить" в расширенном фильтре
    $('.full-filter .btn-primary[type="submit"]').on('click', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Кнопка сброса фильтров
    $('#delete-params-on-filter').on('click', function(e) {
        e.preventDefault();

        // Сбрасываем все поля формы
        $('#filter-form')[0].reset();

        // Сбрасываем select2 если используется
        $('.js-example-responsive2, .js-example-responsive3').val('').trigger('change');

        // Снимаем все чекбоксы
        $('#filter-form input[type="checkbox"]').prop('checked', false);

        // Перезагружаем таблицу
        table.ajax.reload();

        // Скрываем счетчик фильтров
        $('.full-filter-counter').hide();
    });

    // Обновление счетчика активных фильтров
    function updateFilterCounter() {
        let count = 0;

        // Считаем заполненные поля
        $('#filter-form input[type="text"]').each(function() {
            if ($(this).val()) count++;
        });

        // Считаем выбранные select
        $('#filter-form select').each(function() {
            if ($(this).val()) count++;
        });

        // Считаем отмеченные чекбоксы
        count += $('#filter-form input[type="checkbox"]:checked').length;

        // Обновляем отображение
        if (count > 0) {
            $('.full-filter-counter span').text(count);
            $('.full-filter-counter').show();
        } else {
            $('.full-filter-counter').hide();
        }
    }

    // Инициализация тултипов
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
