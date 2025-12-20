$(document).ready(function() {
    // Инициализация DataTables
    const table = $('#example').DataTable({
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

    table.on('draw', function() {
        const info = table.page.info();
        $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');
    });

    $('thead .my-custom-input input').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('tbody .my-custom-input input').prop('checked', isChecked);
    });

    $('tbody').on('change', '.my-custom-input input', function() {
        const allChecked = $('tbody .my-custom-input input:checked').length === $('tbody .my-custom-input input').length;
        $('thead .my-custom-input input').prop('checked', allChecked);
    });

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
