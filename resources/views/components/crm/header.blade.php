@props([
'title' => null,
'tabs' => null,
'addButton' => true,
'addButtonText' => 'Добавить',
'addButtonUrl' => '#'
])

@php
    // Використовуємо props якщо передано, інакше з View Composer
    $title = $title ?? $pageTitle ?? 'Недвижимость';
    $tabs = $tabs ?? $pageTabs ?? [];
@endphp

<header class="header-wrapper">
    <h1 class="header-title">
        {{ $title }}
    </h1>

    @if(count($tabs) > 0)
        <div class="header-tabs">
            <div class="btn-group">
                @foreach($tabs as $tab)
                    @php
                        $isActive = ($currentRoute ?? '') === $tab['route'];
                        $url = \Illuminate\Support\Facades\Route::has($tab['route'])
                            ? route($tab['route'])
                            : '#';
                    @endphp
                    <a href="{{ $url }}"
                       class="btn btn-outline-primary {{ $isActive ? 'active' : '' }}"
                            {{ $isActive ? 'aria-current=page' : '' }}>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="header-actions">
        @if($addButton)

            <a class="btn btn-primary" href="{{ $addButtonUrl }}">
                <span>{{ $addButtonText }}</span>
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z"
                          fill="white"/>
                    <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z"
                          fill="white"/>
                </svg>
            </a>
        @endif
    </div>

</header>
