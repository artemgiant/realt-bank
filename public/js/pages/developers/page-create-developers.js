/**
 * Developers Create Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    initSelect2();

    // Initialize phone input with intl-tel-input
    initPhoneInput();

    // Initialize date picker
    initDatePicker();

    // Initialize logo preview
    initLogoPreview();

    // Initialize contact modal
    initContactModal();

    // Initialize location search
    initLocationSearch();
});

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
 * Initialize phone input with international format
 */
function initPhoneInput() {
    const phoneInputs = document.querySelectorAll('.tel-contact');

    phoneInputs.forEach(function(input) {
        if (typeof intlTelInput !== 'undefined') {
            intlTelInput(input, {
                initialCountry: 'ua',
                preferredCountries: ['ua', 'ru', 'pl', 'de'],
                separateDialCode: true,
                utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js'
            });
        }
    });
}

/**
 * Initialize date picker for birthday field
 */
function initDatePicker() {
    const dateInputs = document.querySelectorAll('.date-piker');

    dateInputs.forEach(function(input) {
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
        logoInput.addEventListener('change', function(e) {
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
                reader.onload = function(event) {
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

/**
 * Initialize contact modal functionality
 */
function initContactModal() {
    const modal = document.getElementById('add-contact-modal');
    const form = document.getElementById('contact-modal-form');
    const saveBtn = document.getElementById('save-contact-btn');

    if (!modal || !form) return;

    // Reset form when modal is closed
    modal.addEventListener('hidden.bs.modal', function() {
        form.reset();
        document.getElementById('contact-id-modal').value = '';
        document.getElementById('contact-found-indicator').classList.add('d-none');

        // Reset phone input
        const phoneInputs = form.querySelectorAll('.tel-contact');
        phoneInputs.forEach(function(input) {
            if (input.iti) {
                input.iti.setNumber('');
            }
        });
    });

    // Handle form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        saveContact();
    });

    // Phone search on blur
    const phoneInput = document.getElementById('tel-contact1-modal');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            searchContactByPhone(this.value);
        });
    }
}

/**
 * Search contact by phone number
 */
function searchContactByPhone(phone) {
    if (!phone || phone.length < 10) return;

    // Clean phone number
    const cleanPhone = phone.replace(/\D/g, '');

    fetch(`/contacts/ajax-search-by-phone?phone=${encodeURIComponent(cleanPhone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.contact) {
                fillContactForm(data.contact);
                document.getElementById('contact-found-indicator').classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error searching contact:', error);
        });
}

/**
 * Fill contact form with data
 */
function fillContactForm(contact) {
    document.getElementById('contact-id-modal').value = contact.id || '';
    document.getElementById('first-name-contact-modal').value = contact.first_name || '';
    document.getElementById('last-name-contact-modal').value = contact.last_name || '';
    document.getElementById('middle-name-contact-modal').value = contact.middle_name || '';
    document.getElementById('email-contact-modal').value = contact.email || '';
    document.getElementById('telegram-contact-modal').value = contact.telegram || '';
    document.getElementById('viber-contact-modal').value = contact.viber || '';
    document.getElementById('whatsapp-contact-modal').value = contact.whatsapp || '';
    document.getElementById('passport-contact-modal').value = contact.passport || '';
    document.getElementById('inn-contact-modal').value = contact.inn || '';

    // Set contact type
    const typeSelect = document.getElementById('type-contact-modal');
    if (typeSelect && contact.contact_type) {
        $(typeSelect).val(contact.contact_type).trigger('change');
    }
}

/**
 * Save contact from modal
 */
function saveContact() {
    const form = document.getElementById('contact-modal-form');
    const saveBtn = document.getElementById('save-contact-btn');
    const spinner = saveBtn.querySelector('.spinner-border');

    // Show loading state
    spinner.classList.remove('d-none');
    saveBtn.disabled = true;

    const formData = new FormData(form);
    const contactId = formData.get('contact_id');

    // Determine if we're updating or creating
    const url = contactId ? `/contacts/${contactId}/ajax` : '/contacts/ajax-store';
    const method = contactId ? 'PUT' : 'POST';

    // Convert FormData to JSON for PUT request
    let body;
    let headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
    };

    if (method === 'PUT') {
        const jsonData = {};
        formData.forEach((value, key) => {
            // Handle array fields like phones[0][phone]
            if (key.includes('[')) {
                const matches = key.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    const [, arrayName, index, fieldName] = matches;
                    if (!jsonData[arrayName]) jsonData[arrayName] = [];
                    if (!jsonData[arrayName][index]) jsonData[arrayName][index] = {};
                    jsonData[arrayName][index][fieldName] = value;
                }
            } else {
                jsonData[key] = value;
            }
        });
        body = JSON.stringify(jsonData);
        headers['Content-Type'] = 'application/json';
    } else {
        body = formData;
    }

    fetch(url, {
        method: method,
        headers: headers,
        body: body
    })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.contact) {
                const contact = data.contact || data;

                // Update hidden field in main form
                document.getElementById('contact_id').value = contact.id;

                // Update contact card display
                updateContactCard(contact);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('add-contact-modal'));
                modal.hide();

                // Show success message
                showToast('Контакт успешно сохранен', 'success');
            } else {
                showToast(data.message || 'Ошибка сохранения контакта', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving contact:', error);
            showToast('Ошибка сохранения контакта', 'error');
        })
        .finally(() => {
            spinner.classList.add('d-none');
            saveBtn.disabled = false;
        });
}

/**
 * Update contact card in the main form
 */
function updateContactCard(contact) {
    const emptyState = document.getElementById('contact-empty');
    const contactData = document.getElementById('contact-data');
    const editBtn = document.querySelector('.btn-edit-client');
    const addBtn = document.querySelector('.btn-add-client');

    if (!contact) {
        // Show empty state
        emptyState.style.display = 'block';
        contactData.style.display = 'none';
        editBtn.style.display = 'none';
        addBtn.style.display = 'block';
        return;
    }

    // Hide empty state, show contact data
    emptyState.style.display = 'none';
    contactData.style.display = 'block';
    editBtn.style.display = 'block';
    addBtn.style.display = 'none';

    // Update contact info
    const fullName = [contact.first_name, contact.last_name].filter(Boolean).join(' ') || '-';
    document.getElementById('contact-name').textContent = fullName;
    document.getElementById('contact-type').textContent = contact.contact_type_name || 'Представитель девелопера';

    // Update phone
    const phoneEl = document.getElementById('contact-phone');
    const primaryPhone = contact.phones && contact.phones[0] ? contact.phones[0].phone : (contact.phone || '-');
    phoneEl.textContent = primaryPhone;
    phoneEl.href = `tel:${primaryPhone.replace(/\D/g, '')}`;

    // Update messenger links
    updateMessengerLink('contact-telegram', contact.telegram, 'https://t.me/');
    updateMessengerLink('contact-viber', contact.viber, 'viber://chat?number=');
    updateMessengerLink('contact-whatsapp', contact.whatsapp, 'https://wa.me/');
}

/**
 * Update messenger link visibility and href
 */
function updateMessengerLink(elementId, value, baseUrl) {
    const element = document.getElementById(elementId);
    if (!element) return;

    if (value) {
        element.style.display = 'flex';
        element.href = baseUrl + value.replace('@', '');
    } else {
        element.style.display = 'none';
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        background: ${type === 'success' ? '#22C55E' : type === 'error' ? '#EF4444' : '#3585F5'};
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
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

// Make removeLogo available globally
window.removeLogo = removeLogo;

/**
 * Initialize location search with Select2
 */
let locationIndex = 0;

function initLocationSearch() {
    // Initialize first location Select2
    initLocationSelect2('#location-search-0');
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
            noResults: function() {
                return 'Ничего не найдено';
            },
            searching: function() {
                return 'Поиск...';
            }
        },
        ajax: {
            url: '/location/search-all',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function(data) {
                return {
                    results: data.results.map(function(item) {
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

    items.forEach(function(item, index) {
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
