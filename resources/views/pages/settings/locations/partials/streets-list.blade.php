@if(isset($streetsList) && $streetsList->total() > 0)
    <table class="roles-table" id="streetsTable">
        <thead>
        <tr>
            <th>Улица</th>
            <th>Микрорайон</th>
            <th>Район города</th>
            <th>Город/Нас. пункт</th>
            <th style="width:100px">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($streetsList as $street)
            <tr data-search="{{ mb_strtolower($street->name . ' ' . ($street->city->name ?? '') . ' ' . ($street->district->name ?? '') . ' ' . ($street->zone->name ?? '')) }}">
                <td style="font-weight:700;color:var(--text-dark)">{{ $street->name }}</td>
                <td>{{ $street->zone->name ?? '—' }}</td>
                <td>{{ $street->district->name ?? '—' }}</td>
                <td>{{ $street->city->name ?? '—' }}</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn-icon" onclick="openStreetDrawer({{ $street->id }})" title="Редактировать">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4z"/></svg>
                        </button>
                        <button class="btn-icon" onclick="openDeleteModal(this)" data-type="street" data-name="{{ $street->name }}" data-id="{{ $street->id }}" data-users="0" title="Удалить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @include('pages.settings.locations.partials.pagination', [
        'paginator' => $streetsList,
        'perPage' => $perPage ?? 10,
        'sectionRoute' => 'settings.streets.index',
    ])
@else
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18"/><path d="M6 6l12 12"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
        <h4>{{ ($search ?? '') ? 'Ничего не найдено' : 'Нет улиц' }}</h4>
        <p>{{ ($search ?? '') ? 'Попробуйте изменить поисковый запрос' : 'Добавьте улицы для автозаполнения адресов объектов' }}</p>
    </div>
@endif
