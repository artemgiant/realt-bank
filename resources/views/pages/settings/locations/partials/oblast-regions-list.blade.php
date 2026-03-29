@if(isset($regionsList) && $regionsList->total() > 0)
    <div class="address-list" id="oblastRegionsAddressList">
        @foreach($regionsList as $region)
            <div class="address-item" data-search="{{ mb_strtolower($region->name . ' ' . ($region->state->name ?? '')) }}">
                <div class="address-icon icon-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                </div>
                <div class="address-info">
                    <h4>{{ $region->name }}</h4>
                    <p>{{ $region->state->name ?? '—' }}</p>
                </div>
                <span class="address-count">{{ $region->cities_count }} {{ trans_choice('город|города|городов', $region->cities_count) }}</span>
                <div class="actions-cell">
                    <button class="btn-icon" onclick="openRegionDrawer({{ $region->id }})" title="Редактировать">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                    </button>
                    <button class="btn-icon" onclick="openDeleteModal(this)" data-type="region" data-name="{{ $region->name }}" data-id="{{ $region->id }}" data-users="{{ $region->cities_count }}" title="Удалить">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @include('pages.settings.locations.partials.pagination', [
        'paginator' => $regionsList,
        'perPage' => $perPage ?? 10,
        'sectionRoute' => 'settings.oblast-regions.index',
    ])
@else
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет районов региона' }}</h4>
        <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте первый район региона' }}</p>
    </div>
@endif
