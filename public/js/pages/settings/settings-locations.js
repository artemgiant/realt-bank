/**
 * Settings Locations JavaScript
 * Handles drawers, search, cascading Select2 for location management
 */

// ========== DRAWER: COUNTRY ==========
function openCountryDrawer(countryId = null) {
    const drawer = document.getElementById('drawerAddCountry');
    const overlay = document.getElementById('drawerCountryOverlay');
    const form = document.getElementById('countryForm');
    const title = document.getElementById('countryDrawerTitle');
    const subtitle = document.getElementById('countryDrawerSubtitle');
    const submitBtn = document.getElementById('countrySubmitBtn');

    form.reset();
    form.action = '/settings/countries';
    document.getElementById('countryMethod').value = 'POST';

    const countryKey = countryId ? String(countryId) : null;

    if (countryKey && countriesData[countryKey]) {
        const country = countriesData[countryKey];
        title.textContent = 'Редактирование страны';
        subtitle.textContent = 'Измените параметры страны';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

        form.action = '/settings/countries/' + countryId;
        document.getElementById('countryMethod').value = 'PUT';
        document.getElementById('countryName').value = country.name || '';
        document.getElementById('countryCode').value = country.code || '';
    } else {
        title.textContent = 'Новая страна';
        subtitle.textContent = 'Добавьте страну для географической структуры';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeCountryDrawer() {
    document.getElementById('drawerCountryOverlay').classList.remove('open');
    document.getElementById('drawerAddCountry').classList.remove('open');
    document.body.style.overflow = '';
}

// ========== DRAWER: STATE ==========
function openStateDrawer(stateId = null) {
    const drawer = document.getElementById('drawerAddState');
    const overlay = document.getElementById('drawerStateOverlay');
    const form = document.getElementById('stateForm');
    const title = document.getElementById('stateDrawerTitle');
    const subtitle = document.getElementById('stateDrawerSubtitle');
    const submitBtn = document.getElementById('stateSubmitBtn');

    form.reset();
    form.action = '/settings/states';
    document.getElementById('stateMethod').value = 'POST';

    // Initialize Select2
    initStateDrawerSelect2();

    const stateKey = stateId ? String(stateId) : null;

    if (stateKey && statesData[stateKey]) {
        const state = statesData[stateKey];
        title.textContent = 'Редактирование региона';
        subtitle.textContent = 'Измените параметры региона';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

        form.action = '/settings/states/' + stateId;
        document.getElementById('stateMethod').value = 'PUT';
        document.getElementById('stateName').value = state.name || '';

        // Set country
        if (state.country_id) {
            $('#state-country-select').val(state.country_id).trigger('change');
        }
    } else {
        title.textContent = 'Новый регион';
        subtitle.textContent = 'Добавьте регион для географической структуры';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeStateDrawer() {
    document.getElementById('drawerStateOverlay').classList.remove('open');
    document.getElementById('drawerAddState').classList.remove('open');
    document.body.style.overflow = '';
}

function initStateDrawerSelect2() {
    if ($('#state-country-select').hasClass('select2-hidden-accessible')) {
        $('#state-country-select').select2('destroy');
    }
    $('#state-country-select').select2({
        width: '100%',
        placeholder: 'Выберите страну...',
        allowClear: true,
        dropdownParent: $('#drawerAddState')
    });
}

// ========== DRAWER: REGION (Район области) ==========
function openRegionDrawer(regionId = null) {
    const drawer = document.getElementById('drawerAddRegion');
    const overlay = document.getElementById('drawerRegionOverlay');
    const form = document.getElementById('regionForm');
    const title = document.getElementById('regionDrawerTitle');
    const subtitle = document.getElementById('regionDrawerSubtitle');
    const submitBtn = document.getElementById('regionSubmitBtn');

    form.reset();
    form.action = '/settings/regions';
    document.getElementById('regionMethod').value = 'POST';

    initRegionDrawerSelect2();

    if (regionId) {
        let region = null;
        for (const sk in statesData) {
            const state = statesData[sk];
            if (state.regions) {
                const found = state.regions.find(r => r.id === regionId);
                if (found) {
                    region = { ...found, state_id: parseInt(sk) };
                    break;
                }
            }
        }

        if (region) {
            title.textContent = 'Редактирование района области';
            subtitle.textContent = 'Измените параметры района области';
            submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

            form.action = '/settings/regions/' + regionId;
            document.getElementById('regionMethod').value = 'PUT';
            document.getElementById('regionName').value = region.name || '';

            if (region.state_id) {
                $('#region-state-select').val(region.state_id).trigger('change');
            }
        }
    } else {
        title.textContent = 'Новый район области';
        subtitle.textContent = 'Добавьте район внутри области';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeRegionDrawer() {
    document.getElementById('drawerRegionOverlay').classList.remove('open');
    document.getElementById('drawerAddRegion').classList.remove('open');
    document.body.style.overflow = '';
}

function initRegionDrawerSelect2() {
    if ($('#region-state-select').hasClass('select2-hidden-accessible')) {
        $('#region-state-select').select2('destroy');
    }
    $('#region-state-select').select2({
        width: '100%',
        placeholder: 'Выберите область...',
        allowClear: true,
        dropdownParent: $('#drawerAddRegion')
    });
}

// ========== DRAWER: DISTRICT ==========
function openDistrictDrawer(districtId = null) {
    const drawer = document.getElementById('drawerAddDistrict');
    const overlay = document.getElementById('drawerDistrictOverlay');
    const form = document.getElementById('districtForm');
    const title = document.getElementById('districtDrawerTitle');
    const subtitle = document.getElementById('districtDrawerSubtitle');
    const submitBtn = document.getElementById('districtSubmitBtn');

    form.reset();
    form.action = '/settings/districts';
    document.getElementById('districtMethod').value = 'POST';

    // Initialize Select2
    initDistrictDrawerSelect2();

    if (districtId) {
        // Find district data from statesData (districts are nested under states.cities)
        let district = null;
        for (const sk in statesData) {
            const state = statesData[sk];
            if (state.cities) {
                for (const city of state.cities) {
                    if (city.districts) {
                        const found = city.districts.find(d => d.id === districtId);
                        if (found) {
                            district = { ...found, city_id: city.id };
                            break;
                        }
                    }
                }
            }
            if (district) break;
        }

        if (district) {
            title.textContent = 'Редактирование района';
            subtitle.textContent = 'Измените параметры района';
            submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

            form.action = '/settings/districts/' + districtId;
            document.getElementById('districtMethod').value = 'PUT';
            document.getElementById('districtName').value = district.name || '';

            // Set city select
            $('#district-city-select').val(district.city_id).trigger('change');
        }
    } else {
        title.textContent = 'Новый район';
        subtitle.textContent = 'Добавьте район внутри города';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDistrictDrawer() {
    document.getElementById('drawerDistrictOverlay').classList.remove('open');
    document.getElementById('drawerAddDistrict').classList.remove('open');
    document.body.style.overflow = '';
}

function initDistrictDrawerSelect2() {
    if ($('#district-city-select').hasClass('select2-hidden-accessible')) {
        $('#district-city-select').select2('destroy');
    }
    $('#district-city-select').select2({
        width: '100%',
        placeholder: 'Выберите город...',
        allowClear: true,
        dropdownParent: $('#drawerAddDistrict')
    });
}

// ========== DRAWER: CITY ==========
function openCityDrawer(cityId = null) {
    const drawer = document.getElementById('drawerAddCity');
    const overlay = document.getElementById('drawerCityOverlay');
    const form = document.getElementById('cityForm');
    const title = document.getElementById('cityDrawerTitle');
    const subtitle = document.getElementById('cityDrawerSubtitle');
    const submitBtn = document.getElementById('citySubmitBtn');

    form.reset();
    form.action = '/settings/cities';
    document.getElementById('cityMethod').value = 'POST';

    // Initialize Select2
    initCityDrawerSelect2();

    // Clear country display
    var countryDisplay = document.getElementById('city-country-display');
    if (countryDisplay) countryDisplay.value = '';

    const cityKey = cityId ? String(cityId) : null;

    if (cityKey && citiesData[cityKey]) {
        const city = citiesData[cityKey];
        title.textContent = 'Редактирование города';
        subtitle.textContent = 'Измените параметры города';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

        form.action = '/settings/cities/' + cityId;
        document.getElementById('cityMethod').value = 'PUT';
        document.getElementById('cityName').value = city.name || '';

        // Set state
        $('#city-state-select').val(city.state_id).trigger('change');
    } else {
        title.textContent = 'Новый город';
        subtitle.textContent = 'Добавьте город или населённый пункт';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeCityDrawer() {
    document.getElementById('drawerCityOverlay').classList.remove('open');
    document.getElementById('drawerAddCity').classList.remove('open');
    document.body.style.overflow = '';
}

function initCityDrawerSelect2() {
    if ($('#city-state-select').hasClass('select2-hidden-accessible')) {
        $('#city-state-select').select2('destroy');
    }

    $('#city-state-select').select2({
        width: '100%',
        placeholder: 'Выберите регион...',
        allowClear: true,
        dropdownParent: $('#drawerAddCity')
    });
}

// ========== DRAWER: ZONE ==========
function openZoneDrawer(zoneId = null) {
    const drawer = document.getElementById('drawerAddZone');
    const overlay = document.getElementById('drawerZoneOverlay');
    const form = document.getElementById('zoneForm');
    const title = document.getElementById('zoneDrawerTitle');
    const subtitle = document.getElementById('zoneDrawerSubtitle');
    const submitBtn = document.getElementById('zoneSubmitBtn');

    form.reset();
    form.action = '/settings/zones';
    document.getElementById('zoneMethod').value = 'POST';

    // Initialize Select2
    initZoneDrawerSelect2();

    const zoneKey = zoneId ? String(zoneId) : null;

    // Clear country display and city select
    var countryDisplay = document.getElementById('zone-country-display');
    if (countryDisplay) countryDisplay.value = '';
    $('#zone-city-select').empty().append('<option value="">Сначала выберите регион...</option>').trigger('change');

    if (zoneKey && zonesData[zoneKey]) {
        const zone = zonesData[zoneKey];
        title.textContent = 'Редактирование микрорайона';
        subtitle.textContent = 'Измените параметры микрорайона';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

        form.action = '/settings/zones/' + zoneId;
        document.getElementById('zoneMethod').value = 'PUT';
        document.getElementById('zoneName').value = zone.name || '';

        // Set state first, then load cities and select the right one
        if (zone.state_id) {
            $('#zone-state-select').val(zone.state_id).trigger('change');
            setTimeout(function() {
                if (zone.city_id) {
                    $('#zone-city-select').val(zone.city_id).trigger('change');
                }
            }, 500);
        }
    } else {
        title.textContent = 'Новый микрорайон';
        subtitle.textContent = 'Добавьте микрорайон или зону внутри города';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeZoneDrawer() {
    document.getElementById('drawerZoneOverlay').classList.remove('open');
    document.getElementById('drawerAddZone').classList.remove('open');
    document.body.style.overflow = '';
}

function initZoneDrawerSelect2() {
    ['#zone-state-select', '#zone-city-select'].forEach(function(sel) {
        if ($(sel).hasClass('select2-hidden-accessible')) {
            $(sel).select2('destroy');
        }
    });

    $('#zone-state-select').select2({
        width: '100%',
        placeholder: 'Выберите регион...',
        allowClear: false,
        dropdownParent: $('#drawerAddZone')
    });

    $('#zone-city-select').select2({
        width: '100%',
        placeholder: 'Выберите город...',
        allowClear: true,
        dropdownParent: $('#drawerAddZone')
    });
}

// ========== DRAWER: STREET ==========
function openStreetDrawer(streetId = null) {
    const drawer = document.getElementById('drawerAddStreet');
    const overlay = document.getElementById('drawerStreetOverlay');
    const form = document.getElementById('streetForm');
    const title = document.getElementById('streetDrawerTitle');
    const subtitle = document.getElementById('streetDrawerSubtitle');
    const submitBtn = document.getElementById('streetSubmitBtn');

    form.reset();
    form.action = '/settings/streets';
    document.getElementById('streetMethod').value = 'POST';

    // Initialize Select2
    initStreetDrawerSelect2();

    const streetKey = streetId ? String(streetId) : null;

    // Clear country display
    var countryDisplay = document.getElementById('street-country-display');
    if (countryDisplay) countryDisplay.value = '';

    if (streetKey && streetsData[streetKey]) {
        const street = streetsData[streetKey];
        title.textContent = 'Редактирование улицы';
        subtitle.textContent = 'Измените параметры улицы';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Сохранить';

        form.action = '/settings/streets/' + streetId;
        document.getElementById('streetMethod').value = 'PUT';
        document.getElementById('streetName').value = street.name || '';

        // Set city (already in dropdown), then load districts/zones
        if (street.city_id) {
            $('#street-city-select').val(street.city_id).trigger('change');
            setTimeout(function() {
                if (street.district_id) {
                    $('#street-district-select').val(street.district_id).trigger('change');
                }
                if (street.zone_id) {
                    $('#street-zone-select').val(street.zone_id).trigger('change');
                }
            }, 500);
        }
    } else {
        title.textContent = 'Новая улица';
        subtitle.textContent = 'Добавьте улицу в справочник';
        submitBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Создать';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeStreetDrawer() {
    document.getElementById('drawerStreetOverlay').classList.remove('open');
    document.getElementById('drawerAddStreet').classList.remove('open');
    document.body.style.overflow = '';
}

function initStreetDrawerSelect2() {
    ['#street-city-select', '#street-district-select', '#street-zone-select'].forEach(function(sel) {
        if ($(sel).hasClass('select2-hidden-accessible')) {
            $(sel).select2('destroy');
        }
    });

    $('#street-city-select').select2({
        width: '100%',
        placeholder: 'Выберите город...',
        allowClear: true,
        dropdownParent: $('#drawerAddStreet')
    });

    $('#street-district-select').select2({
        width: '100%',
        placeholder: 'Выберите регион...',
        allowClear: true,
        dropdownParent: $('#drawerAddStreet')
    });

    $('#street-zone-select').select2({
        width: '100%',
        placeholder: 'Без микрорайона',
        allowClear: true,
        dropdownParent: $('#drawerAddStreet')
    });
}

// ========== CASCADING DROPDOWNS ==========
function loadCitiesByState(stateId, targetSelect, dropdownParent) {
    const $select = $(targetSelect);
    $select.empty().append('<option value="">Загрузка...</option>').trigger('change');

    if (!stateId) {
        $select.empty().append('<option value="">Выберите город...</option>').trigger('change');
        return;
    }

    fetch('/settings/locations/cities-by-state?state_id=' + stateId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        $select.empty().append('<option value="">Выберите город...</option>');
        if (data.results) {
            data.results.forEach(function(city) {
                $select.append('<option value="' + city.id + '">' + city.name + '</option>');
            });
        }
        $select.trigger('change');
    })
    .catch(function() {
        $select.empty().append('<option value="">Ошибка загрузки</option>').trigger('change');
    });
}

function loadDistrictsByCity(cityId, targetSelect) {
    const $select = $(targetSelect);
    $select.empty().append('<option value="">Загрузка...</option>').trigger('change');

    if (!cityId) {
        $select.empty().append('<option value="">Без района</option>').trigger('change');
        return;
    }

    fetch('/settings/locations/districts-by-city?city_id=' + cityId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        $select.empty().append('<option value="">Без района</option>');
        if (data.results) {
            data.results.forEach(function(district) {
                $select.append('<option value="' + district.id + '">' + district.name + '</option>');
            });
        }
        $select.trigger('change');
    })
    .catch(function() {
        $select.empty().append('<option value="">Ошибка загрузки</option>').trigger('change');
    });
}

function loadZonesByCity(cityId, targetSelect) {
    const $select = $(targetSelect);
    $select.empty().append('<option value="">Загрузка...</option>').trigger('change');

    if (!cityId) {
        $select.empty().append('<option value="">Без микрорайона</option>').trigger('change');
        return;
    }

    fetch('/settings/locations/zones-by-city?city_id=' + cityId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        $select.empty().append('<option value="">Без микрорайона</option>');
        if (data.results) {
            data.results.forEach(function(zone) {
                $select.append('<option value="' + zone.id + '">' + zone.name + '</option>');
            });
        }
        $select.trigger('change');
    })
    .catch(function() {
        $select.empty().append('<option value="">Ошибка загрузки</option>').trigger('change');
    });
}

// ========== SEARCH FILTERING ==========
function initLocationSearch(inputId, listSelector, itemSelector) {
    var input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('input', function() {
        var query = this.value.toLowerCase();
        var items = document.querySelectorAll(listSelector + ' ' + itemSelector);

        items.forEach(function(item) {
            var searchData = item.dataset.search || '';
            if (searchData.includes(query)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

// ========== TREE EXPAND/COLLAPSE ==========
function initTreeToggle() {
    document.querySelectorAll('#regionsTreeList .region-parent').forEach(function(item) {
        item.addEventListener('click', function(e) {
            // Don't toggle on button clicks
            if (e.target.closest('.btn-icon') || e.target.closest('.actions-cell')) return;

            var expand = this.querySelector('.tree-expand');
            if (expand) {
                expand.classList.toggle('open');

                var stateId = this.dataset.stateId;
                var children = document.querySelectorAll('.region-child[data-parent-state="' + stateId + '"]');
                children.forEach(function(child) {
                    if (child.style.display === 'none') {
                        child.style.display = '';
                    } else {
                        child.style.display = 'none';
                    }
                });
            }
        });
    });
}

// ========== PAGINATION: PER-PAGE CHANGE ==========
function changePerPage(perPage, sectionRoute) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Tree toggle
    initTreeToggle();

    // Search inputs
    initLocationSearch('searchRegionsInput', '#regionsTreeList', '.region-parent');
    initLocationSearch('searchCitiesInput', '#citiesAddressList', '.address-item');
    initLocationSearch('searchZonesInput', '#zonesAddressList', '.address-item');
    initLocationSearch('searchStreetsInput', '#streetsTable tbody', 'tr');

    // ===== Country drawer events =====
    var countryOverlay = document.getElementById('drawerCountryOverlay');
    if (countryOverlay) countryOverlay.addEventListener('click', closeCountryDrawer);
    var countryClose = document.getElementById('drawerCountryClose');
    if (countryClose) countryClose.addEventListener('click', closeCountryDrawer);
    var countryCancel = document.getElementById('drawerCountryCancel');
    if (countryCancel) countryCancel.addEventListener('click', closeCountryDrawer);

    // ===== State drawer events =====
    var stateOverlay = document.getElementById('drawerStateOverlay');
    if (stateOverlay) stateOverlay.addEventListener('click', closeStateDrawer);
    var stateClose = document.getElementById('drawerStateClose');
    if (stateClose) stateClose.addEventListener('click', closeStateDrawer);
    var stateCancel = document.getElementById('drawerStateCancel');
    if (stateCancel) stateCancel.addEventListener('click', closeStateDrawer);

    // ===== Region drawer events =====
    var regionOverlay = document.getElementById('drawerRegionOverlay');
    if (regionOverlay) regionOverlay.addEventListener('click', closeRegionDrawer);
    var regionClose = document.getElementById('drawerRegionClose');
    if (regionClose) regionClose.addEventListener('click', closeRegionDrawer);
    var regionCancel = document.getElementById('drawerRegionCancel');
    if (regionCancel) regionCancel.addEventListener('click', closeRegionDrawer);

    // ===== District drawer events =====
    var districtOverlay = document.getElementById('drawerDistrictOverlay');
    if (districtOverlay) districtOverlay.addEventListener('click', closeDistrictDrawer);
    var districtClose = document.getElementById('drawerDistrictClose');
    if (districtClose) districtClose.addEventListener('click', closeDistrictDrawer);
    var districtCancel = document.getElementById('drawerDistrictCancel');
    if (districtCancel) districtCancel.addEventListener('click', closeDistrictDrawer);

    // ===== City drawer events =====
    var cityOverlay = document.getElementById('drawerCityOverlay');
    if (cityOverlay) cityOverlay.addEventListener('click', closeCityDrawer);
    var cityClose = document.getElementById('drawerCityClose');
    if (cityClose) cityClose.addEventListener('click', closeCityDrawer);
    var cityCancel = document.getElementById('drawerCityCancel');
    if (cityCancel) cityCancel.addEventListener('click', closeCityDrawer);

    // ===== Zone drawer events =====
    var zoneOverlay = document.getElementById('drawerZoneOverlay');
    if (zoneOverlay) zoneOverlay.addEventListener('click', closeZoneDrawer);
    var zoneClose = document.getElementById('drawerZoneClose');
    if (zoneClose) zoneClose.addEventListener('click', closeZoneDrawer);
    var zoneCancel = document.getElementById('drawerZoneCancel');
    if (zoneCancel) zoneCancel.addEventListener('click', closeZoneDrawer);

    // ===== Street drawer events =====
    var streetOverlay = document.getElementById('drawerStreetOverlay');
    if (streetOverlay) streetOverlay.addEventListener('click', closeStreetDrawer);
    var streetClose = document.getElementById('drawerStreetClose');
    if (streetClose) streetClose.addEventListener('click', closeStreetDrawer);
    var streetCancel = document.getElementById('drawerStreetCancel');
    if (streetCancel) streetCancel.addEventListener('click', closeStreetDrawer);

    // ===== Cascading: City drawer =====
    $(document).on('change', '#city-state-select', function() {
        var countryDisplay = document.getElementById('city-country-display');
        if (countryDisplay) {
            var selectedOption = $(this).find('option:selected');
            var countryName = selectedOption.data('country-name') || '';
            countryDisplay.value = countryName || '';
        }
    });

    // ===== Cascading: Zone drawer =====
    $(document).on('change', '#zone-state-select', function() {
        var stateId = $(this).val();
        loadCitiesByState(stateId, '#zone-city-select', '#drawerAddZone');

        // Auto-fill country from selected state option
        var countryDisplay = document.getElementById('zone-country-display');
        if (countryDisplay) {
            var selectedOption = $(this).find('option:selected');
            var countryName = selectedOption.data('country-name') || '';
            countryDisplay.value = countryName || '';
        }
    });

    // ===== Cascading: Street drawer =====
    $(document).on('change', '#street-city-select', function() {
        var cityId = $(this).val();
        loadDistrictsByCity(cityId, '#street-district-select');
        loadZonesByCity(cityId, '#street-zone-select');

        // Auto-fill country from selected city option
        var countryDisplay = document.getElementById('street-country-display');
        if (countryDisplay) {
            var selectedOption = $(this).find('option:selected');
            var countryName = selectedOption.data('country-name') || '';
            countryDisplay.value = countryName || '';
        }
    });

    // ===== Escape key closes location drawers =====
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCountryDrawer();
            closeStateDrawer();
            closeRegionDrawer();
            closeDistrictDrawer();
            closeCityDrawer();
            closeZoneDrawer();
            closeStreetDrawer();
        }
    });
});
