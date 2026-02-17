/**
 * Управление списком контактов на странице
 * Объект доступен через window.ContactModal.ContactList
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.ContactList = {

    // Максимальное количество контактов
    maxContacts: 5,

    /**
     * Добавление контакта в список на странице
     * @param {Object} contact - Данные контакта
     * @returns {boolean} - Успешность добавления
     */
    add: function(contact) {
        var Config = window.ContactModal.Config;

        var container = document.querySelector(Config.selectors.contactsContainer);
        var addBlock = document.querySelector(Config.selectors.addContactBlock);
        var addMoreBtn = document.querySelector(Config.selectors.addMoreBtn);
        var template = document.querySelector(Config.selectors.cardTemplate);

        if (!container || !template) {
            console.error('Контейнер или шаблон не найдены');
            return false;
        }

        // Проверяем лимит контактов
        var currentCount = container.querySelectorAll('.contact-card').length;
        if (currentCount >= this.maxContacts) {
            alert('Максимум ' + this.maxContacts + ' контактов');
            return false;
        }

        // Проверяем, не добавлен ли уже этот контакт
        if (container.querySelector('[data-contact-id="' + contact.id + '"]')) {
            alert('Этот контакт уже добавлен');
            return false;
        }

        // Клонируем шаблон
        var clone = template.content.cloneNode(true);
        var card = clone.querySelector('.contact-card');

        // Заполняем данными
        this._fillCard(card, contact);

        // Добавляем карточку
        container.appendChild(clone);

        // Скрываем блок "добавить" и показываем кнопку "добавить еще"
        if (addBlock) addBlock.classList.add('d-none');

        // Проверяем лимит для кнопки "добавить ещё"
        var newCount = container.querySelectorAll('.contact-card').length;
        if (addMoreBtn) {
            if (newCount >= this.maxContacts) {
                addMoreBtn.classList.add('d-none');
            } else {
                addMoreBtn.classList.remove('d-none');
            }
        }

        return true;
    },

    /**
     * Удаление контакта из списка
     * @param {number|string} contactId - ID контакта
     */
    remove: function(contactId) {
        var Config = window.ContactModal.Config;

        var container = document.querySelector(Config.selectors.contactsContainer);
        var addBlock = document.querySelector(Config.selectors.addContactBlock);
        var addMoreBtn = document.querySelector(Config.selectors.addMoreBtn);

        var card = container ? container.querySelector('[data-contact-id="' + contactId + '"]') : null;
        if (card) {
            card.remove();
        }

        // Если контактов больше нет - показываем блок "добавить" и скрываем кнопку
        var remainingCards = container ? container.querySelectorAll('.contact-card') : [];
        if (remainingCards.length === 0) {
            if (addBlock) addBlock.classList.remove('d-none');
            if (addMoreBtn) addMoreBtn.classList.add('d-none');
        }
    },

    /**
     * Обновление карточки контакта
     * @param {Object} contact - Данные контакта
     */
    update: function(contact) {
        var Config = window.ContactModal.Config;
        var container = document.querySelector(Config.selectors.contactsContainer);

        if (!container) return;

        var card = container.querySelector('[data-contact-id="' + contact.id + '"]');
        if (card) {
            this._fillCard(card, contact);
        }
    },

    /**
     * Получение списка ID добавленных контактов
     * @returns {Array<string>}
     */
    getContactIds: function() {
        var Config = window.ContactModal.Config;
        var container = document.querySelector(Config.selectors.contactsContainer);
        var ids = [];

        if (container) {
            container.querySelectorAll('.contact-card').forEach(function(card) {
                var id = card.getAttribute('data-contact-id');
                if (id) ids.push(id);
            });
        }

        return ids;
    },

    /**
     * Проверка наличия контакта в списке
     * @param {number|string} contactId - ID контакта
     * @returns {boolean}
     */
    hasContact: function(contactId) {
        var Config = window.ContactModal.Config;
        var container = document.querySelector(Config.selectors.contactsContainer);

        if (!container) return false;

        return container.querySelector('[data-contact-id="' + contactId + '"]') !== null;
    },

    /**
     * Заполнение карточки данными контакта
     * @param {HTMLElement} card - Элемент карточки
     * @param {Object} contact - Данные контакта
     * @private
     */
    _fillCard: function(card, contact) {
        var Config = window.ContactModal.Config;

        card.setAttribute('data-contact-id', contact.id);

        // Имя
        var nameEl = card.querySelector('.contact-name');
        if (nameEl) nameEl.textContent = contact.full_name || '-';

        // Роли контакта через запятую (из roles-contact-modal / API roles_names)
        var typeEl = card.querySelector('.contact-type');
        if (typeEl) typeEl.textContent = contact.roles_names || contact.contact_role_names || '-';

        // Телефон
        var phoneLink = card.querySelector('.contact-phone');
        if (phoneLink && contact.primary_phone) {
            var cleanPhone = contact.primary_phone.replace(/[^0-9+]/g, '');
            phoneLink.href = 'tel:' + cleanPhone;
            phoneLink.textContent = contact.primary_phone;
        }

        // Аватар
        var avatarEl = card.querySelector('.contact-avatar');
        if (avatarEl) {
            avatarEl.src = contact.photo_url || Config.icons.defaultAvatar;
        }

        // Мессенджеры
        var messengersContainer = card.querySelector('.contact-messengers');
        if (messengersContainer) {
            messengersContainer.innerHTML = this._buildMessengersHtml(contact);
        }

        // Hidden input для формы
        var hiddenInput = card.querySelector('.contact-id-input');
        if (hiddenInput) {
            hiddenInput.value = contact.id;
        }
    },

    /**
     * Построение HTML мессенджеров
     * @param {Object} contact - Данные контакта
     * @returns {string}
     * @private
     */
    _buildMessengersHtml: function(contact) {
        var Config = window.ContactModal.Config;
        var html = '';

        // WhatsApp
        if (contact.whatsapp || (contact.messengers && contact.messengers.indexOf('whatsapp') !== -1)) {
            var whatsappLink = contact.whatsapp_link || contact.whatsapp || '#';
            html += '<a href="' + whatsappLink + '" target="_blank">' +
                    '<picture><source srcset="' + Config.icons.whatsapp + '" type="image/webp">' +
                    '<img src="' + Config.icons.whatsapp + '" alt="WhatsApp"></picture></a>';
        }

        // Viber
        if (contact.viber || (contact.messengers && contact.messengers.indexOf('viber') !== -1)) {
            var viberLink = contact.viber_link || contact.viber || '#';
            html += '<a href="' + viberLink + '" target="_blank">' +
                    '<picture><source srcset="' + Config.icons.viber + '" type="image/webp">' +
                    '<img src="' + Config.icons.viber + '" alt="Viber"></picture></a>';
        }

        // Telegram
        if (contact.telegram || (contact.messengers && contact.messengers.indexOf('telegram') !== -1)) {
            var telegramLink = contact.telegram_link || contact.telegram || '#';
            html += '<a href="' + telegramLink + '" target="_blank">' +
                    '<picture><source srcset="' + Config.icons.telegram + '" type="image/webp">' +
                    '<img src="' + Config.icons.telegram + '" alt="Telegram"></picture></a>';
        }

        return html;
    }
};
