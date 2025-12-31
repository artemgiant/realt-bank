/**
 * Поиск локации через локальную базу данных
 * Одно поле поиска по улицам Одесской области
 *
 * Объект доступен через window.LocationSearch
 */
window.LocationSearch = {

    // ========== Конфигурация ==========
    config: {
        // URL для поиска
        searchUrl: '/location/search',

        // Лимит результатов
        limit: 15,

        // Задержка перед запросом (мс)
        debounceDelay: 300,

        // Минимальная длина запроса
        minQueryLength: 2
    },

    // ========== Состояние ==========
    state: {
        // Выбранная улица
        selectedStreet: null,

        // Таймер debounce
        timer: null,

        // Текущие результаты поиска
        results: [],

        // Активный индекс в dropdown
        activeIndex: -1
    },

    // ========== DOM элементы ==========
    elements: {},

    // ========== Инициализация ==========
    init: function() {
        var wrapper = document.querySelector('.location-search-wrapper');
        if (!wrapper) {
            console.warn('LocationSearch: wrapper not found');
            return;
        }

        this._cacheElements(wrapper);
        this._bindEvents();

        console.log('LocationSearch: initialized');
    },

    // ========== Кэширование DOM элементов ==========
    _cacheElements: function(wrapper) {
        this.elements.wrapper = wrapper;
        this.elements.input = wrapper.querySelector('.location-search-input');
        this.elements.dropdown = wrapper.querySelector('.location-search-dropdown');
        this.elements.clearBtn = wrapper.querySelector('.location-search-clear');

        // Hidden inputs
        this.elements.hidden = {
            streetId: wrapper.querySelector('input[name="street_id"]'),
            streetName: wrapper.querySelector('input[name="street_name"]'),
            zoneId: wrapper.querySelector('input[name="zone_id"]'),
            zoneName: wrapper.querySelector('input[name="zone_name"]'),
            regionId: wrapper.querySelector('input[name="lib_region_id"]'),
            regionName: wrapper.querySelector('input[name="region_name"]'),
            townId: wrapper.querySelector('input[name="town_id"]'),
            townName: wrapper.querySelector('input[name="town_name"]')
        };
    },

    // ========== Привязка событий ==========
    _bindEvents: function() {
        var self = this;

        if (!this.elements.input) return;

        // Ввод текста
        this.elements.input.addEventListener('input', function(e) {
            self._onInput(e.target.value);
        });

        // Навигация клавиатурой
        this.elements.input.addEventListener('keydown', function(e) {
            self._onKeydown(e);
        });

        // Фокус - показать результаты если есть
        this.elements.input.addEventListener('focus', function() {
            if (self.state.results.length > 0) {
                self._openDropdown();
            }
        });

        // Кнопка очистки
        if (this.elements.clearBtn) {
            this.elements.clearBtn.addEventListener('click', function() {
                self._clear();
            });
        }

        // Клик вне - закрыть dropdown
        document.addEventListener('click', function(e) {
            if (!self.elements.wrapper.contains(e.target)) {
                self._closeDropdown();
            }
        });
    },

    // ========== Обработка ввода ==========
    _onInput: function(query) {
        var self = this;

        // Очищаем предыдущий таймер
        clearTimeout(this.state.timer);

        // Сбрасываем выбранную улицу при вводе
        if (this.state.selectedStreet) {
            this.state.selectedStreet = null;
            this.elements.wrapper.classList.remove('has-value');
            this._clearHiddenInputs();
        }

        // Проверяем минимальную длину
        if (query.length < this.config.minQueryLength) {
            this._closeDropdown();
            return;
        }

        // Показываем спиннер
        this._setLoading(true);

        // Устанавливаем таймер debounce
        this.state.timer = setTimeout(function() {
            self._search(query);
        }, this.config.debounceDelay);
    },

    // ========== Обработка клавиатуры ==========
    _onKeydown: function(e) {
        var results = this.state.results;
        var activeIndex = this.state.activeIndex;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (activeIndex < results.length - 1) {
                    this.state.activeIndex++;
                    this._highlightResult();
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (activeIndex > 0) {
                    this.state.activeIndex--;
                    this._highlightResult();
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (activeIndex >= 0 && results[activeIndex]) {
                    this._selectResult(results[activeIndex]);
                }
                break;

            case 'Escape':
                this._closeDropdown();
                break;
        }
    },

    // ========== Поиск через AJAX ==========
    _search: function(query) {
        var self = this;

        var url = this.config.searchUrl + '?q=' + encodeURIComponent(query) + '&limit=' + this.config.limit;

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                self._setLoading(false);

                if (data.success) {
                    self._renderResults(data.results);
                } else {
                    self._renderError(data.message || 'Ошибка поиска');
                }
            })
            .catch(function(error) {
                console.error('LocationSearch: search error', error);
                self._setLoading(false);
                self._renderError('Ошибка соединения');
            });
    },

    // ========== Отрисовка результатов ==========
    _renderResults: function(results) {
        var self = this;
        var dropdown = this.elements.dropdown;

        this.state.results = results;
        this.state.activeIndex = -1;

        // Очищаем dropdown
        dropdown.innerHTML = '';

        if (results.length === 0) {
            dropdown.innerHTML = '<div class="location-dropdown-empty">Ничего не найдено</div>';
            this._openDropdown();
            return;
        }

        // Создаем список
        var list = document.createElement('div');
        list.className = 'location-dropdown-list';

        results.forEach(function(item, index) {
            var div = document.createElement('div');
            div.className = 'location-dropdown-item';
            div.setAttribute('data-index', index);

            // Основной текст - название улицы
            // Дополнительный - зона и район
            div.innerHTML =
                '<div class="location-item-main">' + item.name + '</div>' +
                '<div class="location-item-sub">' + self._formatSubtext(item) + '</div>';

            // Клик по результату
            div.addEventListener('click', function() {
                self._selectResult(item);
            });

            list.appendChild(div);
        });

        dropdown.appendChild(list);
        this._openDropdown();
    },

    // ========== Форматирование подтекста ==========
    _formatSubtext: function(item) {
        var parts = [];

        if (item.zone_name) {
            parts.push(item.zone_name);
        }
        if (item.region_name) {
            parts.push(item.region_name);
        }
        if (item.town_name) {
            parts.push(item.town_name);
        }

        return parts.join(', ');
    },

    // ========== Подсветка результата ==========
    _highlightResult: function() {
        var dropdown = this.elements.dropdown;
        var items = dropdown.querySelectorAll('.location-dropdown-item');
        var activeIndex = this.state.activeIndex;

        items.forEach(function(item, index) {
            if (index === activeIndex) {
                item.classList.add('is-active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('is-active');
            }
        });
    },

    // ========== Выбор результата ==========
    _selectResult: function(item) {
        // Сохраняем выбранное значение
        this.state.selectedStreet = item;

        // Обновляем UI
        this.elements.input.value = item.full_address;
        this.elements.wrapper.classList.add('has-value');

        // Обновляем hidden inputs
        this._updateHiddenInputs(item);

        // Закрываем dropdown
        this._closeDropdown();

        console.log('LocationSearch: selected', item);
    },

    // ========== Обновление hidden inputs ==========
    _updateHiddenInputs: function(item) {
        var h = this.elements.hidden;

        if (h.streetId) h.streetId.value = item.id || '';
        if (h.streetName) h.streetName.value = item.name || '';
        if (h.zoneId) h.zoneId.value = item.zone_id || '';
        if (h.zoneName) h.zoneName.value = item.zone_name || '';
        if (h.regionId) h.regionId.value = item.region_id || '';
        if (h.regionName) h.regionName.value = item.region_name || '';
        if (h.townId) h.townId.value = item.town_id || '';
        if (h.townName) h.townName.value = item.town_name || '';
    },

    // ========== Очистка hidden inputs ==========
    _clearHiddenInputs: function() {
        var h = this.elements.hidden;

        if (h.streetId) h.streetId.value = '';
        if (h.streetName) h.streetName.value = '';
        if (h.zoneId) h.zoneId.value = '';
        if (h.zoneName) h.zoneName.value = '';
        if (h.regionId) h.regionId.value = '';
        if (h.regionName) h.regionName.value = '';
        if (h.townId) h.townId.value = '';
        if (h.townName) h.townName.value = '';
    },

    // ========== Полная очистка ==========
    _clear: function() {
        this.state.selectedStreet = null;
        this.state.results = [];
        this.state.activeIndex = -1;

        this.elements.input.value = '';
        this.elements.wrapper.classList.remove('has-value');
        this.elements.dropdown.innerHTML = '';

        this._clearHiddenInputs();
        this._closeDropdown();
    },

    // ========== Открыть dropdown ==========
    _openDropdown: function() {
        this.elements.dropdown.classList.add('is-open');
    },

    // ========== Закрыть dropdown ==========
    _closeDropdown: function() {
        this.elements.dropdown.classList.remove('is-open');
        this.state.activeIndex = -1;
    },

    // ========== Показать/скрыть спиннер ==========
    _setLoading: function(isLoading) {
        if (isLoading) {
            this.elements.wrapper.classList.add('is-loading');
        } else {
            this.elements.wrapper.classList.remove('is-loading');
        }
    },

    // ========== Показать ошибку ==========
    _renderError: function(message) {
        var dropdown = this.elements.dropdown;
        dropdown.innerHTML = '<div class="location-dropdown-empty">' + message + '</div>';
        this._openDropdown();
    }
};

// ========== Автоинициализация ==========
document.addEventListener('DOMContentLoaded', function() {
    window.LocationSearch.init();
});
