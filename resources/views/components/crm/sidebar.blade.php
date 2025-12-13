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
            <li class="nav-list-item">
                <a class="nav-list-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index') }}">
                    <span class="nav-list-icon">
                        <picture>
                            <source srcset="{{ asset('img/icon/side-bar/Finanse.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/side-bar/Finanse.svg') }}" alt="">
                        </picture>
                    </span>
                    <span class="nav-list-text">
                        Недвижимость
                    </span>
                </a>
            </li>
            <li class="nav-list-item">
                <a class="nav-list-link {{ request()->routeIs('deals.*') ? 'active' : '' }}" href="#">
                    <span class="nav-list-icon">
                        <picture>
                            <source srcset="{{ asset('img/icon/side-bar/Deals.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/side-bar/Deals.svg') }}" alt="">
                        </picture>
                        <span class="my-badge">
                            15
                        </span>
                    </span>
                    <span class="nav-list-text">
                        Сделки
                    </span>
                </a>
            </li>
            <li class="nav-list-item">
                <a class="nav-list-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="#">
                    <span class="nav-list-icon">
                        <picture>
                            <source srcset="{{ asset('img/icon/side-bar/Tasks.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/side-bar/Tasks.svg') }}" alt="">
                        </picture>
                        <span class="my-badge">
                            233
                        </span>
                    </span>
                    <span class="nav-list-text">
                        Задачи
                    </span>
                </a>
            </li>
            <li class="nav-list-item">
                <a class="nav-list-link {{ request()->routeIs('agency.*') ? 'active' : '' }}" href="#">
                    <span class="nav-list-icon">
                        <picture>
                            <source srcset="{{ asset('img/icon/side-bar/Company1.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/side-bar/Company1.svg') }}" alt="">
                        </picture>
                    </span>
                    <span class="nav-list-text">
                        Агентство
                    </span>
                </a>
            </li>
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
                    <span class="my-badge"></span>
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
                <a class="nav-info-link" href="{{ route('profile') }}">
                    <picture>
                        <source srcset="{{ asset('img/icon/side-bar/default-avatar.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/side-bar/default-avatar.svg') }}" alt="">
                    </picture>
                </a>
            </li>
        </ul>
    </nav>
</aside>
