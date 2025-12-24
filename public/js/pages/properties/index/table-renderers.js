/**
 * Render-функции для колонок DataTables
 * Объект доступен через window.PropertyRenderers
 */
window.PropertyRenderers = {

    // Чекбокс выбора строки
    checkbox: function(data, type, row) {
        return '<div class="tbody-wrapper checkBox">' +
            '<label class="my-custom-input">' +
            '<input type="checkbox" class="row-checkbox" value="' + data + '">' +
            '<span class="my-custom-box"></span>' +
            '</label></div>';
    },

    // Локация (адрес)
    location: function(data, type, row) {
        return '<div class="tbody-wrapper location">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Тип сделки
    dealType: function(data, type, row) {
        return '<div class="tbody-wrapper type">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Площадь
    area: function(data, type, row) {
        return '<div class="tbody-wrapper area">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Состояние
    condition: function(data, type, row) {
        return '<div class="tbody-wrapper condition">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Этаж
    floor: function(data, type, row) {
        return '<div class="tbody-wrapper floor">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Фото
    photo: function(data, type, row) {
        return '<div class="tbody-wrapper photo">' +
            (data !== '-' ? data : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Цена
    price: function(data, type, row) {
        return '<div class="tbody-wrapper price">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    },

    // Контакт
    contact: function(data, type, row) {
        return '<div class="tbody-wrapper contact">' +
            (data !== '-' ? '<p>' + data + '</p>' : '<span class="text-muted">-</span>') +
            '</div>';
    }
};
