/**
 * Поиск локации через Nominatim (OpenStreetMap)
 * Два поля: Область (select) → Локация (input с поиском)
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
        minQueryLength: 3
    },

    // ========== Список областей Украины ==========
    regions: [
        { id: 'odessa', name: 'Одесская область', nameUk: 'Одеська область' },
        { id: 'kyiv', name: 'Киевская область', nameUk: 'Київська область' },
        { id: 'kyiv_city', name: 'Киев', nameUk: 'Київ' },
        { id: 'kharkiv', name: 'Харьковская область', nameUk: 'Харківська область' },
        { id: 'dnipro', name: 'Днепропетровская область', nameUk: 'Дніпропетровська область' },
        { id: 'lviv', name: 'Львовская область', nameUk: 'Львівська область' },
        { id: 'zaporizhzhia', name: 'Запорожская область', nameUk: 'Запорізька область' },
        { id: 'vinnytsia', name: 'Винницкая область', nameUk: 'Вінницька область' },
        { id: 'volyn', name: 'Волынская область', nameUk: 'Волинська область' },
        { id: 'donetsk', name: 'Донецкая область', nameUk: 'Донецька область' },
        { id: 'zhytomyr', name: 'Житомирская область', nameUk: 'Житомирська область' },
        { id: 'zakarpattia', name: 'Закарпатская область', nameUk: 'Закарпатська область' },
        { id: 'ivano_frankivsk', name: 'Ивано-Франковская область', nameUk: 'Івано-Франківська область' },
        { id: 'kirovohrad', name: 'Кировоградская область', nameUk: 'Кіровоградська область' },
        { id: 'luhansk', name: 'Луганская область', nameUk: 'Луганська область' },
        { id: 'mykolaiv', name: 'Николаевская область', nameUk: 'Миколаївська область' },
        { id: 'poltava', name: 'Полтавская область', nameUk: 'Полтавська область' },
        { id: 'rivne', name: 'Ровенская область', nameUk: 'Рівненська область' },
        { id: 'sumy', name: 'Сумская область', nameUk: 'Сумська область' },
        { id: 'ternopil', name: 'Тернопольская область', nameUk: 'Тернопільська область' },
        { id: 'kherson', name: 'Херсонская область', nameUk: 'Херсонська область' },
        { id: 'khmelnytskyi', name: 'Хмельницкая область', nameUk: 'Хмельницька область' },
        { id: 'cherkasy', name: 'Черкасская область', nameUk: 'Черкаська область' },
        { id: 'chernihiv', name: 'Черниговская область', nameUk: 'Чернігівська область' },
        { id: 'chernivtsi', name: 'Черновицкая область', nameUk: 'Чернівецька область' },
        { id: 'crimea', name: 'АР Крым', nameUk: 'АР Крим' }
    ],

    // ========== Состояние ==========
    state: {
        // Выбранная область
        region: null,  // { id, name, nameUk }

        // Выбранная локация
        location: null,  // { displayName, lat, lon, city, street, houseNumber }

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
        var container = document.querySelector('.location-cascade-wrapper');
        if (!container) {
            console.warn('LocationCascade: container not found');
            return;
        }

        this._cacheElements(container);
        this._populateRegionSelect();
        this._bindEvents();

        console.log('LocationCascade: initialized');
    },

    // ========== Кэширование DOM элементов ==========
    _cacheElements: function(container) {
        // Select области
        this.elements.regionSelect = container.querySelector('#region_id');

        // Поле локации
        var locationField = container.querySelector('.location-field');
        if (locationField) {
            this.elements.location = {
                wrapper: locationField,
                inputWrapper: locationField.querySelector('.location-input-wrapper'),
                input: locationField.querySelector('.location-field-input'),
                dropdown: locationField.querySelector('.location-field-dropdown'),
                clearBtn: locationField.querySelector('.location-field-clear')
            };
        }

        // Hidden inputs
        this.elements.hidden = {
            regionName: container.querySelector('input[name="region_name"]'),
            locationDisplay: container.querySelector('input[name="location_display"]'),
            cityName: container.querySelector('input[name="city_name"]'),
            streetName: container.querySelector('input[name="street_name"]'),
            houseNumber: container.querySelector('input[name="building_number"]'),
            latitude: container.querySelector('input[name="latitude"]'),
            longitude: container.querySelector('input[name="longitude"]')
        };
    },

    // ========== Заполнение select областей ==========
    _populateRegionSelect: function() {
        var select = this.elements.regionSelect;
        if (!select) return;

        // Очищаем
        select.innerHTML = '';

        // Добавляем области
        this.regions.forEach(function(region) {
            var option = document.createElement('option');
            option.value = region.id;
            option.textContent = region.name;
            option.setAttribute('data-name-uk', region.nameUk);

            // Одесская по умолчанию
            if (region.id === 'odessa') {
                option.selected = true;
            }

            select.appendChild(option);
        });

        // Устанавливаем начальное значение
        this.state.region = this.regions.find(function(r) { return r.id === 'odessa'; });
        this._updateHiddenInputs();

        // Обновляем Select2 если есть
        if (typeof $ !== 'undefined' && $(select).data('select2')) {
            $(select).trigger('change.select2');
        }
    },

    // ========== Привязка событий ==========
    _bindEvents: function() {
        var self = this;

        // Изменение области
        if (this.elements.regionSelect) {
            this.elements.regionSelect.addEventListener('change', function(e) {
                var selectedId = e.target.value;
                self.state.region = self.regions.find(function(r) { return r.id === selectedId; });

                // Очищаем локацию при смене области
                self._clearLocation();
                self._updateHiddenInputs();
            });

            // Для Select2
            if (typeof $ !== 'undefined') {
                $(this.elements.regionSelect).on('change', function() {
                    var selectedId = $(this).val();
                    self.state.region = self.regions.find(function(r) { return r.id === selectedId; });
                    self._clearLocation();
                    self._updateHiddenInputs();
                });
            }
        }

        // События поля локации
        if (this.elements.location) {
            var input = this.elements.location.input;
            var clearBtn = this.elements.location.clearBtn;

            // Ввод текста
            input.addEventListener('input', function(e) {
                self._onInput(e.target.value);
            });

            // Навигация клавиатурой
            input.addEventListener('keydown', function(e) {
                self._onKeydown(e);
            });

            // Фокус
            input.addEventListener('focus', function() {
                if (self.state.results.length > 0) {
                    self._openDropdown();
                }
            });

            // Кнопка очистки
            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    self._clearLocation();
                });
            }
        }

        // Клик вне - закрыть dropdown
        document.addEventListener('click', function(e) {
            if (self.elements.location && !self.elements.location.wrapper.contains(e.target)) {
                self._closeDropdown();
            }
        });
    },

    // ========== Обработка ввода ==========
    _onInput: function(query) {
        var self = this;

        // Очищаем предыдущий таймер
        clearTimeout(this.state.timer);

        // Сбрасываем выбранную локацию при вводе
        if (this.state.location) {
            this.state.location = null;
            this.elements.location.inputWrapper.classList.remove('has-value');
        }

        // Проверяем минимальную длину
        if (query.length < this.config.minQueryLength) {
            this._closeDropdown();
            return;
        }

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

    // ========== Поиск через Nominatim ==========
    _search: function(query) {
        var self = this;

        // Показываем спиннер
        this._setLoading(true);

        // Формируем запрос с учетом области (на русском)
        var regionName = this.state.region ? this.state.region.name : '';
        var searchQuery = query + ', ' + regionName + ', Украина';

        var params = new URLSearchParams({
            format: 'json',
            addressdetails: 1,
            countrycodes: this.config.countryCode,
            limit: this.config.limit,
            'accept-language': 'ru',  // Русский язык
            q: searchQuery
        });

        var url = this.config.apiUrl + '?' + params.toString();

        // DEBUG: выводим запрос в консоль (декодированный)
        console.log('=== LocationCascade SEARCH ===');
        console.log('User input:', query);
        console.log('Search query:', searchQuery);
        console.log('Full URL (decoded):', decodeURIComponent(url));

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // DEBUG: выводим ответ
                console.log('Results count:', data.length);
                console.log('Raw results:', data);

                self._setLoading(false);
                self._renderResults(data);
            })
            .catch(function(error) {
                console.error('LocationCascade: search error', error);
                self._setLoading(false);
                self._renderError();
            });
    },

    // ========== Отрисовка результатов ==========
    _renderResults: function(data) {
        var self = this;
        var dropdown = this.elements.location.dropdown;

        // Фильтруем и форматируем результаты
        var results = this._filterResults(data);
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

            // Основной текст - displayName до области
            var mainText = self._trimDisplayName(item.displayName);

            div.innerHTML = '<div class="location-item-main">' + mainText + '</div>';

            // Клик по результату
            div.addEventListener('click', function() {
                self._selectResult(item);
            });

            list.appendChild(div);
        });

        dropdown.appendChild(list);
        this._openDropdown();
    },

    // ========== Фильтрация результатов ==========
    _filterResults: function(data) {
        var self = this;
        var seen = {};
        var results = [];

        console.log('=== Filtering results ===');

        data.forEach(function(item, index) {
            var address = item.address || {};

            // DEBUG: выводим адрес каждого результата
            console.log('Result #' + index + ':', {
                display_name: item.display_name,
                type: item.type,
                city: address.city || address.town || address.village,
                road: address.road,
                house_number: address.house_number,
                suburb: address.suburb || address.city_district
            });

            // Формируем уникальный ключ
            var key = (address.road || address.street || '') + '|' +
                (address.house_number || '') + '|' +
                (address.city || address.town || address.village || '');

            if (!seen[key]) {
                seen[key] = true;
                results.push({
                    displayName: item.display_name,
                    lat: item.lat,
                    lon: item.lon,
                    address: address,
                    type: item.type,
                    city: address.city || address.town || address.village || '',
                    district: address.city_district || address.suburb || '',
                    street: address.road || address.street || '',
                    houseNumber: address.house_number || ''
                });
            }
        });

        console.log('Filtered results:', results.length);

        return results;
    },

    // ========== Форматирование результата (основной текст) ==========
    _formatResultMain: function(item) {
        var parts = [];

        if (item.street) {
            parts.push(item.street);
        }
        if (item.houseNumber) {
            parts.push(item.houseNumber);
        }
        if (!item.street && item.city) {
            parts.push(item.city);
        }

        return parts.join(', ') || item.displayName.split(',')[0];
    },

    // ========== Форматирование результата (доп. текст) ==========
    _formatResultSub: function(item) {
        var parts = [];

        if (item.street && item.city) {
            parts.push(item.city);
        }
        if (item.district) {
            parts.push(item.district);
        }

        return parts.join(', ');
    },

    // ========== Подсветка результата ==========
    _highlightResult: function() {
        var dropdown = this.elements.location.dropdown;
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
        this.state.location = item;

        // Формируем отображаемый текст - displayName до области
        var displayText = this._trimDisplayName(item.displayName);

        // Обновляем UI
        this.elements.location.input.value = displayText;
        this.elements.location.inputWrapper.classList.add('has-value');

        // Обновляем hidden inputs
        this._updateHiddenInputs();

        // Закрываем dropdown
        this._closeDropdown();

        console.log('LocationCascade: selected', item);
        console.log('Display text:', displayText);
    },

    // ========== Обрезать displayName до области ==========
    _trimDisplayName: function(displayName) {
        if (!displayName) return '';

        // Список слов для обрезки (область, Украина и т.д.)
        var cutWords = [
            'область', 'Украина', 'Україна', 'Ukraine',
            'Одесская', 'Одеська', 'Киевская', 'Київська',
            'Харьковская', 'Харківська', 'Днепропетровская', 'Дніпропетровська',
            'Львовская', 'Львівська', 'Запорожская', 'Запорізька'
        ];

        var parts = displayName.split(',').map(function(p) { return p.trim(); });
        var result = [];

        for (var i = 0; i < parts.length; i++) {
            var part = parts[i];
            var shouldCut = false;

            // Проверяем содержит ли часть слова для обрезки
            for (var j = 0; j < cutWords.length; j++) {
                if (part.indexOf(cutWords[j]) !== -1) {
                    shouldCut = true;
                    break;
                }
            }

            if (shouldCut) {
                break;  // Прекращаем добавление
            }

            result.push(part);
        }

        return result.join(', ');
    },

    // ========== Форматирование отображаемого текста ==========
    _formatDisplayText: function(item) {
        var parts = [];

        if (item.city) {
            parts.push(item.city);
        }
        if (item.street) {
            parts.push(item.street);
        }
        if (item.houseNumber) {
            parts.push(item.houseNumber);
        }

        return parts.join(', ') || item.displayName.split(',').slice(0, 3).join(',');
    },

    // ========== Очистка локации ==========
    _clearLocation: function() {
        this.state.location = null;
        this.state.results = [];
        this.state.activeIndex = -1;

        if (this.elements.location) {
            this.elements.location.input.value = '';
            this.elements.location.inputWrapper.classList.remove('has-value');
            this.elements.location.dropdown.innerHTML = '';
        }

        this._closeDropdown();
        this._updateHiddenInputs();
    },

    // ========== Обновление hidden inputs ==========
    _updateHiddenInputs: function() {
        var h = this.elements.hidden;

        // Область
        if (h.regionName) {
            h.regionName.value = this.state.region ? this.state.region.name : '';
        }

        // Локация
        if (this.state.location) {
            if (h.locationDisplay) h.locationDisplay.value = this._formatDisplayText(this.state.location);
            if (h.cityName) h.cityName.value = this.state.location.city || '';
            if (h.streetName) h.streetName.value = this.state.location.street || '';
            if (h.houseNumber) h.houseNumber.value = this.state.location.houseNumber || '';
            if (h.latitude) h.latitude.value = this.state.location.lat || '';
            if (h.longitude) h.longitude.value = this.state.location.lon || '';
        } else {
            if (h.locationDisplay) h.locationDisplay.value = '';
            if (h.cityName) h.cityName.value = '';
            if (h.streetName) h.streetName.value = '';
            // Не очищаем houseNumber - он может быть введен вручную
            if (h.latitude) h.latitude.value = '';
            if (h.longitude) h.longitude.value = '';
        }
    },

    // ========== Открыть dropdown ==========
    _openDropdown: function() {
        if (this.elements.location) {
            this.elements.location.dropdown.classList.add('is-open');
        }
    },

    // ========== Закрыть dropdown ==========
    _closeDropdown: function() {
        if (this.elements.location) {
            this.elements.location.dropdown.classList.remove('is-open');
        }
        this.state.activeIndex = -1;
    },

    // ========== Показать/скрыть спиннер ==========
    _setLoading: function(isLoading) {
        if (this.elements.location) {
            var wrapper = this.elements.location.inputWrapper;
            if (isLoading) {
                wrapper.classList.add('is-loading');
            } else {
                wrapper.classList.remove('is-loading');
            }
        }
    },

    // ========== Показать ошибку ==========
    _renderError: function() {
        var dropdown = this.elements.location.dropdown;
        dropdown.innerHTML = '<div class="location-dropdown-empty">Ошибка поиска. Попробуйте еще раз.</div>';
        this._openDropdown();
    }
};

// ========== Автоинициализация ==========
document.addEventListener('DOMContentLoaded', function() {
    window.LocationCascade.init();
});
