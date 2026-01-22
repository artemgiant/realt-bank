/**
 * Главный файл инициализации модуля контактов
 * Объединяет все модули и запускает инициализацию
 *
 * Зависимости (порядок подключения важен!):
 * 1. config.js
 * 2. utils.js
 * 3. components.js
 * 4. api.js
 * 5. form.js
 * 6. contact-list.js
 * 7. handlers.js
 * 8. main.js (этот файл)
 */
window.ContactModal = window.ContactModal || {};

/**
 * Инициализация модуля контактов
 */
window.ContactModal.init = function() {
    // Инициализируем все обработчики
    if (window.ContactModal.Handlers) {
        window.ContactModal.Handlers.initAll();
    }

    // Добавляем CSS для Select2 в модалке
    this._addStyles();

    console.log('ContactModal модуль инициализирован');
};

/**
 * Добавление стилей для модуля
 * @private
 */
window.ContactModal._addStyles = function() {
    // Проверяем, не добавлены ли уже стили
    if (document.getElementById('contact-modal-styles')) return;

    var style = document.createElement('style');
    style.id = 'contact-modal-styles';
    style.textContent =
        '.select2-container.select2-dropdown { z-index: 1060 !important; }' +
        '.modal .select2-container--open { z-index: 1060 !important; }' +
        '.select2-search__field { width: 100% !important; }' +
        '#contact-found-indicator { font-size: 12px; padding: 4px 8px; }';

    document.head.appendChild(style);
};

/**
 * Запуск при готовности DOM
 */
(function() {
    function onReady() {
        window.ContactModal.init();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }
})();
