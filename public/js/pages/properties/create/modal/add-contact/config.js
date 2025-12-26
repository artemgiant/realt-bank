/**
 * Конфигурация модуля контактов
 * Объект доступен через window.ContactModal.Config
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Config = {
    // Задержка debounce в миллисекундах
    debounceDelay: 600,

    // Минимальная длина телефона для поиска
    searchMinLength: 6,

    // URL эндпоинтов API
    urls: {
        searchByPhone: '/contacts/ajax-search-by-phone',
        store: '/contacts/ajax-store',
        show: '/contacts/{id}/ajax',
        update: '/contacts/{id}/ajax'
    },

    // Максимальное количество телефонов
    maxPhones: 5,

    // Типы контактов
    contactTypes: {
        owner: 'Владелец',
        agent: 'Агент',
        developer: 'Девелопер'
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
        contactIdInput: '#contact-id-modal'
    },

    // Пути к иконкам
    icons: {
        defaultAvatar: '/img/icon/default-avatar-table.svg',
        whatsapp: '/img/icon/icon-table/cnapchat.svg',
        viber: '/img/icon/icon-table/viber.svg',
        telegram: '/img/icon/icon-table/tg.svg'
    }
};
