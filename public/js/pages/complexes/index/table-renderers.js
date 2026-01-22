/**
 * Render-функции для колонок DataTables (Комплексы)
 * Объект доступен через window.ComplexRenderers
 */
window.ComplexRenderers = {

    // Чекбокс выбора строки
    checkbox: function (data, type, row) {
        return '<div class="tbody-wrapper checkBox">' +
            '<label class="my-custom-input">' +
            '<input type="checkbox" class="row-checkbox" value="' + row.id + '">' +
            '<span class="my-custom-box"></span>' +
            '</label></div>';
    },

    // Локация комплекса
    // Формат: 1) Название ЖК (жирный) + годы сдачи, 2) Улица + номер, 3) Район, Город, Область
    location: function (data, type, row) {
        if (!data || !data.has_location) {
            return '<div class="tbody-wrapper location"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper location">';

        // 1. Название ЖК + годы сдачи
        if (data.name) {
            html += '<p><strong>' + data.name + '</strong>';
            if (data.years) {
                html += ' <span>' + data.years + '</span>';
            }
            html += '</p>';
        }

        // 2. Улица + номер дома
        if (data.street) {
            html += '<p>' + data.street + '</p>';
        }

        // 3. Район, Город, Область
        if (data.address) {
            html += '<span>' + data.address + '</span>';
        }

        html += '</div>';
        return html;
    },

    // Тип объекта (категория + типы квартир)
    propertyType: function (data, type, row) {
        if (!data) {
            return '<div class="tbody-wrapper type"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper type">';

        if (data.category) {
            html += '<p>' + data.category + '</p>';
        }

        if (data.types) {
            html += '<span>' + data.types + '</span>';
        }

        html += '</div>';
        return html;
    },

    // Площадь (диапазон от-до)
    area: function (data, type, row) {
        if (!data || (!data.from && !data.to)) {
            return '<div class="tbody-wrapper area"><span class="text-muted">-</span></div>';
        }

        var areaText = '';
        if (data.from && data.to) {
            areaText = data.from + ' - ' + data.to + ' м²';
        } else if (data.from) {
            areaText = 'от ' + data.from + ' м²';
        } else if (data.to) {
            areaText = 'до ' + data.to + ' м²';
        }

        return '<div class="tbody-wrapper area"><p>' + areaText + '</p></div>';
    },

    // Состояние + тип стен
    condition: function (data, type, row) {
        if (!data) {
            return '<div class="tbody-wrapper condition"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper condition">';

        if (data.conditions && data.conditions.length > 0) {
            data.conditions.forEach(function(condition) {
                html += '<p>' + condition + '</p>';
            });
        }

        if (data.wall_type) {
            html += '<span>' + data.wall_type + '</span>';
        }

        html += '</div>';
        return html;
    },

    // Этажность (диапазон)
    floor: function (data, type, row) {
        if (!data) {
            return '<div class="tbody-wrapper floor"><span class="text-muted">-</span></div>';
        }

        return '<div class="tbody-wrapper floor"><p>' + data + '</p></div>';
    },

    // Фото
    photo: function (data, type, row) {
        if (!data || data === '-') {
            return '<div class="tbody-wrapper photo">' +
                '<picture><source srcset="./img/icon/default-foto.svg" type="image/webp">' +
                '<img src="./img/icon/default-foto.svg" alt=""></picture></div>';
        }

        return '<div class="tbody-wrapper photo">' +
            '<picture><source srcset="' + data + '" type="image/webp">' +
            '<img src="' + data + '" alt=""></picture></div>';
    },

    // Цена от + цена за м²
    price: function (data, type, row) {
        if (!data || !data.total) {
            return '<div class="tbody-wrapper price"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper price">';
        html += '<p>' + data.total + '</p>';

        if (data.per_m2) {
            html += '<span>' + data.per_m2 + '/м²</span>';
        }

        html += '</div>';
        return html;
    },

    // Контакт (девелопер/менеджер)
    contact: function (data, type, row) {
        if (!data || !data.has_contact) {
            return '<div class="tbody-wrapper contact"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper contact"><div>';

        if (data.name) {
            html += '<p class="link-name" data-hover-contact>' + data.name + '</p>';
        }

        if (data.company) {
            html += '<p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="' + data.company + '">' + data.company + '</p>';
        }

        if (data.phone) {
            var phoneClean = data.phone.replace(/[^0-9+]/g, '');
            html += '<a href="tel:' + phoneClean + '">' + data.phone + '</a>';
        }

        html += '</div>';

        if (data.logo) {
            html += '<div><picture><source srcset="' + data.logo + '" type="image/webp"><img src="' + data.logo + '" alt=""></picture></div>';
        }

        html += '</div>';
        return html;
    },

    // Действия
    actions: function (data, type, row) {
        var editUrl = '/complexes/' + row.id + '/edit';

        return '<div class="tbody-wrapper block-actions">' +
            '<div class="block-actions-wrapper">' +
            '<div class="menu-burger">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<picture><source srcset="./img/icon/burger-blue.svg" type="image/webp"><img src="./img/icon/burger-blue.svg" alt=""></picture>' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="#">Обновить</a></li>' +
            '<li><a class="dropdown-item" href="' + editUrl + '">Редактировать</a></li>' +
            '<li><a class="dropdown-item delete-complex" href="#" data-id="' + row.id + '">Удалить</a></li>' +
            '<li><a class="dropdown-item" href="#">Отложить</a></li>' +
            '<li><a class="dropdown-item" href="#">Передать</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '<div class="menu-info">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<picture><source srcset="./img/icon/copylinked.svg" type="image/webp"><img src="./img/icon/copylinked.svg" alt=""></picture>' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="#"><span>На сайте</span></a></li>' +
            '<li><a class="dropdown-item" href="#"><span>На Rem.ua</span></a></li>' +
            '<li><a class="dropdown-item" href="#"><span>Видео Youtube</span></a></li>' +
            '<li><a class="dropdown-item" href="#"><span>На карте</span></a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="details-control">' +
            '<picture><source srcset="./img/icon/plus.svg" type="image/webp"><img src="./img/icon/plus.svg" alt=""></picture>' +
            '</button>' +
            '</div>';
    },

    // Формирование HTML для child row (раскрытие строки)
    childRow: function(row) {
        var data = row;

        var html = '<tr class="dop-info-row">' +
            '<td colspan="10" style="border-bottom: none;">' +
            '<div class="tbody-dop-info">' +
            '<div class="info-main">' +
            '<div class="info-main-left">' +
            '<div class="info-main-left-wrapper">' +
            '<div class="description">';

        // Описание комплекса
        if (data.description) {
            html += '<p class="description-text">' +
                '<strong>О комплексе:</strong> ' + data.description +
                '</p>';
        }

        html += '<div class="description-wrapper">';

        // Примечание для агентов
        if (data.agent_notes) {
            html += '<p class="description-text">' +
                '<strong>Примечание для агентов:</strong> ' +
                '<span>' + data.agent_notes + '</span>' +
                '</p>';
        }

        // Специальные условия
        if (data.special_conditions) {
            html += '<p class="description-text">' +
                '<strong>Специальные условия:</strong> ' +
                '<span>' + data.special_conditions + '</span>' +
                '</p>';
        }

        html += '</div></div></div>';

        // Теги особенностей
        if (data.features && data.features.length > 0) {
            html += '<div class="filter-tags">';
            data.features.forEach(function(feature) {
                html += '<div class="badge rounded-pill">' + feature + '</div>';
            });
            html += '</div>';
        }

        // Таблица блоков/секций
        if (data.blocks && data.blocks.length > 0) {
            html += '<div class="table-for-others">' +
                '<table id="blocks-table-' + data.id + '" style="width:98%; margin: auto;">' +
                '<col width="3.478%"><col width="22.174%"><col width="6.695%"><col width="7.478%">' +
                '<col width="9.13%"><col width="5.217%"><col width="6.956%"><col width="6.782%">' +
                '<col width="14.525%"><col width="17.565%">' +
                '<tbody>';

            data.blocks.forEach(function(block) {
                html += '<tr>' +
                    '<td><div class="tbody-wrapper checkBox"></div></td>' +
                    '<td colspan="2"><div class="tbody-wrapper location">' +
                    '<p>' + (block.name || '-') + '</p>' +
                    '<p>' + (block.address || '-') + '</p>' +
                    '</div></td>' +
                    '<td><div class="tbody-wrapper"></div></td>' +
                    '<td><div class="tbody-wrapper condition">' +
                    '<p>' + (block.wall_type || '-') + '</p>' +
                    '<p>' + (block.heating_type || '-') + '</p>' +
                    '</div></td>' +
                    '<td><div class="tbody-wrapper floor"><p>' + (block.floors || '-') + '</p></div></td>' +
                    '<td><div class="tbody-wrapper photo">' +
                    (block.photo ? '<img src="' + block.photo + '" alt="">' : '') +
                    '</div></td>' +
                    '<td><div class="tbody-wrapper"></div></td>' +
                    '<td><div class="tbody-wrapper"></div></td>' +
                    '<td><div class="tbody-wrapper">' + (block.year_built || '-') + ' г.</div></td>' +
                    '</tr>';
            });

            html += '</tbody></table></div>';
        }

        // Footer с ID и датами
        html += '<div class="info-footer">' +
            '<p class="info-footer-data">ID: <span>' + data.id + '</span></p>' +
            '<p class="info-footer-data">Добавлено: <span>' + (data.created_at || '-') + '</span></p>' +
            '<p class="info-footer-data">Обновлено: <span>' + (data.updated_at || '-') + '</span>' +
            '<button class="btn refresh-btn" type="button" data-id="' + data.id + '">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#5FB343" class="bi bi-arrow-repeat" viewBox="0 0 16 16">' +
            '<path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>' +
            '<path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>' +
            '</svg></button></p>' +
            '</div></div></div>' +
            '<div class="info-complex-wrapper">' +
            '<button class="info-complex-btn ms-auto close-btn-other" type="button">Свернуть</button>' +
            '</div></div></td></tr>';

        return html;
    }
};
