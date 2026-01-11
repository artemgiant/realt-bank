@extends('layouts.crm')

@section('title', 'Создание объекта - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/page-create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/location-search.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/field-widths.css') }}">


{{--Плагин по редактированию изображений--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tui-image-editor/3.15.0/tui-image-editor.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tui-color-picker/2.2.6/tui-color-picker.min.css">

{{---- Плагин по автосохранению форм ----}}
    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/form-autosave.css') }}">

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

                    {{-- Основная информация (Контакт и Агент) --}}
                    @include('pages.properties.particles.create._contact_and_agent')

                    {{-- Детальная информация --}}
                    <h3 class="create-filter-title">
                        <span>Подробно</span>
                    </h3>
                    <div class="create-filter-row">

                        {{-- ================================================================== --}}
                        {{-- ГРУППА 1: Тип сделки и комплекс --}}
                        {{-- ================================================================== --}}

                        {{-- Тип сделки --}}
                        <div class="item selects blue-select2 ">
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
                        <div class="item selects ">
                            <label class="item-label" for="deal_kind_id">Вид сделки<span class="text-danger">*</span></label>
                            <select id="deal_kind_id" name="deal_kind_id" class="js-example-responsive3 my-select2" required>
                                <option value=""></option>
                                @foreach($dealKinds as $dealKind)
                                    <option value="{{ $dealKind->id }}" {{ old('deal_kind_id') == $dealKind->id ? 'selected' : '' }}>
                                        {{ $dealKind->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Тип здания --}}
                        <div class="item selects ">
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

                        {{-- Комплекс --}}
                        <div class="item selects ">
                            <label class="item-label" for="complex_id">Комплекс</label>
                            <select id="complex_id" name="complex_id" class="form-control" autocomplete="off">
                                <option value=""></option>
                            </select>
                        </div>

                        {{-- Секция / Корпус (Блок) --}}
                        <div class="item selects  w33">
                            <label class="item-label" for="block_id">Секция / Корпус</label>
                            <select id="block_id" name="block_id" class="form-control" autocomplete="off" disabled>
                                <option value=""></option>
                            </select>
                        </div>



                        {{-- ================================================================== --}}
                        {{-- ГРУППА 3: Локация --}}
                        {{-- ================================================================== --}}

                        @include('pages.properties.particles.create._location_block')



                        {{-- ================================================================== --}}
                        {{-- ГРУППА 2: Тип недвижимости и характеристики --}}
                        {{-- ================================================================== --}}

                        {{-- Тип недвижимости --}}
                        <div class="item selects ">
                            <label class="item-label" for="property_type_id">Тип недвижимости <span class="text-danger">*</span></label>
                            <select id="property_type_id" name="property_type_id" class="js-example-responsive3 my-select2" required>
                                <option value=""></option>
                                @foreach($propertyTypes as $propertyType)
                                    <option value="{{ $propertyType->id }}" {{ old('property_type_id') == $propertyType->id ? 'selected' : '' }}>
                                        {{ $propertyType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        {{-- Количество комнат --}}
                        <div class="item selects ">
                            <label class="item-label" for="room_count_id">Кол-во комнат</label>
                            <select id="room_count_id" name="room_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($roomCounts as $roomCount)
                                    <option value="{{ $roomCount->id }}" {{ old('room_count_id') == $roomCount->id ? 'selected' : '' }}>
                                        {{ $roomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Количество ванных --}}
                        <div class="item selects ">
                            <label class="item-label" for="bathroom_count_id">Кол-во ванных комнат</label>
                            <select id="bathroom_count_id" name="bathroom_count_id" class="js-example-responsive3 my-select2">
                                <option value=""></option>
                                @foreach($bathroomCounts as $bathroomCount)
                                    <option value="{{ $bathroomCount->id }}" {{ old('bathroom_count_id') == $bathroomCount->id ? 'selected' : '' }}>
                                        {{ $bathroomCount->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Состояние --}}
                        <div class="item selects ">
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



                        {{-- ================================================================== --}}
                        {{-- ГРУППА 4: Площади --}}
                        {{-- ================================================================== --}}

                        {{-- Площади --}}
                        <div class="item ">
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
                        <div class="item ">
                            <label for="area_land">Площадь участка</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="area_land" name="area_land" type="text"
                                       value="{{ old('area_land') }}" autocomplete="off" placeholder="Сотки">
                            </div>
                        </div>

                        {{-- ================================================================== --}}
                        {{-- ГРУППА 5: Этажи и конструкция --}}
                        {{-- ================================================================== --}}

                        {{-- Этажи --}}
                        <div class="item ">
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

                        {{-- Высота потолка --}}
                        <div class="item selects ">
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
                        <div class="item selects ">
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

                        {{-- Отопление --}}
                        <div class="item selects ">
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
                        <div class="item selects ">
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

                        {{-- ================================================================== --}}
                        {{-- ГРУППА 6: Цена и финансы --}}
                        {{-- ================================================================== --}}

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
                        <div class="item ">
                            <label class="green" for="price">Цена на объект<span class="text-danger">*</span></label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="price" name="price" type="text"
                                       value="{{ old('price') }}" autocomplete="off" placeholder="" required>
                            </div>
                            @error('price')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Комиссия --}}
                        <div class="item ">
                            <label for="commission">Комиссия от владельца</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="commission" name="commission" type="text"
                                       value="{{ old('commission') }}" autocomplete="off" placeholder="">
                            </div>
                        </div>




                        {{-- ================================================================== --}}
                        {{-- ГРУППА 7: Особености --}}
                        {{-- ================================================================== --}}

                        {{-- Источник --}}
                        <div class="item selects ">
                            <label class="item-label" for="source_id">Источник <span class="text-danger">*</span></label>
                            <select id="source_id" name="source_id" class="js-example-responsive2 my-select2" required>
                                <option value=""></option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Видео YouTube --}}
                        <div class="item ">
                            <label for="youtube_url">Видео YouTube</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="youtube_url" name="youtube_url" type="url"
                                       value="{{ old('youtube_url') }}" autocomplete="off" placeholder="https://youtube.com/...">
                            </div>
                        </div>


                        {{-- Тип контакта --}}
                        <div class="item selects">
                            <label class="item-label" for="contact_type_id">Тип контакта <span class="text-danger">*</span></label>
                            <select id="contact_type_id" name="contact_type_id" class="js-example-responsive3 my-select2" required>
                                <option value=""></option>
                                @foreach($contactTypes as $contactType)
                                    <option value="{{ $contactType->id }}" {{ old('contact_type_id') == $contactType->id ? 'selected' : '' }}>
                                        {{ $contactType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        {{-- Особенности --}}
                        @include('pages.properties.particles.create._features_block')


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
                    <div class="loading-documents document">
                        <label for="document">
                            <input type="file" id="document" name="documents[]" multiple
                                   accept="image/png, image/jpeg, application/pdf">
                            <span>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
							    <path
                                        d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                        fill="#3585F5"/>
							</svg>
							<span class="text">
								Загрузить документы
							</span>
						</span>
                        </label>
                        <div class="filter-tags" data-render-document></div>
                        <div class="error-container" data-error></div>
                    </div>
                </div>

                {{-- Фото объекта --}}
                <div class="create-filter-photo">
                    <div class="photo-info">
                        <div class="photo-info-left">
                            <span class="photo-info-left-title">Фото объекта</span>
                            <div class="photo-info-list-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="loading-photo">
                                            <input type="file" id="loading-photo" name="loading-photo" multiple accept="image/png, image/jpg, image/jpeg, image/heic">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z" fill="#3585F5" />
                                            </svg>
                                            <span>
											Загрузить фото
										</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                            <p class="photo-info-left-text">
                                Первое фото будет обложкой объявления, перетяните фотографии чтобы, поменять порядок
                            </p>
                        </div>
                    </div>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/page-home-table.min.js') }}"></script>
    <script src="{{ asset('js/pages/filter1.min.js') }}"></script>
    <script src="{{ asset('js/pages/full-filter.min.js') }}"></script>
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>




    {{-- Основной скрипт страницы --}}
    <script src="{{ asset('js/pages/properties/create/page-create.js') }}" type="module"></script>

    {{-- Интеграция PhotoLoader с формой --}}
    <script src="{{ asset('js/pages/properties/create/form-submit.js') }}"></script>


    {{-- Модуль контактов (порядок важен!) --}}
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/config.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/utils.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/components.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/api.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/form.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/contact-list.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/handlers.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/main.js') }}"></script>



{{--    LOcation SEARCH--}}
    <script src="{{ asset('js/pages/properties/create/complex-block.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/location-search.js') }}" type="module"></script>
{{--ТЕГИ И ОСОБЕННОСТИ--}}
    <script src="{{ asset('js/pages/properties/create/features-tags.js') }}"></script>

{{--    Маски для цыфровых полей--}}
    <script src="{{ asset('js/pages/properties/create/number-mask.js') }}"></script>

{{--АВТОЗАПОЛНЕНИЕ ТИПА ЗДАНИЯ--}}
    <script src="{{ asset('js/pages/properties/create/building-type-autofill.js') }}"></script>


    {{---- Плагин по автосохранению форм ----}}
    <script src="{{ asset('js/pages/properties/create/form-autosave.js') }}"></script>
    {{--Плагин по редактированию изображений--}}
    <!-- Спочатку залежності -->
    <script src="{{ asset('js/lib/tui-code-snippet.min.js') }}"></script>
    <script src="{{ asset('js/lib/fabric.min.js') }}"></script>
    <script src="{{ asset('js/lib/tui-color-picker.min.js') }}"></script>
    <!-- Потім основний редактор -->
    <script src="{{ asset('js/lib/tui-image-editor.min.js') }}"></script>
    <script src="{{ asset('js/lib/heic2any.min.js') }}"></script>




@endpush
