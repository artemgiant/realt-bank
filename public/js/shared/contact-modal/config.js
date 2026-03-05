/**
 * Конфигурация модуля контактов (единая для всех сущностей)
 * Объект доступен через window.ContactModal.Config
 *
 * Переопределяйте через window.ContactModal.configure({...}) ДО подключения main.js
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Config = {
    // Контекст сущности (properties, companies, complexes, developers)
    context: 'properties',

    // Максимальное количество контактов (0 = без лимита)
    maxContacts: 0,

    // Задержка debounce в миллисекундах
    debounceDelay: 600,

    // Минимальная длина телефона для поиска
    searchMinLength: 6,

    // Максимальное количество телефонов
    maxPhones: 5,

    // URL эндпоинтов API
    urls: {
        searchByPhone: '/contacts/ajax-search-by-phone',
        store: '/contacts/ajax-store',
        show: '/contacts/{id}/ajax',
        update: '/contacts/{id}/ajax'
    },

    // Селекторы элементов
    selectors: {
        modal: '#add-contact-modal',
        form: '#contact-modal-form',
        saveBtn: '#save-contact-btn',
        contactsContainer: '#contacts-list-container',
        addContactBlock: '#add-contact-block',
        addMoreBtn: '#add-more-contact-btn',
        cardTemplate: '#contact-card-template',
        foundIndicator: '#contact-found-indicator',
        phoneInput: '.tel-contact',
        contactIdInput: '#contact-id-modal',
        contactRoleSelect: '#roles-contact-modal'
    },

    // Пути к иконкам
    icons: {
        defaultAvatar: '/img/icon/default-avatar-table.svg',
        whatsapp: '/img/icon/icon-table/cnapchat.svg',
        viber: '/img/icon/icon-table/viber.svg',
        telegram: '/img/icon/icon-table/tg.svg'
    },

    // Поведенческие флаги для разных контекстов
    behavior: {
        // Требовать выбор ролей при сохранении
        requireRoles: true,
        // Не вызывать API при сохранении существующего контакта (для complexes)
        skipApiForExisting: false,
        // Заполнение телефонов с определением страны по dial code
        phoneDialCodeMapping: true,
        // Поддержка pendingContactData (сложный edit mode)
        hasPendingContactData: true
    }
};

/**
 * Глубокое слияние конфигурации
 * @param {Object} overrides - Объект переопределений
 */
window.ContactModal.configure = function(overrides) {
    function deepMerge(target, source) {
        for (var key in source) {
            if (source.hasOwnProperty(key)) {
                if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                    target[key] = target[key] || {};
                    deepMerge(target[key], source[key]);
                } else {
                    target[key] = source[key];
                }
            }
        }
        return target;
    }
    deepMerge(window.ContactModal.Config, overrides);
};
