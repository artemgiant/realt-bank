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
        d.price_from = ($form.find('#price_from').val() || '').replace(/\s/g, '');
        d.price_to = ($form.find('#price_to').val() || '').replace(/\s/g, '');
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

        // Сбрасываем фильтр локации
        if (window.LocationFilterState && window.LocationFilterState.reset) {
            window.LocationFilterState.reset();
        }

        // Скрываем счетчик фильтров
        $('.full-filter-counter').hide();

        // Очищаем URL от параметров фильтров
        history.replaceState(null, '', window.location.pathname);
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
    ].join(', '),

    // Синхронизация фильтров в URL (history.replaceState)
    syncToUrl: function () {
        var params = new URLSearchParams();
        var $form = $(this.formSelector);

        // Текстовые поля (без пробелов в ценах)
        $form.find('input[type="text"]').each(function () {
            var name = $(this).attr('name');
            var val = $(this).val();
            if (name && val && name !== 'search-additionally' && name !== 'search-developer') {
                params.set(name, name === 'price_from' || name === 'price_to' ? val.replace(/\s/g, '') : val);
            }
        });

        // Hidden поля (даты)
        $form.find('input[type="hidden"]').each(function () {
            var name = $(this).attr('name');
            if (name && $(this).val() && name !== '_token' && name !== '_method') {
                params.set(name, $(this).val());
            }
        });

        // Select поля
        $form.find('select').each(function () {
            var name = $(this).attr('name') || $(this).attr('id');
            if (name && $(this).val()) {
                params.set(name, $(this).val());
            }
        });

        // Чекбоксы — массивы через запятую
        var checkboxGroups = {};
        $form.find('input[type="checkbox"]:checked').each(function () {
            var name = $(this).attr('name');
            if (name && name !== 'select-all-checkbox' && !$(this).hasClass('row-checkbox')) {
                var key = name.replace('[]', '');
                if (!checkboxGroups[key]) checkboxGroups[key] = [];
                checkboxGroups[key].push($(this).val());
            }
        });
        for (var key in checkboxGroups) {
            params.set(key, checkboxGroups[key].join(','));
        }

        // Сортировка (только если не дефолтная)
        if (this.sortField !== 'created_at' || this.sortDir !== 'desc') {
            params.set('sort_field', this.sortField);
            params.set('sort_dir', this.sortDir);
        }

        // Локация — сохраняем полное состояние (с именами для восстановления тегов)
        var lfState = window.LocationFilterState ? window.LocationFilterState.getState() : null;
        if (lfState) {
            if (lfState.location) {
                params.set('location_type', lfState.location.type);
                params.set('location_id', lfState.location.id);
                params.set('location_name', lfState.location.name);
            }
            if (lfState.path.country) {
                params.set('lf_country_id', lfState.path.country.id);
                params.set('lf_country_name', lfState.path.country.name);
            }
            if (lfState.path.region) {
                params.set('lf_region_id', lfState.path.region.id);
                params.set('lf_region_name', lfState.path.region.name);
            }
            if (lfState.cities.length > 0) {
                params.set('city_ids', JSON.stringify(lfState.cities.map(function (c) { return c.id; })));
                params.set('lf_city_names', JSON.stringify(lfState.cities.map(function (c) { return c.name; })));
            }
            if (lfState.details.length > 0) {
                params.set('detail_ids', JSON.stringify(lfState.details.map(function (d) { return { type: d.type, id: d.id }; })));
                params.set('lf_detail_names', JSON.stringify(lfState.details.map(function (d) { return d.name; })));
            }
        } else {
            // Fallback на hidden поля
            if ($('#lfType').val()) params.set('location_type', $('#lfType').val());
            if ($('#lfId').val()) params.set('location_id', $('#lfId').val());
            if ($('#lfCities').val()) params.set('city_ids', $('#lfCities').val());
            if ($('#lfDetails').val()) params.set('detail_ids', $('#lfDetails').val());
        }

        var qs = params.toString();
        var newUrl = window.location.pathname + (qs ? '?' + qs : '');
        history.replaceState(null, '', newUrl);
    },

    // Восстановление фильтров из URL-параметров
    restoreFromUrl: function () {
        var params = new URLSearchParams(window.location.search);
        if (params.toString() === '') return false;

        var $form = $(this.formSelector);
        var self = this;

        // Простые текстовые / hidden поля
        params.forEach(function (value, key) {
            // Пропускаем служебные и локацию (восстанавливается location-filter.js)
            var skipKeys = ['sort_field', 'sort_dir', 'location_type', 'location_id', 'location_name',
                'lf_country_id', 'lf_country_name', 'lf_region_id', 'lf_region_name',
                'city_ids', 'lf_city_names', 'detail_ids', 'lf_detail_names'];
            if (skipKeys.indexOf(key) !== -1) return;

            // Чекбокс-группы (без [])
            var $checkbox = $form.find('input[type="checkbox"][name="' + key + '[]"]');
            if ($checkbox.length > 0) {
                var values = value.split(',');
                $checkbox.each(function () {
                    $(this).prop('checked', values.indexOf($(this).val()) !== -1);
                });
                return;
            }

            // Select
            var $select = $form.find('select[name="' + key + '"], select#' + key);
            if ($select.length > 0) {
                $select.val(value);
                if ($select.hasClass('js-example-responsive2') || $select.hasClass('js-example-responsive3')) {
                    $select.trigger('change.select2');
                }
                return;
            }

            // Текстовые и hidden поля
            var $input = $form.find('input[name="' + key + '"]');
            if ($input.length > 0) {
                $input.val(value);
            }
        });

        // Сортировка
        if (params.get('sort_field')) self.sortField = params.get('sort_field');
        if (params.get('sort_dir')) self.sortDir = params.get('sort_dir');

        // Локация восстанавливается самим location-filter.js из URL

        // Обновляем текст кнопок multiple-menu
        $form.find('.multiple-menu').each(function () {
            var $menu = $(this);
            var $btn = $menu.find('.multiple-menu-btn');
            var checkedTexts = [];
            $menu.find('.multiple-menu-list input[type="checkbox"]:checked').each(function () {
                var text = $(this).closest('label').find('.my-custom-text').text().trim();
                if (text) checkedTexts.push(text);
            });
            $btn.text(checkedTexts.join(', '));
            $btn.attr('title', checkedTexts.join(', '));
        });

        // Восстанавливаем отображение datepicker
        var createdFrom = params.get('created_from');
        var createdTo = params.get('created_to');
        if (createdFrom && createdTo && typeof moment !== 'undefined') {
            var from = moment(createdFrom, 'YYYY-MM-DD');
            var to = moment(createdTo, 'YYYY-MM-DD');
            if (from.isSame(to, 'day')) {
                $('#datapiker1').val(from.format('DD.MM.YYYY'));
            } else {
                $('#datapiker1').val(from.format('DD.MM.YYYY') + ' - ' + to.format('DD.MM.YYYY'));
            }
        }

        return true;
    },

    // Инициализация DateRangePicker
    initDatePicker: function () {
        if (typeof moment !== 'undefined' && $.fn.daterangepicker) {
            $('#datapiker1').daterangepicker({
                autoUpdateInput: false,
                alwaysShowCalendars: true,
                ranges: {
                    'Сегодня': [moment(), moment()],
                    'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Этот месяц': [moment().startOf('month'), moment().endOf('month')]
                },
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

            $('#datapiker1').on('apply.daterangepicker', function (ev, picker) {
                if (picker.startDate.isSame(picker.endDate, 'day')) {
                    $(this).val(picker.startDate.format('DD.MM.YYYY'));
                } else {
                    $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
                }
            });

            $('#datapiker1').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        }
    }
};
