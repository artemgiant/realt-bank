/**
 * Главный файл инициализации DataTables для таблицы компаний
 * Использует модули: CompanyRenderers, CompanyTableConfig, CompanyFilters
 */
$(document).ready(function () {

    // Ссылки на модули
    var Config = window.CompanyTableConfig;
    var Filters = window.CompanyFilters;

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

    // Задержка в миллисекундах (600ms)
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
        // Обновляем информацию о количестве
        var info = table.page.info();
        $('#companies-table_info').html('Всего: <b>' + info.recordsDisplay + '</b>');

        // Сбрасываем чекбокс "выбрать все"
        $('#select-all-checkbox').prop('checked', false);

        // Обновляем счетчик фильтров
        Filters.updateCounter();

        // Инициализация тултипов для новых строк
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    var table = $('#companies-table').DataTable(settings);

    // ========== Функции перезагрузки ==========

    // Функция перезагрузки таблицы с debounce для текстовых полей
    var debouncedReload = debounce(function () {
        table.ajax.reload();
    }, DEBOUNCE_DELAY);

    // Функция мгновенной перезагрузки
    function reloadTable() {
        table.ajax.reload();
    }

    // Глобальная функция для обновления таблицы из других модулей
    window.reloadCompaniesTable = function () {
        reloadTable();
    };

    // ========== Обработчики событий ==========

    // Выбрать все / снять все
    $('#select-all-checkbox').on('change', function () {
        var isChecked = $(this).prop('checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    // Обновление состояния "выбрать все" при клике на отдельный чекбокс
    $('#companies-table tbody').on('change', '.row-checkbox', function () {
        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
        $('#select-all-checkbox').prop('checked', allChecked);
    });

    // Применение фильтров при отправке формы
    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // ========== Сортировка ==========

    // Обработчик клика на пункты сортировки
    $(document).on('click', '.sort-option', function (e) {
        e.preventDefault();

        var $this = $(this);
        var sortField = $this.data('sort-field');
        var sortDir = $this.data('sort-dir');

        // Устанавливаем сортировку
        Filters.setSort(sortField, sortDir);

        // Убираем active класс со всех пунктов
        $('.sort-option').removeClass('active');

        // Добавляем active класс текущему пункту
        $this.addClass('active');

        // Перезагружаем таблицу
        reloadTable();
    });

    // ========== Автоматическая фильтрация ==========

    // Обработчик ввода для текстовых полей (с задержкой)
    $('#filter-form').on('input', Filters.textInputSelectors, function () {
        debouncedReload();
    });

    // Обработчик изменения select фильтров (без задержки)
    $('#filter-form').on('change', 'select', function () {
        reloadTable();
    });

    // ========== Кнопки поиска ==========

    // Enter в поле поиска
    $('#search-name').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            reloadTable();
        }
    });

    // ========== Сброс фильтров ==========

    // Кнопка сброса в счетчике фильтров
    $('#delete-params-on-filter').on('click', function (e) {
        e.preventDefault();
        Filters.reset();
        $('.sort-option').removeClass('active');
        table.ajax.reload();
    });

    // ========== Детальная информация (child row) ==========

    function formatChildRow(data) {
        var description = data.description || '';

        // Описание
        var descriptionHtml = '<p class="description-text"><strong>О компании:</strong> ';

        if (description) {
            if (description.length > 200) {
                var visibleText = description.substring(0, 200);
                var hiddenText = description.substring(200);

                descriptionHtml += '<span class="visible-text">' + visibleText + '</span>' +
                    '<span class="dots">...</span>' +
                    '<span class="more-text" style="display: none;">' + hiddenText + '</span>' +
                    '<button class="btn btn-show-text" type="button">Развернуть</button>';
            } else {
                descriptionHtml += description;
            }
        } else {
            descriptionHtml += '-';
        }
        descriptionHtml += '</p>';

        var createdAt = data.created_at_formatted || '-';
        var updatedAt = data.updated_at_formatted || '-';
        var id = data.id;
        var officesCount = data.offices_count || 0;
        var teamCount = data.team_count || 0;
        var propertiesCount = data.properties_count || 0;

        // Кнопка обновления (SVG)
        var refreshBtn = '<button class="btn" type="button">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#5FB343" class="bi bi-arrow-repeat" viewBox="0 0 16 16">' +
            '<path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"></path>' +
            '<path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"></path>' +
            '</svg>' +
            '</button>';

        return '<div class="tbody-dop-info">' +
            '<div class="info-main">' +
            '<div class="info-main-left">' +
            '<div class="info-main-left-wrapper">' +
            '<div class="description">' +
            descriptionHtml +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="info-footer">' +
            '<p class="info-footer-data">ID: <span>' + id + '</span></p>' +
            '<p class="info-footer-data">Добавлено: <span>' + createdAt + '</span></p>' +
            '<p class="info-footer-data">Обновлено: <span>' + updatedAt + '</span>' +
            refreshBtn +
            '</p>' +
            '<p class="info-footer-data">Офисы: <button class="info-footer-btn btn-others" type="button">' + officesCount + '</button></p>' +
            '<p class="info-footer-data">Команда: <span>' + teamCount + '</span></p>' +
            '<p class="info-footer-data">Объекты: <span>' + propertiesCount + '</span></p>' +
            '<button class="info-footer-btn ms-auto close-btn-other" type="button">Свернуть</button>' +
            '</div>' +
            '</div>';
    }

    // Обработчик клика на кнопку разворачивания
    $('#companies-table tbody').on('click', '.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var icon = $(this).find('img');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icon.attr('src', '/img/icon/plus.svg');
        } else {
            row.child(formatChildRow(row.data())).show();
            tr.addClass('shown');
            icon.attr('src', '/img/icon/minus.svg');

            var childTr = $(row.child());
            childTr.addClass('dop-info-row');
            childTr.find('td').css('border-bottom', 'none');
        }
    });

    // Обработчик кнопки "Свернуть"
    $('#companies-table tbody').on('click', '.close-btn-other', function () {
        var childTr = $(this).closest('tr');
        var parentTr = childTr.prev();
        parentTr.find('.details-control').click();
    });

    // Обработчик кнопки "Развернуть/Скрыть" для длинного описания
    $('#companies-table tbody').on('click', '.btn-show-text', function () {
        var container = $(this).closest('.description-text');
        var dots = container.find('.dots');
        var moreText = container.find('.more-text');
        var btn = $(this);

        if (moreText.is(':visible')) {
            moreText.hide();
            dots.show();
            btn.text('Развернуть');
        } else {
            moreText.show();
            dots.hide();
            btn.text('Скрыть');
        }
    });

    // ========== Офисы (раскрывающийся список) ==========

    // Функция рендеринга строки с офисами
    function formatOfficesRow(companyId, offices) {
        // Генерируем строки таблицы для каждого офиса
        var officesRows = '';

        if (offices && offices.length > 0) {
            offices.forEach(function(office) {
                var logoSrc = office.logo || './img/image.png';
                var officeName = office.name || '-';
                var officeAddress = office.address || '-';
                var officeLocation = office.location || '';
                var teamCount = office.team_count || 0;
                var propertiesCount = office.properties_count || 0;

                // Контакт офиса
                var responsibleHtml = '';
                if (office.responsible) {
                    var contactName = office.responsible.name || '-';
                    var contactPosition = office.responsible.position || '';
                    var contactPhone = office.responsible.phone || '';
                    var phoneLink = contactPhone ? '<a href="tel:' + contactPhone.replace(/[^+\d]/g, '') + '">' + contactPhone + '</a>' : '';

                    responsibleHtml = '<div class="tbody-wrapper responsible">' +
                        '<p class="link-name" data-hover-agent>' + contactName + '</p>' +
                        (contactPosition ? '<span>' + contactPosition + '</span>' : '') +
                        phoneLink +
                        '</div>';
                } else {
                    responsibleHtml = '<div class="tbody-wrapper responsible"><span>-</span></div>';
                }

                officesRows += '<tr>' +
                    '<td>' +
                        '<div class="tbody-wrapper photo">' +
                            '<img src="' + logoSrc + '" alt="">' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<div class="tbody-wrapper company">' +
                            '<strong>' + officeName + '</strong>' +
                            '<p>' + officeAddress + '</p>' +
                            (officeLocation ? '<span>' + officeLocation + '</span>' : '') +
                        '</div>' +
                    '</td>' +
                    '<td>' + responsibleHtml + '</td>' +
                    '<td>' +
                        '<div class="tbody-wrapper command">' +
                            '<p><button class="info-footer-btn btn-others" type="button">' + teamCount + '</button></p>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<div class="tbody-wrapper object">' +
                            '<p><button class="info-footer-btn btn-others" type="button">' + propertiesCount + '</button></p>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<div class="tbody-wrapper block-actions">' +

                            '<div class="block-actions-wrapper">' +
                                '<div class="menu-burger">' +
                                    '<div class="dropdown">' +
                                        '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                                            '<img src="./img/icon/burger-blue.svg" alt="">' +
                                        '</button>' +
                                        '<ul class="dropdown-menu">' +
                                            '<li><a class="dropdown-item" href="#">Обновить</a></li>' +
                                            '<li><a class="dropdown-item" href="#">Редактировать</a></li>' +
                                        '</ul>' +
                                    '</div>' +
                                '</div>' +
                                '<label class="bookmark">' +
                                    '<input type="checkbox">' +
                                    '<span>' +
                                        '<img class="non-checked" src="./img/icon/bookmark.svg" alt="">' +
                                        '<img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">' +
                                    '</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
            });
        } else {
            officesRows = '<tr><td colspan="6"><p style="text-align: center; padding: 20px;">Офисы не найдены</p></td></tr>';
        }

        var dopInfoRow = '<div class="table-for-others-info">' +
            '<p class="paragraph">Офисы</p>' +
            '<div>' +
                '<div class="thead-wrapper command">' +
                    '<p>' +
                        '<img src="./img/icon/icon-table/people-fill.svg" alt="">' +
                        '<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Команда">' +
                            '<img src="./img/icon/icon-info.svg" alt="">' +
                        '</span>' +
                    '</p>' +
                '</div>' +
            '</div>' +
            '<div>' +
                '<div class="thead-wrapper object">' +
                    '<p>' +
                        '<img src="./img/icon/icon-table/house-fill.svg" alt="">' +
                        '<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Объекты">' +
                            '<img src="./img/icon/icon-info.svg" alt="">' +
                        '</span>' +
                    '</p>' +
                '</div>' +
            '</div>' +
            '<div class="wrapper-btn-collapse">' +
                '<button class="info-footer-btn btn-collapse" type="button">Свернуть</button>' +
            '</div>' +
        '</div>' +
        '<div class="table-for-others">' +
            '<table id="example2" style="width:98%; margin: auto;">' +
                '<tbody>' + officesRows + '</tbody>' +
            '</table>' +
        '</div>';

        return dopInfoRow;
    }

    // Обработчик клика на кнопку офисов
    $('#companies-table tbody').on('click', '.offices-btn', function () {
        var $btn = $(this);
        var companyId = $btn.data('company-id');
        var originalText = $btn.text();
        var tr = $btn.closest('tr');
        var row = table.row(tr);

        // Проверяем, есть ли уже открытая строка офисов
        var nextTr = tr.next('.dop-info-row-offices');
        if (nextTr.length) {
            // Плавно сворачиваем
            nextTr.find('td > div').slideUp(300, function() {
                nextTr.remove();
                tr.removeClass('shown-offices');
            });
            return;
        }

        // Загружаем офисы через AJAX
        $.ajax({
            url: '/companies/' + companyId + '/offices',
            type: 'GET',
            beforeSend: function () {
                $btn.prop('disabled', true).text('...');
            },
            success: function (response) {
                var offices = response.data || response;
                var content = formatOfficesRow(companyId, offices);
                var html = '<tr class="dop-info-row dop-info-row-offices"><td colspan="12"><div class="offices-slide-wrapper" style="display: none;">' +
                    content + '</div></td></tr>';

                tr.addClass('shown-offices');
                tr.after(html);

                // Плавно раскрываем
                tr.next().find('.offices-slide-wrapper').slideDown(300);

                // Инициализация тултипов
                var tooltipTriggerList = [].slice.call(tr.next().find('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            },
            error: function (xhr) {
                console.error('Ошибка загрузки офисов:', xhr);
                alert('Ошибка загрузки офисов');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btn.text(originalText);
            }
        });
    });

    // Обработчик кнопки "Свернуть" для офисов
    $('#companies-table tbody').on('click', '.btn-collapse', function () {
        var officesTr = $(this).closest('.dop-info-row-offices');
        var parentTr = officesTr.prev();
        // Плавно сворачиваем
        officesTr.find('.offices-slide-wrapper').slideUp(300, function() {
            parentTr.removeClass('shown-offices');
            officesTr.remove();
        });
    });

    // ========== Удаление компании ==========
    $('#companies-table tbody').on('click', '.delete-company', function (e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (confirm('Вы уверены, что хотите удалить эту компанию?')) {
            $.ajax({
                url: '/companies/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    table.ajax.reload();
                },
                error: function (xhr) {
                    alert('Ошибка при удалении компании');
                }
            });
        }
    });

    // ========== Инициализация ==========

    // Инициализация тултипов
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
