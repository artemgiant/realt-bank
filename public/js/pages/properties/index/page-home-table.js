/**
 * Главный файл инициализации DataTables для таблицы объектов
 * Использует модули: PropertyRenderers, PropertyTableConfig, PropertyFilters, PropertyTags
 */
$(document).ready(function() {

    // Ссылки на модули
    var Config = window.PropertyTableConfig;
    var Filters = window.PropertyFilters;
    var Tags = window.PropertyTags;

    // ========== Debounce функция ==========
    // Задержка перед выполнением запроса после окончания ввода
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            var later = function() {
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
        data: function(d) {
            return Filters.collectFilterData(d);
        },
        error: function(xhr, error, thrown) {
            console.error('DataTables AJAX error:', error, thrown);
        }
    };

    // Callback после отрисовки
    settings.drawCallback = function(settings) {
        // Обновляем информацию о количестве
        var info = table.page.info();
        $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');

        // Сбрасываем чекбокс "выбрать все"
        $('#select-all-checkbox').prop('checked', false);

        // Обновляем счетчик фильтров после каждой перезагрузки
        Filters.updateCounter();
    };

    var table = $('#example').DataTable(settings);

    // ========== Функции перезагрузки ==========

    // Функция перезагрузки таблицы с debounce для текстовых полей
    var debouncedReload = debounce(function() {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    // Debounce для обновления тегов
    var debouncedUpdateTags = debounce(function() {
        Tags.update();
    }, DEBOUNCE_DELAY);

    // Функция мгновенной перезагрузки (для select и checkbox)
    function reloadTable() {
        table.ajax.reload();
    }

    // ========== Обработчики событий ==========

    // Обработчик клика на кнопку удаления тега
    $(document).on('click', '.filter-tags .badge button', function() {
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
    $('#select-all-checkbox').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#example tbody').on('change', '.row-checkbox', function() {
        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $('#select-all-checkbox').prop('checked', allChecked);
    });

    // Применение фильтров при отправке формы
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // ========== Автоматическая фильтрация ==========

    // Обработчик ввода для текстовых полей (с задержкой)
    $('#filter-form').on('input', Filters.textInputSelectors, function() {
        debouncedReload();
        debouncedUpdateTags();
    });

    // Select поля - мгновенная реакция
    $('#filter-form').on('change', '#deal_type_id, #currency_id, #status, #full-filter-currency', function() {
        reloadTable();
        Tags.update();
    });

    // Чекбоксы фильтров - мгновенная реакция
    $('#filter-form').on('change', Filters.checkboxSelectors, function() {
        reloadTable();
        Tags.update();
    });

    // Daterangepicker - фильтрация после выбора дат
    $('#datapiker1').on('apply.daterangepicker', function(ev, picker) {
        $('#created_from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#created_to').val(picker.endDate.format('YYYY-MM-DD'));
        reloadTable();
        Tags.update();
    });

    $('#datapiker1').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#created_from').val('');
        $('#created_to').val('');
        reloadTable();
        Tags.update();
    });

    // ========== Кнопки поиска (для совместимости) ==========

    // Кнопка поиска по ID
    $('#search-id-btn').on('click', function() {
        reloadTable();
        Tags.update();
    });

    // Кнопка поиска по контакту
    $('#search-contact-btn').on('click', function() {
        reloadTable();
        Tags.update();
    });

    // Enter в поле поиска по ID
    $('#search_id').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            Tags.update();
        }
    });

    // Enter в поле поиска по контакту
    $('#contact_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            Tags.update();
        }
    });

    // ========== Сброс фильтров ==========

    // Кнопка сброса в счетчике фильтров
    $('#delete-params-on-filter').on('click', function(e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        table.ajax.reload();
    });

    // Кнопка "Сбросить" в расширенном фильтре
    $('#reset-filters-btn').on('click', function(e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        table.ajax.reload();
    });

    // ========== Инициализация ==========

    // Инициализация тегов при загрузке страницы (если есть выбранные фильтры)
    Tags.update();

    // Инициализация тултипов
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
