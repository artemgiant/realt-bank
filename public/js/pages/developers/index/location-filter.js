(function () {
    'use strict';

    // Кэш для данных
    const DATA = {
        countries: [],
        regions: [],
        cities: []
    };

    // API URL
    const API_URL = '/location/filter-data';

    const TYPES = {
        countries: 'country',
        regions: 'region',
        cities: 'city'
    };
    const TYPE_NAMES = {
        country: 'Страна',
        region: 'Регион',
        city: 'Город'
    };
    const LOCATION_TYPES = ['country', 'region', 'city'];

    const state = {
        isOpen: false,
        mode: 'location',
        location: null, // Страна или регион (single select)
        path: { country: null, region: null },
        cities: [] // Массив выбранных городов (multi select)
    };

    const $ = id => document.getElementById(id);
    const el = {
        container: $('lfContainer'),
        input: $('lfInput'),
        search: $('lfSearch'),
        clear: $('lfClear'),
        toggle: $('lfToggle'),
        dropdown: $('lfDropdown'),
        modes: $('lfModes'),
        breadcrumbs: $('lfBreadcrumbs'),
        content: $('lfContent'),
        empty: $('lfEmpty'),
        locationTag: $('lfLocationTag'),
        detailWrap: $('lfDetailWrap'),
        tooltip: $('lfTooltip')
    };

    // Проверка что элементы существуют
    if (!el.container) {
        console.log('Location filter not found on page');
        return;
    }

    const open = () => {
        state.isOpen = true;
        el.dropdown.classList.add('lf-open');
        el.input.classList.add('lf-active');
        el.toggle.classList.add('lf-rotated');
        el.search.focus();
        update();
    };
    const close = () => {
        state.isOpen = false;
        el.dropdown.classList.remove('lf-open');
        el.input.classList.remove('lf-active');
        el.toggle.classList.remove('lf-rotated');
        $('lfTooltip')?.classList.remove('lf-visible');
    };

    const updatePlaceholder = () => {
        const { location, cities } = state;
        if (cities.length > 0) el.search.placeholder = 'Добавить город...';
        else if (!location) el.search.placeholder = 'Страна, регион, город...';
        else if (location.type === 'country') el.search.placeholder = 'Выберите регион...';
        else if (location.type === 'region') el.search.placeholder = 'Выберите город...';
        else el.search.placeholder = 'Поиск...';
    };

    const updateBreadcrumbs = () => {
        const { path } = state;
        let html = '';
        if (path.country) {
            html += `<span class="lf-crumb ${path.region ? 'lf-clickable' : ''}" data-type="country">${path.country.name}</span>`;
            if (path.region) html += `<span class="lf-sep">→</span><span class="lf-crumb" data-type="region">${path.region.name}</span>`;
        }
        el.breadcrumbs.innerHTML = html;
        el.breadcrumbs.classList.toggle('lf-visible', !!html);
    };

    const hideAll = () => Object.keys(DATA).forEach(k => {
        const sec = $(`lf-${k}`);
        if (sec) sec.classList.add('lf-hidden');
    });
    const showEmpty = msg => {
        el.empty.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg><div>${msg}</div>`;
        el.empty.classList.add('lf-visible');
    };
    const highlight = (text, q) => q ? text.replace(new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'), '<span class="lf-highlight">$1</span>') : text;

    // ========== API Functions ==========
    const loadData = async (params = {}) => {
        try {
            const queryParams = new URLSearchParams();

            if (params.location_type) queryParams.append('location_type', params.location_type);
            if (params.location_id) queryParams.append('location_id', params.location_id);
            if (params.search) queryParams.append('search', params.search);

            const response = await fetch(`${API_URL}?${queryParams.toString()}`);
            const result = await response.json();

            if (result.success && result.data) {
                Object.keys(result.data).forEach(key => {
                    if (DATA.hasOwnProperty(key)) {
                        DATA[key] = result.data[key];
                    }
                });
                return result.data;
            }

            return null;
        } catch (error) {
            console.error('Error loading location data:', error);
            showEmpty('Ошибка загрузки данных');
            return null;
        }
    };

    const renderSection = (key, items, multi = false, query = '') => {
        const sec = $(`lf-${key}`), type = TYPES[key];
        if (!sec) return;
        sec.querySelector('ul').innerHTML = items.map(item => {
            const name = highlight(item.name, query), parent = item.city || item.region || '';
            const selected = multi && state.cities.some(c => c.id === item.id);
            return `<li data-type="${type}" data-id="${item.id}" data-name="${item.name}" class="${selected ? 'lf-selected' : ''}">
                <div class="lf-item-content">
                    ${multi ? `<div class="lf-checkbox"><svg viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>` : ''}
                    <span><span class="lf-name">${name}</span>${parent ? `<span class="lf-parent">, ${parent}</span>` : ''}</span>
                </div>
                <span class="lf-count">${item.count.toLocaleString()}</span>
            </li>`;
        }).join('');
        sec.classList.remove('lf-hidden');
    };

    const update = async () => {
        const query = el.search.value.trim();
        updateBreadcrumbs();
        updatePlaceholder();
        hideAll();
        el.empty.classList.remove('lf-visible');
        if (query) await search(query); else await showLocation();
    };

    const showLocation = async () => {
        const { location } = state;

        if (!location) {
            const data = await loadData({ location_type: null });
            if (data && data.countries) {
                renderSection('countries', data.countries);
            }
        } else if (location.type === 'country') {
            const data = await loadData({ location_type: 'country', location_id: location.id });
            if (data && data.regions) {
                data.regions.length ? renderSection('regions', data.regions) : showEmpty('Нет областей');
            }
        } else if (location.type === 'region') {
            const data = await loadData({ location_type: 'region', location_id: location.id });
            if (data && data.cities) {
                // Города с мульти-выбором
                data.cities.length ? renderSection('cities', data.cities, true) : showEmpty('Нет городов');
            }
        }
    };

    const search = async (query) => {
        let params = { search: query };

        if (state.location) {
            if (state.location.type === 'country') {
                params.location_type = 'country';
                params.location_id = state.location.id;
            } else if (state.location.type === 'region') {
                params.location_type = 'region';
                params.location_id = state.location.id;
            }
        }

        const data = await loadData(params);
        if (!data) return;

        let has = false;

        // Страны и регионы - одиночный выбор
        ['countries', 'regions'].forEach(key => {
            if (data[key] && data[key].length) {
                renderSection(key, data[key], false, query);
                has = true;
            }
        });

        // Города - мульти-выбор (если есть регион в пути)
        if (data.cities && data.cities.length) {
            renderSection('cities', data.cities, state.location?.type === 'region', query);
            has = true;
        }

        if (!has) showEmpty('Ничего не найдено');
    };

    const selectLocation = (type, id, name) => {
        if (type === 'country') {
            state.path = { country: { id, name }, region: null };
            state.cities = [];
            state.location = { type, id, name };
        } else if (type === 'region') {
            const region = DATA.regions.find(r => r.id === id),
                country = DATA.countries.find(c => c.id === region?.countryId);
            state.path = {
                country: country ? { id: country.id, name: country.name } : state.path.country,
                region: { id, name }
            };
            state.cities = [];
            state.location = { type, id, name };
        }
        el.search.value = '';
        renderTags();
        update();
    };

    const toggleCity = (id, name) => {
        const idx = state.cities.findIndex(c => c.id === id);
        if (idx >= 0) {
            state.cities.splice(idx, 1);
        } else {
            state.cities.push({ id, name });
        }
        renderTags();
        updateHidden();
        update();
    };

    const removeCity = (id) => {
        state.cities = state.cities.filter(c => c.id !== id);
        renderTags();
        updateHidden();
        if (state.isOpen) update();
    };

    const clearAll = () => {
        state.location = null;
        state.path = { country: null, region: null };
        state.cities = [];
        renderTags();
        updateHidden();
        el.clear.classList.remove('lf-visible');
        if (state.isOpen) update();
    };

    const navigateTo = type => {
        if (type === 'country' && state.path.country) {
            state.location = { type: 'country', ...state.path.country };
            state.path.region = null;
            state.cities = [];
        }
        renderTags();
        update();
    };

    const renderTags = () => {
        // Тег локации (страна/регион)
        if (state.location && state.location.type !== 'city') {
            el.locationTag.innerHTML = `<span class="lf-tag"><span class="lf-tag-type">${TYPE_NAMES[state.location.type]}</span>${state.location.name}<button data-action="clear">×</button></span>`;
            el.clear.classList.add('lf-visible');
        } else {
            el.locationTag.innerHTML = '';
        }

        // Теги городов (мульти-выбор)
        if (state.cities.length > 0) {
            const first = state.cities[0];
            const more = state.cities.length - 1;
            let html = `<span class="lf-tag">${first.name}`;
            if (more > 0) html += `<span class="lf-badge" data-action="tooltip">+${more}</span>`;
            html += `<button data-action="clear-cities">×</button></span>`;

            // Tooltip для дополнительных городов
            html += '<div class="lf-tooltip" id="lfTooltip">';
            if (more > 0) {
                state.cities.slice(1).forEach(c => {
                    html += `<div class="lf-tooltip-item"><span class="lf-tip-name"><span class="lf-tip-type">Город</span>${c.name}</span><button class="lf-tip-remove" data-action="remove-city" data-id="${c.id}">×</button></div>`;
                });
            }
            html += '</div>';

            el.detailWrap.innerHTML = html;
            el.clear.classList.add('lf-visible');
        } else {
            el.detailWrap.innerHTML = '<div class="lf-tooltip" id="lfTooltip"></div>';
            if (!state.location) el.clear.classList.remove('lf-visible');
        }

        updateHidden();
    };

    const updateHidden = (triggerReload = true) => {
        $('lfType').value = state.location?.type || '';
        $('lfId').value = state.location?.id || '';

        // Сохраняем выбранные города как JSON
        const citiesInput = $('lfCities');
        if (citiesInput) {
            citiesInput.value = JSON.stringify(state.cities.map(c => c.id));
        }

        // Обновляем таблицу девелоперов
        if (triggerReload && typeof window.reloadDevelopersTable === 'function') {
            window.reloadDevelopersTable();
        }
    };

    el.input.addEventListener('click', e => {
        if (!e.target.closest('button') && !state.isOpen) open();
    });
    el.toggle.addEventListener('click', e => {
        e.stopPropagation();
        state.isOpen ? close() : open();
    });
    el.clear.addEventListener('click', e => {
        e.stopPropagation();
        clearAll();
    });
    el.search.addEventListener('input', () => {
        if (!state.isOpen) open();
        update();
    });
    el.search.addEventListener('focus', () => {
        if (!state.isOpen) open();
    });
    el.dropdown.addEventListener('click', e => e.stopPropagation());
    el.modes.addEventListener('click', e => {
        const b = e.target.closest('button');
        if (b) update();
    });
    el.breadcrumbs.addEventListener('click', e => {
        const item = e.target.closest('.lf-crumb.lf-clickable');
        if (item) navigateTo(item.dataset.type);
    });
    el.content.addEventListener('click', e => {
        const li = e.target.closest('li');
        if (li) {
            const { type, id, name } = li.dataset;
            if (type === 'city' && state.location?.type === 'region') {
                // Мульти-выбор для городов
                toggleCity(+id, name);
            } else if (LOCATION_TYPES.includes(type) && type !== 'city') {
                // Одиночный выбор для страны/региона
                selectLocation(type, +id, name);
            } else if (type === 'city') {
                // Если город выбран не из региона - переходим к нему
                selectLocation(type, +id, name);
            }
        }
    });

    $('lfTags')?.addEventListener('click', e => {
        const action = e.target.dataset?.action;
        if (action === 'clear') clearAll();
        else if (action === 'clear-cities') {
            state.cities = [];
            renderTags();
            updateHidden();
            if (state.isOpen) update();
        } else if (action === 'tooltip') {
            $('lfTooltip')?.classList.add('lf-visible');
        } else if (action === 'remove-city') {
            removeCity(+e.target.dataset.id);
        }
    });

    $('lfTags')?.addEventListener('mouseenter', e => {
        if (e.target.classList?.contains('lf-badge')) $('lfTooltip')?.classList.add('lf-visible');
    }, true);

    el.detailWrap?.addEventListener('mouseleave', () => $('lfTooltip')?.classList.remove('lf-visible'));

    document.addEventListener('click', e => {
        if (!el.container.contains(e.target)) close();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && state.isOpen) close();
    });

    // Инициализация с Одесской областью по умолчанию
    const initDefaultLocation = async () => {
        try {
            // Загружаем страны
            const data = await loadData({ location_type: null });
            if (data && data.countries) {
                // Ищем Украину
                const ukraine = data.countries.find(c => c.name.toLowerCase().includes('украина') || c.name.toLowerCase().includes('ukraine'));
                if (ukraine) {
                    // Загружаем Регионы Украины
                    const regionsData = await loadData({ location_type: 'country', location_id: ukraine.id });
                    if (regionsData && regionsData.regions) {
                        // Ищем Одесскую область
                        const odessaRegion = regionsData.regions.find(r =>
                            r.name.toLowerCase().includes('одес') ||
                            r.name.toLowerCase().includes('odesa') ||
                            r.name.toLowerCase().includes('odessa')
                        );

                        if (odessaRegion) {
                            // Устанавливаем Одесскую область по умолчанию
                            state.path = {
                                country: { id: ukraine.id, name: ukraine.name },
                                region: { id: odessaRegion.id, name: odessaRegion.name }
                            };
                            state.location = { type: 'region', id: odessaRegion.id, name: odessaRegion.name };
                            renderTags();
                            // Не триггерим обновление таблицы при инициализации
                            updateHidden(false);
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error initializing default location:', error);
        }
    };

    // Запускаем инициализацию
    initDefaultLocation();

    updatePlaceholder();
})();
