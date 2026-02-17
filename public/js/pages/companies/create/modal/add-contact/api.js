/**
 * Работа с API модуля контактов
 * Объект доступен через window.ContactModal.Api
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Api = {

    /**
     * Поиск контакта по номеру телефона
     * @param {string} phone - Номер телефона
     * @returns {Promise<Object>}
     */
    searchByPhone: function(phone) {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;

        var cleanPhone = Utils.cleanPhoneNumber(phone);

        if (cleanPhone.length < Config.searchMinLength) {
            return Promise.resolve({
                success: false,
                found: false,
                message: 'Номер телефона слишком короткий'
            });
        }

        var url = Config.urls.searchByPhone + '?phone=' + encodeURIComponent(phone);

        return fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .catch(function(error) {
            console.error('Ошибка поиска контакта по телефону:', error);
            return {
                success: false,
                found: false,
                message: 'Ошибка при поиске'
            };
        });
    },

    /**
     * Создание нового контакта
     * @param {FormData} formData - Данные формы
     * @returns {Promise<Object>}
     */
    store: function(formData) {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;

        return fetch(Config.urls.store, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': Utils.getCsrfToken()
            },
            body: formData
        })
        .then(function(response) {
            return response.json().then(function(data) {
                if (!response.ok) {
                    throw { response: data, status: response.status };
                }
                return data;
            });
        });
    },

    /**
     * Получение данных контакта по ID
     * @param {number|string} contactId - ID контакта
     * @returns {Promise<Object>}
     */
    show: function(contactId) {
        var Config = window.ContactModal.Config;
        var url = Config.urls.show.replace('{id}', contactId);

        return fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .catch(function(error) {
            console.error('Ошибка получения данных контакта:', error);
            return {
                success: false,
                message: 'Ошибка при загрузке данных контакта'
            };
        });
    },

    /**
     * Обновление контакта
     * @param {number|string} contactId - ID контакта
     * @param {FormData} formData - Данные формы
     * @returns {Promise<Object>}
     */
    update: function(contactId, formData) {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;
        var url = Config.urls.update.replace('{id}', contactId);

        // Добавляем _method для PUT запроса
        formData.append('_method', 'PUT');

        return fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': Utils.getCsrfToken()
            },
            body: formData
        })
        .then(function(response) {
            return response.json().then(function(data) {
                if (!response.ok) {
                    throw { response: data, status: response.status };
                }
                return data;
            });
        });
    },

    /**
     * Сбор телефонов из формы
     * @returns {Array<Object>}
     */
    collectPhones: function() {
        var Config = window.ContactModal.Config;
        var Utils = window.ContactModal.Utils;
        var phones = [];

        document.querySelectorAll(Config.selectors.modal + ' ' + Config.selectors.phoneInput).forEach(function(input, index) {
            var phone = input.value.trim();
            if (phone) {
                phones.push({
                    phone: Utils.formatPhoneWithCountryCode(phone),
                    is_primary: index === 0
                });
            }
        });

        return phones;
    },

    /**
     * Подготовка FormData с телефонами
     * @param {HTMLFormElement} form - Элемент формы
     * @returns {FormData}
     */
    prepareFormData: function(form) {
        var formData = new FormData(form);
        var phones = this.collectPhones();

        // Удаляем старые поля телефонов
        var keysToDelete = [];
        for (var pair of formData.entries()) {
            if (pair[0].startsWith('phones[')) {
                keysToDelete.push(pair[0]);
            }
        }
        keysToDelete.forEach(function(key) {
            formData.delete(key);
        });

        // Добавляем телефоны
        phones.forEach(function(phone, index) {
            formData.append('phones[' + index + '][phone]', phone.phone);
            formData.append('phones[' + index + '][is_primary]', phone.is_primary ? '1' : '0');
        });

        return formData;
    }
};
