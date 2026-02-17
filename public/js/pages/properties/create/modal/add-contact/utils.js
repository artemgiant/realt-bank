/**
 * Утилиты модуля контактов
 * Объект доступен через window.ContactModal.Utils
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Utils = {

    // Таймер для debounce
    _debounceTimer: null,

    /**
     * Debounce функция - задержка выполнения
     * @param {Function} func - Функция для выполнения
     * @param {number} wait - Задержка в миллисекундах
     * @returns {Function}
     */
    debounce: function(func, wait) {
        var self = this;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(self._debounceTimer);
            self._debounceTimer = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    },

    /**
     * Очистка номера телефона от лишних символов
     * @param {string} phone - Номер телефона
     * @returns {string}
     */
    cleanPhoneNumber: function(phone) {
        return phone.replace(/[^0-9+]/g, '');
    },

    /**
     * Форматирование телефона с кодом страны
     * @param {string} phone - Номер телефона
     * @param {HTMLElement} [inputElement] - Элемент input с intl-tel-input (опционально)
     * @returns {string}
     */
    formatPhoneWithCountryCode: function(phone, inputElement) {
        // Если есть intl-tel-input, используем его для получения полного номера
        if (inputElement && inputElement._iti) {
            var fullNumber = inputElement._iti.getNumber();
            if (fullNumber) {
                return fullNumber;
            }
        }

        var cleaned = phone.trim();
        // Убираем все не-цифры для анализа
        var digits = cleaned.replace(/\D/g, '');

        if (cleaned.startsWith('+')) {
            // Уже есть код страны
            return '+' + digits;
        }

        // Если номер начинается с 0 (локальный формат), убираем 0 и добавляем +380
        if (digits.startsWith('0')) {
            return '+380' + digits.substring(1);
        }

        // Иначе предполагаем, что это украинский номер без 0
        return '+380' + digits;
    },

    /**
     * Получение CSRF токена
     * @returns {string}
     */
    getCsrfToken: function() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    },

    /**
     * Установка значения input элемента
     * @param {string} selector - CSS селектор
     * @param {string|null} value - Значение
     */
    setInputValue: function(selector, value) {
        var input = document.querySelector(selector);
        if (input) {
            input.value = value || '';
        }
    },

    /**
     * Получение значения input элемента
     * @param {string} selector - CSS селектор
     * @returns {string}
     */
    getInputValue: function(selector) {
        var input = document.querySelector(selector);
        return input ? input.value : '';
    },

    /**
     * Показать элемент (убрать класс d-none)
     * @param {string|HTMLElement} element - Селектор или элемент
     */
    show: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        if (el) {
            el.classList.remove('d-none');
        }
    },

    /**
     * Скрыть элемент (добавить класс d-none)
     * @param {string|HTMLElement} element - Селектор или элемент
     */
    hide: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        if (el) {
            el.classList.add('d-none');
        }
    },

    /**
     * Проверка видимости элемента
     * @param {string|HTMLElement} element - Селектор или элемент
     * @returns {boolean}
     */
    isVisible: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        return el && !el.classList.contains('d-none');
    }
};
