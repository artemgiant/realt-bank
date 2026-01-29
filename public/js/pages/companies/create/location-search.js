/**
 * Location Search Module for Company Offices
 * Поддержка динамически добавляемых офисов
 */

// ========== Конфигурация ==========
const LocationConfig = {
    api: {
        streetSearch: '/location/search',
        stateSearch: '/location/states/search',
        stateDefault: '/location/states/default',
    },
    debounceDelay: 300,
    minChars: {
        street: 2,
        state: 1,
    },
};

// ========== Утилиты ==========
const LocationUtils = {
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

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    },

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

// ========== Класс поиска региона (для офиса) ==========
class OfficeStateSearch {
    constructor(wrapper, officeIndex) {
        this.wrapper = wrapper;
        this.officeIndex = officeIndex;
        this.input = wrapper.querySelector('.state-search-input');
        this.dropdown = wrapper.querySelector('.state-search-dropdown');
        this.clearBtn = wrapper.querySelector('.state-search-clear');

        // Hidden inputs (ищем внутри wrapper или рядом)
        this.stateIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][state_id]"]`);
        this.countryIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][country_id]"]`);

        this.selectedState = null;
        this.results = [];
        this.activeIndex = -1;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadDefaultState();
    }

    bindEvents() {
        // Ввод текста
        const debouncedSearch = LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay);

        this.input.addEventListener('input', debouncedSearch);

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
        if (this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clear());
        }
    }

    async loadDefaultState() {
        if (this.stateIdInput && this.stateIdInput.value) return;

        const data = await LocationUtils.fetchJson(LocationConfig.api.stateDefault);
        if (data.success && data.state) {
            this.selectState(data.state);
        }
    }

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

    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = '<div class="state-dropdown-empty">Регионы не найдены</div>';
            return;
        }

        this.dropdown.innerHTML = `
            <div class="state-dropdown-list">
                ${this.results.map((state, index) => `
                    <div class="state-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}" data-index="${index}">
                        <div class="state-item-main">${state.name}</div>
                        <div class="state-item-sub">${state.country_name || ''}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.querySelectorAll('.state-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectState(this.results[index]);
            });
        });
    }

    selectState(state) {
        this.selectedState = state;
        this.input.value = state.full_name || state.name;

        if (this.stateIdInput) this.stateIdInput.value = state.id;
        if (this.countryIdInput) this.countryIdInput.value = state.country_id || '';

        this.wrapper.classList.add('has-value');
        this.closeDropdown();

        // Dispatch event для связанного поля улицы
        this.wrapper.dispatchEvent(new CustomEvent('stateSelected', {
            detail: state,
            bubbles: true
        }));
    }

    clear() {
        this.selectedState = null;
        this.input.value = '';
        if (this.stateIdInput) this.stateIdInput.value = '';
        if (this.countryIdInput) this.countryIdInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();

        this.wrapper.dispatchEvent(new CustomEvent('stateCleared', { bubbles: true }));
    }

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

    getSelectedState() {
        return this.selectedState;
    }
}

// ========== Класс поиска улицы (для офиса) ==========
class OfficeStreetSearch {
    constructor(wrapper, officeIndex, stateSearch) {
        this.wrapper = wrapper;
        this.officeIndex = officeIndex;
        this.stateSearch = stateSearch;

        this.input = wrapper.querySelector('.location-search-input');
        this.dropdown = wrapper.querySelector('.location-search-dropdown');
        this.clearBtn = wrapper.querySelector('.location-search-clear');

        // Hidden inputs
        this.streetIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][street_id]"]`);
        this.cityIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][city_id]"]`);
        this.districtIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][district_id]"]`);
        this.zoneIdInput = wrapper.querySelector(`input[name="offices[${officeIndex}][zone_id]"]`);

        this.selectedStreet = null;
        this.results = [];
        this.activeIndex = -1;

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        const debouncedSearch = LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay);

        this.input.addEventListener('input', debouncedSearch);

        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.openDropdown();
            } else if (this.input.value.length >= LocationConfig.minChars.street) {
                this.search(this.input.value);
            }
        });

        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

        if (this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clear());
        }

        // Слушаем события от поля региона
        const officeItem = this.wrapper.closest('.block-offices-item');
        if (officeItem) {
            officeItem.addEventListener('stateSelected', () => this.clear());
            officeItem.addEventListener('stateCleared', () => this.clear());
        }
    }

    async search(query) {
        if (query.length < LocationConfig.minChars.street) {
            this.closeDropdown();
            return;
        }

        this.setLoading(true);

        let url = `${LocationConfig.api.streetSearch}?q=${encodeURIComponent(query)}`;

        // Фильтр по региону если выбран
        const selectedState = this.stateSearch?.getSelectedState();
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

    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = '<div class="location-dropdown-empty">Улицы не найдены</div>';
            return;
        }

        this.dropdown.innerHTML = `
            <div class="location-dropdown-list">
                ${this.results.map((street, index) => `
                    <div class="location-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}" data-index="${index}">
                        <div class="location-item-main">${street.name}</div>
                        <div class="location-item-sub">${[street.zone_name, street.district_name, street.city_name].filter(Boolean).join(', ')}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.querySelectorAll('.location-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectStreet(this.results[index]);
            });
        });
    }

    selectStreet(street) {
        this.selectedStreet = street;

        const addressParts = [street.name];
        if (street.zone_name) addressParts.push(street.zone_name);
        if (street.district_name) addressParts.push(street.district_name);
        if (street.city_name) addressParts.push(street.city_name);

        this.input.value = addressParts.join(', ');

        if (this.streetIdInput) this.streetIdInput.value = street.id;
        if (this.cityIdInput) this.cityIdInput.value = street.city_id || '';
        if (this.districtIdInput) this.districtIdInput.value = street.district_id || '';
        if (this.zoneIdInput) this.zoneIdInput.value = street.zone_id || '';

        this.wrapper.classList.add('has-value');
        this.closeDropdown();
    }

    clear() {
        this.selectedStreet = null;
        this.input.value = '';
        if (this.streetIdInput) this.streetIdInput.value = '';
        if (this.cityIdInput) this.cityIdInput.value = '';
        if (this.districtIdInput) this.districtIdInput.value = '';
        if (this.zoneIdInput) this.zoneIdInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();
    }

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

// ========== Инициализация офиса ==========
function initLocationSearchForOffice(officeElement) {
    const officeIndex = officeElement.dataset.officeIndex;

    const stateWrapper = officeElement.querySelector('.state-search-wrapper');
    const streetWrapper = officeElement.querySelector('.location-search-wrapper');

    let stateSearch = null;
    let streetSearch = null;

    if (stateWrapper) {
        stateSearch = new OfficeStateSearch(stateWrapper, officeIndex);
    }

    if (streetWrapper) {
        streetSearch = new OfficeStreetSearch(streetWrapper, officeIndex, stateSearch);
    }

    // Сохраняем ссылки на элементе
    officeElement._locationSearch = {
        state: stateSearch,
        street: streetSearch
    };

    return { stateSearch, streetSearch };
}

// Экспорт глобально для office-manager.js
window.initLocationSearchForOffice = initLocationSearchForOffice;

// ========== Также поддержка основного блока компании ==========
class MainStateSearch {
    constructor() {
        this.wrapper = document.querySelector('.block-all-info .state-search-wrapper');
        if (!this.wrapper) return;

        this.input = this.wrapper.querySelector('.state-search-input');
        this.dropdown = this.wrapper.querySelector('.state-search-dropdown');
        this.clearBtn = this.wrapper.querySelector('.state-search-clear');

        this.stateIdInput = document.querySelector('input[name="state_id"]');
        this.stateNameInput = document.querySelector('input[name="state_name"]');
        this.countryIdInput = document.querySelector('input[name="country_id"]');
        this.countryNameInput = document.querySelector('input[name="country_name"]');

        this.selectedState = null;
        this.results = [];
        this.activeIndex = -1;

        if (this.input) this.init();
    }

    init() {
        this.bindEvents();
        this.loadDefaultState();
    }

    bindEvents() {
        const debouncedSearch = LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay);

        this.input.addEventListener('input', debouncedSearch);

        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.openDropdown();
            } else if (this.input.value.length >= LocationConfig.minChars.state) {
                this.search(this.input.value);
            }
        });

        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

        if (this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clear());
        }
    }

    async loadDefaultState() {
        if (this.stateIdInput && this.stateIdInput.value) {
            const fullName = this.countryNameInput?.value
                ? `${this.stateNameInput.value}, ${this.countryNameInput.value}`
                : this.stateNameInput?.value || '';
            this.input.value = fullName;
            this.wrapper.classList.add('has-value');
            return;
        }

        const data = await LocationUtils.fetchJson(LocationConfig.api.stateDefault);
        if (data.success && data.state) {
            this.selectState(data.state);
        }
    }

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

    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = '<div class="state-dropdown-empty">Регионы не найдены</div>';
            return;
        }

        this.dropdown.innerHTML = `
            <div class="state-dropdown-list">
                ${this.results.map((state, index) => `
                    <div class="state-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}" data-index="${index}">
                        <div class="state-item-main">${state.name}</div>
                        <div class="state-item-sub">${state.country_name || ''}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.querySelectorAll('.state-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectState(this.results[index]);
            });
        });
    }

    selectState(state) {
        this.selectedState = state;
        this.input.value = state.full_name || state.name;

        if (this.stateIdInput) this.stateIdInput.value = state.id;
        if (this.stateNameInput) this.stateNameInput.value = state.name;
        if (this.countryIdInput) this.countryIdInput.value = state.country_id || '';
        if (this.countryNameInput) this.countryNameInput.value = state.country_name || '';

        this.wrapper.classList.add('has-value');
        this.closeDropdown();

        document.dispatchEvent(new CustomEvent('mainStateSelected', { detail: state }));
    }

    clear() {
        this.selectedState = null;
        this.input.value = '';
        if (this.stateIdInput) this.stateIdInput.value = '';
        if (this.stateNameInput) this.stateNameInput.value = '';
        if (this.countryIdInput) this.countryIdInput.value = '';
        if (this.countryNameInput) this.countryNameInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();

        document.dispatchEvent(new CustomEvent('mainStateCleared'));
    }

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

    getSelectedState() {
        return this.selectedState;
    }
}

class MainStreetSearch {
    constructor(stateSearch) {
        this.wrapper = document.querySelector('.block-all-info .location-search-wrapper');
        if (!this.wrapper) return;

        this.stateSearch = stateSearch;
        this.input = this.wrapper.querySelector('.location-search-input');
        this.dropdown = this.wrapper.querySelector('.location-search-dropdown');
        this.clearBtn = this.wrapper.querySelector('.location-search-clear');

        this.streetIdInput = document.querySelector('input[name="street_id"]');
        this.streetNameInput = document.querySelector('input[name="street_name"]');
        this.zoneIdInput = document.querySelector('input[name="zone_id"]');
        this.zoneNameInput = document.querySelector('input[name="zone_name"]');
        this.districtIdInput = document.querySelector('input[name="district_id"]');
        this.districtNameInput = document.querySelector('input[name="district_name"]');
        this.cityIdInput = document.querySelector('input[name="city_id"]');
        this.cityNameInput = document.querySelector('input[name="city_name"]');

        this.selectedStreet = null;
        this.results = [];
        this.activeIndex = -1;

        if (this.input) this.init();
    }

    init() {
        this.bindEvents();
        this.restoreFromOld();
    }

    restoreFromOld() {
        if (this.streetIdInput && this.streetIdInput.value) {
            const addressParts = [this.streetNameInput?.value];
            if (this.zoneNameInput?.value) addressParts.push(this.zoneNameInput.value);
            if (this.districtNameInput?.value) addressParts.push(this.districtNameInput.value);
            if (this.cityNameInput?.value) addressParts.push(this.cityNameInput.value);

            this.input.value = addressParts.filter(Boolean).join(', ');
            this.wrapper.classList.add('has-value');
        }
    }

    bindEvents() {
        const debouncedSearch = LocationUtils.debounce(() => {
            this.search(this.input.value);
        }, LocationConfig.debounceDelay);

        this.input.addEventListener('input', debouncedSearch);

        this.input.addEventListener('focus', () => {
            if (this.results.length > 0) {
                this.openDropdown();
            } else if (this.input.value.length >= LocationConfig.minChars.street) {
                this.search(this.input.value);
            }
        });

        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.closeDropdown();
            }
        });

        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));

        if (this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clear());
        }

        document.addEventListener('mainStateSelected', () => this.clear());
        document.addEventListener('mainStateCleared', () => this.clear());
    }

    async search(query) {
        if (query.length < LocationConfig.minChars.street) {
            this.closeDropdown();
            return;
        }

        this.setLoading(true);

        let url = `${LocationConfig.api.streetSearch}?q=${encodeURIComponent(query)}`;
        const selectedState = this.stateSearch?.getSelectedState();
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

    renderResults() {
        if (this.results.length === 0) {
            this.dropdown.innerHTML = '<div class="location-dropdown-empty">Улицы не найдены</div>';
            return;
        }

        this.dropdown.innerHTML = `
            <div class="location-dropdown-list">
                ${this.results.map((street, index) => `
                    <div class="location-dropdown-item ${index === this.activeIndex ? 'is-active' : ''}" data-index="${index}">
                        <div class="location-item-main">${street.name}</div>
                        <div class="location-item-sub">${[street.zone_name, street.district_name, street.city_name].filter(Boolean).join(', ')}</div>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown.querySelectorAll('.location-dropdown-item').forEach((item) => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.selectStreet(this.results[index]);
            });
        });
    }

    selectStreet(street) {
        this.selectedStreet = street;

        const addressParts = [street.name];
        if (street.zone_name) addressParts.push(street.zone_name);
        if (street.district_name) addressParts.push(street.district_name);
        if (street.city_name) addressParts.push(street.city_name);

        this.input.value = addressParts.join(', ');

        if (this.streetIdInput) this.streetIdInput.value = street.id;
        if (this.streetNameInput) this.streetNameInput.value = street.name;
        if (this.zoneIdInput) this.zoneIdInput.value = street.zone_id || '';
        if (this.zoneNameInput) this.zoneNameInput.value = street.zone_name || '';
        if (this.districtIdInput) this.districtIdInput.value = street.district_id || '';
        if (this.districtNameInput) this.districtNameInput.value = street.district_name || '';
        if (this.cityIdInput) this.cityIdInput.value = street.city_id || '';
        if (this.cityNameInput) this.cityNameInput.value = street.city_name || '';

        this.wrapper.classList.add('has-value');
        this.closeDropdown();
    }

    clear() {
        this.selectedStreet = null;
        this.input.value = '';
        if (this.streetIdInput) this.streetIdInput.value = '';
        if (this.streetNameInput) this.streetNameInput.value = '';
        if (this.zoneIdInput) this.zoneIdInput.value = '';
        if (this.zoneNameInput) this.zoneNameInput.value = '';
        if (this.districtIdInput) this.districtIdInput.value = '';
        if (this.districtNameInput) this.districtNameInput.value = '';
        if (this.cityIdInput) this.cityIdInput.value = '';
        if (this.cityNameInput) this.cityNameInput.value = '';
        this.wrapper.classList.remove('has-value');
        this.results = [];
        this.closeDropdown();
    }

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
    // Инициализация для основного блока компании
    const mainStateSearch = new MainStateSearch();
    const mainStreetSearch = new MainStreetSearch(mainStateSearch);

    window.LocationSearch = {
        main: {
            state: mainStateSearch,
            street: mainStreetSearch
        },
        initForOffice: initLocationSearchForOffice
    };
});
