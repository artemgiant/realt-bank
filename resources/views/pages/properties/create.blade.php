@extends('layouts.crm')

@section('title', 'Создание объекта - Realt Bank')

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
                Объект
                <span id="property-id">Новый объект</span>
            </h2>
        </div>
        <div class="create-header-right">
            <div class="create-header-add">
                Добавлено:
                <span id="created-at">{{ now()->format('d.m.Y') }}</span>
            </div>
            <div class="create-header-update">
                Обновлено:
                <span id="updated-at">{{ now()->format('d.m.Y') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="create">
        {{-- Сообщения об ошибках --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Ошибки валидации:</strong>
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

                    <div class="create-filter-row row0">
                        <div class="left">
                            <ul class="block-info">
                                <li class="block-info-item">
                                    <div class="info-title-wrapper">
                                        <h2 class="info-title">Контакт</h2>
                                        <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal"
                                                data-bs-target="#add-contact-modal">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                        d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z"
                                                        fill="#AAAAAA"/>
                                            </svg>
                                        </button>
                                        <button class="btn  btn-add-client" type="button" data-bs-toggle="modal"
                                                data-bs-target="#add-contact-modal">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                        d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"
                                                        fill="#AAAAAA"/>
                                                <path
                                                        d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z"
                                                        fill="#AAAAAA"/>
                                                <!--											тут закоментований мінус-->
                                                <!--											<path-->
                                                <!--												d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"-->
                                                <!--												fill="#AAAAAA"/>-->
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="info-avatar">
                                        <picture><source srcset="./img/icon/default-avatar-table.svg" type="image/webp"><img src="./img/icon/default-avatar-table.svg" alt=""></picture>
                                    </div>
                                    <div class="info-contacts">
                                        <p class="info-contacts-name">Василий Федотов</p>
                                        <p class="info-description">Представитель девелопера</p>
                                        <a href="tel:+381231257869" class="info-contacts-tel">+38 (123) 125 - 78 - 69</a>
                                    </div>
                                    <div class="info-links">
                                        <a href="https://wa.me/380XXXXXXXXX">
                                            <picture><source srcset="./img/icon/icon-table/cnapchat.svg" type="image/webp"><img src="./img/icon/icon-table/cnapchat.svg" alt=""></picture>
                                        </a>
                                        <a href="viber://chat?number=%2B380XXXXXXXXX">
                                            <picture><source srcset="./img/icon/icon-table/viber.svg" type="image/webp"><img src="./img/icon/icon-table/viber.svg" alt=""></picture>
                                        </a>
                                        <a href="https://t.me/+380XXXXXXXXX">
                                            <picture><source srcset="./img/icon/icon-table/tg.svg" type="image/webp"><img src="./img/icon/icon-table/tg.svg" alt=""></picture>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                            <div class="left-items-wrapper">
                                <div class="item">
                                    <label for="link-on-the-ad">Ссылка на объявление</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" type="url" id="link-on-the-ad" autocomplete="off" placeholder="Вставьте ссылку" />
                                    </div>
                                </div>
                                <div class="item">
                                    <label class="my-custom-input">
                                        <input type="checkbox" name="penthouse">
                                        <span class="my-custom-box"></span>
                                        <span class="my-custom-text">Открыть контакты и адрес объекта для агентов моей компании</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Основная информация (Контакт и Агент) --}}
                    {{-- Детальная информация --}}
                    <h3 class="create-filter-title">
                        <span>Подробно</span>
                    </h3>
                    <div class="create-filter-row">
                        {{-- Тип сделки --}}
                        <div class="item selects blue-select2">
                            <label class="item-label" for="deal_type_id">Тип сделки <span class="text-danger">*</span></label>
                            <select id="deal_type_id" name="deal_type_id" class="js-example-responsive2" required>
                                <option value="">Выберите тип сделки</option>
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

                        {{-- Вид сделки --}}
                        <div class="item selects">
                            <label class="item-label" for="deal_kind_id">Вид сделки</label>
                            <select id="deal_kind_id" name="deal_kind_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($dealKinds as $dealKind)
                                    <option value="{{ $dealKind->id }}" {{ old('deal_kind_id') == $dealKind->id ? 'selected' : '' }}>
                                        {{ $dealKind->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип здания --}}
                        <div class="item selects">
                            <label class="item-label" for="building_type_id">Тип здания</label>
                            <select id="building_type_id" name="building_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($buildingTypes as $buildingType)
                                    <option value="{{ $buildingType->id }}" {{ old('building_type_id') == $buildingType->id ? 'selected' : '' }}>
                                        {{ $buildingType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        {{-- Локация --}}


                        {{-- Улица --}}


                        {{-- Номер дома / квартиры --}}


                        {{-- Ориентир --}}


                        {{-- Тип недвижимости --}}
                        <div class="item selects">
                            <label class="item-label" for="property_type_id">Тип недвижимости</label>
                            <select id="property_type_id" name="property_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->id }}" {{ old('property_type_id') == $propertyType->id ? 'selected' : '' }}>
                                        {{ $propertyType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Количество комнат --}}
                        <div class="item selects">
                            <label class="item-label" for="room_count_id">Количество комнат</label>
                            <select id="room_count_id" name="room_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($roomCounts as $roomCount)
                                    <option value="{{ $roomCount->id }}" {{ old('room_count_id') == $roomCount->id ? 'selected' : '' }}>
                                        {{ $roomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Состояние --}}
                        <div class="item selects">
                            <label class="item-label" for="condition_id">Состояние</label>
                            <select id="condition_id" name="condition_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($conditions as $condition)
                                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                                        {{ $condition->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Количество ванных --}}
                        <div class="item selects noresize170">
                            <label class="item-label" for="bathroom_count_id">Количество ванных комнат</label>
                            <select id="bathroom_count_id" name="bathroom_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($bathroomCounts as $bathroomCount)
                                    <option value="{{ $bathroomCount->id }}" {{ old('bathroom_count_id') == $bathroomCount->id ? 'selected' : '' }}>
                                        {{ $bathroomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Высота потолка --}}
                        <div class="item selects">
                            <label class="item-label" for="ceiling_height_id">Высота потолка</label>
                            <select id="ceiling_height_id" name="ceiling_height_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($ceilingHeights as $ceilingHeight)
                                    <option value="{{ $ceilingHeight->id }}" {{ old('ceiling_height_id') == $ceilingHeight->id ? 'selected' : '' }}>
                                        {{ $ceilingHeight->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип стен --}}
                        <div class="item selects">
                            <label class="item-label" for="wall_type_id">Тип стен</label>
                            <select id="wall_type_id" name="wall_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($wallTypes as $wallType)
                                    <option value="{{ $wallType->id }}" {{ old('wall_type_id') == $wallType->id ? 'selected' : '' }}>
                                        {{ $wallType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Площади --}}
                        <div class="item w33 noresize200">
                            <span>
                                <label for="area_total">Площадь общая</label> /
                                <label for="area_living">жилая</label> /
                                <label for="area_kitchen">кухни</label>
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

                        {{-- Площадь участка --}}
                        <div class="item noresize120">
                            <label for="area_land">Площадь участка</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="area_land" name="area_land" type="text"
                                       value="{{ old('area_land') }}" autocomplete="off" placeholder="Укажите значение">
                            </div>
                        </div>

                        {{-- Этажи --}}
                        <div class="item noresize120">
                            <span>
                                <label class="item-label" for="floor">Этаж</label> /
                                <label for="floors_total">Этажность</label>
                            </span>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" id="floor" name="floor" type="text"
                                       value="{{ old('floor') }}" autocomplete="off">
                                <input class="item-inputText" id="floors_total" name="floors_total" type="text"
                                       value="{{ old('floors_total') }}" autocomplete="off">
                            </div>
                        </div>

                        {{-- Отопление --}}
                        <div class="item selects">
                            <label class="item-label" for="heating_type_id">Отопление</label>
                            <select id="heating_type_id" name="heating_type_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($heatingTypes as $heatingType)
                                    <option value="{{ $heatingType->id }}" {{ old('heating_type_id') == $heatingType->id ? 'selected' : '' }}>
                                        {{ $heatingType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Год постройки --}}
                        <div class="item selects">
                            <label class="item-label" for="year_built">Год постройки</label>
                            <select id="year_built" name="year_built" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($yearsBuilt as $yearBuilt)
                                    <option value="{{ $yearBuilt->id }}" {{ old('year_built') == $yearBuilt->id ? 'selected' : '' }}>
                                        {{ $yearBuilt->name }}
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

                        {{-- Цена --}}
                        <div class="item">
                            <label class="green" for="price">Цена</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="price" name="price" type="text"
                                       value="{{ old('price') }}" autocomplete="off" placeholder="Введите значение">
                            </div>
                            @error('price')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Комиссия --}}
                        <div class="item noresize170">
                            <label for="commission">Комиссия от владельца</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="commission" name="commission" type="text"
                                       value="{{ old('commission') }}" autocomplete="off" placeholder="Введите значение">
                            </div>
                        </div>

                        {{-- Видео YouTube --}}
                        <div class="item">
                            <label for="youtube_url">Видео YouTube</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="youtube_url" name="youtube_url" type="url"
                                       value="{{ old('youtube_url') }}" autocomplete="off" placeholder="https://linkname.youtube.com">
                            </div>
                        </div>

                        {{-- Источник --}}
                        <div class="item selects">
                            <label class="item-label" for="source_id">Источник</label>
                            <select id="source_id" name="source_id" class="js-example-responsive2 my-select2">
                                <option value=""></option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Особенности --}}
{{--                        <div class="item">--}}
{{--                            <span class="item-label">Особенности</span>--}}
{{--                            <div class="multiple-menu">--}}
{{--                                <button class="multiple-menu-btn" data-open-menu="false" type="button">--}}
{{--                                    Выберите параметры--}}
{{--                                </button>--}}
{{--                                <div class="multiple-menu-wrapper">--}}
{{--                                    <label>--}}
{{--                                        <input class="multiple-menu-search" autocomplete="off" name="search-additionally" type="text" placeholder="Поиск">--}}
{{--                                    </label>--}}
{{--                                    <ul class="multiple-menu-list">--}}
{{--                                        @foreach($features as $feature)--}}
{{--                                            <li class="multiple-menu-item">--}}
{{--                                                <label class="my-custom-input">--}}
{{--                                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}"--}}
{{--                                                            {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}>--}}
{{--                                                    <span class="my-custom-box"></span>--}}
{{--                                                    <span class="my-custom-text">{{ $feature->name }}</span>--}}
{{--                                                </label>--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>

                {{-- Теги фильтра --}}
                <div class="create-filter-tags">
                    <div class="filter-tags" id="applied-filters">
                        {{-- Теги будут добавлены JS --}}
                    </div>
                </div>

                {{-- Описание и фото --}}
                <h3 class="create-filter-title">
                    <span>Описание и фото</span>
                </h3>
                <div class="create-filter-documents">
                    <div class="create-filter-row advertising-wrapper">
                        <div class="left">
                            <div class="title_advertising-wrapper">
                                <label for="title_ru">Заголовок для рекламы</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="title_ru" name="title_ru" type="text"
                                           value="{{ old('title_ru') }}" autocomplete="off" placeholder="Введите заголовок">
                                </div>
                            </div>
                            <div class="note_advertising-wrapper">
                                <label for="agent_notes">Примечание для агентов</label>
                                <div class="item-inputText-wrapper">
                                    <textarea class="item-textareaText" id="agent_notes" name="agent_notes"
                                              autocomplete="off" placeholder="Введите текст">{{ old('agent_notes') }}</textarea>
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
{{--                                    <li class="nav-item">--}}
{{--                                        <button id="generation-ai-about-developer" class="nav-link ai" type="button">--}}
{{--                                            <span>AI Text</span>--}}
{{--                                        </button>--}}
{{--                                    </li>--}}
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-description-advertising" role="tabpanel"
                                         aria-labelledby="ua-tab-description-advertising" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ua">Описание для рекламы</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText" rows="10" data-input-lang="ua"
                                                              id="description_ua" name="description_ua" autocomplete="off"
                                                              placeholder="Введите текст">{{ old('description_ua') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-description-advertising" role="tabpanel"
                                         aria-labelledby="ru-tab-description-advertising" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ru">Описание для рекламы</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText" rows="10" data-input-lang="ru"
                                                              id="description_ru" name="description_ru" autocomplete="off"
                                                              placeholder="Введите текст">{{ old('description_ru') }}</textarea>
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

                    {{-- Загрузка документов --}}
                </div>

                {{-- Фото объекта --}}
                <div class="create-filter-photo">

                    {{-- Кнопки действий --}}


                    <div class="photo-info-btnGroup">
                        <div class="photo-info-btnGroup-wrapper">
                            <div class="photo-info-btnGroup-left">
                                <button class="btn btn-outline-primary" type="button">
                                    Отменить изменения
                                </button>

                            </div>
                            <div class="photo-info-btnGroup-right">
                                <button class="btn btn-outline-success" type="button">
                                    Обновить дату актуальности
                                </button>
                                <button class="btn btn-primary" type="submit">
                                    Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </form>
    </div>

    {{-- Модальные окна --}}
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

    {{-- Координаты для гео-модалки --}}
    <script>
        // Сохраняем координаты в hidden fields
        document.addEventListener('DOMContentLoaded', function() {
            const saveGeoBtn = document.getElementById('save-geo-btn');
            if (saveGeoBtn) {
                saveGeoBtn.addEventListener('click', function() {
                    const lat = document.getElementById('latitude');
                    const lng = document.getElementById('longitude');

                    // Создаем hidden inputs если их нет
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
