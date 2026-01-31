/**
 * Главный файл инициализации таблицы сотрудников
 * Использует модули: EmployeeTableConfig, EmployeeRenderers, EmployeeFilters
 */
(function($) {
    'use strict';

    // Глобальная ссылка на таблицу
    let employeesTable = null;

    // Debounce функция для текстовых полей
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

    /**
     * Инициализация DataTables
     */
    function initDataTable() {
        const config = window.EmployeeTableConfig;
        const renderers = window.EmployeeRenderers;
        const filters = window.EmployeeFilters;

        // Настройка колонок с рендерерами
        const columns = [
            { data: null, render: renderers.checkbox },
            { data: null, render: renderers.photo },
            { data: null, render: renderers.agent },
            { data: null, render: renderers.position },
            { data: null, render: renderers.office },
            { data: null, render: renderers.objectsCount },
            { data: null, render: renderers.clientsCount },
            { data: null, render: renderers.successDeals },
            { data: null, render: renderers.failedDeals },
            { data: null, render: renderers.activeUntil },
            { data: null, render: renderers.actions }
        ];

        // Инициализация DataTables
        employeesTable = $('#example').DataTable({
            processing: config.processing,
            serverSide: config.serverSide,
            ajax: {
                url: config.ajaxUrl,
                type: 'GET',
                data: function(d) {
                    // Добавить параметры фильтров
                    const filterParams = filters.getParams();
                    return $.extend({}, d, filterParams);
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                }
            },
            columns: columns,
            pageLength: config.pageLength,
            pagingType: config.pagingType,
            searching: config.searching,
            ordering: config.ordering,
            language: config.language,
            drawCallback: function() {
                // После отрисовки таблицы
                initTooltips();
                initSelect2InTable();
            }
        });

        return employeesTable;
    }

    /**
     * Инициализация Bootstrap tooltips
     */
    function initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    /**
     * Инициализация Select2 внутри таблицы (должность, офис)
     */
    function initSelect2InTable() {
        $('.js-example-responsive3').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: 'Выберите...',
                    allowClear: true,
                    minimumResultsForSearch: -1,
                    width: '100%'
                });
            }
        });
    }

    /**
     * Инициализация обработчиков событий
     */
    function initEventHandlers() {
        const filters = window.EmployeeFilters;

        // Debounced поиск по тексту
        const debouncedSearch = debounce(function() {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        }, 600);

        // Поиск по имени/email/телефону
        $('#search-name-email-phone').on('input', debouncedSearch);

        // Изменение Select2 фильтров
        $('#position, #statusagents, #company, #offices').on('change', function() {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Изменение даты
        $('#datapiker').on('filter:change', function() {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Изменение тегов
        $(document).on('change', '.multiple-menu-list input[type="checkbox"]', function() {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Сброс фильтров
        $('#full-filter-btn').on('click', function() {
            filters.reset();
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
        });

        // Checkbox "Выбрать все" в заголовке
        $(document).on('change', '.thead-wrapper.checkBox input[type="checkbox"]', function() {
            const isChecked = $(this).is(':checked');
            $('.tbody-wrapper.checkBox input[type="checkbox"]').prop('checked', isChecked);
        });

        // Индивидуальный checkbox в строке
        $(document).on('change', '.tbody-wrapper.checkBox input[type="checkbox"]', function() {
            const total = $('.tbody-wrapper.checkBox input[type="checkbox"]').length;
            const checked = $('.tbody-wrapper.checkBox input[type="checkbox"]:checked').length;
            $('.thead-wrapper.checkBox input[type="checkbox"]').prop('checked', total === checked && total > 0);
        });

        // Удаление сотрудника
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (confirm('Вы уверены, что хотите удалить этого сотрудника?')) {
                $.ajax({
                    url: `/employees/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (employeesTable) {
                            employeesTable.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        alert('Ошибка при удалении сотрудника');
                    }
                });
            }
        });

        // Изменение должности в таблице
        $(document).on('change', '.position-select', function() {
            const employeeId = $(this).data('employee-id');
            const positionId = $(this).val();

            $.ajax({
                url: `/employees/${employeeId}/position`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { position_id: positionId },
                success: function(response) {
                    console.log('Position updated');
                },
                error: function(xhr) {
                    console.error('Position update error:', xhr);
                }
            });
        });

        // Изменение офиса в таблице
        $(document).on('change', '.offices-select', function() {
            const employeeId = $(this).data('employee-id');
            const officeId = $(this).val();

            $.ajax({
                url: `/employees/${employeeId}/office`,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { office_id: officeId },
                success: function(response) {
                    console.log('Office updated');
                },
                error: function(xhr) {
                    console.error('Office update error:', xhr);
                }
            });
        });
    }

    /**
     * Главная функция инициализации
     */
    function init() {
        const filters = window.EmployeeFilters;

        // Инициализация фильтров
        filters.initSelect2();
        filters.initDatePicker();
        filters.initMultipleMenu();

        // Инициализация DataTables
        initDataTable();

        // Инициализация обработчиков
        initEventHandlers();

        // Инициализация tooltips на странице
        initTooltips();

        // Инициализация Select2 в таблице (для статичных данных)
        initSelect2InTable();

        console.log('Employees table initialized');
    }

    // Запуск при готовности DOM
    $(document).ready(init);

})(jQuery);
