/**
 * Settings Page JavaScript
 */

// ========== GLOBAL DATA ==========
let rolesData = {};
let usersData = {};

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

    if (roleId && rolesData[roleId]) {
        const role = rolesData[roleId];
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

    if (userId && usersData[userId]) {
        const user = usersData[userId];
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

        // Password not required for edit
        passwordInput.removeAttribute('required');
        if (passwordGroup.querySelector('.form-label .required')) {
            passwordGroup.querySelector('.form-label .required').style.display = 'none';
        }
        passwordInput.placeholder = 'Оставьте пустым, чтобы не менять';

        // Set role
        if (user.roles && user.roles.length > 0) {
            $('#user-role-select').val(user.roles[0].id).trigger('change');
        }

        // Set employee
        if (user.employee_id) {
            $('#user-employee-select').val(user.employee_id).trigger('change');
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

    if (type === 'role') {
        title.textContent = 'Удалить роль?';
    } else if (type === 'user') {
        title.textContent = 'Удалить пользователя?';
    } else {
        title.textContent = 'Подтвердите удаление';
    }

    nameSpan.textContent = name;

    if (type === 'role' && users > 0) {
        warning.classList.add('show');
        const userWord = users === 1 ? 'пользователь' :
                        (users >= 2 && users <= 4) ? 'пользователя' : 'пользователей';
        warningText.textContent = `У этой роли есть ${users} ${userWord}. Сначала переназначьте их на другую роль.`;
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

    // Don't delete roles with users
    if (deleteTarget.type === 'role' && deleteTarget.users > 0) {
        closeDeleteModal();
        return;
    }

    // Create and submit delete form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/settings/' + deleteTarget.type + 's/' + deleteTarget.id;

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
});
