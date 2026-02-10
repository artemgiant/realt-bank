/**
 * Render-функции для колонок DataTables
 * Объект доступен через window.PropertyRenderers
 */
window.PropertyRenderers = {

    // Чекбокс выбора строки
    checkbox: function (data, type, row) {
        return '<div class="tbody-wrapper checkBox">' +
            '<label class="my-custom-input">' +
            '<input type="checkbox" class="row-checkbox" value="' + data + '">' +
            '<span class="my-custom-box"></span>' +
            '</label></div>';
    },

    // Видимость для агентов (замок) + статус обновления
    visibility: function (data, type, row) {
        var isVisible = row.is_visible_to_agents;
        var lockIcon = '';

        if (isVisible) {
            // Открытый замок (SVG от пользователя)
            lockIcon = '<div class="tbody-wrapper visibility" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Контакт клиента открыт">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" opacity="0.6">' +
                '<path d="M12 17V15" stroke="#10B981" stroke-width="2" stroke-linecap="round"/>' +
                '<rect x="5" y="11" width="14" height="10" rx="2" stroke="#10B981" stroke-width="2"/>' +
                '<path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7" stroke="#10B981" stroke-width="2"/>' +
                '<circle cx="12" cy="16" r="1" fill="#10B981"/>' +
                '</svg>' +
                '</div>';
        } else {
            // Закрытый замок (SVG от пользователя)
            lockIcon = '<div class="tbody-wrapper visibility" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Контакт клиента закрыт">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" opacity="0.5">' +
                '<path d="M12 17V15" stroke="#4B5563" stroke-width="2" stroke-linecap="round"/>' +
                '<rect x="5" y="11" width="14" height="10" rx="2" stroke="#4B5563" stroke-width="2"/>' +
                '<path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="#4B5563" stroke-width="2"/>' +
                '<circle cx="12" cy="16" r="1" fill="#4B5563"/>' +
                '</svg>' +
                '</div>';
        }

        // Иконка "давно не обновлялось" (> 30 дней)
        var warningIcon = '';
        if (row.updated_at) {
            var updated = new Date(row.updated_at);
            var now = new Date();
            var diffTime = Math.abs(now - updated);
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 30) {
                // Оранжевый цвет, полупрозрачный, 20x20
                var warningStyle = 'opacity: 0.6;';
                warningIcon = '<div class="tbody-wrapper warning-icon pt-0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Обновлено более 30 дней назад">' +
                    '<svg width="16" height="16" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" style="' + warningStyle + '">' +
                    '<path d="M6.43196 2.41226L1.13821 11.2498C1.02907 11.4388 0.971319 11.6531 0.970708 11.8713C0.970097 12.0896 1.02665 12.3042 1.13473 12.4938C1.24282 12.6835 1.39867 12.8415 1.58678 12.9522C1.7749 13.0629 1.98871 13.1224 2.20696 13.1248H12.7945C13.0127 13.1224 13.2265 13.0629 13.4146 12.9522C13.6028 12.8415 13.7586 12.6835 13.8667 12.4938C13.9748 12.3042 14.0313 12.0896 14.0307 11.8713C14.0301 11.6531 13.9724 11.4388 13.8632 11.2498L8.56947 2.41226C8.45805 2.22858 8.30117 2.07671 8.11396 1.97131C7.92676 1.86592 7.71555 1.81055 7.50072 1.81055C7.28588 1.81055 7.07467 1.86592 6.88747 1.97131C6.70026 2.07671 6.54338 2.22858 6.43196 2.41226Z" stroke="#fd7e14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />' +
                    '<path d="M7.5 5.625V8.125" stroke="#fd7e14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />' +
                    '<circle cx="7.5" cy="10.625" r="0.625" fill="#fd7e14" />' +
                    '</svg>' +
                    '</div>';
            }
        }

        return '<div class="d-flex flex-column align-items-center justify-content-center">' + lockIcon + warningIcon + '</div>';
    },


    // Локация (адрес)
    // Формат: 1) ЖК (жирный), 2) Дом, Улица, Зона, 3) Район, Город, Область, Страна
    location: function (data, type, row) {
        // Проверяем есть ли данные локации
        if (!data || !data.has_location) {
            return '<div class="tbody-wrapper location"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper location">';

        // 1. ЖК (жирный)
        if (data.complex) {
            html += '<b>' + data.complex + '</b>';
        }

        // 2. Дом, Улица, Зона
        if (data.street) {
            html += '<p>' + data.street + '</p>';
        }

        // 3. Район, Город, Область, Страна
        if (data.address) {
            html += '<span>' + data.address + '</span>';
        }

        html += '</div>';
        return html;
    },

    // Тип недвижимости + количество комнат
    propertyType: function (data, type, row) {
        var roomCount = row.room_count ? '<span>' + row.room_count + '</span>' : '';
        return '<div class="tbody-wrapper type">' +
            (data !== '-' ? '<p>' + data + '</p>' + roomCount : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Площадь (общая/жилая/кухни)
    area: function (data, type, row) {
        var parts = [];
        var areaLand = row.area_land ? '<span>' + row.area_land + ' сот.' + '</span>' : '';
        if (data.total) parts.push(data.total);
        if (data.living) parts.push(data.living);
        if (data.kitchen) parts.push(data.kitchen);

        var areaText = parts.length > 0 ? parts.join('/') + ' м²' : '-';

        return '<div class="tbody-wrapper area">' +
            (areaText !== '-' ? '<p>' + areaText + '</p>' + areaLand : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Состояние + тип здания + тип стен
    condition: function (data, type, row) {
        var buildingType = row.building_type ? '<span>' + row.building_type + '</span>' : '';
        var wallType = row.wall_type ? '<span>' + row.wall_type + '</span>' : '';
        var content = buildingType + (buildingType && wallType ? '<br>' : '') + wallType;

        return '<div class="tbody-wrapper condition">' +
            (data !== '-' ? '<p>' + data + '</p>' + content : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Этаж
    floor: function (data, type, row) {
        return '<div class="tbody-wrapper floor">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Фото (с галереей FancyBox)
    photo: function (data, type, row) {
        if (data === '-' || !data || !data.main) {
            return '<div class="tbody-wrapper photo"><span class="text-muted">-</span></div>';
        }

        var galleryId = 'property-' + row.id;
        var html = '<div class="tbody-wrapper photo">';

        // Главное фото — видимая ссылка
        html += '<a href="' + data.main + '" data-fancybox="' + galleryId + '" style="cursor:pointer;">' +
            '<img src="' + data.main + '" alt="" class="table-photo">' +
            '</a>';

        // Превью при наведении
        html += '<div class="photo-hover-preview"><img src="' + data.main + '" alt=""></div>';

        // Остальные фото — скрытые ссылки для галереи
        if (data.all && data.all.length > 1) {
            for (var i = 0; i < data.all.length; i++) {
                if (data.all[i] !== data.main) {
                    html += '<a href="' + data.all[i] + '" data-fancybox="' + galleryId + '" style="display:none;"></a>';
                }
            }
        }

        html += '</div>';
        return html;
    },

    // Цена + цена за м² + комиссия от владельца
    price: function (data, type, row) {
        var pricePerM2 = row.price_per_m2 ? '<span>' + row.price_per_m2 + ' /м²</span>' : '';
        var commission = row.commission ? '<br><span>Ком: ' + row.commission + '</span>' : '';
        return '<div class="tbody-wrapper price">' +
            (data !== '-' ? '<p class="fw-bold">' + data + '</p>' + pricePerM2 + commission : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Контакт
    contact: function (data, type, row) {
        // Проверяем есть ли контакт
        if (!data || !data.has_contact) {
            return '<div class="tbody-wrapper contact"></div>';
        }

        // Формируем ссылку на телефон
        var phoneHtml = '';
        if (data.phone) {
            var phoneClean = data.phone.replace(/[^0-9+]/g, '');
            phoneHtml = '<a href="tel:' + phoneClean + '">' + data.phone + '</a>';
        }

        return '<div class="tbody-wrapper contact">' +
            '<p class="link-name">' + (data.full_name || '-') + '</p>' +
            (data.contact_type_name ? '<p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="' + data.contact_type_name + '">' + data.contact_type_name + '</p>' : '') +
            phoneHtml +
            '</div>';
    },

    // Действия (пустая ячейка)
    actions: function (data, type, row) {
        return '<div class="tbody-wrapper block-actions">\n' +
            '                        <a href="#" class="btn mail-link" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Написать">\n' +
            '                              <img src="./img/icon/mail.svg" alt="">\n' +
            '                        </a>\n' +
            '                        <div class="block-actions-wrapper">\n' +
            '                           <label class="bookmark">\n' +
            '                              <input type="checkbox">\n' +
            '                              <span>\n' +
            '                                  <img class="non-checked" src="./img/icon/bookmark.svg" alt="">\n' +
            '                                  <img class="on-checked" src="./img/icon/bookmark-cheked.svg" alt="">\n' +
            '                              </span>\n' +
            '                           </label>\n' +
            '                           <div class="menu-burger">\n' +
            '                              <div class="dropdown">\n' +
            '                                 <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">\n' +
            '                                     <img src="./img/icon/burger-blue.svg" alt="">\n' +
            '                                 </button>\n' +
            '                                 <ul class="dropdown-menu" style="">\n' +
            '                                    <li><a class="dropdown-item" href="/properties/' + row.id + '/edit">Редактировать</a></li>\n' +
            '                                 </ul>\n' +
            '                              </div>\n' +
            '                           </div>\n' +
            '                           <div class="menu-info">\n' +
            '                              <div class="dropdown">\n' +
            '                                 <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">\n' +
            '                                     <img src="./img/icon/copylinked.svg" alt="">\n' +
            '                                 </button>\n' +
            '                                 <ul class="dropdown-menu">\n' +
            (row.youtube_url ? '                                    <li><a class="dropdown-item" href="' + row.youtube_url + '" target="_blank"><span>Видео Youtube</span></a></li>\n' : '') +
            (row.tiktok_url ? '                                    <li><a class="dropdown-item" href="' + row.tiktok_url + '" target="_blank"><span>Видео TikTok</span></a></li>\n' : '') +
            '                                 </ul>\n' +
            '                              </div>\n' +
            '                           </div>\n' +
            '                        </div>\n' +
            '                        <button type="button" class="details-control">\n' +
            '                            <img src="./img/icon/plus.svg" alt="">\n' +
            '                        </button>\n' +
            '                    </div>';
    }
};
