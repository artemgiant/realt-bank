(function () {
    'use strict';

    const DATA = {
        countries: [
            {id: 1, name: 'Украина', count: 5678},
            {id: 2, name: 'Турция', count: 892},
            {id: 3, name: 'Кипр', count: 234}
        ],
        regions: [
            {id: 1, name: 'Киевская область', countryId: 1, count: 2345},
            {id: 2, name: 'Одесская область', countryId: 1, count: 1567},
            {id: 3, name: 'Львовская область', countryId: 1, count: 987},
            {id: 4, name: 'Харьковская область', countryId: 1, count: 654},
            {id: 5, name: 'Днепропетровская область', countryId: 1, count: 543},
            {id: 6, name: 'Анталья', countryId: 2, count: 456},
            {id: 7, name: 'Стамбул', countryId: 2, count: 234},
            {id: 8, name: 'Измир', countryId: 2, count: 123},
            {id: 10, name: 'Ларнака', countryId: 3, count: 89},
            {id: 11, name: 'Лимассол', countryId: 3, count: 78},
            {id: 12, name: 'Пафос', countryId: 3, count: 67}
        ],
        cities: [
            {id: 1, name: 'Киев', regionId: 1, region: 'Киевская область', count: 1234},
            {id: 7, name: 'Ирпень', regionId: 1, region: 'Киевская область', count: 234},
            {id: 8, name: 'Буча', regionId: 1, region: 'Киевская область', count: 189},
            {id: 20, name: 'Бровары', regionId: 1, region: 'Киевская область', count: 156},
            {id: 2, name: 'Одесса', regionId: 2, region: 'Одесская область', count: 876},
            {id: 21, name: 'Южный', regionId: 2, region: 'Одесская область', count: 145},
            {id: 22, name: 'Черноморск', regionId: 2, region: 'Одесская область', count: 123},
            {id: 3, name: 'Львов', regionId: 3, region: 'Львовская область', count: 654},
            {id: 4, name: 'Харьков', regionId: 4, region: 'Харьковская область', count: 432},
            {id: 5, name: 'Днепр', regionId: 5, region: 'Днепропетровская область', count: 321},
            {id: 9, name: 'Аланья', regionId: 6, region: 'Анталья', count: 234},
            {id: 13, name: 'Анталья', regionId: 6, region: 'Анталья', count: 178},
            {id: 14, name: 'Стамбул', regionId: 7, region: 'Стамбул', count: 234},
            {id: 15, name: 'Измир', regionId: 8, region: 'Измир', count: 123},
            {id: 16, name: 'Ларнака', regionId: 10, region: 'Ларнака', count: 89},
            {id: 19, name: 'Лимассол', regionId: 11, region: 'Лимассол', count: 78},
            {id: 23, name: 'Пафос', regionId: 12, region: 'Пафос', count: 67}
        ],
        districts: [
            {id: 1, name: 'Печерский район', cityId: 1, city: 'Киев', count: 89},
            {id: 2, name: 'Шевченковский район', cityId: 1, city: 'Киев', count: 76},
            {id: 3, name: 'Голосеевский район', cityId: 1, city: 'Киев', count: 112},
            {id: 4, name: 'Оболонский район', cityId: 1, city: 'Киев', count: 98},
            {id: 5, name: 'Подольский район', cityId: 1, city: 'Киев', count: 67},
            {id: 6, name: 'Приморский район', cityId: 2, city: 'Одесса', count: 156},
            {id: 7, name: 'Аркадия', cityId: 2, city: 'Одесса', count: 89},
            {id: 11, name: 'Киевский район', cityId: 2, city: 'Одесса', count: 134},
            {id: 12, name: 'Пересыпский район', cityId: 2, city: 'Одесса', count: 78},
            {id: 8, name: 'Махмутлар', cityId: 9, city: 'Аланья', count: 145},
            {id: 9, name: 'Оба', cityId: 9, city: 'Аланья', count: 89},
            {id: 10, name: 'Кестель', cityId: 9, city: 'Аланья', count: 67}
        ],
        streets: [
            {id: 1, name: 'ул. Крещатик', cityId: 1, city: 'Киев', count: 23},
            {id: 2, name: 'ул. Банковая', cityId: 1, city: 'Киев', count: 8},
            {id: 3, name: 'ул. Грушевского', cityId: 1, city: 'Киев', count: 15},
            {id: 4, name: 'ул. Дерибасовская', cityId: 2, city: 'Одесса', count: 34},
            {id: 5, name: 'Французский бульвар', cityId: 2, city: 'Одесса', count: 45},
            {id: 6, name: 'Петра Ивахненка', cityId: 2, city: 'Одесса', count: 28},
            {id: 7, name: 'Ататюрк Джаддеси', cityId: 9, city: 'Аланья', count: 56}
        ],
        landmarks: [
            {id: 1, name: 'м. Крещатик', cityId: 1, city: 'Киев', count: 45},
            {id: 2, name: 'м. Золотые ворота', cityId: 1, city: 'Киев', count: 32},
            {id: 3, name: 'ТРЦ Ocean Plaza', cityId: 1, city: 'Киев', count: 12},
            {id: 4, name: 'м. Площадь Льва Толстого', cityId: 1, city: 'Киев', count: 28},
            {id: 5, name: 'Привоз', cityId: 2, city: 'Одесса', count: 23},
            {id: 6, name: 'Фонтанка', cityId: 2, city: 'Одесса', count: 67},
            {id: 7, name: 'Клеопатра Бич', cityId: 9, city: 'Аланья', count: 89}
        ],
        complexes: [
            {id: 1, name: 'ЖК Новопечерские Липки', cityId: 1, city: 'Киев', count: 45},
            {id: 2, name: 'ЖК Французский квартал', cityId: 1, city: 'Киев', count: 67},
            {id: 3, name: 'ЖК Комфорт Таун', cityId: 1, city: 'Киев', count: 89},
            {id: 4, name: 'ЖК Русановская гавань', cityId: 1, city: 'Киев', count: 34},
            {id: 5, name: 'ЖК Аркадия Хиллс', cityId: 2, city: 'Одесса', count: 56},
            {id: 6, name: 'ЖК Гагарин Плаза', cityId: 2, city: 'Одесса', count: 78},
            {id: 7, name: 'Konak Seaside Resort', cityId: 9, city: 'Аланья', count: 123},
            {id: 8, name: 'Emerald Park', cityId: 9, city: 'Аланья', count: 89}
        ],
        blocks: [
            {id: 1, name: 'Блок А', complexId: 1, complex: 'ЖК Новопечерские Липки', cityId: 1, count: 12},
            {id: 2, name: 'Блок Б', complexId: 1, complex: 'ЖК Новопечерские Липки', cityId: 1, count: 18},
            {id: 3, name: 'Блок 1', complexId: 3, complex: 'ЖК Комфорт Таун', cityId: 1, count: 25},
            {id: 4, name: 'Блок 2', complexId: 3, complex: 'ЖК Комфорт Таун', cityId: 1, count: 31},
            {id: 5, name: 'Секция А', complexId: 7, complex: 'Konak Seaside Resort', cityId: 9, count: 45}
        ],
        developers: [
            {id: 1, name: 'Киевгорстрой', cityId: 1, city: 'Киев', count: 234},
            {id: 2, name: 'Укрбуд', cityId: 1, city: 'Киев', count: 156},
            {id: 3, name: 'Интергал-Буд', cityId: 1, city: 'Киев', count: 89},
            {id: 4, name: 'Будова', cityId: 2, city: 'Одесса', count: 67},
            {id: 5, name: 'Karat Group', cityId: 2, city: 'Одесса', count: 54}
        ]
    };

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

    const renderSection = (key, items, multi = false, query = '') => {
        const sec = $(`lf-${key}`), type = TYPES[key];
        sec.querySelector('ul').innerHTML = items.map(item => {
            const name = highlight(item.name, query), parent = item.city || item.region || item.complex || '',
                selected = multi && state.details.some(d => d.type === type && d.id === item.id);
            return `<li data-type="${type}" data-id="${item.id}" data-name="${item.name}" class="${selected ? 'lf-selected' : ''}"><div class="lf-item-content">${multi ? `<div class="lf-checkbox"><svg viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>` : ''}<span><span class="lf-name">${name}</span>${parent ? `<span class="lf-parent">, ${parent}</span>` : ''}</span></div><span class="lf-count">${item.count.toLocaleString()}</span></li>`;
        }).join('');
        sec.classList.remove('lf-hidden');
    };

    const update = () => {
        const query = el.search.value.trim();
        updateBreadcrumbs();
        hideAll();
        el.empty.classList.remove('lf-visible');
        if (query) search(query); else if (state.mode === 'location') showLocation(); else showDetail();
    };

    const showLocation = () => {
        const {location} = state;
        if (!location) renderSection('countries', DATA.countries);
        else if (location.type === 'country') {
            const items = DATA.regions.filter(r => r.countryId === location.id);
            items.length ? renderSection('regions', items) : showEmpty('Нет областей');
        } else if (location.type === 'region') {
            const items = DATA.cities.filter(c => c.regionId === location.id);
            items.length ? renderSection('cities', items) : showEmpty('Нет городов');
        } else showEmpty('Город выбран. Перейдите в "Локация"');
    };

    const showDetail = () => {
        if (!state.path.city) {
            showEmpty('Сначала выберите город');
            return;
        }
        const cityId = state.path.city.id, keys = state.category === 'all' ? DETAIL_KEYS : [state.category];
        let has = false;
        keys.forEach(key => {
            const items = DATA[key].filter(i => i.cityId === cityId);
            if (items.length) {
                renderSection(key, items, true);
                has = true;
            }
        });
        if (!has) showEmpty('Нет данных');
    };

    const search = query => {
        const q = query.toLowerCase();
        let has = false;
        if (state.mode === 'location') {
            ['countries', 'regions', 'cities'].forEach(key => {
                let items = DATA[key].filter(i => i.name.toLowerCase().includes(q));
                if (state.location) {
                    if (key === 'regions' && state.location.type === 'country') items = items.filter(r => r.countryId === state.location.id); else if (key === 'cities' && state.location.type === 'region') items = items.filter(c => c.regionId === state.location.id);
                }
                if (items.length) {
                    renderSection(key, items, false, query);
                    has = true;
                }
            });
        } else if (state.path.city) {
            const cityId = state.path.city.id, keys = state.category === 'all' ? DETAIL_KEYS : [state.category];
            keys.forEach(key => {
                const items = DATA[key].filter(i => i.cityId === cityId && i.name.toLowerCase().includes(q));
                if (items.length) {
                    renderSection(key, items, true, query);
                    has = true;
                }
            });
        }
        if (!has) showEmpty('Ничего не найдено');
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
        if (state.isOpen) update();
    };
    const clearAll = () => {
        state.location = null;
        state.path = {country: null, region: null, city: null};
        state.details = [];
        renderTags();
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

    const updateHidden = () => {
        $('lfType').value = state.location?.type || '';
        $('lfId').value = state.location?.id || '';
        $('lfDetails').value = JSON.stringify(state.details.map(d => ({type: d.type, id: d.id})));
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

    updatePlaceholder();
})();
