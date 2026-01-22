/**
 * Логика фильтров для таблицы комплексов
 * Объект доступен через window.ComplexFilters
 */
window.ComplexFilters = {

    // Селектор формы фильтров
    formSelector: '#filter-form',

    // Текущая сортировка
    sortField: 'created_at',
    sortDir: 'desc',

    // Получение значений отмеченных чекбоксов
    getCheckedValues: function (selector) {
        var values = [];
        $(selector + ':checked').each(function () {
            values.push($(this).val());
        });
        return values.length > 0 ? values : null;
    },

    // Сбор всех параметров фильтров для AJAX запроса
    collectFilterData: function (d) {
        var $form = $(this.formSelector);

        // Сортировка
        d.sort_field = this.sortField;
        d.sort_dir = this.sortDir;

        // Основные фильтры из хедера
        d.category_id = $form.find('#category_id').val();
        d.price_from = $form.find('#price_from').val();
        d.price_to = $form.find('#price_to').val();
        d.currency_id = $form.find('#currency_id').val();

        // Расширенные фильтры
        d.developer_id = $form.find('#developer_id').val();
        d.housing_class_id = $form.find('#housing_class_id').val();

        // Диапазоны
        d.area_from = $form.find('[name="area_from"]').val();
        d.area_to = $form.find('[name="area_to"]').val();
        d.floors_from = $form.find('[name="floors_from"]').val();
        d.floors_to = $form.find('[name="floors_to"]').val();
        d.price_per_m2_from = $form.find('[name="price_per_m2_from"]').val();
        d.price_per_m2_to = $form.find('[name="price_per_m2_to"]').val();

        // Множественные чекбоксы
        d.object_type_id = this.getCheckedValues('[name="object_type_id[]"]');
        d.year_built = this.getCheckedValues('[name="year_built[]"]');
        d.condition_id = this.getCheckedValues('[name="condition_id[]"]');
        d.wall_type_id = this.getCheckedValues('[name="wall_type_id[]"]');
        d.heating_type_id = this.getCheckedValues('[name="heating_type_id[]"]');
        d.features = this.getCheckedValues('[name="features[]"]');

        // Поиск
        d.search_id = $form.find('[name="search_id"]').val();

        // Фильтр локации
        d.location_type = $('#lfType').val();
        d.location_id = $('#lfId').val();
        d.detail_ids = $('#lfDetails').val(); // JSON массив деталей

        return d;
    },

    // Установка сортировки
    setSort: function (field, dir) {
        this.sortField = field;
        this.sortDir = dir;
    },

    // Сброс всех фильтров
    reset: function () {
        var $form = $(this.formSelector);

        // Сбрасываем все поля формы
        $form[0].reset();

        // Сбрасываем select2 если используется
        $('.js-example-responsive2, .js-example-responsive3').val('').trigger('change');

        // Снимаем все чекбоксы
        $form.find('input[type="checkbox"]').prop('checked', false);

        // Сбрасываем скрытые поля локации
        $('#lfType').val('');
        $('#lfId').val('');
        $('#lfDetails').val('');

        // Сбрасываем сортировку на дефолтную
        this.sortField = 'created_at';
        this.sortDir = 'desc';

        // Скрываем счетчик фильтров
        $('.full-filter-counter').hide();
    },

    // Обновление счетчика активных фильтров
    updateCounter: function () {
        var count = 0;
        var $form = $(this.formSelector);

        // Считаем заполненные текстовые поля
        $form.find('input[type="text"]').each(function () {
            var name = $(this).attr('name');
            // Исключаем поля поиска в дропдаунах
            if ($(this).val() && name !== 'search-features') {
                count++;
            }
        });

        // Считаем выбранные select (кроме валюты по умолчанию)
        $form.find('select').each(function () {
            var val = $(this).val();
            var name = $(this).attr('name') || $(this).attr('id');
            // Пропускаем валюту если она первая по умолчанию
            if (val && name !== 'currency_id') {
                count++;
            }
        });

        // Считаем отмеченные чекбоксы фильтров
        $form.find('input[type="checkbox"]:checked').each(function () {
            var name = $(this).attr('name');
            if (name && name !== 'select-all-checkbox' && !$(this).hasClass('row-checkbox')) {
                count++;
            }
        });

        // Считаем фильтр локации
        if ($('#lfId').val() || $('#lfDetails').val()) {
            count++;
        }

        // Обновляем отображение
        if (count > 0) {
            $('.full-filter-counter span').text(count);
            $('.full-filter-counter').show();
        } else {
            $('.full-filter-counter').hide();
        }
    },

    // Селекторы текстовых полей для debounce
    textInputSelectors: [
        '#price_from',
        '#price_to',
        '[name="area_from"]',
        '[name="area_to"]',
        '[name="floors_from"]',
        '[name="floors_to"]',
        '[name="price_per_m2_from"]',
        '[name="price_per_m2_to"]',
        '[name="search_id"]'
    ].join(', '),

    // Селекторы чекбоксов для мгновенной реакции
    checkboxSelectors: [
        '[name="object_type_id[]"]',
        '[name="year_built[]"]',
        '[name="condition_id[]"]',
        '[name="wall_type_id[]"]',
        '[name="heating_type_id[]"]',
        '[name="features[]"]'
    ].join(', ')
};
