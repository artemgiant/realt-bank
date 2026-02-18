/**
 * Settings Page JavaScript
 */

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
function openDrawer() {
    document.getElementById('drawerOverlay').classList.add('open');
    document.getElementById('drawerAddRole').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Initialize Select2 after drawer opens
    initRoleDrawerSelect2();
}

function closeDrawer() {
    document.getElementById('drawerOverlay').classList.remove('open');
    document.getElementById('drawerAddRole').classList.remove('open');
    document.body.style.overflow = '';
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
function openUserDrawer() {
    document.getElementById('drawerUserOverlay').classList.add('open');
    document.getElementById('drawerAddUser').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Initialize Select2 after drawer opens
    initDrawerSelect2();
}

function closeUserDrawer() {
    document.getElementById('drawerUserOverlay').classList.remove('open');
    document.getElementById('drawerAddUser').classList.remove('open');
    document.body.style.overflow = '';
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
        drawerOverlay.addEventListener('click', closeDrawer);
    }

    const drawerClose = document.getElementById('drawerClose');
    if (drawerClose) {
        drawerClose.addEventListener('click', closeDrawer);
    }

    const drawerCancel = document.getElementById('drawerCancel');
    if (drawerCancel) {
        drawerCancel.addEventListener('click', closeDrawer);
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

    // ===== CLOSE ALL DRAWERS ON ESCAPE =====
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDrawer();
            closeUserDrawer();
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
});
