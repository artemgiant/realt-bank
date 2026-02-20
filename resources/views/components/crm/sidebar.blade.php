<aside class="sidebar">
    <a href="{{ route('properties.index') }}" class="sidebar-logo">
        <picture>
            <img src="{{ asset('img/icon/side-bar/logo-F.svg') }}" alt="Faktor">
        </picture>
    </a>
    <nav class="sidebar-nav">
        @foreach($sidebarMenu as $menuItem)
            @php
                $routePrefix = explode('.', $menuItem['route'])[0] ?? '';
                $isActive = str_starts_with($currentRoute ?? '', $routePrefix);
                $url = \Illuminate\Support\Facades\Route::has($menuItem['route'])
                    ? route($menuItem['route'])
                    : '#';
            @endphp
            <a class="sidebar-item {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                <picture>
                    <img src="{{ asset($menuItem['icon']) }}" alt="">
                </picture>
                <span>{{ $menuItem['name'] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="sidebar-bottom">
        @php
            $isSettingsActive = str_starts_with($currentRoute ?? '', 'settings');
        @endphp
        <a class="sidebar-item {{ $isSettingsActive ? 'active' : '' }}" href="{{ route('settings.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            <span>Настройки</span>
        </a>
        <div class="sidebar-user-menu">
            <button class="sidebar-avatar" type="button" id="sidebarUserToggle">
                @auth
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? '')[1] ?? '', 0, 1)) }}
                @else
                    ВВ
                @endauth
            </button>
            <div class="sidebar-popup" id="sidebarUserPopup">
                <div class="sidebar-popup-header">
                    <div class="sidebar-popup-avatar">
                        @auth
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? '')[1] ?? '', 0, 1)) }}
                        @else
                            ВВ
                        @endauth
                    </div>
                    <div class="sidebar-popup-user">
                        <span class="sidebar-popup-name">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="sidebar-popup-email">{{ auth()->user()->email ?? '' }}</span>
                    </div>
                </div>
                <div class="sidebar-popup-divider"></div>
                <a href="#" class="sidebar-popup-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Мой профиль
                </a>
                <div class="sidebar-popup-divider"></div>
                <div class="sidebar-popup-section">
                    <span class="sidebar-popup-label">Язык / Language</span>
                    <div class="sidebar-popup-langs">
                        <button class="sidebar-lang-btn {{ app()->getLocale() === 'uk' ? 'active' : '' }}" data-lang="uk">UA</button>
                        <button class="sidebar-lang-btn {{ app()->getLocale() === 'ru' ? 'active' : '' }}" data-lang="ru">RU</button>
                        <button class="sidebar-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" data-lang="en">EN</button>
                    </div>
                </div>
                <div class="sidebar-popup-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="sidebar-popup-logout">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Выход
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('sidebarUserToggle');
    const popup = document.getElementById('sidebarUserPopup');

    if (toggle && popup) {
        // Toggle popup on avatar click
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            popup.classList.toggle('open');
        });

        // Close popup when clicking outside
        document.addEventListener('click', function(e) {
            if (!popup.contains(e.target) && e.target !== toggle) {
                popup.classList.remove('open');
            }
        });

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                popup.classList.remove('open');
            }
        });

        // Language switch
        document.querySelectorAll('.sidebar-lang-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const lang = this.dataset.lang;
                // Set cookie and reload
                document.cookie = `locale=${lang};path=/;max-age=31536000`;
                window.location.href = `/locale/${lang}`;
            });
        });
    }
});
</script>
