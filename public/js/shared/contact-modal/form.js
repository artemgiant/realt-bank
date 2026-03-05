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

    // Флаг: компоненты модалки инициализированы
    modalComponentsReady: false,

    /**
     * Заполнение формы данными контакта
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
        Utils.setInputValue('#tiktok-contact-modal', contact.tiktok);
        Utils.setInputValue('#instagram-contact-modal', contact.instagram);
        Utils.setInputValue('#facebook-contact-modal', contact.facebook);

        // Заполняем телефоны с определением страны по dial code
        if (Config.behavior.phoneDialCodeMapping) {
            this._fillPhones(contact, form);
        }

        // Заполняем select2 для ролей (единый селектор)
        var roles = contact.roles || contact.contact_role || [];
        if (roles && roles.length) {
            $(Config.selectors.contactRoleSelect).val(roles).trigger('change');
        } else {
            $(Config.selectors.contactRoleSelect).val(null).trigger('change');
        }

        // Теги
        if (contact.tags) {
            $('#tags-client-modal').val(contact.tags).trigger('change');
        }
    },

    /**
     * Заполнение телефонных полей с dial code mapping
     * @private
     */
    _fillPhones: function(contact, form) {
        var Config = window.ContactModal.Config;
        var phoneInputs = form.querySelectorAll(Config.selectors.phoneInput);
        var phones = contact.phones && contact.phones.length
            ? contact.phones
            : (contact.primary_phone ? [{ phone: contact.primary_phone }] : []);

        // Маппинг dial code -> ISO2
        var dialCodeMap = [
            ['380','ua'],['375','by'],['373','md'],['374','am'],['371','lv'],['370','lt'],['372','ee'],
            ['995','ge'],['994','az'],['993','tm'],['992','tj'],['998','uz'],['996','kg'],['971','ae'],['972','il'],
            ['49','de'],['48','pl'],['47','no'],['46','se'],['45','dk'],['44','gb'],
            ['43','at'],['42','cz'],['41','ch'],['40','ro'],['39','it'],['38','ba'],
            ['36','hu'],['35','pt'],['34','es'],['33','fr'],['32','be'],['31','nl'],['30','gr'],
            ['90','tr'],['86','cn'],['82','kr'],['81','jp'],['61','au'],['55','br'],
            ['1','us'],['7','ru']
        ];

        phones.forEach(function(phoneObj, index) {
            var raw = (phoneObj && (phoneObj.phone || phoneObj.number)) ? (phoneObj.phone || phoneObj.number) : '';
            if (!raw || !phoneInputs[index]) return;
            var input = phoneInputs[index];
            var $input = $(input);
            var phoneClean = (raw + '').trim().replace(/\s/g, '');
            var digits = phoneClean.replace(/\D/g, '');

            // Определяем страну и национальные цифры
            var countryIso2 = 'ua';
            var dcLen = 0;
            for (var i = 0; i < dialCodeMap.length; i++) {
                if (digits.indexOf(dialCodeMap[i][0]) === 0) {
                    countryIso2 = dialCodeMap[i][1];
                    dcLen = dialCodeMap[i][0].length;
                    break;
                }
            }
            var nationalDigits = dcLen ? digits.slice(dcLen) : digits;

            // Для Украины добавляем ведущий 0 к 9-значному абонентскому номеру
            if (countryIso2 === 'ua' && nationalDigits.length === 9) {
                nationalDigits = '0' + nationalDigits;
            }

            if (input._iti) {
                var iti = input._iti;
                $input.unmask();
                iti.setCountry(countryIso2);
                input.value = nationalDigits;

                var countryMasks = {
                    'ua': '(99) 999-99-99',
                    'us': '(999) 999-9999',
                    'gb': '9999 999999',
                    'de': '999 99999999',
                    'fr': '9 99-99-99-99',
                    'pl': '999 999-999',
                    'it': '999 999-9999',
                    'es': '999 99-99-99',
                    'default': '(999) 999-99-99'
                };
                var mask = countryMasks[countryIso2] || countryMasks['default'];
                $input.mask(mask, { clearIfNotMatch: false });
            } else {
                var phoneE164 = phoneClean;
                if (phoneE164 && phoneE164.charAt(0) !== '+') phoneE164 = '+' + phoneE164;
                input.value = phoneE164 || raw;
            }
        });
    },

    /**
     * Очистка формы
     */
    clear: function() {
        var Config = window.ContactModal.Config;
        var form = document.querySelector(Config.selectors.form);

        if (form) form.reset();

        // Очищаем hidden поля
        var contactIdInput = document.querySelector(Config.selectors.contactIdInput);
        if (contactIdInput) contactIdInput.value = '';

        // Сбрасываем select2
        $(Config.selectors.contactRoleSelect).val(null).trigger('change');
        $('#tags-client-modal').val('').trigger('change');

        // Скрываем индикатор
        this.hideFoundIndicator();

        // Сбрасываем состояние
        this.currentContactId = null;
        this.isEditMode = false;
        this.pendingContactData = null;
    },

    /**
     * Показать индикатор найденного контакта
     */
    showFoundIndicator: function() {
        var Config = window.ContactModal.Config;
        var indicator = document.querySelector(Config.selectors.foundIndicator);
        if (indicator) indicator.classList.remove('d-none');
    },

    /**
     * Скрыть индикатор найденного контакта
     */
    hideFoundIndicator: function() {
        var Config = window.ContactModal.Config;
        var indicator = document.querySelector(Config.selectors.foundIndicator);
        if (indicator) indicator.classList.add('d-none');
    },

    /**
     * Получение данных контакта из формы (для существующего контакта без API)
     */
    getExistingContactData: function() {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;
        var Api = window.ContactModal.Api;

        var phones = Api.collectPhones();
        var contactRoles = $(Config.selectors.contactRoleSelect).val() || [];

        return {
            id: this.currentContactId,
            full_name: (Utils.getInputValue('#last-name-contact-modal') + ' ' +
                       Utils.getInputValue('#first-name-contact-modal')).trim(),
            primary_phone: phones[0] ? phones[0].phone : '',
            contact_role: contactRoles,
            contact_role_names: $(Config.selectors.contactRoleSelect + ' option:selected').map(function() {
                return $(this).text();
            }).get().join(', ') || '-',
            roles_names: $(Config.selectors.contactRoleSelect + ' option:selected').map(function() {
                return $(this).text();
            }).get().join(', ') || '-',
            messengers: this.getMessengersFromForm(),
            telegram: Utils.getInputValue('#telegram-contact-modal'),
            viber: Utils.getInputValue('#viber-contact-modal'),
            whatsapp: Utils.getInputValue('#whatsapp-contact-modal')
        };
    },

    /**
     * Получение мессенджеров из формы
     */
    getMessengersFromForm: function() {
        var Utils = window.ContactModal.Utils;
        var messengers = [];

        if (Utils.getInputValue('#whatsapp-contact-modal')) messengers.push('whatsapp');
        if (Utils.getInputValue('#viber-contact-modal')) messengers.push('viber');
        if (Utils.getInputValue('#telegram-contact-modal')) messengers.push('telegram');

        return messengers;
    },

    showLoading: function() {
        var Config = window.ContactModal.Config;
        var submitBtn = document.querySelector(Config.selectors.saveBtn);
        var spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
    },

    hideLoading: function() {
        var Config = window.ContactModal.Config;
        var submitBtn = document.querySelector(Config.selectors.saveBtn);
        var spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

        if (submitBtn) submitBtn.disabled = false;
        if (spinner) spinner.classList.add('d-none');
    },

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
