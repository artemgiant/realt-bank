/**
 * Duplicate Address Checker
 * Проверяет наличие объектов с таким же адресом при создании/редактировании
 */
(function () {
    'use strict';

    const CHECK_URL = '/properties/check-duplicate-address';

    let debounceTimer = null;
    let lastCheckedKey = '';

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function getAddressFields() {
        return {
            street_id: document.querySelector('input[name="street_id"]')?.value || '',
            building_number: document.querySelector('#number-house')?.value?.trim() || '',
            apartment_number: document.querySelector('#number-apartment')?.value?.trim() || '',
        };
    }

    function buildCheckKey(fields) {
        return [fields.street_id, fields.building_number, fields.apartment_number].join('|');
    }

    function getContainer() {
        return document.getElementById('duplicate-address-warning');
    }

    function hideWarning() {
        const container = getContainer();
        if (container) {
            container.style.display = 'none';
            container.innerHTML = '';
        }
    }

    function showWarning(duplicates) {
        const container = getContainer();
        if (!container) return;

        const rows = duplicates.map(function (d) {
            const parts = [];
            if (d.address) parts.push(d.address);
            if (d.apartment_number) parts.push('кв. ' + d.apartment_number);

            const info = [];
            if (d.price && d.currency) info.push(d.price + ' ' + d.currency);
            if (d.agent) info.push(d.agent);
            if (d.status) info.push(d.status);

            return '<li style="margin-bottom:6px;">' +
                '<a href="' + d.edit_url + '" target="_blank" style="color:#856404;text-decoration:underline;font-weight:600;">' +
                'ID ' + d.id + '</a> — ' +
                parts.join(', ') +
                (info.length ? ' <span style="color:#666;">(' + info.join(' | ') + ')</span>' : '') +
                '</li>';
        }).join('');

        container.innerHTML =
            '<div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:12px 16px;margin-bottom:12px;">' +
            '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">' +
            '<svg width="18" height="18" viewBox="0 0 16 16" fill="#856404"><path d="M8 1a7 7 0 100 14A7 7 0 008 1zm0 11a1 1 0 110-2 1 1 0 010 2zm1-4H7V4h2v4z"/></svg>' +
            '<strong style="color:#856404;">Обнаружены объекты с таким же адресом!</strong>' +
            '</div>' +
            '<ul style="margin:0;padding-left:20px;list-style:disc;">' + rows + '</ul>' +
            '</div>';

        container.style.display = 'block';
    }

    function checkDuplicate() {
        var fields = getAddressFields();

        if (!fields.street_id || !fields.building_number) {
            hideWarning();
            lastCheckedKey = '';
            return;
        }

        var key = buildCheckKey(fields);
        if (key === lastCheckedKey) return;
        lastCheckedKey = key;

        var params = new URLSearchParams({
            street_id: fields.street_id,
            building_number: fields.building_number,
        });
        if (fields.apartment_number) {
            params.append('apartment_number', fields.apartment_number);
        }

        // Исключаем текущий объект при редактировании
        var propertyIdMeta = document.querySelector('input[name="property_id"]');
        if (propertyIdMeta && propertyIdMeta.value) {
            params.append('exclude_id', propertyIdMeta.value);
        }

        fetch(CHECK_URL + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.exists && data.duplicates && data.duplicates.length > 0) {
                    showWarning(data.duplicates);
                } else {
                    hideWarning();
                }
            })
            .catch(function () {
                // Не блокируем работу формы при ошибке
            });
    }

    function debouncedCheck() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(checkDuplicate, 400);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var houseInput = document.getElementById('number-house');
        var aptInput = document.getElementById('number-apartment');

        if (houseInput) {
            houseInput.addEventListener('input', debouncedCheck);
            houseInput.addEventListener('blur', checkDuplicate);
        }
        if (aptInput) {
            aptInput.addEventListener('input', debouncedCheck);
            aptInput.addEventListener('blur', checkDuplicate);
        }

        // При выборе улицы — проверяем если дом уже заполнен
        document.addEventListener('streetSelected', function () {
            setTimeout(checkDuplicate, 100);
        });

        // При очистке улицы — скрываем
        document.addEventListener('stateSelected', hideWarning);
        document.addEventListener('stateCleared', hideWarning);
    });
})();
