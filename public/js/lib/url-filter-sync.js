/**
 * URL Filter Sync — утилита для синхронизации фильтров с URL-параметрами.
 * Используется на страницах index (properties, complexes, developers, companies, employees).
 *
 * Зависит от jQuery.
 */
window.UrlFilterSync = {

    // Ключи локации, которые пропускаются при восстановлении формы
    _locationKeys: [
        'location_type', 'location_id', 'location_name',
        'lf_country_id', 'lf_country_name', 'lf_region_id', 'lf_region_name',
        'lf_city_id', 'lf_city_name',
        'city_ids', 'lf_city_names', 'detail_ids', 'lf_detail_names'
    ],

    /**
     * Записать текущее состояние фильтров в URL (history.replaceState).
     *
     * @param {Object} opts
     * @param {string}   [opts.formSelector]       — селектор формы ('#filter-form')
     * @param {string[]} [opts.skipTextNames]       — имена текстовых полей, которые нужно пропустить
     * @param {string}   [opts.sortField]           — текущее поле сортировки
     * @param {string}   [opts.sortDir]             — текущее направление сортировки
     * @param {string}   [opts.defaultSortField]    — дефолтное поле (не пишется в URL)
     * @param {string}   [opts.defaultSortDir]      — дефолтное направление
     * @param {Object}   [opts.extraParams]         — дополнительные пары key→value
     */
    syncToUrl: function (opts) {
        opts = opts || {};
        var params = new URLSearchParams();
        var $form = opts.formSelector ? $(opts.formSelector) : null;
        var skipText = opts.skipTextNames || [];

        if ($form && $form.length) {
            // Текстовые поля
            $form.find('input[type="text"]').each(function () {
                var name = $(this).attr('name');
                var val = $(this).val();
                if (name && val && skipText.indexOf(name) === -1) {
                    params.set(name, val);
                }
            });

            // Hidden поля
            $form.find('input[type="hidden"]').each(function () {
                var name = $(this).attr('name');
                if (name && $(this).val() && name !== '_token' && name !== '_method') {
                    params.set(name, $(this).val());
                }
            });

            // Select
            $form.find('select').each(function () {
                var name = $(this).attr('name') || $(this).attr('id');
                if (name && $(this).val()) {
                    params.set(name, $(this).val());
                }
            });

            // Чекбоксы — группируем по name, пишем через запятую
            var cbGroups = {};
            $form.find('input[type="checkbox"]:checked').each(function () {
                var name = $(this).attr('name');
                if (name && name !== 'select-all-checkbox' && !$(this).hasClass('row-checkbox')) {
                    var key = name.replace('[]', '');
                    if (!cbGroups[key]) cbGroups[key] = [];
                    cbGroups[key].push($(this).val());
                }
            });
            for (var key in cbGroups) {
                params.set(key, cbGroups[key].join(','));
            }
        }

        // Сортировка (пишем только если не дефолтная)
        var defSort = opts.defaultSortField || 'created_at';
        var defDir = opts.defaultSortDir || 'desc';
        if (opts.sortField && (opts.sortField !== defSort || opts.sortDir !== defDir)) {
            params.set('sort_field', opts.sortField);
            params.set('sort_dir', opts.sortDir);
        }

        // Доп. параметры
        if (opts.extraParams) {
            for (var k in opts.extraParams) {
                if (opts.extraParams[k]) params.set(k, opts.extraParams[k]);
            }
        }

        // Локация — из глобального LocationFilterState
        var lfState = window.LocationFilterState ? window.LocationFilterState.getState() : null;
        if (lfState) {
            if (lfState.location) {
                params.set('location_type', lfState.location.type);
                params.set('location_id', lfState.location.id);
                params.set('location_name', lfState.location.name);
            }
            if (lfState.path && lfState.path.country) {
                params.set('lf_country_id', lfState.path.country.id);
                params.set('lf_country_name', lfState.path.country.name);
            }
            if (lfState.path && lfState.path.region) {
                params.set('lf_region_id', lfState.path.region.id);
                params.set('lf_region_name', lfState.path.region.name);
            }
            if (lfState.path && lfState.path.city) {
                params.set('lf_city_id', lfState.path.city.id);
                params.set('lf_city_name', lfState.path.city.name);
            }
            if (lfState.cities && lfState.cities.length > 0) {
                params.set('city_ids', JSON.stringify(lfState.cities.map(function (c) { return c.id; })));
                params.set('lf_city_names', JSON.stringify(lfState.cities.map(function (c) { return c.name; })));
            }
            if (lfState.details && lfState.details.length > 0) {
                params.set('detail_ids', JSON.stringify(lfState.details.map(function (d) { return { type: d.type, id: d.id }; })));
                params.set('lf_detail_names', JSON.stringify(lfState.details.map(function (d) { return d.name; })));
            }
        }

        var qs = params.toString();
        history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
    },

    /**
     * Восстановить фильтры из URL-параметров в форму.
     *
     * @param {Object} opts
     * @param {string}   [opts.formSelector]  — селектор формы
     * @param {function} [opts.onSort]        — callback(field, dir) для восстановления сортировки
     * @returns {boolean} — были ли параметры в URL
     */
    restoreFromUrl: function (opts) {
        opts = opts || {};
        var params = new URLSearchParams(window.location.search);
        if (params.toString() === '') return false;

        var $form = opts.formSelector ? $(opts.formSelector) : null;
        var self = this;
        var skipKeys = ['sort_field', 'sort_dir'].concat(self._locationKeys);

        if ($form && $form.length) {
            params.forEach(function (value, key) {
                if (skipKeys.indexOf(key) !== -1) return;

                // Чекбокс-группа
                var $cb = $form.find('input[type="checkbox"][name="' + key + '[]"]');
                if ($cb.length > 0) {
                    var vals = value.split(',');
                    $cb.each(function () {
                        $(this).prop('checked', vals.indexOf($(this).val()) !== -1);
                    });
                    return;
                }

                // Select
                var $sel = $form.find('select[name="' + key + '"], select#' + key);
                if ($sel.length > 0) {
                    $sel.val(value);
                    if ($sel.hasClass('js-example-responsive2') || $sel.hasClass('js-example-responsive3')) {
                        $sel.trigger('change.select2');
                    }
                    return;
                }

                // Input (text / hidden)
                var $inp = $form.find('input[name="' + key + '"]');
                if ($inp.length > 0) {
                    $inp.val(value);
                }
            });

            // Обновляем текст кнопок multiple-menu
            $form.find('.multiple-menu').each(function () {
                var $menu = $(this);
                var $btn = $menu.find('.multiple-menu-btn');
                var texts = [];
                $menu.find('.multiple-menu-list input[type="checkbox"]:checked').each(function () {
                    var t = $(this).closest('label').find('.my-custom-text').text().trim();
                    if (t) texts.push(t);
                });
                if (texts.length > 0) {
                    $btn.text(texts.join(', '));
                    $btn.attr('title', texts.join(', '));
                }
            });
        }

        // Сортировка
        if (opts.onSort && params.get('sort_field')) {
            opts.onSort(params.get('sort_field'), params.get('sort_dir') || 'desc');
        }

        return true;
    },

    /**
     * Очистить URL от параметров фильтров.
     */
    clearUrl: function () {
        history.replaceState(null, '', window.location.pathname);
    }
};
