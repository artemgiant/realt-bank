/**
 * Главный файл инициализации DataTables для таблицы девелоперов
 * Использует модули: DeveloperRenderers, DeveloperTableConfig, DeveloperFilters
 */
$(document).ready(function () {

    // Ссылки на модули
    var Config = window.DeveloperTableConfig;
    var Filters = window.DeveloperFilters;

    // ========== Debounce функция ==========
    function debounce(func, wait) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            var later = function () {
                clearTimeout(timeout);
                func.apply(context, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Задержка в миллисекундах (600ms)
    var DEBOUNCE_DELAY = 600;

    // ========== Инициализация DataTables ==========
    var settings = Config.getBaseSettings();

    // AJAX конфигурация
    settings.ajax = {
        url: Config.ajaxUrl,
        type: 'GET',
        data: function (d) {
            return Filters.collectFilterData(d);
        },
        error: function (xhr, error, thrown) {
            console.error('DataTables AJAX error:', error, thrown);
        }
    };

    // Callback после отрисовки
    settings.drawCallback = function (settings) {
        // Обновляем информацию о количестве
        var info = table.page.info();
        $('#developers-table_info').html('Всего: <b>' + info.recordsDisplay + '</b>');

        // Сбрасываем чекбокс "выбрать все"
        $('#select-all-checkbox').prop('checked', false);

        // Обновляем счетчик фильтров
        Filters.updateCounter();

        // Инициализация тултипов для новых строк
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    var table = $('#developers-table').DataTable(settings);

    // ========== Функции перезагрузки ==========

    // Функция перезагрузки таблицы с debounce для текстовых полей
    var debouncedReload = debounce(function () {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    // Функция мгновенной перезагрузки
    function reloadTable() {
        table.ajax.reload();
    }

    // ========== Обработчики событий ==========

    // Выбрать все / снять все
    $('#select-all-checkbox').on('change', function () {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#developers-table tbody').on('change', '.row-checkbox', function () {
        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $('#select-all-checkbox').prop('checked', allChecked);
    });

    // Применение фильтров при отправке формы
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // ========== Сортировка ==========

    // Обработчик клика на пункты сортировки
    $(document).on('click', '.sort-option', function (e) {
        e.preventDefault();

        var $this = $(this);
        var sortField = $this.data('sort-field');
        var sortDir = $this.data('sort-dir');

        // Устанавливаем сортировку
        Filters.setSort(sortField, sortDir);

        // Убираем active класс со всех пунктов
        $('.sort-option').removeClass('active');

        // Добавляем active класс текущему пункту
        $this.addClass('active');

        // Перезагружаем таблицу
        reloadTable();
    });

    // ========== Автоматическая фильтрация ==========

    // Обработчик ввода для текстовых полей (с задержкой)
    $('#filter-form').on('input', Filters.textInputSelectors, function () {
        debouncedReload();
    });

    // ========== Кнопки поиска ==========

    // Кнопка поиска по ID
    $('#search-id-btn').on('click', function () {
        reloadTable();
    });

    // Кнопка поиска по контакту
    $('#search-contact-btn').on('click', function () {
        reloadTable();
    });

    // Enter в поле поиска по ID
    $('#search_id').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
        }
    });

    // Enter в поле поиска по контакту
    $('#contact_search').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
        }
    });

    // ========== Сброс фильтров ==========

    // Кнопка сброса в счетчике фильтров
    $('#delete-params-on-filter').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    // ========== Детальная информация (child row) ==========

    function formatChildRow(data) {
        var description = data.description || '';
        var website = data.website || '';
        var agentNotes = data.agent_notes || '';

        // Обрезаем описание если длинное
        var shortDesc = description;
        var fullDesc = '';
        var hasMore = false;

        if (description.length > 200) {
            shortDesc = description.substring(0, 200) + '...';
            fullDesc = description;
            hasMore = true;
        }

        var descriptionHtml = description ?
            '<div class="description-text">' +
            '<span class="short-text">' + shortDesc + '</span>' +
            (hasMore ?
                '<span class="full-text" style="display: none;">' + fullDesc + '</span>' +
                '<button class="btn btn-show-text" type="button">Ещё</button>'
                : '') +
            '</div>' : '';

        var websiteHtml = website ?
            '<p class="description-note">' +
            '<strong>Сайт:</strong> ' +
            '<a href="' + website + '" target="_blank">' + website + '</a>' +
            '</p>' : '';

        var agentNotesHtml = agentNotes ?
            '<p class="description-note">' +
            '<strong>Примечание для агентов:</strong>' +
            '<span>' + agentNotes + '</span>' +
            '</p>' : '';

        var createdAt = data.created_at_formatted || '-';
        var updatedAt = data.updated_at_formatted || '-';
        var id = data.id;

        return '<div class="tbody-dop-info">' +
            '<div class="info-main">' +
            '<div class="info-main-left">' +
            '<div class="info-main-left-wrapper">' +
            '<div class="description">' +
            '<h2 class="description-title">' + (data.developer ? data.developer.name : '-') + '</h2>' +
            descriptionHtml +
            websiteHtml +
            agentNotesHtml +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="info-footer">' +
            '<p class="info-footer-data">ID: <span>' + id + '</span></p>' +
            '<p class="info-footer-data">Добавлено: <span>' + createdAt + '</span></p>' +
            '<p class="info-footer-data">Обновлено: <span>' + updatedAt + '</span></p>' +
            '<button class="info-footer-btn ms-auto close-btn-other" type="button">Свернуть</button>' +
            '</div>' +
            '</div>';
    }

    // Обработчик клика на кнопку разворачивания
    $('#developers-table tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var icon = $(this).find('img');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.attr('src', '/img/icon/plus.svg');
        } else {
            row.child(formatChildRow(row.data())).show();
            tr.addClass('shown');
            icon.attr('src', '/img/icon/minus.svg');

            var childTr = $(row.child());
            childTr.addClass('dop-info-row');
            childTr.find('td').css('border-bottom', 'none');
        }
    });

    // Обработчик кнопки "Свернуть"
    $('#developers-table tbody').on('click', '.close-btn-other', function () {
        var childTr = $(this).closest('tr');
        var parentTr = childTr.prev();
        parentTr.find('.details-control').click();
    });

    // Обработчик кнопки "Ещё" для длинного описания
    $('#developers-table tbody').on('click', '.btn-show-text', function () {
        var container = $(this).closest('.description-text');
        var shortText = container.find('.short-text');
        var fullText = container.find('.full-text');

        shortText.hide();
        fullText.show();
        $(this).hide();
    });

    // ========== Удаление девелопера ==========
    $('#developers-table tbody').on('click', '.delete-developer', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (confirm('Вы уверены, что хотите удалить этого девелопера?')) {
            $.ajax({
                url: '/developers/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    table.ajax.reload();
                },
                error: function (xhr) {
                    alert('Ошибка при удалении девелопера');
                }
            });
        }
    });

    // ========== Инициализация ==========

    // Инициализация тултипов
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
