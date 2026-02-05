/**
 * Главный файл инициализации таблицы сотрудников
 * Использует модули: EmployeeTableConfig, EmployeeRenderers, EmployeeFilters
 */
(function ($) {
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
                data: function (d) {
                    // Добавить параметры фильтров
                    const filterParams = filters.getParams();
                    return $.extend({}, d, filterParams);
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                }
            },
            columns: columns,
            pageLength: config.pageLength,
            pagingType: config.pagingType,
            searching: config.searching,
            ordering: config.ordering,
            language: config.language,
            drawCallback: function () {
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
        $('.js-example-responsive3').each(function () {
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
        const debouncedSearch = debounce(function () {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        }, 600);

        // Поиск по имени/email/телефону
        $('#search-name-email-phone').on('input', debouncedSearch);

        // Изменение Select2 фильтров
        $('#position, #statusagents, #company, #offices').on('change', function () {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Изменение даты
        $('#datapiker').on('filter:change', function () {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Изменение тегов
        $(document).on('change', '.multiple-menu-list input[type="checkbox"]', function () {
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
            filters.updateFilterCount();
        });

        // Сброс фильтров
        $('#full-filter-btn').on('click', function () {
            filters.reset();
            // Сбросить активный пункт сортировки на "Дата добавления"
            $('.sort-item').removeClass('active');
            $('.sort-item[data-sort="created_at"]').addClass('active');
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
        });

        // Сортировка по клику на пункт меню
        $(document).on('click', '.sort-item', function (e) {
            e.preventDefault();
            const $item = $(this);
            const sortColumn = $item.data('sort');
            const sortDirection = $item.data('direction');

            // Убрать активный класс со всех пунктов
            $('.sort-item').removeClass('active');
            // Добавить активный класс текущему пункту
            $item.addClass('active');

            // Установить сортировку в фильтрах
            filters.setSort(sortColumn, sortDirection);

            // Перезагрузить таблицу
            if (employeesTable) {
                employeesTable.ajax.reload();
            }
        });

        // Checkbox "Выбрать все" в заголовке
        $(document).on('change', '.thead-wrapper.checkBox input[type="checkbox"]', function () {
            const isChecked = $(this).is(':checked');
            $('.tbody-wrapper.checkBox input[type="checkbox"]').prop('checked', isChecked);
        });

        // Индивидуальный checkbox в строке
        $(document).on('change', '.tbody-wrapper.checkBox input[type="checkbox"]', function () {
            const total = $('.tbody-wrapper.checkBox input[type="checkbox"]').length;
            const checked = $('.tbody-wrapper.checkBox input[type="checkbox"]:checked').length;
            $('.thead-wrapper.checkBox input[type="checkbox"]').prop('checked', total === checked && total > 0);
        });

        // Удаление сотрудника
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (confirm('Вы уверены, что хотите удалить этого сотрудника?')) {
                $.ajax({
                    url: `/employees/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (employeesTable) {
                            employeesTable.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        console.error('Delete error:', xhr);
                        alert('Ошибка при удалении сотрудника');
                    }
                });
            }
        });

        // Изменение должности в таблице
        $(document).on('change', '.position-select', function () {
            const $select = $(this);
            const employeeId = $select.data('employee-id');
            const positionId = $select.val();
            const previousValue = $select.data('previous-value') || '';

            // Если уже есть кнопки подтверждения, удалить их
            $select.closest('.tbody-wrapper').find('.position-confirm-buttons').remove();

            // Сохранить предыдущее значение если его еще нет
            if (!$select.data('previous-value')) {
                $select.data('previous-value', previousValue);
            }

            // Создать кнопки подтверждения
            const $confirmButtons = $(`
                <div class="position-confirm-buttons">
                    <button type="button" class="btn-position-confirm" title="Подтвердить">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" fill="#28a745"/>
                        </svg>
                    </button>
                    <button type="button" class="btn-position-cancel" title="Отменить">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" fill="#dc3545"/>
                        </svg>
                    </button>
                </div>
            `);

            // Вставить кнопки после Select2 контейнера
            const $select2Container = $select.next('.select2-container');
            if ($select2Container.length) {
                $select2Container.css('max-width', 'calc(100% - 70px)');
                $select2Container.after($confirmButtons);
            } else {
                $select.css('max-width', 'calc(100% - 70px)');
                $select.after($confirmButtons);
            }

            // Обработчик подтверждения
            $confirmButtons.find('.btn-position-confirm').on('click', function () {
                $.ajax({
                    url: `/employees/${employeeId}/position`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { position_id: positionId },
                    success: function (response) {
                        console.log('Position updated');
                        $select.data('previous-value', positionId);

                        $confirmButtons.remove();

                        const $select2Container = $select.next('.select2-container');
                        if ($select2Container.length) {
                            $select2Container.css('max-width', '');
                        } else {
                            $select.css('max-width', '');
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.success('Должность обновлена');
                        }
                    },
                    error: function (xhr) {
                        console.error('Position update error:', xhr);
                        $select.val(previousValue).trigger('change.select2');

                        $confirmButtons.remove();

                        const $select2Container = $select.next('.select2-container');
                        if ($select2Container.length) {
                            $select2Container.css('max-width', '');
                        } else {
                            $select.css('max-width', '');
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error('Ошибка при обновлении должности');
                        } else {
                            alert('Ошибка при обновлении должности');
                        }
                    }
                });
            });

            // Обработчик отмены
            $confirmButtons.find('.btn-position-cancel').on('click', function () {
                $select.val(previousValue).trigger('change.select2');

                $confirmButtons.remove();

                const $select2Container = $select.next('.select2-container');
                if ($select2Container.length) {
                    $select2Container.css('max-width', '');
                } else {
                    $select.css('max-width', '');
                }
            });
        });

        // Сохранить начальное значение при инициализации селекта должности
        $(document).on('select2:open', '.position-select', function () {
            const $select = $(this);
            if (!$select.data('previous-value')) {
                $select.data('previous-value', $select.val());
            }
        });

        // Изменение офиса в таблице
        $(document).on('change', '.offices-select', function () {
            const $select = $(this);
            const employeeId = $select.data('employee-id');
            const officeId = $select.val();
            const previousValue = $select.data('previous-value') || '';

            // Если уже есть кнопки подтверждения, удалить их
            $select.closest('.tbody-wrapper').find('.office-confirm-buttons').remove();

            // Сохранить предыдущее значение если его еще нет
            if (!$select.data('previous-value')) {
                $select.data('previous-value', previousValue);
            }

            // Создать кнопки подтверждения
            const $confirmButtons = $(`
                <div class="office-confirm-buttons">
                    <button type="button" class="btn-office-confirm" title="Подтвердить">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" fill="#28a745"/>
                        </svg>
                    </button>
                    <button type="button" class="btn-office-cancel" title="Отменить">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" fill="#dc3545"/>
                        </svg>
                    </button>
                </div>
            `);

            // Вставить кнопки после Select2 контейнера (чтобы были на одной линии)
            const $select2Container = $select.next('.select2-container');
            if ($select2Container.length) {
                // Уменьшить ширину Select2 для освобождения места под кнопки
                $select2Container.css('max-width', 'calc(100% - 70px)');
                $select2Container.after($confirmButtons);
            } else {
                // Если Select2 не инициализирован, вставить после самого select
                $select.css('max-width', 'calc(100% - 70px)');
                $select.after($confirmButtons);
            }

            // Обработчик подтверждения
            $confirmButtons.find('.btn-office-confirm').on('click', function () {
                $.ajax({
                    url: `/employees/${employeeId}/office`,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { office_id: officeId },
                    success: function (response) {
                        console.log('Office updated');
                        // Сохранить новое значение как предыдущее
                        $select.data('previous-value', officeId);

                        // Удалить кнопки подтверждения
                        $confirmButtons.remove();

                        // Восстановить ширину Select2
                        const $select2Container = $select.next('.select2-container');
                        if ($select2Container.length) {
                            $select2Container.css('max-width', '');
                        } else {
                            $select.css('max-width', '');
                        }

                        // Показать уведомление об успехе
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Офис обновлен');
                        }
                    },
                    error: function (xhr) {
                        console.error('Office update error:', xhr);
                        // Вернуть предыдущее значение
                        $select.val(previousValue).trigger('change.select2');

                        // Удалить кнопки подтверждения
                        $confirmButtons.remove();

                        // Восстановить ширину Select2
                        const $select2Container = $select.next('.select2-container');
                        if ($select2Container.length) {
                            $select2Container.css('max-width', '');
                        } else {
                            $select.css('max-width', '');
                        }

                        // Показать ошибку
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Ошибка при обновлении офиса');
                        } else {
                            alert('Ошибка при обновлении офиса');
                        }
                    }
                });
            });

            // Обработчик отмены
            $confirmButtons.find('.btn-office-cancel').on('click', function () {
                // Вернуть предыдущее значение
                $select.val(previousValue).trigger('change.select2');

                // Удалить кнопки подтверждения
                $confirmButtons.remove();

                // Восстановить ширину Select2
                const $select2Container = $select.next('.select2-container');
                if ($select2Container.length) {
                    $select2Container.css('max-width', '');
                } else {
                    $select.css('max-width', '');
                }
            });
        });

        // Сохранить начальное значение при инициализации селекта
        $(document).on('select2:open', '.offices-select', function () {
            const $select = $(this);
            if (!$select.data('previous-value')) {
                $select.data('previous-value', $select.val());
            }
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

        // Добавить стили для кнопок подтверждения
        addConfirmButtonStyles();

        console.log('Employees table initialized');
    }

    /**
     * Добавить стили для кнопок подтверждения офиса и должности
     */
    function addConfirmButtonStyles() {
        if ($('#confirm-buttons-styles').length === 0) {
            const styles = `
                <style id="confirm-buttons-styles">
                    .office-confirm-buttons,
                    .position-confirm-buttons {
                        display: inline-flex;
                        gap: 8px;
                        margin-left: 8px;
                        vertical-align: middle;
                        animation: fadeIn 0.2s ease-in;
                    }

                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                            transform: scale(0.8);
                        }
                        to {
                            opacity: 1;
                            transform: scale(1);
                        }
                    }

                    .btn-office-confirm,
                    .btn-office-cancel,
                    .btn-position-confirm,
                    .btn-position-cancel {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 28px;
                        height: 28px;
                        padding: 0;
                        border: 1px solid #dee2e6;
                        border-radius: 4px;
                        background: white;
                        cursor: pointer;
                        transition: all 0.2s ease;
                    }

                    .btn-office-confirm:hover,
                    .btn-position-confirm:hover {
                        background: #28a745;
                        border-color: #28a745;
                        transform: scale(1.1);
                    }

                    .btn-office-confirm:hover svg path,
                    .btn-position-confirm:hover svg path {
                        fill: white;
                    }

                    .btn-office-cancel:hover,
                    .btn-position-cancel:hover {
                        background: #dc3545;
                        border-color: #dc3545;
                        transform: scale(1.1);
                    }

                    .btn-office-cancel:hover svg path,
                    .btn-position-cancel:hover svg path {
                        fill: white;
                    }

                    .btn-office-confirm:active,
                    .btn-office-cancel:active,
                    .btn-position-confirm:active,
                    .btn-position-cancel:active {
                        transform: scale(0.95);
                    }
                </style>
            `;
            $('head').append(styles);
        }
    }

    // Запуск при готовности DOM
    $(document).ready(init);

})(jQuery);
