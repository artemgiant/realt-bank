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

    // Локация (адрес)
    // Формат: 1) Улица (жирный), 2) Зона, 3) Район, Город, Область, Страна
    location: function (data, type, row) {
        // Проверяем есть ли данные локации
        if (!data || !data.has_location) {
            return '<div class="tbody-wrapper location"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper location">';

        // 1. Улица (жирный)
        if (data.street) {
            html += '<b>' + data.street + '</b>';
        }

        // 2. Зона
        if (data.zone) {
            html += '<p>' + data.zone + '</p>';
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

    // Состояние + тип стен
    condition: function (data, type, row) {
        var wallType = row.wall_type ? '<span>' + row.wall_type + '</span>' : '';
        return '<div class="tbody-wrapper condition">' +
            (data !== '-' ? '<p>' + data + '</p>' + wallType : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Этаж
    floor: function (data, type, row) {
        return '<div class="tbody-wrapper floor">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Фото
    photo: function (data, type, row) {
        return '<div class="tbody-wrapper photo">' +
            (data !== '-' ? data : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Цена
    price: function (data, type, row) {
        var pricePerM2 = row.price_per_m2 ? '<span>' + row.price_per_m2 + ' /м²</span>' : '';
        return '<div class="tbody-wrapper price">' +
            (data !== '-' ? '<p>' + data + '</p>' + pricePerM2 : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Контакт
    contact: function (data, type, row) {
        // Проверяем есть ли контакт
        if (!data || !data.has_contact) {
            return '<div class="tbody-wrapper contact"><span class="text-muted">-</span></div>';
        }

        // Формируем ссылку на телефон
        var phoneHtml = '';
        if (data.phone) {
            var phoneClean = data.phone.replace(/[^0-9+]/g, '');
            phoneHtml = '<a href="tel:' + phoneClean + '">' + data.phone + '</a>';
        }

        return '<div class="tbody-wrapper contact">' +
            '<p class="link-name">' + (data.full_name || '-') + '</p>' +
            '<p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="' + (data.contact_type_name || '') + '">' + (data.contact_type_name || '-') + '</p>' +
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
            '                                    <li><a class="dropdown-item" href="#">Обновить</a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#">Редактировать</a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#">Удалить</a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#">Отложить</a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#">Передати</a></li>\n' +
            '                                 </ul>\n' +
            '                              </div>\n' +
            '                           </div>\n' +
            '                           <div class="menu-info">\n' +
            '                              <div class="dropdown">\n' +
            '                                 <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">\n' +
            '                                     <img src="./img/icon/copylinked.svg" alt="">\n' +
            '                                 </button>\n' +
            '                                 <ul class="dropdown-menu">\n' +
            '                                    <li><a class="dropdown-item" href="#"><span>На сайте</span></a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#"><span>На Rem.ua</span></a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#"><span>Видео Youtube</span></a></li>\n' +
            '                                    <li><a class="dropdown-item" href="#"><span>На карте</span></a></li>\n' +
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
