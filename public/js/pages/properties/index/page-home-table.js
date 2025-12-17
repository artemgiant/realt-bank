$(document).ready(function() {
    // Инициализация DataTables
    var table = $('#example').DataTable({
        searching: false,
        ordering: false,
        processing: false,
        paging: true,
        pagingType: 'simple_numbers',
        info: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/ru.json'
        }
    });

    // Обновление счетчика записей
    table.on('draw', function() {
        var info = table.page.info();
        $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');
    });

    // Выбор всех checkbox в thead
    $('thead .my-custom-input input').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('tbody .my-custom-input input').prop('checked', isChecked);
    });

    // Синхронизация checkbox в thead при изменении в tbody
    $('tbody').on('change', '.my-custom-input input', function() {
        var allChecked = $('tbody .my-custom-input input:checked').length === $('tbody .my-custom-input input').length;
        $('thead .my-custom-input input').prop('checked', allChecked);
    });

    // Инициализация tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
