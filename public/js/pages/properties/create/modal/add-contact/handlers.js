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

        // Создаем debounced функцию поиска (только при создании контакта, не при редактировании)
        this._debouncedSearch = Utils.debounce(function(phone) {
            if (Form.isEditMode) return; // при редактировании не меняем контакт по поиску телефона
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
                var firstPhoneInput = document.querySelector(Config.selectors.modal + ' ' + Config.selectors.phoneInput);
                if (e.target === firstPhoneInput && !Form.isEditMode) {
                    self._debouncedSearch(e.target.value);
                }
            }
        });
    },

    /**
     * Инициализация отправки формы
     */
    initFormSubmit: function() {
        var Config = window.ContactModal.Config;
        var Api = window.ContactModal.Api;
        var Form = window.ContactModal.Form;
        var ContactList = window.ContactModal.ContactList;

        document.addEventListener('submit', function(e) {
            if (e.target.matches(Config.selectors.form)) {
                e.preventDefault();

                var form = e.target;
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

                // Создание: создаём нового контакта
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

                    // Загружаем данные контакта; форма заполнится после initAll() (в shown.bs.modal или здесь, если компоненты уже готовы)
                    Api.show(contactId).then(function(data) {
                        if (data.success) {
                            Form.pendingContactData = data.contact;
                            if (Form.modalComponentsReady) {
                                setTimeout(function() {
                                    Form.fill(Form.pendingContactData);
                                    Form.showFoundIndicator();
                                    Form.pendingContactData = null;
                                }, 100);
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
