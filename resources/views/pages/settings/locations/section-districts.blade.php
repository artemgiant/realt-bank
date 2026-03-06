{{-- Section: Districts (Районы города) --}}
<div class="settings-section {{ $activeSection === 'districts' ? 'active' : '' }}" id="section-districts">
    <div class="settings-breadcrumb">
        <a href="{{ route('settings.index') }}">Настройки</a> <span>›</span> <span class="current">Районы города</span>
    </div>
    <div class="section-header">
        <div>
            <h2>Районы города</h2>
            <p>Районы внутри городов и населённых пунктов</p>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <form class="section-search" method="GET" action="{{ route('settings.districts.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input name="search" id="searchDistrictsInput" placeholder="Поиск района..." value="{{ $search ?? '' }}" autocomplete="off">
                @if(isset($perPage) && $perPage != 25)
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                @endif
            </form>
            <button class="btn btn-primary" onclick="openDistrictDrawer()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if(isset($districtsList) && $districtsList->total() > 0)
                <div class="address-list" id="districtsAddressList">
                    @foreach($districtsList as $district)
                        <div class="address-item" data-search="{{ mb_strtolower($district->name . ' ' . ($district->city->name ?? '')) }}">
                            <div class="address-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/></svg>
                            </div>
                            <div class="address-info">
                                <h4>{{ $district->name }}</h4>
                                <p>{{ $district->city->name ?? '—' }}</p>
                            </div>
                            <span class="address-count">{{ $district->streets_count }} {{ trans_choice('улица|улицы|улиц', $district->streets_count) }}</span>
                            <div class="actions-cell">
                                <button class="btn-icon" onclick="openDistrictDrawer({{ $district->id }})" title="Редактировать">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                                </button>
                                <button class="btn-icon" onclick="openDeleteModal(this)" data-type="district" data-name="{{ $district->name }}" data-id="{{ $district->id }}" data-users="{{ $district->streets_count }}" title="Удалить">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @include('pages.settings.locations.partials.pagination', [
                    'paginator' => $districtsList,
                    'perPage' => $perPage ?? 10,
                    'sectionRoute' => 'settings.districts.index',
                ])
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 10-16 0c0 3 2.7 7 8 11.7z"/></svg>
                    <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет районов города' }}</h4>
                    <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте первый район города' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
