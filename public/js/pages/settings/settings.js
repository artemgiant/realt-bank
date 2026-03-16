/**
 * Settings Page JavaScript
 */

// ========== GLOBAL DATA ==========
// These are populated from the Blade template BEFORE this script loads
// Do NOT redeclare them here - they already exist as global variables

// ========== SECTION NAVIGATION ==========
function showSection(name) {
    // Hide all sections
    document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));

    // Show target section
    const target = document.getElementById('section-' + name);
    if (target) target.classList.add('active');

    // Update nav items
    document.querySelectorAll('.settings-nav .nav-item').forEach(n => n.classList.remove('active'));
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

// ========== DRAWER: ROLE ==========
function openRoleDrawer(roleId = null) {
    const drawer = document.getElementById('drawerAddRole');
    const overlay = document.getElementById('drawerOverlay');
    const form = document.getElementById('roleForm');
    const title = drawer.querySelector('.drawer-header h3');
    const subtitle = drawer.querySelector('.drawer-subtitle');
    const submitBtn = drawer.querySelector('.drawer-footer .btn-primary');

    // Reset form
    form.reset();
    form.action = '/settings/roles';
    document.getElementById('roleMethod').value = 'POST';

    // Reset type cards
    document.querySelectorAll('.drawer-type-card input').forEach(input => input.checked = false);
    document.querySelector('.drawer-type-card input[value="agent"]').checked = true;

    // Reset Select2
    if ($('#role-copy-from-select').hasClass('select2-hidden-accessible')) {
        $('#role-copy-from-select').val('').trigger('change');
    }

    // Convert to string for JSON key lookup
    const roleKey = roleId ? String(roleId) : null;

    if (roleKey && rolesData[roleKey]) {
        const role = rolesData[roleKey];
        title.textContent = 'Редактирование роли';
        subtitle.textContent = 'Измените параметры роли';
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Сохранить
        `;

        form.action = '/settings/roles/' + roleId;
        document.getElementById('roleMethod').value = 'PUT';

        document.getElementById('roleDisplayName').value = role.display_name || '';
        document.getElementById('roleName').value = role.name || '';
        document.getElementById('roleDescription').value = role.description || '';

        // Set type radio
        const typeRadio = document.querySelector(`.drawer-type-card input[value="${role.type || 'agent'}"]`);
        if (typeRadio) typeRadio.checked = true;
    } else {
        title.textContent = 'Новая роль';
        subtitle.textContent = 'Создайте роль и настройте уровень доступа';
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Создать роль
        `;
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Initialize Select2 after drawer opens
    initRoleDrawerSelect2();
}

function closeRoleDrawer() {
    document.getElementById('drawerOverlay').classList.remove('open');
    document.getElementById('drawerAddRole').classList.remove('open');
    document.body.style.overflow = '';
}

// Alias for backwards compatibility
function openDrawer() {
    openRoleDrawer();
}

function closeDrawer() {
    closeRoleDrawer();
}

// ========== SELECT2: ROLE DRAWER ==========
function initRoleDrawerSelect2() {
    // Destroy existing instance first
    if ($('#role-copy-from-select').hasClass('select2-hidden-accessible')) {
        $('#role-copy-from-select').select2('destroy');
    }

    // Initialize Select2 for copy from role select (with search)
    $('#role-copy-from-select').select2({
        width: '100%',
        placeholder: 'Не копировать',
        allowClear: true,
        dropdownParent: $('#drawerAddRole')
    });
}

// ========== DRAWER: USER ==========
function openUserDrawer(userId = null) {
    const drawer = document.getElementById('drawerAddUser');
    const overlay = document.getElementById('drawerUserOverlay');
    const form = document.getElementById('userForm');
    const title = drawer.querySelector('.drawer-header h3');
    const subtitle = drawer.querySelector('.drawer-subtitle');
    const submitBtn = drawer.querySelector('.drawer-footer .btn-primary');
    const passwordGroup = document.getElementById('passwordGroup');
    const passwordInput = document.getElementById('userPassword');

    // Reset form
    form.reset();
    form.action = '/settings/users';
    document.getElementById('userMethod').value = 'POST';

    // Reset Select2
    if ($('#user-role-select').hasClass('select2-hidden-accessible')) {
        $('#user-role-select').val('').trigger('change');
    }
    if ($('#user-employee-select').hasClass('select2-hidden-accessible')) {
        $('#user-employee-select').val('').trigger('change');
    }

    // Convert to string for JSON key lookup
    const userKey = userId ? String(userId) : null;

    if (userKey && usersData && usersData[userKey]) {
        const user = usersData[userKey];
        title.textContent = 'Редактирование пользователя';
        subtitle.textContent = 'Измените данные учётной записи';
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Сохранить
        `;

        form.action = '/settings/users/' + userId;
        document.getElementById('userMethod').value = 'PUT';

        document.getElementById('userName').value = user.name || '';
        document.getElementById('userEmail').value = user.email || '';

        // Phone will be set AFTER initUserPhoneInput() is called (see below)
        document.getElementById('userPhone').value = '';

        // Password: leave empty, not required for edit
        passwordInput.value = '';
        passwordInput.removeAttribute('required');
        if (passwordGroup.querySelector('.form-label .required')) {
            passwordGroup.querySelector('.form-label .required').style.display = 'none';
        }
        passwordInput.placeholder = 'Оставьте пустым, чтобы не менять';

        // Set role
        if (user.roles && user.roles.length > 0) {
            $('#user-role-select').val(user.roles[0].id).trigger('change');
        }

        // Set employee (employee has user_id, so we get it from user.employee)
        if (user.employee && user.employee.id) {
            $('#user-employee-select').val(user.employee.id).trigger('change');
        }

        // Set active status
        document.getElementById('userActive').checked = user.is_active ?? true;
    } else {
        title.textContent = 'Новый пользователь';
        subtitle.textContent = 'Создайте учётную запись и назначьте роль';
        submitBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Создать пользователя
        `;

        // Password required for create
        passwordInput.setAttribute('required', 'required');
        if (passwordGroup.querySelector('.form-label .required')) {
            passwordGroup.querySelector('.form-label .required').style.display = '';
        }
        passwordInput.placeholder = 'Минимум 8 символов';
    }

    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Initialize phone with intl-tel-input FIRST
    initUserPhoneInput();

    // Set phone value AFTER intl-tel-input is initialized
    if (userKey && usersData && usersData[userKey]) {
        const user = usersData[userKey];
        if (userPhoneIti && user.phone) {
            var digits = user.phone.replace(/\D/g, '');
            userPhoneIti.setNumber('+' + digits);
        }
    }

    // Initialize Select2 after drawer opens
    initDrawerSelect2();
}

function closeUserDrawer() {
    document.getElementById('drawerUserOverlay').classList.remove('open');
    document.getElementById('drawerAddUser').classList.remove('open');
    document.body.style.overflow = '';
}

// ========== CONFIRM DELETE MODAL ==========
let deleteTarget = null;

// Delete type labels and warnings
var deleteTypeLabels = {
    role: 'Удалить роль?',
    user: 'Удалить пользователя?',
    country: 'Удалить страну?',
    state: 'Удалить регион?',
    region: 'Удалить район области?',
    district: 'Удалить район?',
    city: 'Удалить город?',
    zone: 'Удалить микрорайон?',
    street: 'Удалить улицу?'
};

function openDeleteModal(btn) {
    const type = btn.dataset.type;
    const name = btn.dataset.name;
    const users = parseInt(btn.dataset.users) || 0;
    const id = btn.dataset.id;

    deleteTarget = { type, name, users, id };

    const overlay = document.getElementById('confirmDeleteOverlay');
    const modal = document.getElementById('confirmDeleteModal');
    const title = document.getElementById('confirmDeleteTitle');
    const nameSpan = document.getElementById('confirmDeleteName');
    const warning = document.getElementById('confirmDeleteWarning');
    const warningText = document.getElementById('confirmDeleteWarningText');

    title.textContent = deleteTypeLabels[type] || 'Подтвердите удаление';
    nameSpan.textContent = name;

    if (users > 0 && type !== 'street') {
        warning.classList.add('show');
        if (type === 'role') {
            const userWord = users === 1 ? 'пользователь' :
                            (users >= 2 && users <= 4) ? 'пользователя' : 'пользователей';
            warningText.textContent = `У этой роли есть ${users} ${userWord}. Сначала переназначьте их на другую роль.`;
        } else if (type === 'country') {
            warningText.textContent = `В этой стране есть ${users} регион(ов). Сначала удалите или перенесите их.`;
        } else if (type === 'state') {
            warningText.textContent = `В этом регионе есть ${users} город(ов). Сначала удалите или перенесите их.`;
        } else if (type === 'region') {
            warningText.textContent = `В этом районе области есть ${users} город(ов). Сначала удалите или перенесите их.`;
        } else if (type === 'city') {
            warningText.textContent = `В этом городе есть ${users} улиц(а). Сначала удалите или перенесите их.`;
        } else if (type === 'district') {
            warningText.textContent = `В этом районе есть ${users} улиц(а). Сначала удалите или перенесите их.`;
        } else if (type === 'zone') {
            warningText.textContent = `В этом микрорайоне есть ${users} улиц(а). Сначала удалите или перенесите их.`;
        }
    } else {
        warning.classList.remove('show');
    }

    overlay.classList.add('open');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('confirmDeleteOverlay').classList.remove('open');
    document.getElementById('confirmDeleteModal').classList.remove('open');
    document.body.style.overflow = '';
    deleteTarget = null;
}

function confirmDelete() {
    if (!deleteTarget) {
        closeDeleteModal();
        return;
    }

    // Don't delete items with dependencies
    if (deleteTarget.users > 0 && deleteTarget.type !== 'street') {
        closeDeleteModal();
        return;
    }

    // Build correct plural route path
    var typePlurals = { role: 'roles', user: 'users', country: 'countries', state: 'states', region: 'regions', district: 'districts', city: 'cities', zone: 'zones', street: 'streets' };
    var typePlural = typePlurals[deleteTarget.type] || (deleteTarget.type + 's');

    // Create and submit delete form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/settings/' + typePlural + '/' + deleteTarget.id;

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    document.body.appendChild(form);
    form.submit();
}

// Legacy function for backwards compatibility
function openConfirmDelete(options) {
    openDeleteModal({
        dataset: {
            type: options.type,
            name: options.name,
            users: '0',
            id: ''
        }
    });
}

function closeConfirmDelete() {
    closeDeleteModal();
}

// ========== SELECT2 INITIALIZATION ==========
function initDrawerSelect2() {
    // Destroy existing instances first
    if ($('#user-role-select').hasClass('select2-hidden-accessible')) {
        $('#user-role-select').select2('destroy');
    }
    if ($('#user-employee-select').hasClass('select2-hidden-accessible')) {
        $('#user-employee-select').select2('destroy');
    }

    // Initialize Select2 for role select (no search)
    $('#user-role-select').select2({
        width: '100%',
        minimumResultsForSearch: -1,
        placeholder: 'Выберите роль...',
        allowClear: false,
        dropdownParent: $('#drawerAddUser')
    });

    // Initialize Select2 for employee select (with search)
    $('#user-employee-select').select2({
        width: '100%',
        placeholder: 'Выберите сотрудника...',
        allowClear: true,
        dropdownParent: $('#drawerAddUser')
    });
}

// ========== PHONE INPUT (intl-tel-input) ==========
var userPhoneIti = null;

function initUserPhoneInput() {
    var phoneInput = document.getElementById('userPhone');
    if (!phoneInput || typeof intlTelInput === 'undefined') return;

    // Destroy previous instance
    if (userPhoneIti) {
        userPhoneIti.destroy();
        userPhoneIti = null;
    }

    userPhoneIti = intlTelInput(phoneInput, {
        initialCountry: 'ua',
        separateDialCode: true,
        nationalMode: true,
        autoPlaceholder: 'off',
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js',
    });

    var countryMasks = {
        'ua': '(99) 999-99-99',
        'us': '(999) 999-9999',
        'gb': '9999 999999',
        'de': '999 99999999',
        'pl': '999 999 999',
        'default': '999 999 9999'
    };

    function applyPhoneMask(countryCode) {
        var mask = countryMasks[countryCode] || countryMasks['default'];
        if (typeof $ !== 'undefined' && $.fn.mask) {
            $(phoneInput).unmask().mask(mask, { placeholder: '_' });
        }
    }

    applyPhoneMask('ua');

    phoneInput.addEventListener('countrychange', function() {
        var data = userPhoneIti.getSelectedCountryData();
        applyPhoneMask(data.iso2);
    });
}

function buildFullPhone() {
    if (!userPhoneIti) return '';
    var data = userPhoneIti.getSelectedCountryData();
    var dialCode = '+' + data.dialCode;
    var national = document.getElementById('userPhone').value.trim();
    if (!national) return '';

    // UA: plugin shows +380, mask gives "(95) 090-22-93" (9 digits, no leading 0)
    // DB stores: "+38 (095) 090-22-93" — need to insert 0 after "("
    if (data.iso2 === 'ua') {
        var digits = national.replace(/\D/g, '');
        return '+38 (0' + digits.substr(0, 2) + ') ' + digits.substr(2, 3) + '-' + digits.substr(5, 2) + '-' + digits.substr(7, 2);
    }
    return dialCode + ' ' + national;
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // ===== DRAWER: ROLE =====
    const drawerOverlay = document.getElementById('drawerOverlay');
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', closeRoleDrawer);
    }

    const drawerClose = document.getElementById('drawerClose');
    if (drawerClose) {
        drawerClose.addEventListener('click', closeRoleDrawer);
    }

    const drawerCancel = document.getElementById('drawerCancel');
    if (drawerCancel) {
        drawerCancel.addEventListener('click', closeRoleDrawer);
    }

    // ===== DRAWER: USER =====
    const drawerUserOverlay = document.getElementById('drawerUserOverlay');
    if (drawerUserOverlay) {
        drawerUserOverlay.addEventListener('click', closeUserDrawer);
    }

    const drawerUserClose = document.getElementById('drawerUserClose');
    if (drawerUserClose) {
        drawerUserClose.addEventListener('click', closeUserDrawer);
    }

    const drawerUserCancel = document.getElementById('drawerUserCancel');
    if (drawerUserCancel) {
        drawerUserCancel.addEventListener('click', closeUserDrawer);
    }

    // ===== CONFIRM DELETE MODAL =====
    const confirmDeleteOverlay = document.getElementById('confirmDeleteOverlay');
    if (confirmDeleteOverlay) {
        confirmDeleteOverlay.addEventListener('click', closeDeleteModal);
    }

    const confirmDeleteCancel = document.getElementById('confirmDeleteCancel');
    if (confirmDeleteCancel) {
        confirmDeleteCancel.addEventListener('click', closeDeleteModal);
    }

    const confirmDeleteSubmit = document.getElementById('confirmDeleteSubmit');
    if (confirmDeleteSubmit) {
        confirmDeleteSubmit.addEventListener('click', confirmDelete);
    }

    // ===== USER FORM SUBMIT: build full phone =====
    var userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function() {
            document.getElementById('userPhoneHidden').value = buildFullPhone();
        });
    }

    // ===== CLOSE ALL MODALS ON ESCAPE =====
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRoleDrawer();
            closeUserDrawer();
            closeDeleteModal();
        }
    });

    // ===== TAB SWITCHING =====
    document.querySelectorAll('.content-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Update active tab
            tab.parentElement.querySelectorAll('.content-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Switch permission tab content if data-perm-tab exists
            const permTab = tab.dataset.permTab;
            if (permTab) {
                document.querySelectorAll('.perm-tab-content').forEach(content => content.classList.remove('active'));
                const targetContent = document.getElementById('perm-' + permTab);
                if (targetContent) targetContent.classList.add('active');
            }
        });
    });

    // ===== TREE EXPAND/COLLAPSE =====
    document.querySelectorAll('.tree-item.level-1').forEach(item => {
        item.addEventListener('click', function() {
            const expand = this.querySelector('.tree-expand');
            if (expand && !expand.classList.contains('empty')) {
                expand.classList.toggle('open');
            }
        });
    });

    // ===== SEARCH USERS =====
    const searchUsersInput = document.getElementById('searchUsersInput');
    if (searchUsersInput) {
        searchUsersInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr[data-search]');

            rows.forEach(row => {
                const searchData = row.dataset.search;
                if (searchData.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ===== PERMISSIONS MATRIX =====
    initPermissionsMatrix();
});

// ========== PERMISSIONS MATRIX ==========
let permissionsChanged = false;
let originalPermissions = {};
let currentPermissions = {};

function initPermissionsMatrix() {
    const checkboxes = document.querySelectorAll('.perm-check');
    if (checkboxes.length === 0) return;

    // Store original state
    checkboxes.forEach(cb => {
        const key = cb.dataset.permission + '_' + cb.dataset.role;
        originalPermissions[key] = cb.checked;
        currentPermissions[key] = cb.checked;
    });

    // Add change listeners
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const key = this.dataset.permission + '_' + this.dataset.role;
            currentPermissions[key] = this.checked;
            checkPermissionsChanged();
        });
    });

    // Save button
    const saveBtn = document.getElementById('permSaveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', savePermissions);
    }

    // Reset button
    const resetBtn = document.getElementById('permResetBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetPermissions);
    }
}

function checkPermissionsChanged() {
    const saveBtn = document.getElementById('permSaveBtn');
    const resetBtn = document.getElementById('permResetBtn');

    permissionsChanged = false;
    for (const key in currentPermissions) {
        if (currentPermissions[key] !== originalPermissions[key]) {
            permissionsChanged = true;
            break;
        }
    }

    if (saveBtn) saveBtn.style.display = permissionsChanged ? '' : 'none';
    if (resetBtn) resetBtn.style.display = permissionsChanged ? '' : 'none';
}

function resetPermissions() {
    const checkboxes = document.querySelectorAll('.perm-check');
    checkboxes.forEach(cb => {
        const key = cb.dataset.permission + '_' + cb.dataset.role;
        cb.checked = originalPermissions[key];
        currentPermissions[key] = originalPermissions[key];
    });
    checkPermissionsChanged();
}

function savePermissions() {
    const saveBtn = document.getElementById('permSaveBtn');
    const indicator = document.getElementById('permSaveIndicator');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Build matrix object
    const matrix = {};
    for (const key in currentPermissions) {
        const [permission, roleId] = key.split('_');
        if (!matrix[permission]) matrix[permission] = {};
        matrix[permission][roleId] = currentPermissions[key];
    }

    // Show loading
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
            </svg>
            Сохранение...
        `;
    }

    fetch('/settings/permissions/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ matrix: matrix })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update original state
            for (const key in currentPermissions) {
                originalPermissions[key] = currentPermissions[key];
            }
            checkPermissionsChanged();
            showToast('Права успешно сохранены', 'success');
        } else {
            showToast(data.message || 'Ошибка сохранения', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving permissions:', error);
        showToast('Ошибка сохранения прав', 'error');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Сохранить изменения
            `;
        }
    });
}

// Simple toast notification
function showToast(message, type = 'info') {
    // Remove existing toast
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + type;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">&times;</button>
    `;
    document.body.appendChild(toast);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 3000);
}
