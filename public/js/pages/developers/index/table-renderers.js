/**
 * Render-функции для колонок DataTables (Developers)
 * Объект доступен через window.DeveloperRenderers
 */
window.DeveloperRenderers = {

    // Чекбокс выбора строки
    checkbox: function (data, type, row) {
        return '<div class="tbody-wrapper checkBox">' +
            '<label class="my-custom-input">' +
            '<input type="checkbox" class="row-checkbox" value="' + data + '">' +
            '<span class="my-custom-box"></span>' +
            '</label></div>';
    },

    // Девелопер (логотип + название + локация)
    developer: function (data, type, row) {
        if (!data) {
            return '<div class="tbody-wrapper developer"><span class="text-muted">-</span></div>';
        }

        var logoHtml = '';
        if (data.logo_url) {
            logoHtml = '<div><picture><source srcset="' + data.logo_url + '" type="image/webp"><img src="' + data.logo_url + '" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"></picture></div>';
        } else {
            logoHtml = '<div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><span style="font-size: 20px; color: #999;">D</span></div>';
        }

        var html = '<div class="tbody-wrapper developer">' +
            '<div class="developer-wrapper">' +
            logoHtml +
            '<div>' +
            '<p><strong>' + (data.name || '-') + '</strong></p>' +
            '<span>' + (data.location || '-') + '</span>' +
            '</div>' +
            '</div>' +
            '</div>';

        return html;
    },

    // Год основания
    yearFounded: function (data, type, row) {
        return '<div class="tbody-wrapper year">' +
            (data && data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Количество комплексов
    complexesCount: function (data, type, row) {
        return '<div class="tbody-wrapper complex">' +
            '<p>' + (data || 0) + '</p>' +
            '</div>';
    },

    // Контакт
    contact: function (data, type, row) {
        if (!data || !data.has_contact) {
            return '<div class="tbody-wrapper contact"><span class="text-muted">-</span></div>';
        }

        var phoneHtml = '';
        if (data.phone) {
            var phoneClean = data.phone.replace(/[^0-9+]/g, '');
            phoneHtml = '<a href="tel:' + phoneClean + '">' + data.phone + '</a>';
        }

        return '<div class="tbody-wrapper contact">' +
            '<div>' +
            '<p class="link-name" data-hover-contact="">' + (data.full_name || '-') + '</p>' +
            '<p data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="' + (data.contact_type_name || '') + '">' + (data.contact_type_name || '-') + '</p>' +
            phoneHtml +
            '</div>' +
            '</div>';
    },

    // Действия
    actions: function (data, type, row) {
        var editUrl = '/developers/' + data + '/edit';

        return '<div class="tbody-wrapper block-actions">' +
            '<div class="block-actions-wrapper">' +
            '<div class="menu-burger">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<img src="/img/icon/burger-blue.svg" alt="">' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="' + editUrl + '">Редактировать</a></li>' +
            '<li><a class="dropdown-item delete-developer" href="#" data-id="' + data + '">Удалить</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '<div class="menu-info">' +
            '<div class="dropdown">' +
            '<button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
            '<img src="/img/icon/copylinked.svg" alt="">' +
            '</button>' +
            '<ul class="dropdown-menu">' +
            '<li><a class="dropdown-item" href="#"><span>Сайт компании</span></a></li>' +
            '<li><a class="dropdown-item" href="#"><span>Материалы девелопера</span></a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<button type="button" class="details-control">' +
            '<img src="/img/icon/plus.svg" alt="">' +
            '</button>' +
            '</div>';
    }
};
