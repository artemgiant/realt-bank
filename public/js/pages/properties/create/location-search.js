/**
 * Location Search Module
 * Поиск региона и улицы с автозаполнением
 */

// ========== Конфигурация ==========
const LocationConfig = {
    // API endpoints
    api: {
        streetSearch: '/location/search',
        stateSearch: '/location/states/search',
        stateDefault: '/location/states/default',
    },
    // Задержка перед поиском (ms)
    debounceDelay: 300,
    // Минимум символов для поиска
    minChars: {
        street: 2,
        state: 1,
    },
};

// ========== Утилиты ==========
const LocationUtils = {
    /**
     * Debounce функция
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Получение CSRF токена
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    },

    /**
     * Fetch с обработкой ошибок
     */
    async fetchJson(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    ...options.headers,
                },
                ...options,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            return { success: false, error: error.message };
        }
    },
};

// ========== Класс поиска региона ==========
class StateSearchManager {
    constructor() {
        this.wrapper = document.querySelector('.state-search-wrapper');
        if (!this.wrapper) return;

        this.input = this.wrapper.querySelector('.state-search-input');
        this.dropdown = this.wrapper.querySelector('.state-search-dropdown');
        this.clearBtn = this.wrapper.querySelector('.state-search-clear');

        // Hidden inputs
        this.stateIdInput = document.querySelector('input[name="state_id"]');
        this.stateNameInput = document.querySelector('input[name="state_name"]');
        this.countryIdInput = document.querySelector('input[name="country_id"]');
        this.countryNameInput = document.querySelector('input[name="country_name"]');

        // Состояние
        this.selectedState = null;
        this.results = [];
        this.activeIndex = -1;

        // Callback при выборе региона
        this.onStateSelect = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadDefaultState();
    }

    bindEvents() {
        // Ввод текста
        this.input.addEventListener('input', LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay));

        // Фокус
        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.openDropdown();
            } else if (this.input.value.length >= LocationConfig.minChars.state) {
                this.search(this.input.value);
            }
        });

        // Клик вне dropdown
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Клавиатурная навигация
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Кнопка очистки
        this.clearBtn.addEventListener('click', () => this.clear());
    }

    /**
     * Загрузка региона по умолчанию (Одесский регион)
     */
    async loadDefaultState() {
        // Если уже есть выбранное значение из old() - не загружаем дефолтное
        if (this.stateIdInput.value) {
            this.input.value = this.stateNameInput.value;
            this.wrapper.classList.add('has-value');
            this.selectedState = {
                id: this.stateIdInput.value,
                name: this.stateNameInput.value,
                country_id: this.countryIdInput.value,
                country_name: this.countryNameInput.value,
            };
            return;
        }

        const data = await LocationUtils.fetchJson(LocationConfig.api.stateDefault);

        if (data.success && data.state) {
            this.selectState(data.state);
        }
    }

    /**
     * Поиск регионов
     */
    async search(query) {
        if (query.length < LocationConfig.minChars.state) {
            this.closeDropdown();
            return;
        }

        this.setLoading(true);

        const url = `${LocationConfig.api.stateSearch}?q=${encodeURIComponent(query)}`;
        const data = await LocationUtils.fetchJson(url);

        this.setLoading(false);

        if (data.success) {
            this.results = data.results;
            this.renderResults();
            this.openDropdown();
        }
    }

    /**
     * Рендер результатов
     */
    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = `
                <div class="state-dropdown-empty">
                    Регионы не найдены
                </div>
            `;
            return;
        }

        const html = `
            <div class="state-dropdown-list">
                ${this.results.map((state, index) => `
                    <div class="state-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}"
                         data-index="${index}">
                        <div class="state-item-main">${state.name}</div>
                        <div class="state-item-sub">${state.country_name || ''}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.innerHTML = html;

        // Bind click events
        this.dropdown.querySelectorAll('.state-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectState(this.results[index]);
            });
        });
    }

    /**
     * Выбор региона
     */
    selectState(state) {
        this.selectedState = state;

        // Заполняем input
        this.input.value = state.name;

        // Заполняем hidden inputs
        this.stateIdInput.value = state.id;
        this.stateNameInput.value = state.name;
        this.countryIdInput.value = state.country_id || '';
        this.countryNameInput.value = state.country_name || '';

        // Обновляем UI
        this.wrapper.classList.add('has-value');
        this.closeDropdown();

        // Callback для фильтрации улиц
        if (this.onStateSelect) {
            this.onStateSelect(state);
        }

        // Диспатчим событие для других модулей
        document.dispatchEvent(new CustomEvent('stateSelected', { detail: state }));
    }

    /**
     * Очистка
     */
    clear() {
        this.selectedState = null;
        this.input.value = '';
        this.stateIdInput.value = '';
        this.stateNameInput.value = '';
        this.countryIdInput.value = '';
        this.countryNameInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();

        // Диспатчим событие
        document.dispatchEvent(new CustomEvent('stateCleared'));
    }

    /**
     * Обработка клавиш
     */
    handleKeydown(e) {
        if (!this.dropdown.classList.contains('is-open')) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.activeIndex = Math.min(this.activeIndex + 1, this.results.length - 1);
                this.renderResults();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.activeIndex = Math.max(this.activeIndex - 1, 0);
                this.renderResults();
                break;
            case 'Enter':
                e.preventDefault();
                if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
                    this.selectState(this.results[this.activeIndex]);
                }
                break;
            case 'Escape':
                this.closeDropdown();
                break;
        }
    }

    setLoading(loading) {
        this.wrapper.classList.toggle('is-loading', loading);
    }

    openDropdown() {
        this.dropdown.classList.add('is-open');
    }

    closeDropdown() {
        this.dropdown.classList.remove('is-open');
        this.activeIndex = -1;
    }

    /**
     * Получить текущий выбранный регион
     */
    getSelectedState() {
        return this.selectedState;
    }
}

// ========== Класс поиска улицы ==========
class StreetSearchManager {
    constructor(stateSearchManager) {
        this.wrapper = document.querySelector('.location-search-wrapper');
        if (!this.wrapper) return;

        this.input = this.wrapper.querySelector('.location-search-input');
        this.dropdown = this.wrapper.querySelector('.location-search-dropdown');
        this.clearBtn = this.wrapper.querySelector('.location-search-clear');

        // Hidden inputs
        this.streetIdInput = this.wrapper.querySelector('input[name="street_id"]');
        this.streetNameInput = this.wrapper.querySelector('input[name="street_name"]');
        this.zoneIdInput = this.wrapper.querySelector('input[name="zone_id"]');
        this.zoneNameInput = this.wrapper.querySelector('input[name="zone_name"]');
        this.districtIdInput = this.wrapper.querySelector('input[name="district_id"]');
        this.districtNameInput = this.wrapper.querySelector('input[name="district_name"]');
        this.cityIdInput = this.wrapper.querySelector('input[name="city_id"]');
        this.cityNameInput = this.wrapper.querySelector('input[name="city_name"]');

        // Связь с поиском региона
        this.stateSearchManager = stateSearchManager;

        // Состояние
        this.selectedStreet = null;
        this.results = [];
        this.activeIndex = -1;

        this.init();
    }

    init() {
        this.bindEvents();
        this.restoreFromOld();
    }

    /**
     * Восстановление из old() если есть
     */
    restoreFromOld() {
        if (this.streetIdInput.value) {
            // Формируем полный адрес из сохраненных данных
            const addressParts = [this.streetNameInput.value];
            if (this.zoneNameInput.value) {
                addressParts.push(this.zoneNameInput.value);
            }
            if (this.districtNameInput.value) {
                addressParts.push(this.districtNameInput.value);
            }
            if (this.cityNameInput.value) {
                addressParts.push(this.cityNameInput.value);
            }

            this.input.value = addressParts.join(', ');
            this.wrapper.classList.add('has-value');
            this.selectedStreet = {
                id: this.streetIdInput.value,
                name: this.streetNameInput.value,
                zone_name: this.zoneNameInput.value,
                district_name: this.districtNameInput.value,
                city_name: this.cityNameInput.value,
            };
        }
    }

    bindEvents() {
        // Ввод текста
        this.input.addEventListener('input', LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay));

        // Фокус
        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.openDropdown();
            } else if (this.input.value.length >= LocationConfig.minChars.street) {
                this.search(this.input.value);
            }
        });

        // Клик вне dropdown
        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Клавиатурная навигация
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

        // Кнопка очистки
        this.clearBtn.addEventListener('click', () => this.clear());

        // Слушаем выбор региона - очищаем улицу
        document.addEventListener('stateSelected', () => {
            this.clear();
        });

        document.addEventListener('stateCleared', () => {
            this.clear();
        });
    }

    /**
     * Поиск улиц
     */
    async search(query) {
        if (query.length < LocationConfig.minChars.street) {
            this.closeDropdown();
            return;
        }

        this.setLoading(true);

        // Формируем URL с фильтром по региону
        let url = `${LocationConfig.api.streetSearch}?q=${encodeURIComponent(query)}`;

        // Добавляем state_id если выбран регион
        const selectedState = this.stateSearchManager?.getSelectedState();
        if (selectedState?.id) {
            url += `&state_id=${selectedState.id}`;
        }

        const data = await LocationUtils.fetchJson(url);

        this.setLoading(false);

        if (data.success) {
            this.results = data.results;
            this.renderResults();
            this.openDropdown();
        }
    }

    /**
     * Рендер результатов
     */
    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = `
                <div class="location-dropdown-empty">
                    Улицы не найдены
                </div>
            `;
            return;
        }

        const html = `
            <div class="location-dropdown-list">
                ${this.results.map((street, index) => `
                    <div class="location-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}"
                         data-index="${index}">
                        <div class="location-item-main">${street.name}</div>
                        <div class="location-item-sub">${[street.zone_name, street.district_name, street.city_name].filter(Boolean).join(', ')}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.innerHTML = html;

        // Bind click events
        this.dropdown.querySelectorAll('.location-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectStreet(this.results[index]);
            });
        });
    }

    /**
     * Выбор улицы
     */
    selectStreet(street) {
        this.selectedStreet = street;

        // Формируем полный адрес: улица, зона, район, город
        const addressParts = [street.name];
        if (street.zone_name) {
            addressParts.push(street.zone_name);
        }
        if (street.district_name) {
            addressParts.push(street.district_name);
        }
        if (street.city_name) {
            addressParts.push(street.city_name);
        }

        // Заполняем input полным адресом
        this.input.value = addressParts.join(', ');

        // Заполняем hidden inputs
        this.streetIdInput.value = street.id;
        this.streetNameInput.value = street.name;
        this.zoneIdInput.value = street.zone_id || '';
        this.zoneNameInput.value = street.zone_name || '';
        this.districtIdInput.value = street.district_id || '';
        this.districtNameInput.value = street.district_name || '';
        this.cityIdInput.value = street.city_id || '';
        this.cityNameInput.value = street.city_name || '';

        // Обновляем UI
        this.wrapper.classList.add('has-value');
        this.closeDropdown();

        // Диспатчим событие
        document.dispatchEvent(new CustomEvent('streetSelected', { detail: street }));
    }

    /**
     * Очистка
     */
    clear() {
        this.selectedStreet = null;
        this.input.value = '';
        this.streetIdInput.value = '';
        this.streetNameInput.value = '';
        this.zoneIdInput.value = '';
        this.zoneNameInput.value = '';
        this.districtIdInput.value = '';
        this.districtNameInput.value = '';
        this.cityIdInput.value = '';
        this.cityNameInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();
    }

    /**
     * Обработка клавиш
     */
    handleKeydown(e) {
        if (!this.dropdown.classList.contains('is-open')) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.activeIndex = Math.min(this.activeIndex + 1, this.results.length - 1);
                this.renderResults();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.activeIndex = Math.max(this.activeIndex - 1, 0);
                this.renderResults();
                break;
            case 'Enter':
                e.preventDefault();
                if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
                    this.selectStreet(this.results[this.activeIndex]);
                }
                break;
            case 'Escape':
                this.closeDropdown();
                break;
        }
    }

    setLoading(loading) {
        this.wrapper.classList.toggle('is-loading', loading);
    }

    openDropdown() {
        this.dropdown.classList.add('is-open');
    }

    closeDropdown() {
        this.dropdown.classList.remove('is-open');
        this.activeIndex = -1;
    }
}

// ========== Инициализация ==========
document.addEventListener('DOMContentLoaded', () => {
    // Инициализируем поиск региона
    const stateSearch = new StateSearchManager();

    // Инициализируем поиск улицы с передачей менеджера региона
    const streetSearch = new StreetSearchManager(stateSearch);

    // Экспортируем для внешнего использования
    window.LocationSearch = {
        state: stateSearch,
        street: streetSearch,
    };
});
