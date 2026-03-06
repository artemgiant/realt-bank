{{-- Settings Navigation --}}
<div class="settings-nav">
    <div class="nav-section-label">Доступ</div>
    <div class="nav-group">
        <a href="{{ route('settings.users.index') }}" class="nav-item {{ $activeSection === 'users' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                <path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Пользователи
            <span class="nav-item-badge">{{ $usersCount ?? 0 }}</span>
        </a>
        <a href="{{ route('settings.roles.index') }}" class="nav-item {{ $activeSection === 'roles' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Роли
            <span class="nav-item-badge">{{ $rolesCount ?? 0 }}</span>
        </a>
        <a href="{{ route('settings.permissions.index') }}" class="nav-item {{ $activeSection === 'permissions' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            Разрешения
            <span class="nav-item-badge">{{ $permissions->count() ?? 0 }}</span>
        </a>
    </div>

    <div class="nav-section-label">Локации</div>
    @php
        $fmtCount = function(int $n): string {
            if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1, '.', ''), '0'), '.') . 'M';
            if ($n >= 1000) return rtrim(rtrim(number_format($n / 1000, 1, '.', ''), '0'), '.') . 'K';
            return (string) $n;
        };
    @endphp
    <div class="nav-group">
        <a href="{{ route('settings.countries.index') }}" class="nav-item {{ $activeSection === 'countries' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
            </svg>
            Страны
            <span class="nav-item-badge" title="{{ number_format($countriesCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($countriesCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.regions.index') }}" class="nav-item {{ $activeSection === 'regions' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                <line x1="8" y1="2" x2="8" y2="18"/>
                <line x1="16" y1="6" x2="16" y2="22"/>
            </svg>
            Регионы
            <span class="nav-item-badge" title="{{ number_format($statesCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($statesCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.oblast-regions.index') }}" class="nav-item {{ $activeSection === 'oblast-regions' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
            </svg>
            Районы обл.
            <span class="nav-item-badge" title="{{ number_format($regionsCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($regionsCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.cities.index') }}" class="nav-item {{ $activeSection === 'cities' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                <path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/>
            </svg>
            Города / Нас. пункты
            <span class="nav-item-badge" title="{{ number_format($citiesCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($citiesCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.districts.index') }}" class="nav-item {{ $activeSection === 'districts' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/>
            </svg>
            Районы г.
            <span class="nav-item-badge" title="{{ number_format($districtsCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($districtsCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.zones.index') }}" class="nav-item {{ $activeSection === 'zones' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18"/><path d="M9 3v18"/>
            </svg>
            Микрорайоны
            <span class="nav-item-badge" title="{{ number_format($zonesCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($zonesCount ?? 0) }}</span>
        </a>
        <a href="{{ route('settings.streets.index') }}" class="nav-item {{ $activeSection === 'streets' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18"/><path d="M6 6l12 12"/>
                <rect x="3" y="3" width="18" height="18" rx="2"/>
            </svg>
            Улицы
            <span class="nav-item-badge" title="{{ number_format($streetsCount ?? 0, 0, '', ' ') }}">{{ $fmtCount($streetsCount ?? 0) }}</span>
        </a>
    </div>
</div>
