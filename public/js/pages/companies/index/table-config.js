/**
 * Конфигурация DataTables для таблицы компаний
 * Объект доступен через window.CompanyTableConfig
 */
window.CompanyTableConfig = {

    // URL для AJAX запросов
    ajaxUrl: '/companies/ajax-data',

    // Определение колонок
    getColumns: function () {
        var R = window.CompanyRenderers;

        return [
            {
                data: 'checkbox',
                orderable: false,
                render: R.checkbox
            },
            {
                data: 'logo_url',
                orderable: false,
                render: R.logo
            },
            {
                data: 'company',
                render: R.company
            },
            {
                data: 'director',
                render: R.director
            },
            {
                data: 'offices_count',
                orderable: false,
                render: R.officesCount
            },
            {
                data: 'team_count',
                orderable: false,
                render: R.teamCount
            },
            {
                data: 'properties_count',
                orderable: false,
                render: R.propertiesCount
            },
            {
                data: 'deals_count',
                orderable: false,
                render: R.dealsCount
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
        zeroRecords: '<div class="text-center py-4"><div class="text-muted"><p class="mb-2">Компании не найдены</p></div></div>',
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
    getBaseSettings: function () {
        return {
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            paging: true,
            pageLength: 10,
            pagingType: 'simple_numbers',
            info: true,
            order: [[2, 'desc']],
            columns: this.getColumns(),
            language: this.language
        };
    }
};
