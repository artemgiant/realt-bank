/**
 * Конфигурация DataTables для таблицы сотрудников
 */
window.EmployeeTableConfig = {
    // AJAX URL для загрузки данных (будет добавлен позже)
    ajaxUrl: '/employees/ajax-data',

    // Определение колонок таблицы
    columns: [
        {
            data: 'checkbox',
            name: 'checkbox',
            orderable: false,
            searchable: false,
            width: '3%'
        },
        {
            data: 'photo',
            name: 'photo',
            orderable: false,
            searchable: false,
            width: '6%'
        },
        {
            data: 'agent',
            name: 'agent',
            orderable: true,
            width: '15%'
        },
        {
            data: 'position',
            name: 'position',
            orderable: true,
            width: '14%'
        },
        {
            data: 'office',
            name: 'office',
            orderable: true,
            width: '14%'
        },
        {
            data: 'objects_count',
            name: 'objects_count',
            orderable: true,
            width: '6%'
        },
        {
            data: 'clients_count',
            name: 'clients_count',
            orderable: true,
            width: '6%'
        },
        {
            data: 'success_deals',
            name: 'success_deals',
            orderable: true,
            width: '6%'
        },
        {
            data: 'failed_deals',
            name: 'failed_deals',
            orderable: true,
            width: '8%'
        },
        {
            data: 'active_until',
            name: 'active_until',
            orderable: true,
            width: '9%'
        },
        {
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false,
            width: '11%'
        }
    ],

    // Языковые настройки (русский)
    language: {
        url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/ru.json',
        emptyTable: 'Нет данных',
        info: 'Показано _START_ - _END_ из _TOTAL_',
        infoEmpty: 'Показано 0 - 0 из 0',
        infoFiltered: '(отфильтровано из _MAX_)',
        lengthMenu: 'Показать _MENU_ записей',
        loadingRecords: 'Загрузка...',
        processing: 'Обработка...',
        search: 'Поиск:',
        zeroRecords: 'Совпадений не найдено',
        paginate: {
            first: 'Первая',
            last: 'Последняя',
            next: 'Следующая',
            previous: 'Предыдущая'
        }
    },

    // Базовые параметры DataTables
    pageLength: 10,
    pagingType: 'simple_numbers',
    searching: false,
    ordering: false,
    processing: true,
    serverSide: true
};
