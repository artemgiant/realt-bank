(function () {
    'use strict';

    // Кэш для данных
    const DATA = {
        countries: [],
        regions: [],
        cities: [],
        districts: [],
        streets: [],
        landmarks: [],
        complexes: [],
        blocks: [],
        developers: []
    };

    // API URL
    const API_URL = '/location/filter-data';

    const TYPES = {
        countries: 'country',
        regions: 'region',
        cities: 'city',
        districts: 'district',
        streets: 'street',
        landmarks: 'landmark',
        complexes: 'complex',
        blocks: 'block',
        developers: 'developer'
    };
    const TYPE_NAMES = {
        country: 'Страна',
        region: 'Область',
        city: 'Город',
        district: 'Район',
        street: 'Улица',
        landmark: 'Зона',
        complex: 'ЖК',
        block: 'Блок',
        developer: 'Дев.'
    };
    const LOCATION_TYPES = ['country', 'region', 'city'];
    const DETAIL_TYPES = ['district', 'street', 'landmark', 'complex', 'block', 'developer'];
    const DETAIL_KEYS = ['districts', 'streets', 'landmarks', 'complexes', 'blocks', 'developers'];

    const state = {
        isOpen: false,
        mode: 'location',
        category: 'all',
        location: null,
        path: {country: null, region: null, city: null},
        details: []
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
        categories: $('lfCategories'),
        breadcrumbs: $('lfBreadcrumbs'),
        content: $('lfContent'),
        empty: $('lfEmpty'),
        locationTag: $('lfLocationTag'),
        detailWrap: $('lfDetailWrap'),
        tooltip: $('lfTooltip')
    };

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

    const setMode = mode => {
        state.mode = mode;
        el.modes.querySelectorAll('button').forEach(b => b.classList.toggle('lf-active', b.dataset.mode === mode));
        el.categories.classList.toggle('lf-visible', mode === 'detail');
        updatePlaceholder();
        update();
    };
    const setCategory = cat => {
        state.category = cat;
        el.categories.querySelectorAll('button').forEach(b => b.classList.toggle('lf-active', b.dataset.cat === cat));
        update();
    };

    const updatePlaceholder = () => {
        const {mode, location} = state;
        if (mode === 'detail') el.search.placeholder = 'Район, улица, ЖК, ориентир...';
        else if (!location) el.search.placeholder = 'Страна, область, город...';
        else if (location.type === 'country') el.search.placeholder = 'Выберите область...';
        else if (location.type === 'region') el.search.placeholder = 'Выберите город...';
        else el.search.placeholder = 'Поиск...';
    };

    const updateBreadcrumbs = () => {
        const {path, mode} = state;
        let html = '';
        if (path.country) {
            html += `<span class="lf-crumb ${path.region ? 'lf-clickable' : ''}" data-type="country">${path.country.name}</span>`;
            if (path.region) html += `<span class="lf-sep">→</span><span class="lf-crumb ${path.city ? 'lf-clickable' : ''}" data-type="region">${path.region.name}</span>`;
            if (path.city) html += `<span class="lf-sep">→</span><span class="lf-crumb" data-type="city">${path.city.name}</span>`;
        }
        el.breadcrumbs.innerHTML = html;
        el.breadcrumbs.classList.toggle('lf-visible', !!html && mode === 'location');
    };

    const hideAll = () => Object.keys(DATA).forEach(k => $(`lf-${k}`).classList.add('lf-hidden'));
    const showEmpty = msg => {
        el.empty.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg><div>${msg}</div>`;
        el.empty.classList.add('lf-visible');
    };
    const highlight = (text, q) => q ? text.replace(new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi'), '<span class="lf-highlight">$1</span>') : text;

    // ========== API Functions ==========
    /**
     * Загрузка данных из API
     */
    const loadData = async (params = {}) => {
        try {
            const queryParams = new URLSearchParams();

            if (params.location_type) queryParams.append('location_type', params.location_type);
            if (params.location_id) queryParams.append('location_id', params.location_id);
            if (params.city_id) queryParams.append('city_id', params.city_id);
            if (params.detail_type) queryParams.append('detail_type', params.detail_type);
            if (params.search) queryParams.append('search', params.search);

            const response = await fetch(`${API_URL}?${queryParams.toString()}`);
            const result = await response.json();

            if (result.success && result.data) {
                // Обновляем кэш данных
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
        sec.querySelector('ul').innerHTML = items.map(item => {
            const name = highlight(item.name, query), parent = item.city || item.region || item.complex || '',
                selected = multi && state.details.some(d => d.type === type && d.id === item.id);
            return `<li data-type="${type}" data-id="${item.id}" data-name="${item.name}" class="${selected ? 'lf-selected' : ''}"><div class="lf-item-content">${multi ? `<div class="lf-checkbox"><svg viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>` : ''}<span><span class="lf-name">${name}</span>${parent ? `<span class="lf-parent">, ${parent}</span>` : ''}</span></div><span class="lf-count">${item.count.toLocaleString()}</span></li>`;
        }).join('');
        sec.classList.remove('lf-hidden');
    };

    const update = async () => {
        const query = el.search.value.trim();
        updateBreadcrumbs();
        hideAll();
        el.empty.classList.remove('lf-visible');
        if (query) await search(query); else if (state.mode === 'location') await showLocation(); else await showDetail();
    };

    const showLocation = async () => {
        const {location} = state;

        if (!location) {
            // Загружаем страны
            const data = await loadData({location_type: null});
            if (data && data.countries) {
                renderSection('countries', data.countries);
            }
        } else if (location.type === 'country') {
            // Загружаем области для страны
            const data = await loadData({location_type: 'country', location_id: location.id});
            if (data && data.regions) {
                data.regions.length ? renderSection('regions', data.regions) : showEmpty('Нет областей');
            }
        } else if (location.type === 'region') {
            // Загружаем города для области
            const data = await loadData({location_type: 'region', location_id: location.id});
            if (data && data.cities) {
                data.cities.length ? renderSection('cities', data.cities) : showEmpty('Нет городов');
            }
        } else {
            showEmpty('Город выбран. Перейдите в "Локация"');
        }
    };

    const showDetail = async () => {
        if (!state.path.city) {
            showEmpty('Сначала выберите город');
            return;
        }

        const cityId = state.path.city.id;
        // Конвертируем категорию (множественное число) в тип (единственное число)
        const detailType = state.category === 'all' ? null : TYPES[state.category];

        // Загружаем детали для города
        const data = await loadData({city_id: cityId, detail_type: detailType});

        if (!data) return;

        const keys = state.category === 'all' ? DETAIL_KEYS : [state.category];
        let has = false;

        keys.forEach(key => {
            if (data[key] && data[key].length) {
                renderSection(key, data[key], true);
                has = true;
            }
        });

        if (!has) showEmpty('Нет данных');
    };

    const search = async (query) => {
        if (state.mode === 'location') {
            // Поиск в режиме Location
            let params = {search: query};

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
            ['countries', 'regions', 'cities'].forEach(key => {
                if (data[key] && data[key].length) {
                    renderSection(key, data[key], false, query);
                    has = true;
                }
            });

            if (!has) showEmpty('Ничего не найдено');

        } else if (state.path.city) {
            // Поиск в режиме Detail
            const cityId = state.path.city.id;
            const detailType = state.category === 'all' ? null : TYPES[state.category];

            const data = await loadData({
                city_id: cityId,
                detail_type: detailType,
                search: query
            });

            if (!data) return;

            const keys = state.category === 'all' ? DETAIL_KEYS : [state.category];
            let has = false;

            keys.forEach(key => {
                if (data[key] && data[key].length) {
                    renderSection(key, data[key], true, query);
                    has = true;
                }
            });

            if (!has) showEmpty('Ничего не найдено');
        }
    };

    const selectLocation = (type, id, name) => {
        if (type === 'country') {
            state.path = {country: {id, name}, region: null, city: null};
            state.details = [];
        } else if (type === 'region') {
            const region = DATA.regions.find(r => r.id === id),
                country = DATA.countries.find(c => c.id === region?.countryId);
            state.path = {
                country: country ? {id: country.id, name: country.name} : state.path.country,
                region: {id, name},
                city: null
            };
            state.details = [];
        } else if (type === 'city') {
            const city = DATA.cities.find(c => c.id === id), region = DATA.regions.find(r => r.id === city?.regionId),
                country = DATA.countries.find(c => c.id === region?.countryId);
            state.path = {
                country: country ? {id: country.id, name: country.name} : state.path.country,
                region: region ? {id: region.id, name: region.name} : state.path.region,
                city: {id, name}
            };
            state.details = [];
        }
        state.location = {type, id, name};
        el.search.value = '';
        renderTags();
        type === 'city' ? setMode('detail') : update();
    };

    const toggleDetail = (type, id, name) => {
        const idx = state.details.findIndex(d => d.type === type && d.id === id);
        idx >= 0 ? state.details.splice(idx, 1) : state.details.push({type, id, name});
        renderTags();
        update();
    };
    const removeDetail = (type, id) => {
        state.details = state.details.filter(d => !(d.type === type && d.id === id));
        renderTags();
        updateHidden(); // Обновляем скрытые поля и таблицу
        if (state.isOpen) update();
    };
    const clearAll = () => {
        state.location = null;
        state.path = {country: null, region: null, city: null};
        state.details = [];
        renderTags();
        updateHidden(); // Обновляем скрытые поля и таблицу
        el.clear.classList.remove('lf-visible');
        if (state.isOpen) setMode('location');
    };
    const navigateTo = type => {
        if (type === 'country' && state.path.country) {
            state.location = {type: 'country', ...state.path.country};
            state.path.region = null;
            state.path.city = null;
            state.details = [];
        } else if (type === 'region' && state.path.region) {
            state.location = {type: 'region', ...state.path.region};
            state.path.city = null;
            state.details = [];
        }
        renderTags();
        update();
    };

    const renderTags = () => {
        if (state.location) {
            el.locationTag.innerHTML = `<span class="lf-tag"><span class="lf-tag-type">${TYPE_NAMES[state.location.type]}</span>${state.location.name}<button data-action="clear">×</button></span>`;
            el.clear.classList.add('lf-visible');
        } else el.locationTag.innerHTML = '';
        if (state.details.length) {
            const first = state.details[0], more = state.details.length - 1;
            let html = `<span class="lf-tag">${first.name}`;
            if (more > 0) html += `<span class="lf-badge" data-action="tooltip">+${more}</span>`;
            html += `<button data-action="clear-details">×</button></span><div class="lf-tooltip" id="lfTooltip">`;
            if (more > 0) state.details.slice(1).forEach(d => {
                html += `<div class="lf-tooltip-item"><span class="lf-tip-name"><span class="lf-tip-type">${TYPE_NAMES[d.type]}</span>${d.name}</span><button class="lf-tip-remove" data-action="remove" data-type="${d.type}" data-id="${d.id}">×</button></div>`;
            });
            html += '</div>';
            el.detailWrap.innerHTML = html;
        } else el.detailWrap.innerHTML = '<div class="lf-tooltip" id="lfTooltip"></div>';
        updateHidden();
    };

    const updateHidden = (triggerReload = true) => {
        $('lfType').value = state.location?.type || '';
        $('lfId').value = state.location?.id || '';
        $('lfDetails').value = JSON.stringify(state.details.map(d => ({type: d.type, id: d.id})));

        // Обновляем таблицу недвижимости (если не отключено)
        if (triggerReload && typeof window.reloadPropertiesTable === 'function') {
            window.reloadPropertiesTable();
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
        if (b) setMode(b.dataset.mode);
    });
    el.categories.addEventListener('click', e => {
        const b = e.target.closest('button');
        if (b) setCategory(b.dataset.cat);
    });
    el.breadcrumbs.addEventListener('click', e => {
        const item = e.target.closest('.lf-crumb.lf-clickable');
        if (item) navigateTo(item.dataset.type);
    });
    el.content.addEventListener('click', e => {
        const li = e.target.closest('li');
        if (li) {
            const {type, id, name} = li.dataset;
            if (state.mode === 'location' && LOCATION_TYPES.includes(type)) selectLocation(type, +id, name); else if (state.mode === 'detail' && DETAIL_TYPES.includes(type)) toggleDetail(type, +id, name);
        }
    });
    $('lfTags').addEventListener('click', e => {
        const action = e.target.dataset.action;
        if (action === 'clear') clearAll(); else if (action === 'clear-details') {
            state.details = [];
            renderTags();
            if (state.isOpen) update();
        } else if (action === 'tooltip') $('lfTooltip')?.classList.add('lf-visible'); else if (action === 'remove') removeDetail(e.target.dataset.type, +e.target.dataset.id);
    });
    $('lfTags').addEventListener('mouseenter', e => {
        if (e.target.classList.contains('lf-badge')) $('lfTooltip')?.classList.add('lf-visible');
    }, true);
    el.detailWrap.addEventListener('mouseleave', () => $('lfTooltip')?.classList.remove('lf-visible'));
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
            const data = await loadData({location_type: null});
            if (data && data.countries) {
                // Ищем Украину
                const ukraine = data.countries.find(c => c.name.toLowerCase().includes('украина') || c.name.toLowerCase().includes('ukraine'));
                if (ukraine) {
                    // Загружаем области Украины
                    const regionsData = await loadData({location_type: 'country', location_id: ukraine.id});
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
                                country: {id: ukraine.id, name: ukraine.name},
                                region: {id: odessaRegion.id, name: odessaRegion.name},
                                city: null
                            };
                            state.location = {type: 'region', id: odessaRegion.id, name: odessaRegion.name};
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
