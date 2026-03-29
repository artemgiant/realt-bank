{{-- Section: Regions (States + Districts tree) --}}
<div class="settings-section {{ $activeSection === 'regions' ? 'active' : '' }}" id="section-regions">
    <div class="settings-breadcrumb">
        <a href="{{ route('settings.index') }}">Настройки</a> <span>›</span> <span class="current">Регионы</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Регионы</h2>
            <p>Управление географической структурой</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <form class="section-search" method="GET" action="{{ route('settings.regions.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="search" id="searchRegionsInput" placeholder="Поиск региона..." value="{{ $search ?? '' }}" autocomplete="off">
                @if(isset($perPage) && $perPage != 25)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif
            </form>
            <button class="btn btn-primary" onclick="openRegionDrawer()" style="margin-right:4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Район региона
            </button>
            <button class="btn btn-primary" onclick="openStateDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Область
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;" id="regionsCardBody">
            @include('pages.settings.locations.partials.regions-list', [
                'states' => $states ?? null,
                'perPage' => $perPage ?? 10,
                'search' => $search ?? '',
            ])
        </div>
    </div>
</div>
