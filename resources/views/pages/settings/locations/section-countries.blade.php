{{-- Section: Countries --}}
<div class="settings-section {{ $activeSection === 'countries' ? 'active' : '' }}" id="section-countries">
    <div class="settings-breadcrumb">
        <a href="{{ route('settings.index') }}">Настройки</a> <span>›</span> <span class="current">Страны</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Страны</h2>
            <p>Справочник стран для географической структуры</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <form class="section-search" method="GET" action="{{ route('settings.countries.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="search" id="searchCountriesInput" placeholder="Поиск страны..." value="{{ $search ?? '' }}" autocomplete="off">
                @if(isset($perPage) && $perPage != 25)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif
            </form>
            <button class="btn btn-primary" onclick="openCountryDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if(isset($countriesList) && $countriesList->total() > 0)
                <div class="address-list" id="countriesAddressList">
                    @foreach($countriesList as $country)
                        <div class="address-item" data-search="{{ mb_strtolower($country->name . ' ' . ($country->code ?? '')) }}">
                            <div class="address-icon country-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                            </div>
                            <div class="address-info">
                                <h4>{{ $country->name }}</h4>
                                <p>{{ $country->code ?? '—' }}</p>
                            </div>
                            <span class="address-count">{{ $country->states_count }} {{ trans_choice('область|области|областей', $country->states_count) }}</span>
                            <div class="actions-cell">
                                <button class="btn-icon" onclick="openCountryDrawer({{ $country->id }})" title="Редактировать">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                </button>
                                <button class="btn-icon" onclick="openDeleteModal(this)" data-type="country" data-name="{{ $country->name }}" data-id="{{ $country->id }}" data-users="{{ $country->states_count }}" title="Удалить">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @include('pages.settings.locations.partials.pagination', [
                    'paginator' => $countriesList,
                    'perPage' => $perPage ?? 10,
                    'sectionRoute' => 'settings.countries.index',
                ])
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет стран' }}</h4>
                    <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте первую страну для начала работы с географией' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
