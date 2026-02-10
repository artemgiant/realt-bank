/**
 * Работа с формой модуля контактов
 * Объект доступен через window.ContactModal.Form
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Form = {

    // ID текущего контакта (найденного/редактируемого)
    currentContactId: null,

    // Режим редактирования
    isEditMode: false,

    // Данные контакта для заполнения после открытия модалки (редактирование)
    pendingContactData: null,

    // Флаг: компоненты модалки (intl-tel-input, маска) инициализированы
    modalComponentsReady: false,

    /**
     * Заполнение формы данными контакта
     * @param {Object} contact - Данные контакта
     */
    fill: function(contact) {
        var Utils = window.ContactModal.Utils;
        var Config = window.ContactModal.Config;
        var form = document.querySelector(Config.selectors.form);

        if (!form) return;

        // Устанавливаем ID контакта
        Utils.setInputValue(Config.selectors.contactIdInput, contact.id);

        // Заполняем текстовые поля
        Utils.setInputValue('#first-name-contact-modal', contact.first_name);
        Utils.setInputValue('#last-name-contact-modal', contact.last_name);
        Utils.setInputValue('#middle-name-contact-modal', contact.middle_name);
        Utils.setInputValue('#email-contact-modal', contact.email);
        Utils.setInputValue('#comment-contact-modal', contact.comment);
        Utils.setInputValue('#telegram-contact-modal', contact.telegram);
        Utils.setInputValue('#viber-contact-modal', contact.viber);
        Utils.setInputValue('#whatsapp-contact-modal', contact.whatsapp);
        Utils.setInputValue('#passport-contact-modal', contact.passport);
        Utils.setInputValue('#inn-contact-modal', contact.inn);

        // Заполняем телефоны с учётом кода страны и маски (intl-tel-input + PhoneInputManager)
        var phoneInputs = form.querySelectorAll(Config.selectors.phoneInput);
        var phones = contact.phones && contact.phones.length ? contact.phones : (contact.primary_phone ? [{ phone: contact.primary_phone }] : []);
        phones.forEach(function(phoneObj, index) {
            var raw = (phoneObj && (phoneObj.phone || phoneObj.number)) ? (phoneObj.phone || phoneObj.number) : '';
            if (!raw || !phoneInputs[index]) return;
            var input = phoneInputs[index];
            var phoneE164 = (raw + '').trim().replace(/\s/g, '');
            if (phoneE164 && phoneE164.charAt(0) !== '+') {
                phoneE164 = '+' + phoneE164;
            }
            if (input._iti) {
                var iti = input._iti;
                iti.setNumber(phoneE164);
                // Для маски нужен национальный номер (только цифры), иначе отображается "+380502345332" без форматирования
                var countryData = iti.getSelectedCountryData();
                var dialCode = (countryData && countryData.dialCode) ? String(countryData.dialCode).replace(/\D/g, '') : '';
                var digits = phoneE164.replace(/\D/g, '');
                var nationalDigits = dialCode.length && digits.indexOf(dialCode) === 0
                    ? digits.slice(dialCode.length)
                    : digits.slice(-9);
                if (nationalDigits.length === 8 && countryData && countryData.iso2 === 'ua') {
                    nationalDigits = '0' + nationalDigits;
                }
                input.value = nationalDigits;
                if (typeof $ !== 'undefined') {
                    $(input).trigger('countrychange');
                    $(input).trigger('input');
                } else {
                    input.dispatchEvent(new Event('countrychange', { bubbles: true }));
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            } else {
                input.value = phoneE164 || raw;
            }
        });

        // Заполняем select2
        if (contact.contact_type) {
            $('#type-contact-modal').val(contact.contact_type).trigger('change');
        }
        if (contact.roles && contact.roles.length) {
            $('#roles-contact-modal').val(contact.roles).trigger('change');
        } else {
            $('#roles-contact-modal').val(null).trigger('change');
        }
        if (contact.tags) {
            $('#tags-client-modal').val(contact.tags).trigger('change');
        }
    },

    /**
     * Очистка формы
     */
    clear: function() {
        var Config = window.ContactModal.Config;
        var form = document.querySelector(Config.selectors.form);

        if (form) {
            form.reset();
        }

        // Очищаем hidden поля
        var contactIdInput = document.querySelector(Config.selectors.contactIdInput);
        if (contactIdInput) {
            contactIdInput.value = '';
        }

        // Сбрасываем select2
        $('#type-contact-modal').val('').trigger('change');
        $('#roles-contact-modal').val(null).trigger('change');
        $('#tags-client-modal').val('').trigger('change');

        // Скрываем индикатор
        this.hideFoundIndicator();

        // Сбрасываем состояние
        this.currentContactId = null;
        this.isEditMode = false;
    },

    /**
     * Показать индикатор найденного контакта
     */
    showFoundIndicator: function() {
        var Config = window.ContactModal.Config;
        var indicator = document.querySelector(Config.selectors.foundIndicator);
        if (indicator) {
            indicator.classList.remove('d-none');
        }
    },

    /**
     * Скрыть индикатор найденного контакта
     */
    hideFoundIndicator: function() {
        var Config = window.ContactModal.Config;
        var indicator = document.querySelector(Config.selectors.foundIndicator);
        if (indicator) {
            indicator.classList.add('d-none');
        }
    },

    /**
     * Получение данных контакта из формы (для существующего контакта)
     * @returns {Object}
     */
    getExistingContactData: function() {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;
        var Api = window.ContactModal.Api;

        var phones = Api.collectPhones();
        var contactType = Utils.getInputValue('#type-contact-modal');

        return {
            id: this.currentContactId,
            full_name: (Utils.getInputValue('#last-name-contact-modal') + ' ' +
                       Utils.getInputValue('#first-name-contact-modal')).trim(),
            primary_phone: phones[0] ? phones[0].phone : '',
            contact_type: contactType,
            contact_type_name: Config.contactTypes[contactType] || '-',
            messengers: this.getMessengersFromForm(),
            telegram: Utils.getInputValue('#telegram-contact-modal'),
            viber: Utils.getInputValue('#viber-contact-modal'),
            whatsapp: Utils.getInputValue('#whatsapp-contact-modal')
        };
    },

    /**
     * Получение мессенджеров из формы
     * @returns {Array<string>}
     */
    getMessengersFromForm: function() {
        var Utils = window.ContactModal.Utils;
        var messengers = [];

        if (Utils.getInputValue('#whatsapp-contact-modal')) messengers.push('whatsapp');
        if (Utils.getInputValue('#viber-contact-modal')) messengers.push('viber');
        if (Utils.getInputValue('#telegram-contact-modal')) messengers.push('telegram');

        return messengers;
    },

    /**
     * Показать спиннер на кнопке сохранения
     */
    showLoading: function() {
        var Config = window.ContactModal.Config;
        var submitBtn = document.querySelector(Config.selectors.saveBtn);
        var spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
    },

    /**
     * Скрыть спиннер на кнопке сохранения
     */
    hideLoading: function() {
        var Config = window.ContactModal.Config;
        var submitBtn = document.querySelector(Config.selectors.saveBtn);
        var spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

        if (submitBtn) submitBtn.disabled = false;
        if (spinner) spinner.classList.add('d-none');
    },

    /**
     * Отображение ошибок валидации
     * @param {Object} errors - Объект ошибок
     */
    showValidationErrors: function(errors) {
        var errorMessage = 'Ошибки:\n';

        Object.values(errors).forEach(function(messages) {
            if (Array.isArray(messages)) {
                errorMessage += messages.join('\n') + '\n';
            } else {
                errorMessage += messages + '\n';
            }
        });

        alert(errorMessage);
    }
};
