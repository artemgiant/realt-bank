{{-- Settings Navigation --}}
<div class="settings-nav">
    <div class="nav-section-label">Доступ</div>
    <div class="nav-group">
        <div class="nav-item active" onclick="showSection('users')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                <path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Пользователи
            <span class="nav-item-badge">101</span>
        </div>
        <div class="nav-item" onclick="showSection('roles')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Роли
            <span class="nav-item-badge">8</span>
        </div>
        <div class="nav-item" onclick="showSection('permissions')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Разрешения
        </div>
    </div>
</div>
