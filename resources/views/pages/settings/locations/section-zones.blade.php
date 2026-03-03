{{-- Section: Zones (Микрорайоны) --}}
<div class="settings-section {{ $activeSection === 'zones' ? 'active' : '' }}" id="section-zones">
    <div class="settings-breadcrumb">
        <a href="{{ route('settings.index') }}">Настройки</a> <span>›</span> <span class="current">Микрорайоны</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Микрорайоны</h2>
            <p>Зоны и микрорайоны внутри городов</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <form class="section-search" method="GET" action="{{ route('settings.zones.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="search" id="searchZonesInput" placeholder="Поиск микрорайона..." value="{{ $search ?? '' }}" autocomplete="off">
                @if(isset($perPage) && $perPage != 25)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif
            </form>
            <button class="btn btn-primary" onclick="openZoneDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if(isset($zonesList) && $zonesList->total() > 0)
                <div class="address-list" id="zonesAddressList">
                    @foreach($zonesList as $zone)
                        <div class="address-item" data-search="{{ mb_strtolower($zone->name . ' ' . ($zone->city->name ?? '') . ' ' . ($zone->district->name ?? '')) }}">
                            <div class="address-icon zone-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 3v18"/></svg>
                            </div>
                            <div class="address-info">
                                <h4>{{ $zone->name }}</h4>
                                <p>{{ $zone->city->name ?? '—' }}@if($zone->district), {{ $zone->district->name }}@endif @if($zone->city && $zone->city->state) — {{ $zone->city->state->name }}@endif</p>
                            </div>
                            <span class="address-count">{{ $zone->streets_count }} {{ trans_choice('улица|улицы|улиц', $zone->streets_count) }}</span>
                            <div class="actions-cell">
                                <button class="btn-icon" onclick="openZoneDrawer({{ $zone->id }})" title="Редактировать">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                </button>
                                <button class="btn-icon" onclick="openDeleteModal(this)" data-type="zone" data-name="{{ $zone->name }}" data-id="{{ $zone->id }}" data-users="{{ $zone->streets_count }}" title="Удалить">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @include('pages.settings.locations.partials.pagination', [
                    'paginator' => $zonesList,
                    'perPage' => $perPage ?? 10,
                    'sectionRoute' => 'settings.zones.index',
                ])
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 3v18"/></svg>
                    <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет микрорайонов' }}</h4>
                    <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте микрорайоны для более точной привязки адресов' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
