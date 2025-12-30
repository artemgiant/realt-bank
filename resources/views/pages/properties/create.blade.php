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




                    {{-- Основная информация (Контакт и Агент) --}}

                    @include('pages.properties.particles.create._contact_and_agent')


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



                        {{-- Локация --}}
                        <div class="item w50">
                            <label>Локация</label>
                            <div class="my-dropdown">
                                <div class="my-dropdown-input-wrapper">
                                    <!-- 05.06.2025		-->
                                    <button class="my-dropdown-geo-btn" data-bs-toggle="modal" data-bs-target="#geoModal">
                                        <picture><source srcset="./img/icon/geo.svg" type="image/webp"><img src="./img/icon/geo.svg" alt=""></picture>
                                    </button>
                                    <!-- 05.06.2025		-->

                                    <label class="my-dropdown-label">
                                        <input class="my-dropdown-input" type="text" autocomplete="off" placeholder="Введите название">
                                    </label>
                                    <!--		28.03.2025 оновив-->
                                    <button class="my-dropdown-btn arrow-down" id="btn-open-menu" type="button">
                                        <picture><source srcset="./img/icon/arrow-right-white.svg" type="image/webp"><img src="./img/icon/arrow-right-white.svg" alt=""></picture>
                                    </button>
                                    <!--		28.03.2025 оновив-->
                                </div>
                                <div class="my-dropdown-list-wrapper" style="display: none">
                                    <div class="my-dropdown-list">
                                        <div class="scroller">
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-radio">
                                                    <input class="my-dropdown-item-radio" type="radio" name="country">
                                                    <span class="my-dropdown-item-radio-text">
							Україна (<span>24</span>)
						</span>
                                                </label>
                                                <div class="my-dropdown-next-list" style="display: none">
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-radio">
                                                            <input class="my-dropdown-item-radio" type="radio" name="district">
                                                            <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-radio">
                                                            <input class="my-dropdown-item-radio" type="radio" name="district">
                                                            <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-radio">
                                                    <input class="my-dropdown-item-radio" type="radio" name="country">
                                                    <span class="my-dropdown-item-radio-text">
						Великобритания (<span>24</span>)
					</span>
                                                </label>
                                                <div class="my-dropdown-next-list" style="display: none">
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-radio">
                                                            <input class="my-dropdown-item-radio" type="radio" name="district">
                                                            <span class="my-dropdown-item-radio-text">
									Дніпропетровська обл. (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-radio">
                                                            <input class="my-dropdown-item-radio" type="radio" name="district">
                                                            <span class="my-dropdown-item-radio-text">
									Одеська обл. (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-dropdown-list second" style="display: none">
                                        <div class="scroller">
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-checkbox">
                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                    <span class="my-dropdown-item-checkbox-text">
							Дніпро (<span>24</span>)
						</span>
                                                </label>
                                                <div class="my-dropdown-next-list" style="display: none">
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                        </label>
                                                        <div class="my-dropdown-next-next-list" style="display: none">
                                                            <div class="my-dropdown-item">
                                                                <label class="my-dropdown-item-label-checkbox">
                                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                    <span class="my-dropdown-item-checkbox-text">
											Лівобережний 3 (<span>24</span>)
										</span>
                                                                </label>
                                                            </div>
                                                            <div class="my-dropdown-item">
                                                                <label class="my-dropdown-item-label-checkbox">
                                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                    <span class="my-dropdown-item-checkbox-text">
											Лівобережний 2 (<span>24</span>)
										</span>
                                                                </label>
                                                            </div>
                                                            <div class="my-dropdown-item">
                                                                <label class="my-dropdown-item-label-checkbox">
                                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                                    <span class="my-dropdown-item-checkbox-text">
											Лівобережний 1 (<span>24</span>)
										</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-checkbox">
                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                    <span class="my-dropdown-item-checkbox-text">
						Одесса (<span>24</span>)
					</span>
                                                </label>
                                                <div class="my-dropdown-next-list" style="display: none">
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-checkbox">
                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                    <span class="my-dropdown-item-checkbox-text">
						Київ (<span>24</span>)
					</span>
                                                </label>
                                            </div>
                                            <div class="my-dropdown-item">
                                                <label class="my-dropdown-item-label-checkbox">
                                                    <input class="my-dropdown-item-checkbox" type="checkbox">
                                                    <span class="my-dropdown-item-checkbox-text">
						Харків (<span>24</span>)
					</span>
                                                </label>
                                                <div class="my-dropdown-next-list" style="display: none">
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									АНД район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Індустріальний район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Центральний район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Новокадацький район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                    <div class="my-dropdown-item">
                                                        <label class="my-dropdown-item-label-checkbox">
                                                            <input class="my-dropdown-item-checkbox" type="checkbox">
                                                            <span class="my-dropdown-item-checkbox-text">
									Шевченківський район (<span>24</span>)
								</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-dropdown-search-wrapper" style="display: none">
                                    <div class="my-dropdown-search-list">
                                        <div class="scroller">
                                            <div class="my-dropdown-search-item">
                                                <div class="eqweqw">
                                                    Одесская обл (24)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Улица --}}
                        <div class="item selects">
                            <label class="item-label" for="street">Улица</label>
                            <select id="street" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option value=""></option>
                                <option value="company">
                                    Тенистая
                                </option>
                            </select>
                        </div>

                        {{-- Номер дома / квартиры --}}

                        <div class="item noresize120">
						<span>
							<label class="item-label" for="number-house">№ Дом</label> /
							<label for="number-apartment">Квартира</label>
						</span>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" id="number-house" type="text" autocomplete="off">
                                <input class="item-inputText" id="number-apartment" type="text" autocomplete="off">
                            </div>
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

    {{-- Модуль контактов (порядок важен!) --}}
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/config.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/utils.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/components.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/api.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/form.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/contact-list.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/handlers.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/main.js') }}"></script>

@endpush
