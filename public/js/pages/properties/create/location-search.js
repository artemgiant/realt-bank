/**
 * Каскадный поиск локации через Nominatim (OpenStreetMap)
 * Три поля: Населенный пункт → Район → Улица
 *
 * Объект доступен через window.LocationCascade
 */
window.LocationCascade = {

    // ========== Конфигурация ==========
    config: {
        // Nominatim API
        apiUrl: 'https://nominatim.openstreetmap.org/search',

        // Ограничение на Украину
        countryCode: 'ua',

        // Лимит результатов
        limit: 10,

        // Задержка перед запросом (мс)
        debounceDelay: 500,

        // Минимальная длина запроса
        minQueryLength: 2
    },

    // ========== Состояние ==========
    state: {
        // Выбранные значения
        city: null,        // { name, displayName, lat, lon, osmId }
        district: null,    // { name, displayName, lat, lon, osmId }
        street: null,      // { name, displayName, lat, lon, osmId }
        building: '',

        // Таймеры debounce
        timers: {
            city: null,
            district: null,
            street: null
        },

        // Текущие результаты поиска
        results: {
            city: [],
            district: [],
            street: []
        },

        // Активный индекс в dropdown (для навигации клавиатурой)
        activeIndex: {
            city: -1,
            district: -1,
            street: -1
        }
    },

    // ========== DOM элементы ==========
    elements: {},

    // ========== Инициализация ==========
    init: function() {
        var container = document.querySelector('.location-cascade-wrapper');
        if (!container) {
            console.warn('LocationCascade: container not found');
            return;
        }

        this._cacheElements(container);
        this._bindEvents();
        this._updateFieldStates();

        console.log('LocationCascade: initialized');
    },

    // ========== Кэширование DOM элементов ==========
    _cacheElements: function(container) {
        var self = this;

        ['city', 'district', 'street'].forEach(function(field) {
            var fieldEl = container.querySelector('.location-field.' + field + '-field');
            if (fieldEl) {
                self.elements[field] = {
                    wrapper: fieldEl,
                    inputWrapper: fieldEl.querySelector('.location-input-wrapper'),
                    input: fieldEl.querySelector('.location-field-input'),
                    dropdown: fieldEl.querySelector('.location-field-dropdown'),
                    clearBtn: fieldEl.querySelector('.location-field-clear')
                };
            }
        });

        // Поле номера дома
        var buildingField = container.querySelector('.location-field.building-field');
        if (buildingField) {
            this.elements.building = {
                input: buildingField.querySelector('.location-field-input')
            };
        }

        // Hidden inputs
        this.elements.hidden = {
            cityName: container.querySelector('input[name="city_name"]'),
            cityLat: container.querySelector('input[name="city_lat"]'),
            cityLng: container.querySelector('input[name="city_lng"]'),
            districtName: container.querySelector('input[name="district_name"]'),
            streetName: container.querySelector('input[name="street_name"]'),
            streetLat: container.querySelector('input[name="street_lat"]'),
            streetLng: container.querySelector('input[name="street_lng"]'),
            buildingNumber: container.querySelector('input[name="building_number"]')
        };

        // Debug блок
        this.elements.debug = container.querySelector('.location-coords-debug');
    },

    // ========== Привязка событий ==========
    _bindEvents: function() {
        var self = this;

        // События для каждого поля поиска
        ['city', 'district', 'street'].forEach(function(field) {
            if (!self.elements[field]) return;

            var input = self.elements[field].input;
            var clearBtn = self.elements[field].clearBtn;
            var selectedClear = self.elements[field].selectedClear;

            // Ввод текста
            input.addEventListener('input', function(e) {
                self._onInput(field, e.target.value);
            });

            // Навигация клавиатурой
            input.addEventListener('keydown', function(e) {
                self._onKeydown(field, e);
            });

            // Фокус
            input.addEventListener('focus', function() {
                if (self.state.results[field].length > 0) {
                    self._openDropdown(field);
                }
            });

            // Кнопка очистки в инпуте
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    self._clearField(field);
                });
            }
        });

        // Поле номера дома
        if (this.elements.building && this.elements.building.input) {
            this.elements.building.input.addEventListener('input', function(e) {
                self.state.building = e.target.value;
                self._updateHiddenInputs();
                self._updateDebug();
            });
        }

        // Клик вне - закрыть все dropdown
        document.addEventListener('click', function(e) {
            ['city', 'district', 'street'].forEach(function(field) {
                if (self.elements[field] && !self.elements[field].wrapper.contains(e.target)) {
                    self._closeDropdown(field);
                }
            });
        });
    },

    // ========== Обработка ввода ==========
    _onInput: function(field, query) {
        var self = this;

        // Очищаем предыдущий таймер
        clearTimeout(this.state.timers[field]);

        // Проверяем минимальную длину
        if (query.length < this.config.minQueryLength) {
            this._closeDropdown(field);
            this._setInputLoading(field, false);
            return;
        }

        // Показываем спиннер
        this._setInputLoading(field, true);

        // Debounce запрос
        this.state.timers[field] = setTimeout(function() {
            self._search(field, query);
        }, this.config.debounceDelay);
    },

    // ========== Навигация клавиатурой ==========
    _onKeydown: function(field, e) {
        var dropdown = this.elements[field].dropdown;
        if (!dropdown.classList.contains('is-open')) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this._navigateResults(field, 1);
                break;
            case 'ArrowUp':
                e.preventDefault();
                this._navigateResults(field, -1);
                break;
            case 'Enter':
                e.preventDefault();
                var idx = this.state.activeIndex[field];
                if (idx >= 0 && this.state.results[field][idx]) {
                    this._selectResult(field, this.state.results[field][idx]);
                }
                break;
            case 'Escape':
                this._closeDropdown(field);
                break;
        }
    },

    // ========== Навигация по результатам ==========
    _navigateResults: function(field, direction) {
        var items = this.elements[field].dropdown.querySelectorAll('.location-dropdown-item');
        var count = items.length;

        if (count === 0) return;

        // Убираем активный класс
        var oldIdx = this.state.activeIndex[field];
        if (oldIdx >= 0 && items[oldIdx]) {
            items[oldIdx].classList.remove('is-active');
        }

        // Вычисляем новый индекс
        var newIdx = oldIdx + direction;
        if (newIdx >= count) newIdx = 0;
        if (newIdx < 0) newIdx = count - 1;

        this.state.activeIndex[field] = newIdx;

        // Добавляем активный класс
        items[newIdx].classList.add('is-active');
        items[newIdx].scrollIntoView({ block: 'nearest' });
    },

    // ========== Поиск через Nominatim ==========
    _search: function(field, query) {
        var self = this;
        var params = this._buildSearchParams(field, query);
        var url = this.config.apiUrl + '?' + params.toString();

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                self._setInputLoading(field, false);
                self._renderResults(field, data);
            })
            .catch(function(error) {
                console.error('LocationCascade: search error', error);
                self._setInputLoading(field, false);
                self._renderError(field);
            });
    },

    // ========== Построение параметров поиска ==========
    _buildSearchParams: function(field, query) {
        var params = new URLSearchParams({
            format: 'json',
            addressdetails: 1,
            countrycodes: this.config.countryCode,
            limit: this.config.limit,
            'accept-language': 'uk'  // Украинский язык для результатов
        });

        switch (field) {
            case 'city':
                // Поиск населенных пунктов
                params.set('q', query + ', Україна');
                break;

            case 'district':
                // Поиск районов в выбранном городе
                if (this.state.city) {
                    params.set('q', 'район ' + query + ', ' + this.state.city.name);
                } else {
                    params.set('q', 'район ' + query);
                }
                break;

            case 'street':
                // Поиск улиц - добавляем "вулиця" для точности
                var streetQuery = 'вулиця ' + query;
                if (this.state.city) {
                    streetQuery += ', ' + this.state.city.name;
                }
                params.set('q', streetQuery);
                break;
        }

        return params;
    },

    // ========== Отрисовка результатов ==========
    _renderResults: function(field, results) {
        var self = this;
        var dropdown = this.elements[field].dropdown;

        // Фильтруем результаты по типу
        var filtered = this._filterResults(field, results);

        this.state.results[field] = filtered;
        this.state.activeIndex[field] = -1;

        // Очищаем dropdown
        dropdown.innerHTML = '';

        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="location-dropdown-empty">Ничего не найдено</div>';
            this._openDropdown(field);
            return;
        }

        // Создаем элементы
        filtered.forEach(function(item, index) {
            var div = document.createElement('div');
            div.className = 'location-dropdown-item';
            div.setAttribute('data-index', index);

            var name = self._getDisplayName(field, item);
            var type = self._getTypeLabel(field, item);

            div.innerHTML =
                '<div class="location-dropdown-item-name">' + self._escapeHtml(name) + '</div>' +
                (type ? '<div class="location-dropdown-item-type">' + self._escapeHtml(type) + '</div>' : '');

            div.addEventListener('click', function() {
                self._selectResult(field, item);
            });

            dropdown.appendChild(div);
        });

        this._openDropdown(field);
    },

    // ========== Фильтрация результатов по типу ==========
    _filterResults: function(field, results) {
        var filtered = [];

        switch (field) {
            case 'city':
                // Все населенные пункты - города, поселки, села, административные единицы
                filtered = results.filter(function(item) {
                    var type = item.type || '';
                    var cls = item.class || '';
                    var addresstype = item.addresstype || '';

                    // Пропускаем если это город/село/поселок
                    return cls === 'place' ||
                        cls === 'boundary' ||
                        addresstype === 'city' ||
                        addresstype === 'town' ||
                        addresstype === 'village' ||
                        addresstype === 'hamlet' ||
                        type === 'city' ||
                        type === 'town' ||
                        type === 'village' ||
                        type === 'hamlet' ||
                        type === 'administrative';
                });
                break;

            case 'district':
                // Районы, микрорайоны
                filtered = results.filter(function(item) {
                    var type = item.type || '';
                    var name = (item.display_name || '').toLowerCase();
                    return type === 'suburb' ||
                        type === 'neighbourhood' ||
                        type === 'quarter' ||
                        type === 'district' ||
                        name.indexOf('район') !== -1 ||
                        name.indexOf('мікрорайон') !== -1 ||
                        name.indexOf('микрорайон') !== -1;
                });
                break;

            case 'street':
                // Улицы - расширенный фильтр
                filtered = results.filter(function(item) {
                    var cls = item.class || '';
                    var type = item.type || '';
                    var addresstype = item.addresstype || '';
                    var address = item.address || {};

                    // Пропускаем если есть road в адресе или это дорога
                    return cls === 'highway' ||
                        cls === 'place' ||
                        addresstype === 'road' ||
                        addresstype === 'street' ||
                        type === 'street' ||
                        type === 'road' ||
                        type === 'residential' ||
                        type === 'pedestrian' ||
                        type === 'primary' ||
                        type === 'secondary' ||
                        type === 'tertiary' ||
                        type === 'unclassified' ||
                        address.road;
                });
                break;

            default:
                filtered = results;
        }

        // Дедупликация по имени + району
        return this._deduplicateResults(field, filtered);
    },

    // ========== Дедупликация результатов ==========
    _deduplicateResults: function(field, results) {
        var seen = {};
        var unique = [];

        results.forEach(function(item) {
            var address = item.address || {};
            var key = '';

            switch (field) {
                case 'city':
                    // Ключ: название города
                    key = (address.city || address.town || address.village || item.name || '').toLowerCase();
                    break;

                case 'district':
                    // Ключ: название района + город
                    var districtName = address.suburb || address.borough || address.neighbourhood || item.name || '';
                    var cityName = address.city || '';
                    key = (districtName + '|' + cityName).toLowerCase();
                    break;

                case 'street':
                    // Ключ: название улицы + район
                    var streetName = address.road || item.name || '';
                    var suburb = address.suburb || address.borough || '';
                    key = (streetName + '|' + suburb).toLowerCase();
                    break;

                default:
                    key = item.display_name || '';
            }

            if (key && !seen[key]) {
                seen[key] = true;
                unique.push(item);
            }
        });

        return unique;
    },

    // ========== Получение отображаемого имени ==========
    _getDisplayName: function(field, item) {
        var address = item.address || {};

        switch (field) {
            case 'city':
                return address.city || address.town || address.village ||
                    address.hamlet || item.name || item.display_name;

            case 'district':
                return address.suburb || address.neighbourhood ||
                    address.quarter || address.district ||
                    item.name || item.display_name;

            case 'street':
                return address.road || address.street ||
                    item.name || item.display_name;

            default:
                return item.display_name;
        }
    },

    // ========== Получение метки типа (подсказка под названием) ==========
    _getTypeLabel: function(field, item) {
        var address = item.address || {};
        var parts = [];

        switch (field) {
            case 'city':
                // Для города показываем область
                if (address.state) parts.push(address.state);
                if (address.county) parts.push(address.county);
                break;

            case 'district':
                // Для района показываем город
                if (address.city) parts.push(address.city);
                break;

            case 'street':
                // Для улицы показываем район и город
                if (address.suburb) parts.push(address.suburb);
                else if (address.borough) parts.push(address.borough);
                if (address.city) parts.push(address.city);
                break;

            default:
                if (address.state) parts.push(address.state);
                if (address.city) parts.push(address.city);
        }

        return parts.join(', ') || item.type || '';
    },

    // ========== Выбор результата ==========
    _selectResult: function(field, item) {
        var self = this;
        var address = item.address || {};

        // Сохраняем выбранное значение
        this.state[field] = {
            name: this._getDisplayName(field, item),
            displayName: item.display_name,
            lat: item.lat,
            lon: item.lon,
            osmId: item.osm_id,
            address: address
        };

        // Обновляем UI
        this._updateFieldUI(field);

        // Логика автоподстановки района при выборе улицы
        if (field === 'street' && !this.state.district) {
            var districtName = address.suburb || address.neighbourhood ||
                address.quarter || address.district;
            if (districtName) {
                this.state.district = {
                    name: districtName,
                    displayName: districtName,
                    lat: null,
                    lon: null,
                    osmId: null,
                    address: address
                };
                this._updateFieldUI('district');
            }
        }

        // Обновляем состояние зависимых полей
        this._updateFieldStates();

        // Обновляем hidden inputs
        this._updateHiddenInputs();

        // Обновляем debug
        this._updateDebug();

        // Закрываем dropdown
        this._closeDropdown(field);

        console.log('LocationCascade: selected ' + field, this.state[field]);
    },

    // ========== Обновление UI поля ==========
    _updateFieldUI: function(field) {
        var el = this.elements[field];
        var value = this.state[field];

        if (value) {
            // Показываем выбранное значение в самом инпуте
            el.input.value = value.name;
            el.inputWrapper.classList.add('has-value');
        } else {
            // Очищаем
            el.input.value = '';
            el.inputWrapper.classList.remove('has-value');
            el.input.placeholder = this._getPlaceholder(field);
        }
    },

    // ========== Получение placeholder ==========
    _getPlaceholder: function(field) {
        switch (field) {
            case 'city': return 'Введите название...';
            case 'district': return 'Введите район...';
            case 'street': return 'Введите улицу...';
            default: return '';
        }
    },

    // ========== Обновление состояния полей (disabled/enabled) ==========
    _updateFieldStates: function() {
        // Район - доступен только если выбран город
        if (this.elements.district) {
            var districtInput = this.elements.district.input;
            if (this.state.city) {
                districtInput.disabled = false;
                districtInput.placeholder = 'Введите район...';
            } else {
                districtInput.disabled = true;
                districtInput.placeholder = 'Сначала выберите город';
            }
        }

        // Улица - доступна только если выбран город
        if (this.elements.street) {
            var streetInput = this.elements.street.input;
            if (this.state.city) {
                streetInput.disabled = false;
                streetInput.placeholder = 'Введите улицу...';
            } else {
                streetInput.disabled = true;
                streetInput.placeholder = 'Сначала выберите город';
            }
        }

        // Номер дома - доступен только если выбрана улица
        if (this.elements.building) {
            var buildingInput = this.elements.building.input;
            if (this.state.street) {
                buildingInput.disabled = false;
                buildingInput.placeholder = '№';
            } else {
                buildingInput.disabled = true;
                buildingInput.placeholder = '—';
            }
        }
    },

    // ========== Очистка поля ==========
    _clearField: function(field) {
        // Очищаем значение
        this.state[field] = null;

        // Очищаем зависимые поля
        if (field === 'city') {
            this.state.district = null;
            this.state.street = null;
            this.state.building = '';
            this._updateFieldUI('district');
            this._updateFieldUI('street');
            if (this.elements.building) {
                this.elements.building.input.value = '';
            }
        } else if (field === 'district') {
            // Район очищаем, улицу оставляем
        }

        // Обновляем UI
        this._updateFieldUI(field);
        this._updateFieldStates();
        this._updateHiddenInputs();
        this._updateDebug();

        // Фокус на инпут
        this.elements[field].input.focus();

        console.log('LocationCascade: cleared ' + field);
    },

    // ========== Обновление hidden inputs ==========
    _updateHiddenInputs: function() {
        var h = this.elements.hidden;

        // Город
        if (h.cityName) h.cityName.value = this.state.city ? this.state.city.name : '';
        if (h.cityLat) h.cityLat.value = this.state.city ? this.state.city.lat : '';
        if (h.cityLng) h.cityLng.value = this.state.city ? this.state.city.lon : '';

        // Район
        if (h.districtName) h.districtName.value = this.state.district ? this.state.district.name : '';

        // Улица
        if (h.streetName) h.streetName.value = this.state.street ? this.state.street.name : '';
        if (h.streetLat) h.streetLat.value = this.state.street ? this.state.street.lat : '';
        if (h.streetLng) h.streetLng.value = this.state.street ? this.state.street.lon : '';

        // Номер дома
        if (h.buildingNumber) h.buildingNumber.value = this.state.building;
    },

    // ========== Обновление debug блока ==========
    _updateDebug: function() {
        if (!this.elements.debug) return;

        var info = [];

        if (this.state.city) {
            info.push('Город: ' + this.state.city.name + ' (' + this.state.city.lat + ', ' + this.state.city.lon + ')');
        }
        if (this.state.district) {
            info.push('Район: ' + this.state.district.name);
        }
        if (this.state.street) {
            info.push('Улица: ' + this.state.street.name + ' (' + this.state.street.lat + ', ' + this.state.street.lon + ')');
        }
        if (this.state.building) {
            info.push('Дом: ' + this.state.building);
        }

        if (info.length > 0) {
            this.elements.debug.textContent = info.join(' | ');
            this.elements.debug.classList.remove('d-none');
        } else {
            this.elements.debug.classList.add('d-none');
        }
    },

    // ========== Показать/скрыть спиннер ==========
    _setInputLoading: function(field, isLoading) {
        if (this.elements[field]) {
            var wrapper = this.elements[field].inputWrapper;
            if (isLoading) {
                wrapper.classList.add('is-loading');
            } else {
                wrapper.classList.remove('is-loading');
            }
        }
    },

    // ========== Открыть dropdown ==========
    _openDropdown: function(field) {
        if (this.elements[field]) {
            this.elements[field].dropdown.classList.add('is-open');
        }
    },

    // ========== Закрыть dropdown ==========
    _closeDropdown: function(field) {
        if (this.elements[field]) {
            this.elements[field].dropdown.classList.remove('is-open');
            this.state.activeIndex[field] = -1;
        }
    },

    // ========== Ошибка запроса ==========
    _renderError: function(field) {
        var dropdown = this.elements[field].dropdown;
        dropdown.innerHTML = '<div class="location-dropdown-empty">Ошибка поиска</div>';
        this._openDropdown(field);
    },

    // ========== Экранирование HTML ==========
    _escapeHtml: function(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

// ========== Автоинициализация ==========
document.addEventListener('DOMContentLoaded', function() {
    window.LocationCascade.init();
});
