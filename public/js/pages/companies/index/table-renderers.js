/**
 * Render-функции для колонок DataTables (Companies)
 * Объект доступен через window.CompanyRenderers
 */
window.CompanyRenderers = {

    // Чекбокс выбора строки
    checkbox: function (data, type, row) {
        return '<div class="tbody-wrapper checkBox">' +
            '<label class="my-custom-input">' +
            '<input type="checkbox" class="row-checkbox" value="' + data + '">' +
            '<span class="my-custom-box"></span>' +
            '</label></div>';
    },

    // Логотип компании
    logo: function (data, type, row) {
        if (data) {
            return '<div class="tbody-wrapper photo">' +
                '<picture>' +
                '<source srcset="' + data + '" type="image/webp">' +
                '<img src="' + data + '" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">' +
                '</picture>' +
                '</div>';
        }

        return '<div class="tbody-wrapper photo">' +
            '<div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">' +
            '<span style="font-size: 20px; color: #999;">C</span>' +
            '</div>' +
            '</div>';
    },

    // Компания (название + адрес)
    company: function (data, type, row) {
        if (!data) {
            return '<div class="tbody-wrapper company"><span class="text-muted">-</span></div>';
        }

        var html = '<div class="tbody-wrapper company">' +
            '<div class="company-wrapper">' +
            '<div>' +
            '<p><strong>' + (data.name || '-') + '</strong></p>' +
            '<span class="text-muted">' + (data.address || '-') + '</span>' +
            '</div>' +
            '</div>' +
            '</div>';

        return html;
    },

    // Директор (контактное лицо)
    director: function (data, type, row) {
        if (!data || !data.has_contact) {
            return '<div class="tbody-wrapper director"><span class="text-muted">-</span></div>';
        }

        var phoneHtml = '';
        if (data.phone) {
            var phoneClean = data.phone.replace(/[^0-9+]/g, '');
            phoneHtml = '<a href="tel:' + phoneClean + '">' + data.phone + '</a>';
        }

        return '<div class="tbody-wrapper director">' +
            '<div>' +
            '<p class="link-name">' + (data.full_name || '-') + '</p>' +
            phoneHtml +
            '</div>' +
            '</div>';
    },

    // Количество офисов
    officesCount: function (data, type, row) {
        return '<div class="tbody-wrapper offices">' +
            '<button class="btn btn-sm btn-outline-primary offices-btn" type="button" data-company-id="' + row.id + '">' +
            (data || 0) +
            '</button>' +
            '</div>';
    },

    // Количество в команде
    teamCount: function (data, type, row) {
        return '<div class="tbody-wrapper team">' +
            '<p>' + (data || 0) + '</p>' +
            '</div>';
    },

    // Количество объектов
    propertiesCount: function (data, type, row) {
        return '<div class="tbody-wrapper properties">' +
            '<p>' + (data || 0) + '</p>' +
            '</div>';
    },

    // Комиссия
    commission: function (data, type, row) {
        return '<div class="tbody-wrapper commission">' +
            '<p>' + (data || '-') + '</p>' +
            '</div>';
    },

    // Действия
    actions: function (data, type, row) {
        var editUrl = '/companies/' + data + '/edit';

        return '<div class="tbody-wrapper block-actions">' +
            '<div class="block-actions-wrapper">' +
            '<div class="menu-burger">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<img src="/img/icon/burger-blue.svg" alt="">' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="' + editUrl + '">Редактировать</a></li>' +
            '<li><a class="dropdown-item delete-company" href="#" data-id="' + data + '">Удалить</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            (row.website ? '<div class="menu-info">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<img src="/img/icon/copylinked.svg" alt="">' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="' + row.website + '" target="_blank"><span>Сайт компании</span></a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' : '') +
            '</div>' +
            '<button type="button" class="details-control">' +
            '<img src="/img/icon/plus.svg" alt="">' +
            '</button>' +
            '</div>';
    }
};
