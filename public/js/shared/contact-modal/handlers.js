/**
 * Обработчики событий модуля контактов (единые для всех сущностей)
 * Поведение определяется Config.behavior
 * Объект доступен через window.ContactModal.Handlers
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Handlers = {

    _debouncedSearch: null,

    /**
     * Инициализация обработчиков модального окна
     */
    initModalHandlers: function() {
        var Config = window.ContactModal.Config;
        var Components = window.ContactModal.Components;
        var Form = window.ContactModal.Form;

        var modal = document.querySelector(Config.selectors.modal);
        if (!modal) return;

        // Очищаем форму ДО начала анимации открытия (чтобы не было видно старых данных)
        modal.addEventListener('show.bs.modal', function() {
            if (!Form.isEditMode) {
                Form.clear();
            }
        });

        // После открытия — инициализируем компоненты и заполняем данные (если edit mode)
        modal.addEventListener('shown.bs.modal', function() {
            setTimeout(function() {
                Components.initAll();

                if (Config.behavior.hasPendingContactData) {
                    Form.modalComponentsReady = true;
                    if (Form.isEditMode && Form.pendingContactData) {
                        setTimeout(function() {
                            Form.fill(Form.pendingContactData);
                            Form.showFoundIndicator();
                            Form.pendingContactData = null;
                        }, 150);
                    }
                }
            }, 300);
        });

        // При закрытии модалки — очищаем форму и уничтожаем компоненты
        modal.addEventListener('hidden.bs.modal', function() {
            Form.clear();
            Components.destroyAll();
            Form.modalComponentsReady = false;
        });
    },

    /**
     * Инициализация поиска по телефону
     */
    initPhoneSearch: function() {
        var self = this;
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;
        var Api = window.ContactModal.Api;
        var Form = window.ContactModal.Form;

        this._debouncedSearch = Utils.debounce(function(phone) {
            if (Form.isEditMode) return;
            Api.searchByPhone(phone).then(function(data) {
                if (data.success && data.found) {
                    Form.fill(data.contact);
                    Form.showFoundIndicator();
                    Form.currentContactId = data.contact.id;
                } else {
                    Form.hideFoundIndicator();
                    Form.currentContactId = null;
                }
            });
        }, Config.debounceDelay);

        document.addEventListener('input', function(e) {
            if (e.target.matches(Config.selectors.modal + ' ' + Config.selectors.phoneInput)) {
                var firstPhoneInput = document.querySelector(Config.selectors.modal + ' ' + Config.selectors.phoneInput);
                if (e.target === firstPhoneInput && !Form.isEditMode) {
                    self._debouncedSearch(e.target.value);
                }
            }
        });
    },

    /**
     * Валидация одного телефона
     */
    validateSinglePhone: function(input) {
        var value = input.value || '';
        var digits = value.replace(/\D/g, '');

        if (!digits) return { isValid: true, message: '' };

        var iti = input._iti;
        var countryData = iti ? iti.getSelectedCountryData() : null;
        var countryCode = countryData ? countryData.iso2 : 'ua';

        // UA: 9 цифр без початкового 0 (формат маски: XX XXX XX XX)
        var phoneLengths = {
            'ua': { min: 9, max: 9 },
            'us': { min: 10, max: 10 },
            'gb': { min: 10, max: 11 },
            'de': { min: 10, max: 12 },
            'pl': { min: 9, max: 9 },
            'default': { min: 7, max: 15 }
        };

        var lengths = phoneLengths[countryCode] || phoneLengths['default'];

        if (digits.length < lengths.min) {
            return { isValid: false, message: 'Минимум ' + lengths.min + ' цифр' };
        }

        if (digits.length > lengths.max) {
            return { isValid: false, message: 'Максимум ' + lengths.max + ' цифр' };
        }

        return { isValid: true, message: '' };
    },

    /**
     * Показать иконку ошибки на поле телефона
     */
    showPhoneError: function(input, message) {
        var $input = $(input);
        var $wrapper = $input.closest('.iti');

        $wrapper.removeClass('is-invalid is-valid');
        $wrapper.find('.phone-validation-icon').remove();
        $wrapper.addClass('is-invalid');

        var $icon = $('<span class="phone-validation-icon phone-validation-icon--error" title="' + message + '">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<circle cx="12" cy="12" r="10"></circle>' +
            '<line x1="12" y1="8" x2="12" y2="12"></line>' +
            '<line x1="12" y1="16" x2="12.01" y2="16"></line>' +
            '</svg></span>');
        $wrapper.append($icon);

        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip($icon[0], { placement: 'top' });
        }
    },

    /**
     * Очистить ошибку на поле телефона
     */
    clearPhoneError: function(input) {
        var $input = $(input);
        var $wrapper = $input.closest('.iti');

        $wrapper.removeClass('is-invalid is-valid');
        $wrapper.find('.phone-validation-icon').remove();
    },

    /**
     * Валидация телефонов в форме
     */
    validatePhones: function() {
        var self = this;
        var Config = window.ContactModal.Config;
        var errors = [];
        var phoneInputs = document.querySelectorAll(Config.selectors.modal + ' ' + Config.selectors.phoneInput);
        var hasValidPhone = false;

        phoneInputs.forEach(function(input, index) {
            var value = input.value || '';
            var digits = value.replace(/\D/g, '');

            self.clearPhoneError(input);

            if (!digits) {
                if (index === 0) {
                    errors.push('Введите номер телефона');
                    self.showPhoneError(input, 'Введите номер телефона');
                }
                return;
            }

            var validation = self.validateSinglePhone(input);

            if (!validation.isValid) {
                errors.push('Телефон ' + (index + 1) + ': ' + validation.message);
                self.showPhoneError(input, validation.message);
                return;
            }

            hasValidPhone = true;
        });

        if (!hasValidPhone && errors.length === 0) {
            errors.push('Введите хотя бы один номер телефона');
        }

        return errors;
    },

    /**
     * Инициализация отправки формы
     */
    initFormSubmit: function() {
        var self = this;
        var Config = window.ContactModal.Config;
        var Api = window.ContactModal.Api;
        var Form = window.ContactModal.Form;
        var ContactList = window.ContactModal.ContactList;

        document.addEventListener('submit', function(e) {
            if (e.target.matches(Config.selectors.form)) {
                e.preventDefault();

                var form = e.target;
                var validationErrors = [];

                // Валидация телефонов
                var phoneErrors = self.validatePhones();
                if (phoneErrors.length > 0) {
                    validationErrors = validationErrors.concat(phoneErrors);
                }

                // Валидация ролей (если requireRoles = true)
                if (Config.behavior.requireRoles) {
                    var rolesVal = $(Config.selectors.contactRoleSelect).val();
                    if (!rolesVal || rolesVal.length === 0) {
                        validationErrors.push('Выберите роли контакта');
                    }
                }

                if (validationErrors.length > 0) {
                    Form.showValidationErrors({ _client: validationErrors });
                    return;
                }

                Form.showLoading();

                // Существующий контакт
                if (Form.currentContactId) {
                    // skipApiForExisting: просто берём данные из формы без API вызова
                    if (Config.behavior.skipApiForExisting) {
                        var contactData = Form.getExistingContactData();
                        var added = ContactList.add(contactData);
                        if (added !== false) {
                            var modalEl = document.querySelector(Config.selectors.modal);
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        Form.hideLoading();
                        return;
                    }

                    // Обычное поведение: обновляем через API
                    var formData = Api.prepareFormData(form);
                    Api.update(Form.currentContactId, formData)
                        .then(function(data) {
                            if (data.success && data.contact) {
                                if (ContactList.hasContact(data.contact.id)) {
                                    ContactList.update(data.contact);
                                } else {
                                    ContactList.add(data.contact);
                                }
                                var modalEl = document.querySelector(Config.selectors.modal);
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                            }
                        })
                        .catch(function(error) {
                            console.error('Ошибка:', error);
                            if (error.response && error.response.errors) {
                                Form.showValidationErrors(error.response.errors);
                            } else {
                                alert(error.response ? error.response.message : 'Произошла ошибка при сохранении');
                            }
                        })
                        .finally(function() {
                            Form.hideLoading();
                        });
                    return;
                }

                // Создание нового контакта
                var formData = Api.prepareFormData(form);
                Api.store(formData)
                    .then(function(data) {
                        if (data.success) {
                            var added = ContactList.add(data.contact);
                            if (added !== false) {
                                var modalEl = document.querySelector(Config.selectors.modal);
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                            }
                        }
                    })
                    .catch(function(error) {
                        console.error('Ошибка:', error);
                        if (error.response && error.response.errors) {
                            Form.showValidationErrors(error.response.errors);
                        } else {
                            alert(error.response ? error.response.message : 'Произошла ошибка при сохранении');
                        }
                    })
                    .finally(function() {
                        Form.hideLoading();
                    });
            }
        });
    },

    /**
     * Инициализация удаления контакта
     */
    initRemoveContact: function() {
        var ContactList = window.ContactModal.ContactList;

        document.addEventListener('click', function(e) {
            var removeBtn = e.target.closest('[data-remove-contact]');
            if (removeBtn) {
                var card = removeBtn.closest('.contact-card');
                var contactId = card ? card.getAttribute('data-contact-id') : null;

                if (contactId && confirm('Удалить контакт из списка?')) {
                    ContactList.remove(contactId);
                }
            }
        });
    },

    /**
     * Инициализация редактирования контакта
     */
    initEditContact: function() {
        var Config = window.ContactModal.Config;
        var Api = window.ContactModal.Api;
        var Form = window.ContactModal.Form;

        document.addEventListener('click', function(e) {
            var editBtn = e.target.closest('[data-edit-contact]');
            if (editBtn) {
                var card = editBtn.closest('.contact-card');
                var contactId = card ? card.getAttribute('data-contact-id') : null;

                if (contactId) {
                    Form.isEditMode = true;
                    Form.currentContactId = contactId;

                    Api.show(contactId).then(function(data) {
                        if (data.success) {
                            if (Config.behavior.hasPendingContactData) {
                                Form.pendingContactData = data.contact;
                                if (Form.modalComponentsReady) {
                                    setTimeout(function() {
                                        Form.fill(Form.pendingContactData);
                                        Form.showFoundIndicator();
                                        Form.pendingContactData = null;
                                    }, 100);
                                }
                            } else {
                                setTimeout(function() {
                                    Form.fill(data.contact);
                                    Form.showFoundIndicator();
                                }, 400);
                            }
                        }
                    });
                }
            }
        });
    },

    /**
     * Инициализация всех обработчиков
     */
    initAll: function() {
        this.initModalHandlers();
        this.initPhoneSearch();
        this.initFormSubmit();
        this.initRemoveContact();
        this.initEditContact();
    }
};
