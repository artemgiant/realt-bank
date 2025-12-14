<aside class="sidebar">
    <nav class="nav">
        <ul class="nav-list">
            <li class="nav-list-item">
                <a href="{{ route('properties.index') }}" class="nav-list-link sidebar-logo">
                    <picture>
                        <source srcset="{{ asset('img/icon/side-bar/logo-F.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/side-bar/logo-F.svg') }}" alt="Realt Bank">
                    </picture>
                </a>
            </li>
            @foreach($sidebarMenu as $menuItem)
                @php
                    $routePrefix = explode('.', $menuItem['route'])[0] ?? '';
                    $isActive = str_starts_with($currentRoute ?? '', $routePrefix);
                    $url = \Illuminate\Support\Facades\Route::has($menuItem['route'])
                        ? route($menuItem['route'])
                        : '#';
                @endphp
                <li class="nav-list-item">
                    <a class="nav-list-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
                        <span class="nav-list-icon">
                            <picture>
                                <source srcset="{{ asset($menuItem['icon']) }}" type="image/webp">
                                <img src="{{ asset($menuItem['icon']) }}" alt="">
                            </picture>
                        </span>
                        <span class="nav-list-text">
                            {{ $menuItem['name'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
        <ul class="nav-info">
            <li class="nav-info-item">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ strtoupper(app()->getLocale()) }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">UA</a></li>
                        <li><a class="dropdown-item" href="#">RU</a></li>
                        <li><a class="dropdown-item" href="#">EN</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-info-item">
                <a class="nav-info-link position-relative" href="#">
                    <picture>
                        <source srcset="{{ asset('img/icon/side-bar/mail-white.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/side-bar/mail-white.svg') }}" alt="">
                    </picture>
                </a>
            </li>
            <li class="nav-info-item">
                <a class="nav-info-link" href="#">
                    <picture>
                        <source srcset="{{ asset('img/icon/side-bar/settings-white.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/side-bar/settings-white.svg') }}" alt="">
                    </picture>
                </a>
            </li>
            <li class="nav-info-item">
                <a class="nav-info-link" >
                    <picture>
                        <source srcset="{{ asset('img/icon/side-bar/default-avatar.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/side-bar/default-avatar.svg') }}" alt="">
                    </picture>
                </a>
            </li>
        </ul>
    </nav>
</aside>
