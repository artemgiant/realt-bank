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

// ========== DRAWER ==========
function openDrawer() {
    document.getElementById('drawerOverlay').classList.add('open');
    document.getElementById('drawerAddRole').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    document.getElementById('drawerOverlay').classList.remove('open');
    document.getElementById('drawerAddRole').classList.remove('open');
    document.body.style.overflow = '';
}

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Drawer overlay click
    const drawerOverlay = document.getElementById('drawerOverlay');
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', closeDrawer);
    }

    // Drawer close button
    const drawerClose = document.getElementById('drawerClose');
    if (drawerClose) {
        drawerClose.addEventListener('click', closeDrawer);
    }

    // Drawer cancel button
    const drawerCancel = document.getElementById('drawerCancel');
    if (drawerCancel) {
        drawerCancel.addEventListener('click', closeDrawer);
    }

    // Close drawer on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });

    // Tab switching inside content
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

    // Tree item expand/collapse
    document.querySelectorAll('.tree-item.level-1').forEach(item => {
        item.addEventListener('click', function() {
            const expand = this.querySelector('.tree-expand');
            if (expand && !expand.classList.contains('empty')) {
                expand.classList.toggle('open');
            }
        });
    });
});
