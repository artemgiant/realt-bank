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
