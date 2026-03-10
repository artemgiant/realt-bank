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
        <div class="card-body" style="padding:0;">
            @if(isset($states) && $states->total() > 0)
                <div class="address-list" id="regionsTreeList">
                    @foreach($states as $state)
                        <div class="address-item region-parent" data-state-id="{{ $state->id }}" data-search="{{ mb_strtolower($state->name) }}">
                            <div class="address-icon region-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                            </div>
                            <div class="address-info">
                                <h4>{{ $state->name }}</h4>
                                <p>{{ $state->country->name ?? '—' }}</p>
                            </div>
                            @if($state->regions->count() > 0 || $state->cities->flatMap->districts->count() > 0)
                                <span class="tree-expand" style="cursor:pointer;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg>
                                </span>
                            @endif
                            <span class="address-count">
                                {{ $state->regions_count }} р-н,
                                {{ $state->cities_count }} {{ trans_choice('город|города|городов', $state->cities_count) }}
                            </span>
                            <div class="actions-cell">
                                <button class="btn-icon" onclick="event.stopPropagation(); openStateDrawer({{ $state->id }})" title="Редактировать">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                </button>
                                <button class="btn-icon" onclick="event.stopPropagation(); openDeleteModal(this)" data-type="state" data-name="{{ $state->name }}" data-id="{{ $state->id }}" data-users="{{ $state->cities_count }}" title="Удалить">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </div>
                        </div>

                        @foreach($state->regions as $region)
                            <div class="address-item region-child" data-parent-state="{{ $state->id }}" data-search="{{ mb_strtolower($region->name) }}" style="display:none;">
                                <div class="address-icon" style="width:28px;height:28px;margin-left:14px;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                                </div>
                                <div class="address-info">
                                    <h4 style="font-size:13px;font-weight:600;">{{ $region->name }}</h4>
                                    <p>Район региона</p>
                                </div>
                                <span class="address-count">{{ $region->cities_count }} {{ trans_choice('город|города|городов', $region->cities_count) }}</span>
                                <div class="actions-cell">
                                    <button class="btn-icon" onclick="event.stopPropagation(); openRegionDrawer({{ $region->id }})" title="Редактировать">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    </button>
                                    <button class="btn-icon" onclick="event.stopPropagation(); openDeleteModal(this)" data-type="region" data-name="{{ $region->name }}" data-id="{{ $region->id }}" data-users="{{ $region->cities_count }}" title="Удалить">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        @foreach($state->cities as $city)
                            @foreach($city->districts as $district)
                                <div class="address-item region-child" data-parent-state="{{ $state->id }}" data-search="{{ mb_strtolower($district->name) }}" style="display:none;">
                                    <div class="address-icon" style="width:28px;height:28px;margin-left:14px;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/></svg>
                                    </div>
                                    <div class="address-info">
                                        <h4 style="font-size:13px;font-weight:600;">{{ $district->name }}</h4>
                                        <p>{{ $city->name }}</p>
                                    </div>
                                    <span class="address-count">{{ $district->streets_count }} {{ trans_choice('улица|улицы|улиц', $district->streets_count) }}</span>
                                    <div class="actions-cell">
                                        <button class="btn-icon" onclick="event.stopPropagation(); openDistrictDrawer({{ $district->id }})" title="Редактировать">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                        </button>
                                        <button class="btn-icon" onclick="event.stopPropagation(); openDeleteModal(this)" data-type="district" data-name="{{ $district->name }}" data-id="{{ $district->id }}" data-users="{{ $district->streets_count }}" title="Удалить">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @endforeach
                </div>

                @include('pages.settings.locations.partials.pagination', [
                    'paginator' => $states,
                    'perPage' => $perPage ?? 10,
                    'sectionRoute' => 'settings.regions.index',
                ])
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/></svg>
                    <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет регионов' }}</h4>
                    <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте первый регион для начала работы с географией' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
