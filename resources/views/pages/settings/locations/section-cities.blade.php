{{-- Section: Cities --}}
<div class="settings-section {{ $activeSection === 'cities' ? 'active' : '' }}" id="section-cities">
    <div class="settings-breadcrumb">
        <a href="{{ route('settings.index') }}">Настройки</a> <span>›</span> <span class="current">Города / Нас. пункты</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Города / Нас. пункты</h2>
            <p>Список городов для привязки адресов объектов</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <form class="section-search" method="GET" action="{{ route('settings.cities.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="search" id="searchCitiesInput" placeholder="Поиск города..." value="{{ $search ?? '' }}" autocomplete="off">
                @if(isset($perPage) && $perPage != 25)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif
            </form>
            <button class="btn btn-primary" onclick="openCityDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;" id="citiesCardBody">
            @include('pages.settings.locations.partials.cities-list', [
                'citiesList' => $citiesList ?? null,
                'perPage' => $perPage ?? 10,
                'search' => $search ?? '',
            ])
        </div>
    </div>
</div>
