/**
 * Утилиты модуля контактов
 * Объект доступен через window.ContactModal.Utils
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Utils = {

    // Таймер для debounce
    _debounceTimer: null,

    /**
     * Debounce функция
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
     */
    cleanPhoneNumber: function(phone) {
        return phone.replace(/[^0-9+]/g, '');
    },

    /**
     * Форматирование телефона с кодом страны.
     * Украинские номера: +38 (0XX) XXX-XX-XX
     * Остальные: E.164
     */
    formatPhoneWithCountryCode: function(phone, inputElement) {
        var digits, countryData, countryCode;

        if (inputElement && inputElement._iti) {
            var iti = inputElement._iti;
            countryData = iti.getSelectedCountryData();
            countryCode = countryData ? countryData.iso2 : null;
            digits = (phone || '').replace(/\D/g, '');

            // Украинский номер — формат +38 (0XX) XXX-XX-XX
            if (countryCode === 'ua' && digits.length === 10 && digits.charAt(0) === '0') {
                var areaCode = digits.substring(1, 3);
                var p1 = digits.substring(3, 6);
                var p2 = digits.substring(6, 8);
                var p3 = digits.substring(8, 10);
                return '+38 (0' + areaCode + ') ' + p1 + '-' + p2 + '-' + p3;
            }

            // Для остальных стран — E.164 через intl-tel-input
            var fullNumber = iti.getNumber();
            if (fullNumber) return fullNumber;
        }

        // Fallback без intl-tel-input
        var cleaned = (phone || '').trim();
        digits = cleaned.replace(/\D/g, '');

        if (cleaned.startsWith('+')) {
            return '+' + digits;
        }

        // Локальный украинский номер
        if (digits.startsWith('0') && digits.length === 10) {
            var sub = digits.substring(1);
            var ac = sub.substring(0, 2);
            var s1 = sub.substring(2, 5);
            var s2 = sub.substring(5, 7);
            var s3 = sub.substring(7, 9);
            return '+38 (0' + ac + ') ' + s1 + '-' + s2 + '-' + s3;
        }

        return '+380' + digits;
    },

    /**
     * Получение CSRF токена
     */
    getCsrfToken: function() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    },

    /**
     * Установка значения input элемента
     */
    setInputValue: function(selector, value) {
        var input = document.querySelector(selector);
        if (input) {
            input.value = value || '';
        }
    },

    /**
     * Получение значения input элемента
     */
    getInputValue: function(selector) {
        var input = document.querySelector(selector);
        return input ? input.value : '';
    },

    /**
     * Показать элемент (убрать класс d-none)
     */
    show: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        if (el) el.classList.remove('d-none');
    },

    /**
     * Скрыть элемент (добавить класс d-none)
     */
    hide: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        if (el) el.classList.add('d-none');
    },

    /**
     * Проверка видимости элемента
     */
    isVisible: function(element) {
        var el = typeof element === 'string' ? document.querySelector(element) : element;
        return el && !el.classList.contains('d-none');
    }
};
