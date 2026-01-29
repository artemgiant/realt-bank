/**
 * Конфигурация DataTables для таблицы объектов
 * Объект доступен через window.PropertyTableConfig
 */
window.PropertyTableConfig = {

    // URL для AJAX запросов
    ajaxUrl: '/properties/ajax-data',

    // Определение колонок
    getColumns: function() {
        var R = window.PropertyRenderers;

        return [
            {
                data: 'checkbox',
                orderable: false,
                render: R.checkbox
            },
            {
                data: 'warnings',
                orderable: false,
                render: R.warnings
            },
            {
                data: 'location',
                render: R.location
            },
            {
                data: 'property_type',
                render: R.propertyType
            },
            {
                data: 'area',
                render: R.area
            },
            {
                data: 'condition',
                render: R.condition
            },
            {
                data: 'floor',
                render: R.floor
            },
            {
                data: 'photo',
                orderable: false,
                render: R.photo
            },
            {
                data: 'price',
                render: R.price
            },
            {
                data: 'contact',
                render: R.contact
            },
            {
                data: 'actions',
                orderable: false,
                render: R.actions
            }
        ];
    },

    // Языковые настройки
    language: {
        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Загрузка...</span></div>',
        lengthMenu: 'Показать _MENU_ записей',
        zeroRecords: '<div class="text-center py-4"><div class="text-muted"><p class="mb-2">Объекты не найдены</p></div></div>',
        info: 'Показано _START_ - _END_ из _TOTAL_ записей',
        infoEmpty: 'Записи отсутствуют',
        infoFiltered: '(отфильтровано из _MAX_ записей)',
        search: 'Поиск:',
        paginate: {
            first: 'Первая',
            last: 'Последняя',
            next: 'Следующая',
            previous: 'Предыдущая'
        }
    },

    // Базовые настройки DataTables
    getBaseSettings: function() {
        return {
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            paging: true,
            pageLength: 10,
            pagingType: 'simple_numbers',
            info: true,
            order: [[7, 'desc']], // Сортировка по цене по умолчанию
            columns: this.getColumns(),
            language: this.language
        };
    }
};
