/**
 * Главный файл инициализации DataTables для таблицы комплексов
 * Использует модули: ComplexRenderers, ComplexTableConfig, ComplexFilters, ComplexTags
 */
$(document).ready(function () {

    // Ссылки на модули
    var Config = window.ComplexTableConfig;
    var Filters = window.ComplexFilters;
    var Tags = window.ComplexTags;
    var Renderers = window.ComplexRenderers;

    // ========== Debounce функция ==========
    function debounce(func, wait) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            var later = function () {
                clearTimeout(timeout);
                func.apply(context, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    var DEBOUNCE_DELAY = 600;

    // ========== Инициализация DataTables ==========
    var settings = Config.getBaseSettings();

    // AJAX конфигурация
    settings.ajax = {
        url: Config.ajaxUrl,
        type: 'GET',
        data: function (d) {
            return Filters.collectFilterData(d);
        },
        error: function (xhr, error, thrown) {
            console.error('DataTables AJAX error:', error, thrown);
        }
    };

    // Callback после отрисовки
    settings.drawCallback = function (settings) {
        var info = table.page.info();
        $('#example_info').html('Всего: <b>' + info.recordsDisplay + '</b>');
        $('#select-all-checkbox').prop('checked', false);
        Filters.updateCounter();
        initTooltips();
        initPhotoHoverPreview();
    };

    var table = $('#example').DataTable(settings);

    // ========== Функции перезагрузки ==========

    var debouncedReload = debounce(function () {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    var debouncedUpdateTags = debounce(function () {
        Tags.update();
    }, DEBOUNCE_DELAY);

    function reloadTable() {
        table.ajax.reload();
    }

    // Глобальная функция для обновления таблицы из фильтра локации
    window.reloadComplexesTable = function () {
        reloadTable();
    };

    // ========== Обработчики событий ==========

    // Клик на кнопку удаления тега
    $(document).on('click', '.filter-tags .badge button', function () {
        var $tag = $(this).closest('.badge');
        var filterType = $tag.data('filter-type');
        var filterValue = $tag.data('filter-value');

        Tags.remove(filterType, filterValue);
        Tags.update();
        reloadTable();
    });

    // Выбрать все / снять все
    $('#select-all-checkbox').on('change', function () {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#example tbody').on('change', '.row-checkbox', function () {
        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $('#select-all-checkbox').prop('checked', allChecked);
    });

    // Применение фильтров при отправке формы
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // ========== Сортировка ==========

    $(document).on('click', '.sort-option', function (e) {
        e.preventDefault();

        var $this = $(this);
        var sortField = $this.data('sort-field');
        var sortDir = $this.data('sort-dir');

        Filters.setSort(sortField, sortDir);
        $('.sort-option').removeClass('active');
        $this.addClass('active');
        reloadTable();
    });

    // ========== Автоматическая фильтрация ==========

    // Текстовые поля с задержкой
    $('#filter-form').on('input', Filters.textInputSelectors, function () {
        debouncedReload();
        debouncedUpdateTags();
    });

    // Select поля - мгновенная реакция
    $('#filter-form').on('change', '#category_id, #currency_id, #developer_id, #housing_class_id', function () {
        reloadTable();
        Tags.update();
    });

    // Чекбоксы фильтров - мгновенная реакция
    $('#filter-form').on('change', Filters.checkboxSelectors, function () {
        reloadTable();
        Tags.update();
    });

    // ========== Кнопки поиска ==========

    $('#search-id-btn').on('click', function () {
        reloadTable();
        Tags.update();
    });

    // Enter в поле поиска по ID
    $('#search_id').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
            Tags.update();
        }
    });

    // ========== Сброс фильтров ==========

    $('#delete-params-on-filter').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    $('#reset-filters-btn').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        Tags.update();
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    // ========== Детальная информация (child row) ==========

    // Обработчик клика на кнопку разворачивания
    $('#example tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var icon = $(this).find('img');

        if (row.child.isShown()) {
            // Закрываем строку
            row.child.hide();
            tr.removeClass('shown active');
            icon.attr('src', './img/icon/plus.svg');
        } else {
            // Открываем строку
            var childHtml = Renderers.childRow(row.data());
            tr.after(childHtml);
            tr.addClass('shown active');
            icon.attr('src', './img/icon/minus.svg');
            initTooltips();
            initPhotoHoverPreview();
        }
    });

    // Обработчик кнопки "Свернуть"
    $('#example tbody').on('click', '.close-btn-other, .info-complex-btn', function () {
        var dopInfoRow = $(this).closest('.dop-info-row');
        var parentTr = dopInfoRow.prev();
        var icon = parentTr.find('.details-control img');

        parentTr.removeClass('shown active');
        icon.attr('src', './img/icon/plus.svg');
        dopInfoRow.remove();
    });

    // Обработчик кнопки "Ещё" для длинного описания
    $('#example tbody').on('click', '.btn-show-text', function () {
        var container = $(this).closest('.description-text');
        var moreText = container.find('.more-text');

        if (moreText.is(':visible')) {
            moreText.hide();
            $(this).text('Ещё');
        } else {
            moreText.show();
            $(this).text('Скрыть');
        }
    });

    // ========== Удаление комплекса ==========

    $(document).on('click', '.delete-complex', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (confirm('Вы уверены, что хотите удалить этот комплекс?')) {
            $.ajax({
                url: '/complexes/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    table.ajax.reload();
                },
                error: function (xhr) {
                    alert('Ошибка при удалении');
                }
            });
        }
    });

    // ========== Вспомогательные функции ==========

    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    function initPhotoHoverPreview() {
        // Создаем попап для прев'ю фото (якщо ще не існує)
        if ($('#photo-preview-popup').length === 0) {
            $('body').append('<div id="photo-preview-popup"><img src="" alt=""></div>');
        }

        var $popup = $('#photo-preview-popup');
        var $popupImg = $popup.find('img');
        var hoverTimeout;

        // Обработчик наведения на фото
        $('.tbody-wrapper.photo img').off('mouseenter mouseleave').hover(
            function() {
                var $img = $(this);
                var imgSrc = $img.attr('src');

                // Пропускаємо якщо це дефолтна іконка
                if (imgSrc.includes('default-foto.svg')) return;

                hoverTimeout = setTimeout(function() {
                    $popupImg.attr('src', imgSrc);
                    $popup.show();
                }, 300);
            },
            function() {
                clearTimeout(hoverTimeout);
                $popup.hide();
            }
        );

        $popup.hover(
            function() {},
            function() {
                $popup.hide();
            }
        );
    }

    // ========== Расширенный фильтр (toggle) ==========

    $('#full-filter-btn').on('click', function () {
        $('.full-filter').slideToggle(300);
    });

    // ========== Multiple menu (фильтры с чекбоксами) ==========

    $(document).on('click', '.multiple-menu-btn', function (e) {
        e.stopPropagation();
        var $btn = $(this);
        var $wrapper = $btn.next('.multiple-menu-wrapper');
        var isOpen = $btn.attr('data-open-menu') === 'true';

        // Закрываем все другие меню
        $('.multiple-menu-btn').not($btn).attr('data-open-menu', 'false');
        $('.multiple-menu-wrapper').not($wrapper).hide();

        // Переключаем текущее меню
        if (isOpen) {
            $btn.attr('data-open-menu', 'false');
            $wrapper.hide();
        } else {
            $btn.attr('data-open-menu', 'true');
            $wrapper.show();
        }
    });

    // Закрытие при клике вне меню
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.multiple-menu').length) {
            $('.multiple-menu-btn').attr('data-open-menu', 'false');
            $('.multiple-menu-wrapper').hide();
        }
    });

    // Поиск внутри multiple-menu
    $(document).on('input', '.multiple-menu-search', function () {
        var query = $(this).val().toLowerCase();
        var $list = $(this).closest('.multiple-menu-wrapper').find('.multiple-menu-item');

        $list.each(function () {
            var text = $(this).find('.my-custom-text').text().toLowerCase();
            $(this).toggle(text.indexOf(query) > -1);
        });
    });

    // Обновление текста кнопки при выборе чекбоксов
    $(document).on('change', '.multiple-menu-item input[type="checkbox"]', function () {
        var $menu = $(this).closest('.multiple-menu');
        var $btn = $menu.find('.multiple-menu-btn');
        var checked = $menu.find('input[type="checkbox"]:checked');

        if (checked.length === 0) {
            $btn.text('Все');
        } else if (checked.length === 1) {
            $btn.text(checked.first().closest('label').find('.my-custom-text').text());
        } else {
            $btn.text(checked.first().closest('label').find('.my-custom-text').text() + ' +' + (checked.length - 1));
        }
    });

    // ========== Инициализация ==========

    Tags.update();
    initTooltips();
    initPhotoHoverPreview();

    // Select2 инициализация
    if ($.fn.select2) {
        $('.js-example-responsive2').select2({
            width: '100%',
            minimumResultsForSearch: -1
        });

        $('.js-example-responsive3').select2({
            width: '100%',
            minimumResultsForSearch: -1
        });
    }
});
