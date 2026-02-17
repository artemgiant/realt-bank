/**
 * Обработчики событий модуля контактов
 * Объект доступен через window.ContactModal.Handlers
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Handlers = {

    // Debounced функция поиска
    _debouncedSearch: null,

    /**
     * Инициализация обработчиков модального окна
     */
    initModalHandlers: function() {
        var self = this;
        var Config = window.ContactModal.Config;
        var Components = window.ContactModal.Components;
        var Form = window.ContactModal.Form;

        var modal = document.querySelector(Config.selectors.modal);
        if (!modal) return;

        // При открытии модалки
        modal.addEventListener('shown.bs.modal', function() {
            setTimeout(function() {
                Components.initAll();

                Form.modalComponentsReady = true;
                if (Form.isEditMode && Form.pendingContactData) {
                    // Заполняем форму только после инициализации компонентов (intl-tel-input, маска)
                    setTimeout(function() {
                        Form.fill(Form.pendingContactData);
                        Form.showFoundIndicator();
                        Form.pendingContactData = null;
                    }, 150);
                } else if (!Form.isEditMode) {
                    Form.clear();
                }
            }, 300);
        });

        // При закрытии модалки
        modal.addEventListener('hidden.bs.modal', function() {
            Components.destroyAll();
            Form.isEditMode = false;
            Form.pendingContactData = null;
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

        // Создаем debounced функцию поиска
        this._debouncedSearch = Utils.debounce(function(phone) {
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

        // Обработчик ввода телефона
        document.addEventListener('input', function(e) {
            if (e.target.matches(Config.selectors.modal + ' ' + Config.selectors.phoneInput)) {
                // Только для первого телефона делаем поиск
                var firstPhoneInput = document.querySelector(Config.selectors.modal + ' ' + Config.selectors.phoneInput);
                if (e.target === firstPhoneInput && !Form.isEditMode) {
                    self._debouncedSearch(e.target.value);
                }
            }
        });
    },

    /**
     * Валидация одного телефона
     * @param {HTMLElement} input - элемент input
     * @returns {Object} - { isValid: boolean, message: string }
     */
    validateSinglePhone: function(input) {
        var value = input.value || '';
        var digits = value.replace(/\D/g, '');

        if (!digits) {
            return { isValid: true, message: '' };
        }

        var iti = input._iti;
        var countryData = iti ? iti.getSelectedCountryData() : null;
        var countryCode = countryData ? countryData.iso2 : 'ua';

        // Для Украины: 9 цифр без начального 0 (формат маски: XX XXX XX XX)
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
     * @param {HTMLElement} input - элемент input
     * @param {string} message - сообщение об ошибке
     */
    showPhoneError: function(input, message) {
        var $input = $(input);
        var $wrapper = $input.closest('.iti');

        // Удаляем предыдущие состояния
        $wrapper.removeClass('is-invalid is-valid');
        $wrapper.find('.phone-validation-icon').remove();

        $wrapper.addClass('is-invalid');

        // Добавляем иконку ошибки с tooltip
        var $icon = $('<span class="phone-validation-icon phone-validation-icon--error" title="' + message + '">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<circle cx="12" cy="12" r="10"></circle>' +
            '<line x1="12" y1="8" x2="12" y2="12"></line>' +
            '<line x1="12" y1="16" x2="12.01" y2="16"></line>' +
            '</svg></span>');
        $wrapper.append($icon);

        // Инициализируем Bootstrap tooltip
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip($icon[0], { placement: 'top' });
        }
    },

    /**
     * Очистить ошибку на поле телефона
     * @param {HTMLElement} input - элемент input
     */
    clearPhoneError: function(input) {
        var $input = $(input);
        var $wrapper = $input.closest('.iti');

        $wrapper.removeClass('is-invalid is-valid');
        $wrapper.find('.phone-validation-icon').remove();
    },

    /**
     * Валидация телефонов в форме с показом иконок
     * @returns {Array} - Массив ошибок валидации
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

            // Сначала очищаем предыдущие ошибки
            self.clearPhoneError(input);

            if (!digits) {
                if (index === 0) {
                    errors.push('Введите номер телефона');
                    self.showPhoneError(input, 'Введите номер телефона');
                }
                return;
            }

            // Валидируем телефон
            var validation = self.validateSinglePhone(input);

            if (!validation.isValid) {
                errors.push('Телефон ' + (index + 1) + ': ' + validation.message);
                self.showPhoneError(input, validation.message);
                return;
            }

            hasValidPhone = true;
        });

        // Если нет ни одного валидного телефона
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

                // Валидация телефонов
                var validationErrors = [];
                var phoneErrors = self.validatePhones();
                if (phoneErrors.length > 0) {
                    validationErrors = validationErrors.concat(phoneErrors);
                }

                // Валидация ролей
                var rolesVal = $('#contact-role-modal').val();
                if (!rolesVal || rolesVal.length === 0) {
                    validationErrors.push('Выберите роль контакта');
                }

                if (validationErrors.length > 0) {
                    Form.showValidationErrors({ _client: validationErrors });
                    return;
                }

                Form.showLoading();

                var formData = Api.prepareFormData(form);

                // Редактирование: обновляем контакт на сервере и в списке
                if (Form.currentContactId) {
                    Api.update(Form.currentContactId, formData)
                        .then(function(data) {
                            if (data.success && data.contact) {
                                ContactList.update(data.contact);
                                var modalEl = document.querySelector(Config.selectors.modal);
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                                Form.clear();
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

                // Создаем нового контакта
                Api.store(formData)
                    .then(function(data) {
                        if (data.success) {
                            var added = ContactList.add(data.contact);

                            if (added !== false) {
                                var modalEl = document.querySelector(Config.selectors.modal);
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();

                                Form.clear();
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

                    // Загружаем данные контакта
                    Api.show(contactId).then(function(data) {
                        if (data.success) {
                            // Сохраняем данные для заполнения после открытия модалки
                            Form.pendingContactData = data.contact;

                            // Если модалка уже открыта и компоненты инициализированы - заполняем сразу
                            if (Form.modalComponentsReady) {
                                Form.fill(data.contact);
                                Form.showFoundIndicator();
                                Form.pendingContactData = null;
                            }
                            // Иначе данные будут заполнены в обработчике shown.bs.modal
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
