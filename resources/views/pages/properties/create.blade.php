@extends('layouts.crm')

@section('title', 'Створення об\'єкта - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/page-create.min.css') }}">
@endpush

@section('header')
    <div class="create-header">
        <div class="create-header-left">
            <a class="create-header-back" href="{{ route('properties.index') }}">
                <picture>
                    <source srcset="{{ asset('img/icon/arrow-back-link.svg') }}" type="image/webp">
                    <img src="{{ asset('img/icon/arrow-back-link.svg') }}" alt="Back">
                </picture>
            </a>
            <h2 class="create-header-title">
                Об'єкт
                <span id="property-id">Новий об'єкт</span>
            </h2>
        </div>
        <div class="create-header-right">
            <div class="create-header-add">
                Додано:
                <span id="created-at">{{ now()->format('d.m.Y') }}</span>
            </div>
            <div class="create-header-update">
                Оновлено:
                <span id="updated-at">{{ now()->format('d.m.Y') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="create">
        {{-- Повідомлення про помилки --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Помилки валідації:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="property-form" action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="create-filter">
                <div class="create-filter-data">
                    {{-- Основна інформація (Контакт і Агент) --}}
                    <div class="create-filter-row row0">
                        <div class="left">
                            <ul class="block-info">
                                <li class="block-info-item">
                                    <div class="info-title-wrapper">
                                        <h2 class="info-title">Контакт</h2>
                                        <button class="btn btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA"/>
                                            </svg>
                                        </button>
                                        <button class="btn btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"/>
                                                <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="hidden" name="contact_id" id="contact_id" value="{{ old('contact_id') }}">
                                    <div class="info-avatar">
                                        <picture>
                                            <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}" type="image/webp">
                                            <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar">
                                        </picture>
                                    </div>
                                    <div class="info-contacts">
                                        <p class="info-contacts-name" id="contact-name">Виберіть контакт</p>
                                        <p class="info-description" id="contact-description">-</p>
                                        <a href="tel:" class="info-contacts-tel" id="contact-tel">-</a>
                                    </div>
                                    <div class="info-links">
                                        <a href="#" class="contact-link whatsapp" id="contact-whatsapp" style="display: none;">
                                            <picture>
                                                <source srcset="{{ asset('img/icon/icon-table/cnapchat.svg') }}" type="image/webp">
                                                <img src="{{ asset('img/icon/icon-table/cnapchat.svg') }}" alt="">
                                            </picture>
                                        </a>
                                        <a href="#" class="contact-link viber" id="contact-viber" style="display: none;">
                                            <picture>
                                                <source srcset="{{ asset('img/icon/icon-table/viber.svg') }}" type="image/webp">
                                                <img src="{{ asset('img/icon/icon-table/viber.svg') }}" alt="">
                                            </picture>
                                        </a>
                                        <a href="#" class="contact-link telegram" id="contact-telegram" style="display: none;">
                                            <picture>
                                                <source srcset="{{ asset('img/icon/icon-table/tg.svg') }}" type="image/webp">
                                                <img src="{{ asset('img/icon/icon-table/tg.svg') }}" alt="">
                                            </picture>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                            <div class="left-items-wrapper">
                                <div class="item">
                                    <label for="external_url">Посилання на оголошення</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" type="url" id="external_url" name="external_url"
                                               value="{{ old('external_url') }}" autocomplete="off" placeholder="Вставте посилання">
                                    </div>
                                </div>
                                <div class="item">
                                    <label class="my-custom-input">
                                        <input type="checkbox" name="is_visible_to_agents" value="1" {{ old('is_visible_to_agents') ? 'checked' : '' }}>
                                        <span class="my-custom-box"></span>
                                        <span class="my-custom-text">Відкрити контакти та адресу об'єкта для агентів моєї компанії</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Інформація про агента --}}
                        <div class="right">
                            <ul class="block-info">
                                <li class="block-info-item">
                                    <div class="info-title-wrapper">
                                        <h2 class="info-title">Агент</h2>
                                        <button class="btn btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#transfer-to-agent">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#EF9629" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="info-avatar">
                                        <picture>
                                            <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}" type="image/webp">
                                            <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar">
                                        </picture>
                                    </div>
                                    <div class="info-contacts">
                                        <p class="info-contacts-name" id="agent-name">{{ auth()->user()->name }}</p>
                                        <p class="info-description" id="agent-description">Поточний агент</p>
                                        <a href="tel:" class="info-contacts-tel" id="agent-tel">-</a>
                                    </div>
                                </li>
                            </ul>
                            <div class="item">
                                <label for="notes">Нотатки</label>
                                <div class="item-inputText-wrapper">
                                    <textarea class="item-textareaText" id="notes" name="notes" autocomplete="off"
                                              placeholder="Введіть текст">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Детальна інформація --}}
                    <h3 class="create-filter-title">
                        <span>Детально</span>
                    </h3>
                    <div class="create-filter-row">
                        {{-- Тип угоди --}}
                        <div class="item selects blue-select2">
                            <label class="item-label" for="deal_type_id">Тип угоди <span class="text-danger">*</span></label>
                            <select id="deal_type_id" name="deal_type_id" class="js-example-responsive2" required>
                                <option value="">Виберіть тип угоди</option>
                                @foreach($dealTypes as $dealType)
                                    <option value="{{ $dealType->id }}" {{ old('deal_type_id') == $dealType->id ? 'selected' : '' }}>
                                        {{ $dealType->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('deal_type_id')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Вид угоди --}}
                        <div class="item selects">
                            <label class="item-label" for="deal_kind_id">Вид угоди</label>
                            <select id="deal_kind_id" name="deal_kind_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($dealKinds as $dealKind)
                                    <option value="{{ $dealKind->id }}" {{ old('deal_kind_id') == $dealKind->id ? 'selected' : '' }}>
                                        {{ $dealKind->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип будівлі --}}
                        <div class="item selects">
                            <label class="item-label" for="building_type_id">Тип будівлі</label>
                            <select id="building_type_id" name="building_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($buildingTypes as $buildingType)
                                    <option value="{{ $buildingType->id }}" {{ old('building_type_id') == $buildingType->id ? 'selected' : '' }}>
                                        {{ $buildingType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Комплекс --}}
                        <div class="item selects">
                            <label class="item-label" for="complex_id">Комплекс</label>
                            <select id="complex_id" name="complex_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($complexes as $complex)
                                    <option value="{{ $complex->id }}" {{ old('complex_id') == $complex->id ? 'selected' : '' }}>
                                        {{ $complex->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Секція / Корпус --}}
                        <div class="item selects w33">
                            <label class="item-label" for="section_id">Секція / Корпус</label>
                            <select id="section_id" name="section_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                {{-- Заповнюється через AJAX на основі вибраного комплексу --}}
                            </select>
                        </div>

                        {{-- Локація --}}
                        <div class="item w50">
                            <label>Локація</label>
                            @include('pages.properties.modals.location-dropdown')
                            <input type="hidden" name="country_id" id="country_id" value="{{ old('country_id') }}">
                            <input type="hidden" name="region_id" id="region_id" value="{{ old('region_id') }}">
                            <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}">
                            <input type="hidden" name="district_id" id="district_id" value="{{ old('district_id') }}">
                            <input type="hidden" name="zone_id" id="zone_id" value="{{ old('zone_id') }}">
                        </div>

                        {{-- Вулиця --}}
                        <div class="item selects">
                            <label class="item-label" for="street_id">Вулиця</label>
                            <select id="street_id" name="street_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                {{-- Заповнюється через AJAX на основі вибраного міста --}}
                            </select>
                        </div>

                        {{-- Номер будинку / квартири --}}
                        <div class="item noresize120">
                            <span>
                                <label class="item-label" for="building_number">№ Будинок</label> /
                                <label for="apartment_number">Квартира</label>
                            </span>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" id="building_number" name="building_number" type="text"
                                       value="{{ old('building_number') }}" autocomplete="off">
                                <input class="item-inputText" id="apartment_number" name="apartment_number" type="text"
                                       value="{{ old('apartment_number') }}" autocomplete="off">
                            </div>
                        </div>

                        {{-- Орієнтир --}}
                        <div class="item selects noresize120">
                            <label class="item-label" for="landmark_id">Орієнтир/Станція</label>
                            <select id="landmark_id" name="landmark_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                {{-- Заповнюється через AJAX на основі вибраного міста --}}
                            </select>
                        </div>

                        {{-- Тип нерухомості --}}
                        <div class="item selects">
                            <label class="item-label" for="property_type_id">Тип нерухомості</label>
                            <select id="property_type_id" name="property_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->id }}" {{ old('property_type_id') == $propertyType->id ? 'selected' : '' }}>
                                        {{ $propertyType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Кількість кімнат --}}
                        <div class="item selects">
                            <label class="item-label" for="room_count_id">Кількість кімнат</label>
                            <select id="room_count_id" name="room_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($roomCounts as $roomCount)
                                    <option value="{{ $roomCount->id }}" {{ old('room_count_id') == $roomCount->id ? 'selected' : '' }}>
                                        {{ $roomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Стан --}}
                        <div class="item selects">
                            <label class="item-label" for="condition_id">Стан</label>
                            <select id="condition_id" name="condition_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($conditions as $condition)
                                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                        {{ $condition->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Кількість ванних --}}
                        <div class="item selects noresize170">
                            <label class="item-label" for="bathroom_count_id">Кількість ванних кімнат</label>
                            <select id="bathroom_count_id" name="bathroom_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($bathroomCounts as $bathroomCount)
                                    <option value="{{ $bathroomCount->id }}" {{ old('bathroom_count_id') == $bathroomCount->id ? 'selected' : '' }}>
                                        {{ $bathroomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Висота стелі --}}
                        <div class="item selects">
                            <label class="item-label" for="ceiling_height_id">Висота стелі</label>
                            <select id="ceiling_height_id" name="ceiling_height_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($ceilingHeights as $ceilingHeight)
                                    <option value="{{ $ceilingHeight->id }}" {{ old('ceiling_height_id') == $ceilingHeight->id ? 'selected' : '' }}>
                                        {{ $ceilingHeight->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип стін --}}
                        <div class="item selects">
                            <label class="item-label" for="wall_type_id">Тип стін</label>
                            <select id="wall_type_id" name="wall_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($wallTypes as $wallType)
                                    <option value="{{ $wallType->id }}" {{ old('wall_type_id') == $wallType->id ? 'selected' : '' }}>
                                        {{ $wallType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Площі --}}
                        <div class="item w33 noresize200">
                            <span>
                                <label for="area_total">Площа загальна</label> /
                                <label for="area_living">житлова</label> /
                                <label for="area_kitchen">кухні</label>
                            </span>
                            <div class="item-inputText-wrapper shtrih2">
                                <input class="item-inputText" id="area_total" name="area_total" type="text"
                                       value="{{ old('area_total') }}" autocomplete="off" placeholder="000">
                                <input class="item-inputText" id="area_living" name="area_living" type="text"
                                       value="{{ old('area_living') }}" autocomplete="off" placeholder="000">
                                <input class="item-inputText" id="area_kitchen" name="area_kitchen" type="text"
                                       value="{{ old('area_kitchen') }}" autocomplete="off" placeholder="000">
                            </div>
                        </div>

                        {{-- Площа ділянки --}}
                        <div class="item noresize120">
                            <label for="area_land">Площа ділянки</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="area_land" name="area_land" type="text"
                                       value="{{ old('area_land') }}" autocomplete="off" placeholder="Вкажіть значення">
                            </div>
                        </div>

                        {{-- Поверхи --}}
                        <div class="item noresize120">
                            <span>
                                <label class="item-label" for="floor">Поверх</label> /
                                <label for="floors_total">Поверховість</label>
                            </span>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" id="floor" name="floor" type="text"
                                       value="{{ old('floor') }}" autocomplete="off">
                                <input class="item-inputText" id="floors_total" name="floors_total" type="text"
                                       value="{{ old('floors_total') }}" autocomplete="off">
                            </div>
                        </div>

                        {{-- Опалення --}}
                        <div class="item selects">
                            <label class="item-label" for="heating_type_id">Опалення</label>
                            <select id="heating_type_id" name="heating_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($heatingTypes as $heatingType)
                                    <option value="{{ $heatingType->id }}" {{ old('heating_type_id') == $heatingType->id ? 'selected' : '' }}>
                                        {{ $heatingType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Рік побудови --}}
                        <div class="item selects">
                            <label class="item-label" for="year_built">Рік побудови</label>
                            <select id="year_built" name="year_built" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ old('year_built') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Валюта --}}
                        <div class="item selects">
                            <label class="item-label" for="currency_id">Валюта <span class="text-danger">*</span></label>
                            <select id="currency_id" name="currency_id" class="js-example-responsive2" required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}"
                                            {{ old('currency_id', $currencies->firstWhere('is_default', true)?->id) == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->code }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ціна --}}
                        <div class="item">
                            <label class="green" for="price">Ціна</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="price" name="price" type="text"
                                       value="{{ old('price') }}" autocomplete="off" placeholder="Введіть значення">
                            </div>
                            @error('price')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Комісія --}}
                        <div class="item noresize170">
                            <label for="commission">Комісія від власника</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="commission" name="commission" type="text"
                                       value="{{ old('commission') }}" autocomplete="off" placeholder="Введіть значення">
                            </div>
                        </div>

                        {{-- Відео YouTube --}}
                        <div class="item">
                            <label for="youtube_url">Відео YouTube</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="youtube_url" name="youtube_url" type="url"
                                       value="{{ old('youtube_url') }}" autocomplete="off" placeholder="https://linkname.youtube.com">
                            </div>
                        </div>

                        {{-- Джерело --}}
                        <div class="item selects">
                            <label class="item-label" for="source_id">Джерело</label>
                            <select id="source_id" name="source_id" class="js-example-responsive2 my-select2">
                                <option value=""></option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Особливості --}}
                        <div class="item">
                            <span class="item-label">Особливості</span>
                            <div class="multiple-menu">
                                <button class="multiple-menu-btn" data-open-menu="false" type="button">
                                    Виберіть параметри
                                </button>
                                <div class="multiple-menu-wrapper">
                                    <label>
                                        <input class="multiple-menu-search" autocomplete="off" name="search-additionally" type="text" placeholder="Пошук">
                                    </label>
                                    <ul class="multiple-menu-list">
                                        @foreach($features as $feature)
                                            <li class="multiple-menu-item">
                                                <label class="my-custom-input">
                                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                                            {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}>
                                                    <span class="my-custom-box"></span>
                                                    <span class="my-custom-text">{{ $feature->name }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Теги фільтра --}}
                <div class="create-filter-tags">
                    <div class="filter-tags" id="applied-filters">
                        {{-- Теги будуть додані JS --}}
                    </div>
                </div>

                {{-- Опис і фото --}}
                <h3 class="create-filter-title">
                    <span>Опис і фото</span>
                </h3>
                <div class="create-filter-documents">
                    <div class="create-filter-row advertising-wrapper">
                        <div class="left">
                            <div class="title_advertising-wrapper">
                                <label for="title_ru">Заголовок для реклами</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="title_ru" name="title_ru" type="text"
                                           value="{{ old('title_ru') }}" autocomplete="off" placeholder="Введіть заголовок">
                                </div>
                            </div>
                            <div class="note_advertising-wrapper">
                                <label for="agent_notes">Примітка для агентів</label>
                                <div class="item-inputText-wrapper">
                                    <textarea class="item-textareaText" id="agent_notes" name="agent_notes"
                                              autocomplete="off" placeholder="Введіть текст">{{ old('agent_notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="right">
                            <div class="tab-the-name">
                                <ul class="nav nav-tabs" id="tab-about-developer" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ua-tab-description-advertising" data-bs-toggle="tab"
                                                data-bs-target="#ua-tab-pane-description-advertising" type="button" role="tab"
                                                aria-controls="ua-tab-pane-description-advertising" aria-selected="false">UA</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="ru-tab-description-advertising" data-bs-toggle="tab"
                                                data-bs-target="#ru-tab-pane-description-advertising" type="button" role="tab"
                                                aria-controls="ru-tab-pane-description-advertising" aria-selected="true">RU</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="en-tab-description-advertising" data-bs-toggle="tab"
                                                data-bs-target="#en-tab-pane-description-advertising" type="button" role="tab"
                                                aria-controls="en-tab-pane-description-advertising" aria-selected="false">EN</button>
                                    </li>
                                    <li class="nav-item">
                                        <button id="generation-ai-about-developer" class="nav-link ai" type="button">
                                            <span>AI Text</span>
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-description-advertising" role="tabpanel"
                                         aria-labelledby="ua-tab-description-advertising" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ua">Опис для реклами</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText" rows="10" data-input-lang="ua"
                                                              id="description_ua" name="description_ua" autocomplete="off"
                                                              placeholder="Введіть текст">{{ old('description_ua') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-description-advertising" role="tabpanel"
                                         aria-labelledby="ru-tab-description-advertising" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ru">Опис для реклами</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText" rows="10" data-input-lang="ru"
                                                              id="description_ru" name="description_ru" autocomplete="off"
                                                              placeholder="Введіть текст">{{ old('description_ru') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="en-tab-pane-description-advertising" role="tabpanel"
                                         aria-labelledby="en-tab-description-advertising" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_en">Description for advertising</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText" rows="10" data-input-lang="en"
                                                              id="description_en" name="description_en" autocomplete="off"
                                                              placeholder="Enter text">{{ old('description_en') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Завантаження документів --}}
                    <div class="loading-documents document">
                        <label for="documents">
                            <input type="file" id="documents" name="documents[]" multiple accept="image/png, image/jpeg, application/pdf, .doc, .docx, .xls, .xlsx">
                            <span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z" fill="#3585F5"/>
                                </svg>
                                <span class="text">Завантажити документи</span>
                            </span>
                        </label>
                        <div class="filter-tags" data-render-document></div>
                        <div class="error-container" data-error></div>
                    </div>
                </div>

                {{-- Фото об'єкта --}}
                <div class="create-filter-photo">
                    <div class="photo-info">
                        <div class="photo-info-left">
                            <span class="photo-info-left-title">Фото об'єкта</span>
                            <div class="photo-info-list-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="photos">
                                            <input type="file" id="photos" name="photos[]" multiple accept="image/png, image/jpg, image/jpeg, image/heic, image/webp">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z" fill="#3585F5" />
                                            </svg>
                                            <span>Завантажити фото</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                            <p class="photo-info-left-text">
                                Перше фото буде обкладинкою оголошення, перетягніть фотографії щоб змінити порядок
                            </p>
                        </div>
                        <div class="photo-info-right">
                            <span class="photo-info-right-title">Історія змін</span>
                            <ul class="history-info" id="history-changes">
                                <li class="history-info-item">
                                    <span>{{ now()->format('H:i d.m.Y') }} — {{ auth()->user()->name }}</span>
                                    <p>Створення об'єкта</p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Кнопки дій --}}
                    <div class="photo-info-btnGroup">
                        <div class="photo-info-btnGroup-wrapper">
                            <div class="photo-info-btnGroup-left">
                                <a href="{{ route('properties.index') }}" class="btn btn-outline-primary">
                                    Скасувати
                                </a>
                            </div>
                            <div class="photo-info-btnGroup-right">
                                <button type="submit" name="status" value="draft" class="btn btn-outline-success">
                                    Зберегти як чернетку
                                </button>
                                <button type="submit" name="status" value="active" class="btn btn-primary">
                                    Опублікувати
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Модальні вікна --}}
    @include('pages.properties.modals.contact-modal')
    @include('pages.properties.modals.employee-modal')
    @include('pages.properties.modals.transfer-agent-modal')
    @include('pages.properties.modals.geo-modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/page-home-table.min.js') }}"></script>
    <script src="{{ asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ asset('js/pages/full-filter.min.js') }}"></script>
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ asset('js/pages/modal-geo.min.js') }}"></script>
    <script src="{{ asset('js/pages/page-create.min.js') }}" type="module"></script>

    {{-- Координати для гео-модалки --}}
    <script>
        // Зберігаємо координати в hidden fields
        document.addEventListener('DOMContentLoaded', function() {
            const saveGeoBtn = document.getElementById('save-geo-btn');
            if (saveGeoBtn) {
                saveGeoBtn.addEventListener('click', function() {
                    const lat = document.getElementById('latitude');
                    const lng = document.getElementById('longitude');

                    // Створюємо hidden inputs якщо їх немає
                    let latInput = document.querySelector('input[name="latitude"]');
                    let lngInput = document.querySelector('input[name="longitude"]');

                    if (!latInput) {
                        latInput = document.createElement('input');
                        latInput.type = 'hidden';
                        latInput.name = 'latitude';
                        document.getElementById('property-form').appendChild(latInput);
                    }

                    if (!lngInput) {
                        lngInput = document.createElement('input');
                        lngInput.type = 'hidden';
                        lngInput.name = 'longitude';
                        document.getElementById('property-form').appendChild(lngInput);
                    }

                    latInput.value = lat.value;
                    lngInput.value = lng.value;
                });
            }
        });
    </script>
@endpush
