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
        <div class="dropdown">
            <button class="sidebar-avatar dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                @auth
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name ?? '')[1] ?? '', 0, 1)) }}
                @else
                    ВВ
                @endauth
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Выход</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</aside>
