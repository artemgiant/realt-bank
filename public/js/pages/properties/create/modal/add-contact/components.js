/**
 * Инициализация компонентов модуля контактов
 * Объект доступен через window.ContactModal.Components
 */
window.ContactModal = window.ContactModal || {};

window.ContactModal.Components = {

    // Экземпляр PhoneInputManager
    phoneManager: null,

    // Флаг инициализации Select2
    select2Initialized: false,

    /**
     * Инициализация PhoneInputManager
     */
    initPhoneInputManager: function() {
        if (this.phoneManager) return;

        var btnSelector = '.btn-new-tel';
        var wrapperSelector = '#add-contact-modal .modal-row .item.phone';

        if (!document.querySelector(btnSelector)) return;

        // Проверяем наличие класса PhoneInputManager
        if (typeof PhoneInputManager === 'undefined') {
            console.warn('PhoneInputManager не найден');
            return;
        }

        try {
            this.phoneManager = new PhoneInputManager({
                btnSelector: btnSelector,
                wrapperSelector: wrapperSelector,
                inputClass: 'tel-contact',
                maxPhones: window.ContactModal.Config.maxPhones,
                initialCountry: 'ua',
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js',
                countryMasks: {
                    'ua': '(99) 999-99-99',
                    'us': '(999) 999-9999',
                    'gb': '9999 999999',
                    'de': '999 99999999',
                    'fr': '9 99-99-99-99',
                    'pl': '999 999-999',
                    'it': '999 999-9999',
                    'es': '999 99-99-99',
                    'default': '(999) 999-99-99'
                }
            });
        } catch (error) {
            console.error('Ошибка инициализации PhoneInputManager:', error);
        }
    },

    /**
     * Инициализация Select2
     */
    initSelect2: function() {
        if (this.select2Initialized) return;

        var select2Configs = [
            {
                selector: '#tags-client-modal',
                options: {
                    dropdownParent: $('#add-contact-modal'),
                    width: '100%',
                    placeholder: 'Выберите тег',
                    allowClear: true,
                    language: { noResults: function() { return "Результатов не найдено"; } }
                }
            },
            {
                selector: '#type-contact-modal',
                options: {
                    dropdownParent: $('#add-contact-modal'),
                    width: '100%',
                    placeholder: 'Выберите тип',
                    allowClear: true,
                    language: { noResults: function() { return "Результатов не найдено"; } }
                }
            }
        ];

        try {
            select2Configs.forEach(function(config) {
                var $el = $(config.selector);
                if ($el.length) {
                    // Уничтожаем если уже инициализирован
                    if ($el.data('select2')) {
                        $el.select2('destroy');
                    }
                    $el.select2(config.options);

                    // Фокус на поле поиска при открытии
                    $el.on('select2:open', function() {
                        setTimeout(function() {
                            var searchField = document.querySelector('.select2-search__field');
                            if (searchField) searchField.focus();
                        }, 100);
                    });
                }
            });

            this.select2Initialized = true;
        } catch (error) {
            console.error('Ошибка инициализации Select2:', error);
        }
    },

    /**
     * Инициализация PhotoLoader
     */
    initPhotoLoader: function() {
        var modalElement = document.getElementById('add-contact-modal');
        if (!modalElement) return;

        // Проверяем наличие класса PhotoLoaderMini
        if (typeof PhotoLoaderMini === 'undefined') {
            console.warn('PhotoLoaderMini не найден');
            return;
        }

        try {
            new PhotoLoaderMini({
                inputIdSelector: '#loading-photo-contact-modal',
                wrapperClassSelector: '.photo-info-list',
                context: modalElement
            });
        } catch (error) {
            console.error('Ошибка инициализации PhotoLoaderMini:', error);
        }
    },

    /**
     * Инициализация DateRangePicker
     */
    initDatePicker: function() {
        var $datepicker = $('#datapiker-contact-modal');
        if (!$datepicker.length) return;

        try {
            $datepicker.daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'DD-MM-YYYY',
                    separator: ' - ',
                    applyLabel: 'Применить',
                    cancelLabel: 'Отмена',
                    weekLabel: 'Н',
                    daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                    firstDay: 1
                },
                drops: 'auto'
            });
        } catch (error) {
            console.error('Ошибка инициализации DateRangePicker:', error);
        }
    },

    /**
     * Инициализация всех компонентов
     */
    initAll: function() {
        this.initPhoneInputManager();
        this.initSelect2();
        this.initPhotoLoader();
        this.initDatePicker();
    },

    /**
     * Уничтожение компонентов при закрытии модалки
     */
    destroyAll: function() {
        // Уничтожаем PhoneInputManager
        if (this.phoneManager && typeof this.phoneManager.destroy === 'function') {
            this.phoneManager.destroy();
            this.phoneManager = null;
        }

        // Уничтожаем Select2
        ['#tags-client-modal', '#type-contact-modal'].forEach(function(selector) {
            var $el = $(selector);
            if ($el.data('select2')) {
                $el.select2('destroy');
            }
        });

        this.select2Initialized = false;
    }
};
