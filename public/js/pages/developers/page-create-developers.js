/**
 * Developers Create Page JavaScript
 */

let locationIndex = 0;

/**
 * Initialize Select2 dropdowns
 */
function initSelect2() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.my-select2').select2({
            placeholder: 'Выберите...',
            allowClear: true,
            width: '100%'
        });

        $('.js-example-responsive2').select2({
            placeholder: 'Выберите...',
            allowClear: true,
            width: '100%'
        });
    }
}

/**
 * Initialize date picker for birthday field
 */
function initDatePicker() {
    const dateInputs = document.querySelectorAll('.date-piker');

    dateInputs.forEach(function (input) {
        if (typeof $.fn.daterangepicker !== 'undefined') {
            $(input).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                locale: {
                    format: 'DD.MM.YYYY',
                    daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                    monthNames: [
                        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
                    ],
                    firstDay: 1
                },
                drops: 'auto'
            });
        }
    });
}

/**
 * Initialize logo preview
 */
function initLogoPreview() {
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Разрешены только файлы: JPEG, PNG, WebP');
                    logoInput.value = '';
                    return;
                }

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Максимальный размер файла 2MB');
                    logoInput.value = '';
                    return;
                }

                // Create preview
                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreview.innerHTML = `
                        <div class="logo-preview-item">
                            <img src="${event.target.result}" alt="Logo preview" style="max-width: 100px; max-height: 100px; border-radius: 8px;">
                            <button type="button" class="btn-remove-logo" onclick="removeLogo()">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5 3.5L3.5 12.5M3.5 3.5L12.5 12.5" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Remove logo preview
 */
function removeLogo() {
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');

    if (logoInput) {
        logoInput.value = '';
    }

    if (logoPreview) {
        logoPreview.innerHTML = '';
    }
}

// Add CSS animation for logo preview
const style = document.createElement('style');
style.textContent = `
    .logo-preview-item {
        position: relative;
        display: inline-block;
    }
    .btn-remove-logo {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #E5E5E5;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-remove-logo:hover {
        background: #FEF2F2;
        border-color: #EF4444;
    }
`;
document.head.appendChild(style);

// Make functions available globally
window.removeLogo = removeLogo;

/**
 * Initialize location search with Select2
 */
function initLocationSearch() {
    // Initialize all existing location Select2 fields
    $('.location-search').each(function () {
        initLocationSelect2(this);

        // Track the highest index to avoid conflicts when adding new locations
        const id = $(this).attr('id');
        if (id) {
            const match = id.match(/location-search-(\d+)/);
            if (match) {
                const index = parseInt(match[1]);
                if (index > locationIndex) {
                    locationIndex = index;
                }
            }
        }
    });
}

/**
 * Initialize Select2 for location search
 */
function initLocationSelect2(selector) {
    if (typeof $.fn.select2 === 'undefined') return;

    $(selector).select2({
        placeholder: 'Введите страну, область или город',
        allowClear: false,
        width: '100%',
        minimumInputLength: 0,
        language: {
            noResults: function () {
                return 'Ничего не найдено';
            },
            searching: function () {
                return 'Поиск...';
            }
        },
        ajax: {
            url: '/location/search-all',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.type + ':' + item.id,
                            text: item.full_name || item.name,
                            type: item.type,
                            location_id: item.id,
                            name: item.name,
                            full_name: item.full_name
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: formatLocationResult,
        templateSelection: formatLocationSelection
    });
}

/**
 * Format location result in dropdown
 */
function formatLocationResult(item) {
    if (item.loading) {
        return item.text;
    }

    const typeLabels = {
        'country': 'Страна',
        'state': 'Область',
        'city': 'Город'
    };

    const $container = $(
        '<div class="select2-location-result">' +
        '<span class="location-text">' + item.text + '</span>' +
        '<span class="location-type-badge">' + (typeLabels[item.type] || '') + '</span>' +
        '</div>'
    );

    return $container;
}

/**
 * Format selected location
 */
function formatLocationSelection(item) {
    if (!item.id) {
        return item.text;
    }
    return item.text;
}

/**
 * Add new location field
 */
function addLocation() {
    locationIndex++;
    const container = document.getElementById('locations-container');

    const locationItem = document.createElement('div');
    locationItem.className = 'location-item';
    locationItem.setAttribute('data-location-index', locationIndex);
    locationItem.innerHTML = `
        <div class="location-search-wrapper">
            <select class="location-search" id="location-search-${locationIndex}" name="locations[${locationIndex}][location]">
                <option value="">Выберите страну, область или город</option>
            </select>
            <button type="button" class="btn-remove-location" onclick="removeLocation(this)">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 3.5L3.5 12.5M3.5 3.5L12.5 12.5" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(locationItem);

    // Initialize Select2 for new field
    initLocationSelect2('#location-search-' + locationIndex);

    // Show remove button on first location if we have more than one
    updateRemoveButtons();
}

/**
 * Remove location field
 */
function removeLocation(button) {
    const locationItem = button.closest('.location-item');
    const container = document.getElementById('locations-container');

    // Don't remove if it's the only one
    if (container.querySelectorAll('.location-item').length <= 1) {
        return;
    }

    // Destroy Select2 before removing
    const select = locationItem.querySelector('.location-search');
    if (select && $(select).data('select2')) {
        $(select).select2('destroy');
    }

    locationItem.remove();

    // Update remove buttons visibility
    updateRemoveButtons();
}

/**
 * Update visibility of remove buttons
 */
function updateRemoveButtons() {
    const container = document.getElementById('locations-container');
    const items = container.querySelectorAll('.location-item');

    items.forEach(function (item, index) {
        const removeBtn = item.querySelector('.btn-remove-location');
        if (removeBtn) {
            // Show remove button only if there's more than one location
            removeBtn.style.display = items.length > 1 ? 'flex' : 'none';
        }
    });
}

// Make functions available globally
window.addLocation = addLocation;
window.removeLocation = removeLocation;

// Main initialization script
(function () {
    function init() {
        // Initialize Select2
        initSelect2();

        // Initialize date picker
        initDatePicker();

        // Initialize logo preview
        initLogoPreview();

        // Initialize location search
        initLocationSearch();

        // Update remove buttons visibility
        updateRemoveButtons();

        // ContactModal module initializes automatically from main.js
    }

    // Если DOM уже загружен - инициализируем сразу, иначе ждём
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
