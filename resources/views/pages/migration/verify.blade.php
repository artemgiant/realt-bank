@extends('layouts.crm')

@section('title', 'Верификация миграции')

@push('styles')
<style>
    .verify-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .verify-card.status-ok { border-left: 4px solid #198754; }
    .verify-card.status-mismatch { border-left: 4px solid #dc3545; }
    .verify-card.status-pending { border-left: 4px solid #6c757d; }

    .verify-card-header {
        background: #f8f9fa;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }
    .verify-card-header:hover { background: #e9ecef; }

    .verify-card-body {
        display: none;
        padding: 0;
    }
    .verify-card.open .verify-card-body { display: block; }

    .compare-table {
        width: 100%;
        font-size: 13px;
    }
    .compare-table th {
        background: #f1f3f5;
        padding: 6px 12px;
        font-weight: 600;
        width: 160px;
        white-space: nowrap;
        vertical-align: top;
    }
    .compare-table td {
        padding: 6px 12px;
        vertical-align: top;
    }
    .compare-table .val-old { background: #fff3cd; }
    .compare-table .val-new { background: #d1e7dd; }
    .compare-table .val-mismatch { background: #f8d7da !important; }

    .badge-type {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .badge-apartment { background: #0d6efd; color: #fff; }
    .badge-house { background: #198754; color: #fff; }
    .badge-commercial { background: #6f42c1; color: #fff; }

    .stats-bar {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .stats-bar .stat-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 20px;
        text-align: center;
        min-width: 120px;
    }
    .stats-bar .stat-item .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #212529;
    }
    .stats-bar .stat-item .stat-label {
        font-size: 12px;
        color: #6c757d;
    }

    .verify-status-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .verify-errors {
        background: #fff3f3;
        padding: 8px 12px;
        border-top: 1px solid #f5c2c7;
        font-size: 13px;
        color: #842029;
    }
    .verify-errors li { margin-bottom: 2px; }

    .toolbar {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    #batch-progress {
        display: none;
        margin-bottom: 12px;
    }
    #batch-progress .progress {
        height: 24px;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="p-3">
    <h4 class="mb-3">Верификация миграции из Factor</h4>

    {{-- Статистика --}}
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['total_new']) }}</div>
            <div class="stat-label">Новая база</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['total_old']) }}</div>
            <div class="stat-label">Старая база</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['apartments']) }}</div>
            <div class="stat-label">Квартиры</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['houses']) }}</div>
            <div class="stat-label">Дома</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['commercial']) }}</div>
            <div class="stat-label">Коммерция</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['photos_new']) }}</div>
            <div class="stat-label">Фото</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
            <div class="stat-label">Контакты</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['translations']) }}</div>
            <div class="stat-label">Переводы</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($stats['with_complex']) }}</div>
            <div class="stat-label">С ЖК</div>
        </div>
    </div>

    {{-- Тулбар: фильтры + кнопки --}}
    <div class="toolbar">
        <form method="GET" action="{{ route('migration.verify') }}" class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="width: 160px"
                   placeholder="ID или номер дома" value="{{ $search }}">

            <select name="type" class="form-select form-select-sm" style="width: 160px">
                <option value="">Все типы</option>
                <option value="23" {{ $typeFilter == '23' ? 'selected' : '' }}>Квартиры</option>
                <option value="28" {{ $typeFilter == '28' ? 'selected' : '' }}>Дома</option>
                <option value="40" {{ $typeFilter == '40' ? 'selected' : '' }}>Коммерция</option>
            </select>

            <select name="per_page" class="form-select form-select-sm" style="width: 100px">
                @foreach([10, 20, 50, 100] as $pp)
                    <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-primary">Фильтр</button>
            <a href="{{ route('migration.verify') }}" class="btn btn-sm btn-outline-secondary">Сброс</a>
        </form>

        <div class="ms-auto d-flex gap-2">
            <button id="btn-verify-page" class="btn btn-sm btn-warning">
                Проверить эту страницу
            </button>
            <button id="btn-verify-all" class="btn btn-sm btn-danger">
                Проверить все объекты
            </button>
            <button id="btn-expand-all" class="btn btn-sm btn-outline-secondary">
                Развернуть все
            </button>
        </div>
    </div>

    {{-- Прогресс-бар массовой проверки --}}
    <div id="batch-progress">
        <div class="d-flex justify-content-between mb-1">
            <small id="batch-progress-text">Проверка...</small>
            <small id="batch-progress-stats"></small>
        </div>
        <div class="progress">
            <div id="batch-progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
    </div>

    {{-- Список объектов --}}
    @forelse($properties as $property)
        @php
            $old = $oldObjects[$property->id] ?? null;
            $loc = $oldLocations[$property->id] ?? [];
            $oldPhotoCount = $oldPhotoCounts[$property->id] ?? 0;
            $typeLabels = [23 => ['Квартира', 'apartment'], 28 => ['Дом', 'house'], 40 => ['Коммерция', 'commercial']];
            $typeInfo = $typeLabels[$property->property_type_id] ?? ['?', 'secondary'];
        @endphp

        <div class="verify-card status-pending" id="card-{{ $property->id }}" data-id="{{ $property->id }}">
            <div class="verify-card-header" onclick="toggleCard({{ $property->id }})">
                <div class="d-flex align-items-center gap-2">
                    <strong>#{{ $property->id }}</strong>
                    <span class="badge badge-type badge-{{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                    <span class="text-muted">
                        {{ $property->city?->name ?? '—' }},
                        {{ $property->street?->name ?? '—' }}
                        {{ $property->building_number ?? '' }}
                        @if($property->apartment_number) кв. {{ $property->apartment_number }} @endif
                    </span>
                    <span class="text-muted">|</span>
                    <span class="text-muted">{{ $property->area_total ?? '—' }} м², {{ number_format($property->price ?? 0, 0, '.', ' ') }} $</span>
                    <span class="text-muted">| Фото: {{ $property->photos->count() }}/{{ $oldPhotoCount }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="verify-status-badge badge bg-secondary" id="status-{{ $property->id }}">ожидает</span>
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); verifyOne({{ $property->id }})">
                        Проверить
                    </button>
                </div>
            </div>

            <div class="verify-card-body">
                @if($old)
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Поле</th>
                            <th class="val-old">Старая база (factor_dump)</th>
                            <th class="val-new">Новая база (realt_bank)</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Тип --}}
                        @php
                            $oldTypeMap = [1 => 'Квартира (1)', 2 => 'Дом (2)', 3 => 'Коммерция (3)'];
                            $newTypeMap = [23 => 'Квартира (23)', 28 => 'Дом (28)', 40 => 'Коммерция (40)'];
                        @endphp
                        <tr>
                            <th>Тип</th>
                            <td class="val-old">{{ $oldTypeMap[$old->status] ?? $old->status }}</td>
                            <td class="val-new">{{ $newTypeMap[$property->property_type_id] ?? $property->property_type_id }}</td>
                        </tr>

                        {{-- Город --}}
                        <tr class="{{ mb_strtolower(trim($loc['city'] ?? '')) !== mb_strtolower(trim($property->city?->name ?? '')) ? 'val-mismatch' : '' }}">
                            <th>Город</th>
                            <td class="val-old">{{ $loc['city'] ?? '—' }}</td>
                            <td class="val-new">{{ $property->city?->name ?? '—' }}</td>
                        </tr>

                        {{-- Район --}}
                        <tr class="{{ mb_strtolower(trim($loc['district'] ?? '')) !== mb_strtolower(trim($property->district?->name ?? '')) ? 'val-mismatch' : '' }}">
                            <th>Район</th>
                            <td class="val-old">{{ $loc['district'] ?? '—' }}</td>
                            <td class="val-new">{{ $property->district?->name ?? '—' }}</td>
                        </tr>

                        {{-- Жилмассив --}}
                        <tr class="{{ mb_strtolower(trim($loc['zone'] ?? '')) !== mb_strtolower(trim($property->zone?->name ?? '')) ? 'val-mismatch' : '' }}">
                            <th>Жилмассив</th>
                            <td class="val-old">{{ $loc['zone'] ?? '—' }}</td>
                            <td class="val-new">{{ $property->zone?->name ?? '—' }}</td>
                        </tr>

                        {{-- Улица --}}
                        <tr class="{{ mb_strtolower(trim($loc['street'] ?? '')) !== mb_strtolower(trim($property->street?->name ?? '')) ? 'val-mismatch' : '' }}">
                            <th>Улица</th>
                            <td class="val-old">{{ $loc['street'] ?? '—' }}</td>
                            <td class="val-new">{{ $property->street?->name ?? '—' }}</td>
                        </tr>

                        {{-- Дом --}}
                        <tr class="{{ ($old->number_house ?: null) !== $property->building_number ? 'val-mismatch' : '' }}">
                            <th>Номер дома</th>
                            <td class="val-old">{{ $old->number_house ?: '—' }}</td>
                            <td class="val-new">{{ $property->building_number ?: '—' }}</td>
                        </tr>

                        {{-- Квартира --}}
                        <tr class="{{ ($old->num_flat ?: null) !== $property->apartment_number ? 'val-mismatch' : '' }}">
                            <th>Квартира</th>
                            <td class="val-old">{{ $old->num_flat ?: '—' }}</td>
                            <td class="val-new">{{ $property->apartment_number ?: '—' }}</td>
                        </tr>

                        {{-- Координаты --}}
                        <tr>
                            <th>Координаты</th>
                            <td class="val-old">{{ $old->coords ?: '—' }}</td>
                            <td class="val-new">{{ $property->latitude && $property->longitude ? $property->latitude . ',' . $property->longitude : '—' }}</td>
                        </tr>

                        {{-- Площади --}}
                        <tr class="{{ (float)($old->total_area ?: 0) !== (float)($property->area_total ?: 0) ? 'val-mismatch' : '' }}">
                            <th>Площадь общая</th>
                            <td class="val-old">{{ $old->total_area ?: '—' }}</td>
                            <td class="val-new">{{ $property->area_total ?: '—' }}</td>
                        </tr>

                        <tr class="{{ (float)($old->area_live ?: 0) !== (float)($property->area_living ?: 0) ? 'val-mismatch' : '' }}">
                            <th>Площадь жилая</th>
                            <td class="val-old">{{ $old->area_live ?: '—' }}</td>
                            <td class="val-new">{{ $property->area_living ?: '—' }}</td>
                        </tr>

                        <tr class="{{ (float)($old->area_kitchen ?: 0) !== (float)($property->area_kitchen ?: 0) ? 'val-mismatch' : '' }}">
                            <th>Площадь кухни</th>
                            <td class="val-old">{{ $old->area_kitchen ?: '—' }}</td>
                            <td class="val-new">{{ $property->area_kitchen ?: '—' }}</td>
                        </tr>

                        {{-- Площадь участка --}}
                        @php
                            $oldAreaLand = $old->area_total_ych_t ?: null;
                            if (!$oldAreaLand && !empty($old->area_home)) {
                                $parsed = (float) preg_replace('/[^\d.]/', '', $old->area_home);
                                $oldAreaLand = $parsed > 0 ? $parsed : null;
                            }
                        @endphp
                        @if($oldAreaLand || $property->area_land)
                        <tr class="{{ $oldAreaLand && abs((float)$oldAreaLand - (float)($property->area_land ?: 0)) > 0.01 ? 'val-mismatch' : '' }}">
                            <th>Площадь участка</th>
                            <td class="val-old">{{ $oldAreaLand ?: '—' }}</td>
                            <td class="val-new">{{ $property->area_land ?: '—' }}</td>
                        </tr>
                        @endif

                        {{-- Этаж (в старой базе перепутаны: all_floors=этаж, floor_build=этажность) --}}
                        <tr>
                            <th>Этаж / Этажность</th>
                            <td class="val-old">{{ $old->all_floors ?: '—' }} / {{ $old->floor_build ?: '—' }}</td>
                            <td class="val-new">{{ $property->floor ?: '—' }} / {{ $property->floors_total ?: '—' }}</td>
                        </tr>

                        {{-- Цена --}}
                        <tr class="{{ (float)($old->price ?: 0) !== (float)($property->price ?: 0) ? 'val-mismatch' : '' }}">
                            <th>Цена</th>
                            <td class="val-old">{{ $old->price ? number_format($old->price, 0, '.', ' ') : '—' }}</td>
                            <td class="val-new">{{ $property->price ? number_format($property->price, 0, '.', ' ') : '—' }}</td>
                        </tr>

                        <tr>
                            <th>Цена за м²</th>
                            <td class="val-old">{{ $old->price_area ? number_format($old->price_area, 0, '.', ' ') : '—' }}</td>
                            <td class="val-new">{{ $property->price_per_m2 ? number_format($property->price_per_m2, 0, '.', ' ') : '—' }}</td>
                        </tr>

                        {{-- Фото --}}
                        <tr class="{{ $oldPhotoCount !== $property->photos->count() ? 'val-mismatch' : '' }}">
                            <th>Фото</th>
                            <td class="val-old">{{ $oldPhotoCount }} шт.</td>
                            <td class="val-new">{{ $property->photos->count() }} шт.</td>
                        </tr>

                        {{-- === НОВЫЕ ПОЛЯ === --}}
                        @php
                            $oldData = json_decode($old->data ?? '{}', false);
                            $oldComplexName = $old->complex ? \Illuminate\Support\Facades\DB::connection('factor_dump')->table('lib_other')->where('id', $old->complex)->value('name') : null;
                            $newComplexName = $property->complex_id ? \Illuminate\Support\Facades\DB::table('complexes')->where('id', $property->complex_id)->value('name') : null;
                            $translation = \App\Models\Property\PropertyTranslation::where('property_id', $property->id)->where('locale', 'ru')->first();
                            $contactable = \Illuminate\Support\Facades\DB::table('contactables')->where('contactable_type', \App\Models\Property\Property::class)->where('contactable_id', $property->id)->first();
                            $contact = $contactable ? \App\Models\Contact\Contact::with('phones')->find($contactable->contact_id) : null;
                        @endphp

                        {{-- ЖК --}}
                        <tr class="{{ $oldComplexName && !$newComplexName ? 'val-mismatch' : '' }}">
                            <th>ЖК</th>
                            <td class="val-old">{{ $oldComplexName ?: '—' }}</td>
                            <td class="val-new">{{ $newComplexName ?: '—' }}</td>
                        </tr>

                        {{-- Год постройки --}}
                        <tr>
                            <th>Год постройки</th>
                            <td class="val-old">{{ $oldData->year_building ?? '—' }}</td>
                            <td class="val-new">{{ $property->year_built ?? '—' }}</td>
                        </tr>

                        {{-- Заголовок --}}
                        <tr class="{{ ($oldData->title ?? null) && !$translation ? 'val-mismatch' : '' }}">
                            <th>Заголовок</th>
                            <td class="val-old">{{ \Illuminate\Support\Str::limit($oldData->title ?? '—', 80) }}</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($translation->title ?? '—', 80) }}</td>
                        </tr>

                        {{-- Описание --}}
                        <tr>
                            <th>Описание</th>
                            <td class="val-old">{{ \Illuminate\Support\Str::limit($oldData->description ?? '—', 100) }}</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($translation->description ?? '—', 100) }}</td>
                        </tr>

                        {{-- Контакт: имя --}}
                        @php
                            $oldContactName = $oldData->name_sale ?? null;
                            $newContactName = $contact->first_name ?? null;
                            $nameMismatch = $oldContactName && $newContactName
                                && mb_strtolower(trim($oldContactName)) !== mb_strtolower(trim($newContactName));
                        @endphp
                        <tr class="{{ ($oldContactName && !$contact) || $nameMismatch ? 'val-mismatch' : '' }}">
                            <th>Контакт: имя</th>
                            <td class="val-old">{{ $oldContactName ?? '—' }}</td>
                            <td class="val-new">{{ $newContactName ?? '—' }}</td>
                        </tr>

                        {{-- Контакт: телефон --}}
                        @php
                            $oldPhone = $oldData->telephone ?? null;
                            $newPhone = $contact ? ($contact->phones->first()?->phone ?? null) : null;
                            $normalizedOld = $oldPhone ? preg_replace('/\D/', '', $oldPhone) : null;
                            $phoneMismatch = $normalizedOld && $normalizedOld !== $newPhone;
                        @endphp
                        <tr class="{{ ($oldPhone && !$newPhone) || $phoneMismatch ? 'val-mismatch' : '' }}">
                            <th>Контакт: телефон</th>
                            <td class="val-old">{{ $oldPhone ?? '—' }}</td>
                            <td class="val-new">{{ $newPhone ?? '—' }}</td>
                        </tr>

                        {{-- Контакт: роль --}}
                        @php
                            $typeSaleLabels = [1 => 'Риелтор', 2 => 'Собственник'];
                            $oldRole = $typeSaleLabels[$old->type_sale ?? 0] ?? null;
                            $newRole = $contactable->role ?? null;
                            $roleMismatch = $oldRole && $newRole && $oldRole !== $newRole;
                        @endphp
                        @if($oldRole || $newRole)
                        <tr class="{{ $roleMismatch ? 'val-mismatch' : '' }}">
                            <th>Контакт: роль</th>
                            <td class="val-old">{{ $oldRole ?? '—' }} (type_sale={{ $old->type_sale ?? '—' }})</td>
                            <td class="val-new">{{ $newRole ?? '—' }}</td>
                        </tr>
                        @endif

                        {{-- Контакт: 2-й контакт --}}
                        @if(!empty($oldData->name_sale_2))
                        <tr>
                            <th>Контакт 2</th>
                            <td class="val-old">{{ $oldData->name_sale_2 }}</td>
                            <td class="val-new">
                                @php
                                    $secondContactable = \Illuminate\Support\Facades\DB::table('contactables')
                                        ->where('contactable_type', \App\Models\Property\Property::class)
                                        ->where('contactable_id', $property->id)
                                        ->where('contact_id', '!=', $contactable->contact_id ?? 0)
                                        ->first();
                                    $secondContact = $secondContactable ? \Illuminate\Support\Facades\DB::table('contacts')->where('id', $secondContactable->contact_id)->first() : null;
                                @endphp
                                {{ $secondContact->first_name ?? '—' }}
                            </td>
                        </tr>
                        @endif

                        {{-- Заметки для коллег --}}
                        <tr>
                            <th>Заметки коллегам</th>
                            <td class="val-old">{{ \Illuminate\Support\Str::limit($oldData->notes ?? '—', 80) }}</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($property->agent_notes ?? '—', 80) }}</td>
                        </tr>

                        {{-- Комиссия --}}
                        @php
                            $oldCommission = $oldData->price_rieltor_proc ?? $oldData->price_rieltor ?? null;
                            $oldCommType = !empty($oldData->price_rieltor_proc) ? 'percent' : (!empty($oldData->price_rieltor) ? 'fixed' : null);
                            $commMismatch = $oldCommission && (
                                (float)($property->commission ?? 0) != (float)$oldCommission
                                || ($oldCommType && $property->commission_type !== $oldCommType)
                            );
                        @endphp
                        <tr class="{{ $commMismatch ? 'val-mismatch' : '' }}">
                            <th>Комиссия</th>
                            <td class="val-old">
                                @if(!empty($oldData->price_rieltor_proc))
                                    {{ $oldData->price_rieltor_proc }}%
                                @elseif(!empty($oldData->price_rieltor))
                                    {{ $oldData->price_rieltor }} (fixed)
                                @else
                                    —
                                @endif
                            </td>
                            <td class="val-new">{{ $property->commission ?? '—' }} {{ $property->commission ? "({$property->commission_type})" : '' }}</td>
                        </tr>

                        {{-- YouTube --}}
                        @if(!empty($oldData->youtube) || $property->youtube_url)
                        <tr class="{{ !empty($oldData->youtube) && $property->youtube_url !== ($oldData->youtube ?? null) ? 'val-mismatch' : '' }}">
                            <th>YouTube</th>
                            <td class="val-old">{{ \Illuminate\Support\Str::limit($oldData->youtube ?? '—', 60) }}</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($property->youtube_url ?? '—', 60) }}</td>
                        </tr>
                        @endif

                        {{-- External URL --}}
                        @php
                            $expectedUrl = !empty($oldData->linkToAd) ? $oldData->linkToAd : ($old->rem_url ?: null);
                        @endphp
                        @if($expectedUrl || $property->external_url)
                        <tr class="{{ $expectedUrl && $property->external_url !== $expectedUrl ? 'val-mismatch' : '' }}">
                            <th>Внешняя ссылка</th>
                            <td class="val-old">{{ \Illuminate\Support\Str::limit($expectedUrl ?? '—', 60) }}</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($property->external_url ?? '—', 60) }}</td>
                        </tr>
                        @endif

                        {{-- Сотрудник --}}
                        @php
                            $oldUserRow = $old->user_id ? \Illuminate\Support\Facades\DB::connection('factor_dump')->table('users')->where('id', $old->user_id)->first() : null;
                            $oldUserFullName = $oldUserRow ? trim(($oldUserRow->sname ?? '') . ' ' . ($oldUserRow->name ?? '') . ' ' . ($oldUserRow->parent_name ?? '')) : null;
                            $oldFilialId = $oldUserRow->filial ?? null;
                            $oldFilialName = $oldFilialId ? \Illuminate\Support\Facades\DB::connection('factor_dump')->table('filials')->where('id', $oldFilialId)->value('name') : null;

                            $newUserRow = $property->user_id ? \Illuminate\Support\Facades\DB::table('users')->where('id', $property->user_id)->first() : null;
                            $newEmployee = $property->employee_id ? \App\Models\Employee\Employee::with(['office'])->find($property->employee_id) : null;
                            $newOfficeName = $newEmployee?->office?->name ?? null;

                            $userNameMismatch = $oldUserRow && $newUserRow
                                && mb_strtolower(trim($oldUserRow->email ?? '')) !== mb_strtolower(trim($newUserRow->email ?? ''));
                            $filialMismatch = $oldFilialName && $newOfficeName
                                && mb_strtolower(trim($oldFilialName)) !== mb_strtolower(trim($newOfficeName));
                        @endphp
                        {{-- Сотрудник: имя --}}
                        <tr class="{{ $userNameMismatch ? 'val-mismatch' : '' }}">
                            <th>Сотрудник: имя</th>
                            <td class="val-old">{{ $oldUserFullName ?: '—' }}<br><small class="text-muted">{{ $oldUserRow->email ?? '' }} | id={{ $old->user_id ?? '—' }}</small></td>
                            <td class="val-new">{{ $newUserRow->name ?? '—' }}<br><small class="text-muted">{{ $newUserRow->email ?? '' }} | user={{ $property->user_id ?? '—' }}, emp={{ $property->employee_id ?? '—' }}</small></td>
                        </tr>

                        {{-- Сотрудник: телефон --}}
                        @if($oldUserRow && ($oldUserRow->tel ?? null))
                        <tr>
                            <th>Сотрудник: телефон</th>
                            <td class="val-old">{{ $oldUserRow->tel }}</td>
                            <td class="val-new">{{ $newEmployee->phone ?? '—' }}</td>
                        </tr>
                        @endif

                        {{-- Сотрудник: филиал --}}
                        <tr class="{{ $filialMismatch ? 'val-mismatch' : '' }}">
                            <th>Сотрудник: филиал</th>
                            <td class="val-old">{{ $oldFilialName ?: '—' }} (filial={{ $oldFilialId ?? '—' }})</td>
                            <td class="val-new">{{ $newOfficeName ?: '—' }} (office_id={{ $newEmployee->office_id ?? '—' }})</td>
                        </tr>

                        {{-- Тип контакта --}}
                        @php
                            $contactTypeMap = [1 => 'Риелтор → 202', 2 => 'Собственник → 195'];
                            $expectedContactTypeId = [1 => 202, 2 => 195][$old->type_sale ?? 0] ?? null;
                        @endphp
                        @if($old->type_sale ?? null)
                        <tr class="{{ $expectedContactTypeId && $property->contact_type_id != $expectedContactTypeId ? 'val-mismatch' : '' }}">
                            <th>Тип контакта</th>
                            <td class="val-old">type_sale={{ $old->type_sale }} ({{ $contactTypeMap[$old->type_sale] ?? '?' }})</td>
                            <td class="val-new">contact_type_id={{ $property->contact_type_id ?? 'NULL' }}</td>
                        </tr>
                        @endif

                        {{-- Видимость --}}
                        <tr class="{{ (bool)($old->open ?? 1) !== (bool)$property->is_visible_to_agents ? 'val-mismatch' : '' }}">
                            <th>Видимость</th>
                            <td class="val-old">open={{ $old->open ?? 1 }}</td>
                            <td class="val-new">is_visible={{ $property->is_visible_to_agents ? '1' : '0' }}</td>
                        </tr>

                        {{-- Features --}}
                        @php
                            $featureCount = \Illuminate\Support\Facades\DB::table('property_features')->where('property_id', $property->id)->count();
                            $oldFeatures = collect(['the_balkon', 'the_plase_auto', 'the_vid_na'])
                                ->filter(fn($f) => !empty($old->$f))
                                ->map(fn($f) => $f . '=' . $old->$f)
                                ->implode(', ');
                        @endphp
                        @if($oldFeatures || $featureCount > 0)
                        <tr class="{{ $oldFeatures && $featureCount === 0 ? 'val-mismatch' : '' }}">
                            <th>Features</th>
                            <td class="val-old">{{ $oldFeatures ?: '—' }}</td>
                            <td class="val-new">{{ $featureCount }} шт.</td>
                        </tr>
                        @endif

                        {{-- Notes --}}
                        @if($property->notes)
                        <tr>
                            <th>Заметки (notes)</th>
                            <td class="val-old" colspan="1">—</td>
                            <td class="val-new">{{ \Illuminate\Support\Str::limit($property->notes, 120) }}</td>
                        </tr>
                        @endif

                        {{-- Даты --}}
                        @php
                            $createdMatch = $old->date_created
                                ? \Carbon\Carbon::parse($old->date_created)->format('Y-m-d H:i:s') === $property->created_at->format('Y-m-d H:i:s')
                                : true;
                        @endphp
                        <tr class="{{ !$createdMatch ? 'val-mismatch' : '' }}">
                            <th>Создан</th>
                            <td class="val-old">{{ $old->date_created ?? '—' }}</td>
                            <td class="val-new">{{ $property->created_at }}</td>
                        </tr>

                        <tr>
                            <th>Обновлён</th>
                            <td class="val-old">{{ $old->date_updated ?? '—' }}</td>
                            <td class="val-new">{{ $property->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
                @else
                    <div class="p-3 text-danger">Объект не найден в старой базе!</div>
                @endif

                {{-- Блок ошибок (заполняется через JS) --}}
                <div class="verify-errors" id="errors-{{ $property->id }}" style="display:none">
                    <strong>Несоответствия:</strong>
                    <ul id="errors-list-{{ $property->id }}"></ul>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Нет перенесённых объектов</div>
    @endforelse

    {{-- Пагинация --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $properties->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Развернуть/свернуть карточку
    function toggleCard(id) {
        document.getElementById('card-' + id).classList.toggle('open');
    }

    // Проверка одного объекта
    function verifyOne(id) {
        var badge = document.getElementById('status-' + id);
        badge.className = 'verify-status-badge badge bg-info';
        badge.textContent = 'проверка...';

        $.ajax({
            url: '/migration/verify/' + id,
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                var card = document.getElementById('card-' + id);
                card.classList.remove('status-pending', 'status-ok', 'status-mismatch');

                if (data.status === 'ok') {
                    badge.className = 'verify-status-badge badge bg-success';
                    badge.textContent = 'OK';
                    card.classList.add('status-ok');
                    $('#errors-' + id).hide();
                } else {
                    badge.className = 'verify-status-badge badge bg-danger';
                    badge.textContent = 'ошибки: ' + data.errors.length;
                    card.classList.add('status-mismatch');

                    var list = $('#errors-list-' + id);
                    list.empty();
                    data.errors.forEach(function(err) {
                        list.append('<li>' + err + '</li>');
                    });
                    $('#errors-' + id).show();
                    card.classList.add('open');
                }
            },
            error: function() {
                badge.className = 'verify-status-badge badge bg-danger';
                badge.textContent = 'ошибка запроса';
            }
        });
    }

    // Массовая проверка (текущая страница)
    $('#btn-verify-page').on('click', function() {
        var ids = [];
        $('.verify-card').each(function() {
            ids.push(parseInt($(this).data('id')));
        });
        runBatchVerify(ids);
    });

    // Массовая проверка (все объекты)
    $('#btn-verify-all').on('click', function() {
        if (!confirm('Проверить все объекты? Это может занять некоторое время.')) return;
        runBatchVerify([]);
    });

    function runBatchVerify(ids) {
        var $progress = $('#batch-progress');
        var $bar = $('#batch-progress-bar');
        var $text = $('#batch-progress-text');
        var $stats = $('#batch-progress-stats');

        $progress.show();
        $bar.css('width', '0%').removeClass('bg-success bg-danger');
        $text.text('Запуск проверки...');
        $stats.text('');

        $.ajax({
            url: '/migration/verify-batch',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { ids: ids },
            success: function(data) {
                var pct = 100;
                $bar.css('width', pct + '%');

                if (data.fail === 0) {
                    $bar.addClass('bg-success');
                    $text.text('Все проверки пройдены!');
                } else {
                    $bar.addClass('bg-danger');
                    $text.text('Найдены несоответствия');
                }
                $stats.text('OK: ' + data.ok + ' | Ошибки: ' + data.fail + ' | Всего: ' + data.total);

                // Обновляем статусы на странице
                if (data.results) {
                    Object.keys(data.results).forEach(function(id) {
                        var r = data.results[id];
                        var badge = document.getElementById('status-' + id);
                        var card = document.getElementById('card-' + id);
                        if (!badge || !card) return;

                        card.classList.remove('status-pending', 'status-ok', 'status-mismatch');

                        if (r.status === 'ok') {
                            badge.className = 'verify-status-badge badge bg-success';
                            badge.textContent = 'OK';
                            card.classList.add('status-ok');
                        } else {
                            badge.className = 'verify-status-badge badge bg-danger';
                            badge.textContent = 'ошибки: ' + r.errors.length;
                            card.classList.add('status-mismatch');

                            var list = $('#errors-list-' + id);
                            list.empty();
                            r.errors.forEach(function(err) {
                                list.append('<li>' + err + '</li>');
                            });
                            $('#errors-' + id).show();
                        }
                    });
                }
            },
            error: function() {
                $bar.css('width', '100%').addClass('bg-danger');
                $text.text('Ошибка при проверке');
            }
        });
    }

    // Развернуть все
    $('#btn-expand-all').on('click', function() {
        var allOpen = $('.verify-card.open').length === $('.verify-card').length;
        if (allOpen) {
            $('.verify-card').removeClass('open');
            $(this).text('Развернуть все');
        } else {
            $('.verify-card').addClass('open');
            $(this).text('Свернуть все');
        }
    });
</script>
@endpush
