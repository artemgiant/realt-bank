/**
 * Логика фильтров для таблицы объектов
 * Объект доступен через window.PropertyFilters
 */
window.PropertyFilters = {

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
        d.deal_type_id = $form.find('#deal_type_id').val();
        d.price_from = $form.find('#price_from').val();
        d.price_to = $form.find('#price_to').val();
        d.currency_id = $form.find('#currency_id').val();

        // Расширенные фильтры
        d.status = $form.find('#status').val();
        d.area_from = $form.find('[name="area_from"]').val();
        d.area_to = $form.find('[name="area_to"]').val();
        d.area_living_from = $form.find('[name="area_living_from"]').val();
        d.area_living_to = $form.find('[name="area_living_to"]').val();
        d.area_kitchen_from = $form.find('[name="area_kitchen_from"]').val();
        d.area_kitchen_to = $form.find('[name="area_kitchen_to"]').val();
        d.area_land_from = $form.find('[name="area_land_from"]').val();
        d.area_land_to = $form.find('[name="area_land_to"]').val();
        d.floor_from = $form.find('[name="floor_from"]').val();
        d.floor_to = $form.find('[name="floor_to"]').val();
        d.floors_total_from = $form.find('[name="floors_total_from"]').val();
        d.floors_total_to = $form.find('[name="floors_total_to"]').val();
        d.price_per_m2_from = $form.find('[name="price_per_m2_from"]').val();
        d.price_per_m2_to = $form.find('[name="price_per_m2_to"]').val();

        // Множественные чекбоксы
        d.property_type_id = this.getCheckedValues('[name="property_type_id[]"]');
        d.condition_id = this.getCheckedValues('[name="condition_id[]"]');
        d.building_type_id = this.getCheckedValues('[name="building_type_id[]"]');
        d.year_built = this.getCheckedValues('[name="year_built[]"]');
        d.wall_type_id = this.getCheckedValues('[name="wall_type_id[]"]');
        d.room_count_id = this.getCheckedValues('[name="room_count_id[]"]');
        d.heating_type_id = this.getCheckedValues('[name="heating_type_id[]"]');
        d.bathroom_count_id = this.getCheckedValues('[name="bathroom_count_id[]"]');
        d.ceiling_height_id = this.getCheckedValues('[name="ceiling_height_id[]"]');
        d.features = this.getCheckedValues('[name="features[]"]');
        d.developer_id = this.getCheckedValues('[name="developer_id[]"]');

        // Поиск
        d.search_id = $form.find('[name="search_id"]').val();
        d.contact_search = $form.find('[name="contact_search"]').val();

        // Даты
        d.created_from = $form.find('[name="created_from"]').val();
        d.created_to = $form.find('[name="created_to"]').val();

        // Фильтр локации (новый)
        d.location_type = $('#lfType').val();
        d.location_id = $('#lfId').val();
        d.city_ids = $('#lfCities').val();    // JSON массив выбранных городов
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

        // Очищаем скрытые поля дат
        $('#created_from').val('');
        $('#created_to').val('');

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
            var id = $(this).attr('id');
            // Исключаем поля поиска в дропдаунах и видимый инпут даты
            if ($(this).val() && name !== 'search-additionally' && name !== 'search-developer' && id !== 'datapiker1') {
                count++;
            }
        });

        // Считаем фильтр по дате (как один фильтр)
        if ($('#created_from').val() || $('#created_to').val()) {
            count++;
        }

        // Считаем выбранные select (кроме валюты по умолчанию)
        $form.find('select').each(function () {
            var val = $(this).val();
            var name = $(this).attr('name') || $(this).attr('id');
            // Пропускаем валюту если она первая по умолчанию
            if (val && name !== 'currency_id' && name !== 'full-filter-currency') {
                count++;
            }
        });

        // Считаем отмеченные чекбоксы фильтров (не select-all)
        $form.find('input[type="checkbox"]:checked').each(function () {
            var name = $(this).attr('name');
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
    },

    // Селекторы текстовых полей для debounce
    textInputSelectors: [
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
    ].join(', '),

    // Селекторы чекбоксов для мгновенной реакции
    checkboxSelectors: [
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
    ].join(', ')
};
