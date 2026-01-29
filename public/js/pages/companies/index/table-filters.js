/**
 * Логика фильтров для таблицы компаний
 * Объект доступен через window.CompanyFilters
 */
window.CompanyFilters = {

    // Селектор формы фильтров
    formSelector: '#filter-form',

    // Текущая сортировка
    sortField: 'created_at',
    sortDir: 'desc',

    // Сбор всех параметров фильтров для AJAX запроса
    collectFilterData: function (d) {
        var $form = $(this.formSelector);

        // Сортировка
        d.sort_field = this.sortField;
        d.sort_dir = this.sortDir;

        // Поиск по названию
        d.search_name = $form.find('[name="search_name"]').val();

        // Фильтр по типу компании
        d.company_type = $form.find('[name="company_type"]').val();

        // Фильтр по статусу (is_active)
        d.status = $form.find('[name="status"]').val();

        // Фильтр локации
        d.location_type = $('#lfType').val();
        d.location_id = $('#lfId').val();
        d.city_ids = $('#lfCities').val(); // JSON массив выбранных городов

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

        // Сбрасываем сортировку на дефолтную
        this.sortField = 'created_at';
        this.sortDir = 'desc';

        // Сбрасываем select на первое значение
        $form.find('select').each(function() {
            $(this).prop('selectedIndex', 0);
        });

        // Скрываем счетчик фильтров
        $('.full-filter-counter').hide();
    },

    // Обновление счетчика активных фильтров
    updateCounter: function () {
        var count = 0;
        var $form = $(this.formSelector);

        // Считаем заполненные текстовые поля
        $form.find('input[type="text"]').each(function () {
            if ($(this).val()) {
                count++;
            }
        });

        // Считаем select с выбранными (не пустыми) значениями
        $form.find('select').each(function () {
            if ($(this).val()) {
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
        '[name="search_name"]'
    ].join(', ')
};
