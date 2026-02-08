/**
 * Главный файл инициализации DataTables для таблицы объектов
 * Использует модули: PropertyRenderers, PropertyTableConfig, PropertyFilters, PropertyTags
 */
$(document).ready(function () {

    // Ссылки на модули
    var Config = window.PropertyTableConfig;
    var Filters = window.PropertyFilters;
    var Tags = window.PropertyTags;

    // ========== Debounce функция ==========
    // Задержка перед выполнением запроса после окончания ввода
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
        $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');

        // Сбрасываем чекбокс "выбрать все"
        $('#select-all-checkbox').prop('checked', false);

        // Обновляем счетчик фильтров после каждой перезагрузки
        Filters.updateCounter();

        // Инициализация FancyBox для фото в таблице
        if (typeof Fancybox !== 'undefined') {
            Fancybox.unbind('#example [data-fancybox]');
            Fancybox.bind('#example [data-fancybox]', {
                Thumbs: false,
                Toolbar: true,
                Images: {
                    zoom: true,
                },
            });
        }
    };



    var table = $('#example').DataTable(settings);

    // ========== Функции перезагрузки ==========

    // Функция перезагрузки таблицы с debounce для текстовых полей
    var debouncedReload = debounce(function () {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    // Debounce для обновления тегов
    var debouncedUpdateTags = debounce(function () {
        Tags.update();
    }, DEBOUNCE_DELAY);

    // Функция мгновенной перезагрузки (для select и checkbox)
    function reloadTable() {
        table.ajax.reload();
    }

    // ========== Обработчики событий ==========

    // Обработчик клика на кнопку удаления тега
    $(document).on('click', '.filter-tags .badge button', function () {
        var $tag = $(this).closest('.badge');
        var filterType = $tag.data('filter-type');
        var filterValue = $tag.data('filter-value');

        // Удаляем тег и очищаем фильтр
        Tags.remove(filterType, filterValue);

        // Обновляем теги
        Tags.update();

        // Перезагружаем таблицу
        reloadTable();
    });

    // Выбрать все / снять все
    $('#select-all-checkbox').on('change', function () {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#example tbody').on('change', '.row-checkbox', function () {
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
        debouncedUpdateTags();
    });

    // Select поля - мгновенная реакция
    $('#filter-form').on('change', '#deal_type_id, #currency_id, #status, #full-filter-currency', function () {
        reloadTable();
        Tags.update();
    });

    // Чекбоксы фильтров - мгновенная реакция
    $('#filter-form').on('change', Filters.checkboxSelectors, function () {
        reloadTable();
        Tags.update();
    });

    // Daterangepicker - фильтрация после выбора дат
    $('#datapiker1').on('apply.daterangepicker', function (ev, picker) {
        $('#created_from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#created_to').val(picker.endDate.format('YYYY-MM-DD'));
        reloadTable();
        Tags.update();
    });

    $('#datapiker1').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $('#created_from').val('');
        $('#created_to').val('');
        reloadTable();
        Tags.update();
    });

    // ========== Кнопки поиска (для совместимости) ==========

    // Кнопка поиска по ID
    $('#search-id-btn').on('click', function () {
        reloadTable();
        Tags.update();
    });

    // Кнопка поиска по контакту
    $('#search-contact-btn').on('click', function () {
        reloadTable();
        Tags.update();
    });

    // Enter в поле поиска по ID
    $('#search_id').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            Tags.update();
        }
    });

    // Enter в поле поиска по контакту
    $('#contact_search').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            Tags.update();
        }
    });

    // ========== Сброс фильтров ==========

    // Кнопка сброса в счетчике фильтров
    $('#delete-params-on-filter').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        // Убираем active класс со всех пунктов сортировки
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    // Кнопка "Сбросить" в расширенном фильтре
    $('#reset-filters-btn').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        // Убираем active класс со всех пунктов сортировки
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    // ========== Обработчик изменений фильтра локации ==========

    // Создаем глобальную функцию для обновления таблицы из фильтра локации
    window.reloadPropertiesTable = function () {
        reloadTable();
    };

    // ========== Детальная информация (child row) ==========

    function nl2br(str) {
        if (!str) return '';
        return str.replace(/\n/g, '<br>');
    }

    function escapeHtml(str) {
        if (str == null) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function buildBlockInfo(data) {
        var isVisibleToAgents = !!data.is_visible_to_agents;
        var contact = data.contact_for_display || null;
        var agent = data.agent || null;
        var defaultAvatar = './img/icon/default-avatar-table.svg';

        var showContactCard = isVisibleToAgents && contact;
        var showAgentCard = !isVisibleToAgents && agent;
        if (!showContactCard && !showAgentCard) {
            return '<ul class="block-info"></ul>';
        }

        var btnText = showContactCard ? 'Показать контакты' : 'Связаться с агентом';
        var btnClass = 'btn btn-outline-primary btn-block-info-toggle';
        var detailHtml = '';
        if (showContactCard) {
            detailHtml = '<li class="block-info-item block-info-detail block-info-detail-hidden">' +
                '<div class="info-title-wrapper"><h2 class="info-title">Клиент</h2></div>' +
                '<div class="info-avatar">' +
                '<img src="' + escapeHtml(defaultAvatar) + '" alt="">' +
                '</div>' +
                '<div class="info-contacts">' +
                '<p class="info-contacts-name">' + escapeHtml(contact.full_name) + '</p>' +
                '<p class="info-description">' + escapeHtml(contact.contact_type_name) + '</p>' +
                '<a href="tel:' + escapeHtml((contact.phone || '').replace(/\s/g, '')) + '" class="info-contacts-tel">' + escapeHtml(contact.phone || '-') + '</a>' +
                '</div>' +
                '</li>';
        } else {
            var agentPhoto = (agent.photo_url && agent.photo_url.length) ? escapeHtml(agent.photo_url) : defaultAvatar;
            detailHtml = '<li class="block-info-item block-info-detail block-info-detail-hidden">' +
                '<div class="info-title-wrapper"><h2 class="info-title">Агент</h2></div>' +
                '<div class="info-avatar">' +
                '<img src="' + escapeHtml(agentPhoto) + '" alt="">' +
                '</div>' +
                '<div class="info-contacts">' +
                '<p class="info-contacts-name">' + escapeHtml(agent.full_name) + '</p>' +
                '<p class="info-description">' + escapeHtml(agent.company_name || '') + '</p>' +
                '<a href="tel:' + escapeHtml((agent.phone || '').replace(/\s/g, '')) + '" class="info-contacts-tel">' + escapeHtml(agent.phone || '-') + '</a>' +
                '</div>' +
                '</li>';
        }

        return '<ul class="block-info">' +
            '<li class="block-info-item">' +
            '<div class="info-btn-wrapper">' +
            '<button class="' + btnClass + '" type="button">' + escapeHtml(btnText) + '</button>' +
            '</div>' +
            '</li>' +
            detailHtml +
            '</ul>';
    }

    function formatChildRow(data) {
        // Формируем теги особенностей
        var featuresHtml = '';
        if (data.features && data.features.length > 0) {
            data.features.forEach(function (feature) {
                featuresHtml += '<div class="badge rounded-pill">' + escapeHtml(feature) + '</div>';
            });
        }

        // Заголовок и описание
        var title = data.title || 'Без заголовка';
        var description = data.description || '';

        // Обрезаем описание если длинное (примерно, так как стили могут быть разные)
        // Но в верстке есть кнопка "Ещё" и скрытый текст, поэтому реализуем логику скрытия
        var shortDesc = description;
        var fullDesc = '';
        var hasMore = false;

        if (description.length > 200) {
            shortDesc = description.substring(0, 200) + '...';
            fullDesc = description;
            hasMore = true;
        }

        var descriptionHtml = '<div class="description-text">' +
            '<span class="short-text">' + nl2br(escapeHtml(shortDesc)) + '</span>' +
            (hasMore ?
                '<span class="full-text" style="display: none;">' + nl2br(escapeHtml(fullDesc)) + '</span>' +
                '<button class="btn btn-show-text" type="button">Ещё</button>'
                : '') +
            '</div>';

        // Заметки
        var agentNotesHtml = data.agent_notes ?
            '<p class="description-note">' +
            '<strong>Примечание для агентов: </strong>' +
            '<span>' + nl2br(escapeHtml(data.agent_notes)) + '</span>' +
            '</p>' : '';

        // Форматируем даты
        var createdAt = data.created_at_formatted || '-';
        var updatedAt = data.updated_at_formatted || '-';
        var id = data.id;

        return '<div class="tbody-dop-info">' +
            '<div class="info-main">' +
            '<div class="info-main-left">' +
            '<div class="info-main-left-wrapper">' +
            '<div class="description">' +
            '<h2 class="description-title">' + escapeHtml(title) + '</h2>' +
            descriptionHtml +
            agentNotesHtml +
            (data.personal_notes ?
            '<p class="description-note">' +
            '<strong>Заметка: </strong>' +
            '<span>' + nl2br(escapeHtml(data.personal_notes)) + '</span>' +
            '</p>' : '') +
            '</div>' +

            buildBlockInfo(data) +

            '</div>' +
            '<div class="filter-tags">' +
            featuresHtml +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="info-footer">' +
            '<p class="info-footer-data">ID: <span>' + id + '</span></p>' +
            '<p class="info-footer-data">Добавлено: <span>' + createdAt + '</span></p>' +
            '<p class="info-footer-data">Обновлено: <span>' + updatedAt + '</span>' +
            '<button class="btn" type="button">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#5FB343" class="bi bi-arrow-repeat" viewBox="0 0 16 16">' +
            '<path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"></path>' +
            '<path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"></path>' +
            '</svg>' +
            '</button>' +
            '</p>' +
            '<p class="info-footer-data">Сделки: <button class="info-footer-btn" type="button">0</button></p>' +
            '<p class="info-footer-data">Дубликаты: <button class="info-footer-btn btn-others" type="button">0</button></p>' +
            '<button class="info-footer-btn ms-auto close-btn-other" type="button">Свернуть</button>' +
            '</div>' +
            '</div>';
    }

    // Обработчик клика на кнопку разворачивания
    $('#example tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var icon = $(this).find('img');

        if (row.child.isShown()) {
            // Закрываем строку
            row.child.hide();
            tr.removeClass('shown');
            icon.attr('src', './img/icon/plus.svg');
        } else {
            // Открываем строку
            row.child(formatChildRow(row.data())).show();
            tr.addClass('shown');
            icon.attr('src', './img/icon/minus.svg');

            // Добавляем класс к созданной строке (tr) и стили для td
            var childTr = $(row.child());
            childTr.addClass('dop-info-row');
            childTr.find('td').css('border-bottom', 'none');
        }
    });

    // Обработчик кнопки "Свернуть"
    $('#example tbody').on('click', '.close-btn-other', function () {
        // Находим родительскую кнопку Details и кликаем
        var childTr = $(this).closest('tr');
        var parentTr = childTr.prev();
        parentTr.find('.details-control').click();
    });

    // Обработчик кнопки "Ещё" для длинного описания
    $('#example tbody').on('click', '.btn-show-text', function () {
        var container = $(this).closest('.description-text');
        var shortText = container.find('.short-text');
        var fullText = container.find('.full-text');

        shortText.hide();
        fullText.show();
        $(this).hide();
    });

    // Обработчик кнопки "Показать контакты" / "Связаться с агентом" в block-info
    $('#example tbody').on('click', '.btn-block-info-toggle', function () {
        var $blockInfo = $(this).closest('.block-info');
        $blockInfo.find('.block-info-detail').toggleClass('block-info-detail-hidden');
    });

    // ========== Инициализация ==========

    // Инициализация тегов при загрузке страницы (если есть выбранные фильтры)
    Tags.update();

    // Инициализация тултипов
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
