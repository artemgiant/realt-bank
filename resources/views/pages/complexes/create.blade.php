@extends('layouts.crm')

@section('title', 'Создание объекта - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/complexes/create/page-create-complex.css') }}">


    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/location-search.css') }}">

    {{--Плагин по редактированию изображений--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tui-image-editor/3.15.0/tui-image-editor.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tui-color-picker/2.2.6/tui-color-picker.min.css">

    {{---- Плагин по автосохранению форм ----}}
    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/field-widths.css') }}">
@endpush

@section('header')
    <div class="create-header">
        <div class="create-header-left">
            <a class="create-header-back" href="{{ route('complexes.index') }}">
                <picture><source srcset="{{ asset('img/icon/arrow-back-link.svg') }}" type="image/webp"><img src="{{ asset('img/icon/arrow-back-link.svg') }}" alt=""></picture>
            </a>
            <h2 class="create-header-title">
                Новый комплекс
            </h2>
        </div>
    </div>
@endsection


@section('content')

    <form action="{{ route('complexes.store') }}" method="POST" enctype="multipart/form-data" id="complex-form">
        @csrf

    <div class="create">

        {{-- Сообщения об успехе/ошибке/валидации --}}
        <x-alerts />



        <div class="create-filter">
            <h3 class="create-filter-title">
                <span>Общая информация</span>
            </h3>
            <div class="create-filter-wrapper">
                <div class="create-filter-left">
                    <!-- Перша група табів -->
                    <div class="item">
                        <div class="tab-the-name">
                            <ul class="nav nav-tabs" id="tab-the-name" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ua-tab" data-bs-toggle="tab"
                                            data-bs-target="#ua-tab-pane" type="button" role="tab"
                                            aria-controls="ua-tab-pane" aria-selected="false">UA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ru-tab" data-bs-toggle="tab"
                                            data-bs-target="#ru-tab-pane" type="button" role="tab"
                                            aria-controls="ru-tab-pane" aria-selected="true">RU
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="en-tab" data-bs-toggle="tab"
                                            data-bs-target="#en-tab-pane" type="button" role="tab"
                                            aria-controls="en-tab-pane" aria-selected="false">EN
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade" id="ua-tab-pane" role="tabpanel"
                                     aria-labelledby="ua-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-complex-ua">Назва комплексу</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ua" id="name-complex-ua" autocomplete="off"
                                                       name="name_ua"
                                                       value="{{ old('name_ua') }}"
                                                       placeholder="Назва">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane" role="tabpanel"
                                     aria-labelledby="ru-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-complex-ru">Название комплекса</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ru" id="name-complex-ru" autocomplete="off"
                                                       name="name_ru"
                                                       value="{{ old('name_ru') }}"
                                                       placeholder="Название">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane" role="tabpanel"
                                     aria-labelledby="en-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-complex-en">Name of the complex</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="en" id="name-complex-en" autocomplete="off"
                                                       name="name_en"
                                                       value="{{ old('name_en') }}"
                                                       placeholder="The name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Друга група табів -->
                    <div class="item">
                        <div class="tab-the-name">
                            <ul class="nav nav-tabs" id="tab-about-developer" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ua-tab-about-developer" data-bs-toggle="tab"
                                            data-bs-target="#ua-tab-pane-about-developer" type="button" role="tab"
                                            aria-controls="ua-tab-pane-about-developer" aria-selected="false">UA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ru-tab-about-developer" data-bs-toggle="tab"
                                            data-bs-target="#ru-tab-pane-about-developer" type="button" role="tab"
                                            aria-controls="ru-tab-pane-about-developer" aria-selected="true">RU
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="en-tab-about-developer" data-bs-toggle="tab"
                                            data-bs-target="#en-tab-pane-about-developer" type="button" role="tab"
                                            aria-controls="en-tab-pane-about-developer" aria-selected="false">EN
                                    </button>
                                </li>

                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade" id="ua-tab-pane-about-developer" role="tabpanel"
                                     aria-labelledby="ua-tab-about-developer" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label for="description-complex-ua">Опис комплексу</label>
                                            <div class="item-inputText-wrapper">
												<textarea class="item-textareaText"
                                                          data-input-lang="ua" id="description-complex-ua"
                                                          autocomplete="off"
                                                          name="description_ua"
                                                          placeholder="Введіть текст">{{ old('description_ua') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane-about-developer" role="tabpanel"
                                     aria-labelledby="ru-tab-about-developer" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label for="description-complex-ru">Описание комплекса</label>
                                            <div class="item-inputText-wrapper">
												<textarea class="item-textareaText"
                                                          data-input-lang="ru" id="description-complex-ru"
                                                          autocomplete="off"
                                                          name="description_ru"
                                                          placeholder="Введите текст">{{ old('description_ru') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane-about-developer" role="tabpanel"
                                     aria-labelledby="en-tab-about-developer" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label for="description-complex-en">Description of the complex</label>
                                            <div class="item-inputText-wrapper">
												<textarea class="item-textareaText"
                                                          data-input-lang="en" id="description-complex-en"
                                                          autocomplete="off"
                                                          name="description_en"
                                                          placeholder="Enter text">{{ old('description_en') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="create-filter-right">

                    {{-- Контейнер для списка контактов --}}
                    <div id="contacts-list-container">
                        {{-- Контакты будут добавляться через JS --}}
                    </div>

                    {{-- Блок добавления контакта (показывается если нет контактов) --}}
                    <div class="item">
                        <ul class="block-info" id="add-contact-block">
                            <li class="block-info-item">
                                <div class="info-title-wrapper">
                                    <h2 class="info-title">Контакт</h2>
                                    <button class="btn btn-add-client" type="button" data-bs-toggle="modal"
                                            data-bs-target="#add-contact-modal">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"
                                                    fill="#AAAAAA"></path>
                                            <path
                                                    d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z"
                                                    fill="#AAAAAA"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="info-avatar">
                                    <picture>
                                        <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}"
                                                type="image/webp">
                                        <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar">
                                    </picture>
                                </div>
                                <div class="info-contacts">
                                    <p class="info-contacts-name text-muted">Контакт не выбран</p>
                                    <p class="info-description text-muted">Нажмите + чтобы добавить</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- Кнопка добавления еще одного контакта (показывается когда есть хотя бы один) --}}
                    <div id="add-more-contact-btn" class="d-none mb-3">
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal"
                                data-bs-target="#add-contact-modal">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" class="me-1">
                                <path
                                        d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"
                                        fill="currentColor"></path>
                                <path
                                        d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z"
                                        fill="currentColor"></path>
                            </svg>
                            Добавить контакт
                        </button>
                    </div>




                    <div class="loading-documents loading-logo">
                        <label for="logo">
                            <input type="file" id="logo" name="logo"
                                   accept="image/png, image/jpeg, image/webp">
                            <span>
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
									<path
                                            d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                            fill="#3585F5"/>
								</svg>
								<span class="text">
									Загрузить лого
								</span>
							</span>
                        </label>
                        <div class="filter-tags" data-render-document></div>
                        <div class="error-container"></div>
                    </div>
                </div>


                <div class="create-filter-row">
                    <div class="item">
                        <label class="item-label" for="developer_id">Девелопер</label>
                        <select id="developer_id" name="developer_id" class="js-example-responsive3 my-select2" autocomplete="off">
                            <option value="">Выберите девелопера</option>
                        </select>
                    </div>
                    <div class="item">
                        <label for="website">Сайт комплекса</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="url" id="website" name="website"
                                   value="{{ old('website') }}"
                                   autocomplete="off" placeholder="https://linkname.com">
                        </div>
                    </div>
                    <div class="item w50">
                        <label for="agent_notes">Примечание для агентов</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="text" id="agent_notes" name="agent_notes"
                                   value="{{ old('agent_notes') }}"
                                   autocomplete="off" placeholder="Введите текст">
                        </div>
                    </div>
                    <div class="item">
                        <label for="company_website">Сайт компании</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="url" id="company_website" name="company_website"
                                   value="{{ old('company_website') }}"
                                   autocomplete="off" placeholder="https://linkname.com">
                        </div>
                    </div>
                    <div class="item">
                        <label for="materials_url">Материалы девелопера</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="url" id="materials_url" name="materials_url"
                                   value="{{ old('materials_url') }}"
                                   autocomplete="off" placeholder="https://linkname.com">
                        </div>
                    </div>
                    <div class="item w50">
                        <label for="special_conditions">Специальные условия (Акции и скидки для сайта)</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="text" id="special_conditions" name="special_conditions"
                                   value="{{ old('special_conditions') }}"
                                   autocomplete="off" placeholder="Введите текст">
                        </div>
                    </div>
                </div>
            </div>
            <div class="create-filter-documents">


                <div class="loading-plan">
                    <label for="plans">
                        <input type="file" id="plans" name="plans[]" multiple
                               accept="image/png, image/jpeg, image/webp">
                        <span>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
							    <path
                                        d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z"
                                        fill="#3585F5"/>
							</svg>
							<span class="text">
								Загрузить план
							</span>
						</span>
                    </label>
                    <div class="filter-tags" data-render-document></div>
                    <div class="error-container" data-error></div>
                </div>
            </div>
            <div class="create-filter-photo">
                <div class="photo-info">
                    <div class="photo-info-wrapper">
                        <span class="photo-info-title">Фото комплекса (первое фото будет обложкой объявления, перетяните фотографии чтобы поменять порядок)</span>
                        <div class="photo-info-wrapper-wrapper">
                            <ul class="photo-info-list">
                                <li class="photo-info-btn-wrapper">
                                    <label class="photo-info-btn" for="photos">
                                        <input type="file" id="photos" name="photos[]" multiple
                                               accept="image/png, image/jpg, image/jpeg, image/heic, image/webp">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z"
                                                    fill="#3585F5"/>
                                        </svg>
                                        <span>
									Загрузить фото
								</span>
                                    </label>
                                </li>
                            </ul>
                            <div class="error-container"></div>
                        </div>
                    </div>
                </div>
                <div class="create-filter-row">


                    {{-- ================================================================== --}}
                    {{-- ГРУППА 3: Локация --}}
                    {{-- ================================================================== --}}
                    @include('pages.complexes.particles.create._location_block')


                    <div class="item w15">
                        <span class="item-label">Класс жилья</span>
                        <div class="multiple-menu" id="housing-classes-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Выберите
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($housingClasses as $housingClass)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="housing_classes[]" value="{{ $housingClass->id }}"
                                                    {{ in_array($housingClass->id, old('housing_classes', [])) ? 'checked' : '' }}>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $housingClass->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>



                    <div class="item w15">
                        <span class="item-label">Категория</span>
                        <div class="multiple-menu" id="categories-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Выберите
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($complexCategories as $category)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $category->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="item w15">
                        <span class="item-label">Типы объектов</span>
                        <div class="multiple-menu" id="object-types-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Выберите
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($objectTypes as $objectType)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="object_types[]" value="{{ $objectType->id }}"
                                                        {{ in_array($objectType->id, old('object_types', [])) ? 'checked' : '' }}>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $objectType->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="create-filter-row">



                    <div class="item w15">
                        <label class="item-label" for="objects_count">Количество объектов</label>
                        <div class="item-inputText-wrapper">
                            <input class="item-inputText" type="number" id="objects_count" name="objects_count"
                                   value="{{ old('objects_count') }}"
                                   autocomplete="off" placeholder="0" min="0">
                        </div>
                    </div>

                    <div class="item w15">
                        <span class="item-label">Состояние</span>
                        <div class="multiple-menu" id="conditions-menu">
                            <button class="multiple-menu-btn" type="button" data-open-menu="false">
                                Выберите
                            </button>
                            <div class="multiple-menu-wrapper">
                                <ul class="multiple-menu-list">
                                    @foreach($conditions as $condition)
                                        <li class="multiple-menu-item">
                                            <label class="my-custom-input">
                                                <input type="checkbox" name="conditions[]" value="{{ $condition->id }}"
                                                    {{ in_array($condition->id, old('conditions', [])) ? 'checked' : '' }}>
                                                <span class="my-custom-box"></span>
                                                <span class="my-custom-text">{{ $condition->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="item w15">
                        <label class="item-label" for="area_from">Площадь общая</label>
                        <div class="item-inputText-wrapper shtrih">
                            <input class="item-inputText" id="area_from" name="area_from" type="number" step="0.01"
                                   value="{{ old('area_from') }}" placeholder="От" autocomplete="off" min="0">
                            <input class="item-inputText" id="area_to" name="area_to" type="number" step="0.01"
                                   value="{{ old('area_to') }}" placeholder="До" autocomplete="off" min="0">
                        </div>
                    </div>
                    <div class="item w15">
						<span>
							<label class="item-label" for="price_per_m2">Цена от за м²</label>
							/
							<label class="item-label" for="price_total">Объект</label>
						</span>
                        <div class="item-action-wrapper">
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" type="number" step="0.01" id="price_per_m2" name="price_per_m2"
                                       value="{{ old('price_per_m2') }}"
                                       autocomplete="off" placeholder="м²" min="0">
                            </div>
                            /
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" type="number" step="0.01" id="price_total" name="price_total"
                                       value="{{ old('price_total') }}"
                                       autocomplete="off" placeholder="100 000 000" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="item w10">
                        <label class="item-label" for="currency">Валюта</label>
                        <select id="currency" name="currency" class="js-example-responsive2" autocomplete="off">
                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="UAH" {{ old('currency') == 'UAH' ? 'selected' : '' }}>UAH</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                    </div>

                    {{-- Особенности --}}
                    @include('pages.complexes.particles.create._features_block')

                </div>
                {{-- Контейнер для отображения выбранных особенностей (тегов) --}}
                <div class="create-filter-tags">
                    <div class="filter-tags" id="applied-filters">
                        {{-- Теги будут добавляться динамически через JS --}}
                    </div>
                </div>


                <div class="create-filter-locations">
                    <h5 class="mb-3">Корпуса / Секции </h5>

                    <ul class="create-filter-locations-list" id="blocks-list">
                        {{-- Первый блок (индекс 0) --}}
                        <li class="create-filter-locations-item block-item" data-block-index="0">
                            <div class="item w15">
                                <label class="green" for="blocks-0-name">Корпус / Секция</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="text" id="blocks-0-name" name="blocks[0][name]"
                                           value="{{ old('blocks.0.name') }}"
                                           autocomplete="off" placeholder="Введите название">
                                </div>
                                <div class="add_new-tel">
                                    <button type="button" class="btn btn-new-tel btn-remove-block" title="Удалить секцию" style="display: none;">
                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#ff4444" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="item w15">
                                <label class="item-label" for="blocks-0-street_id">Улица</label>
                                <select id="blocks-0-street_id" name="blocks[0][street_id]" class="js-example-responsive3 my-select2 block-street-select" autocomplete="off">
                                    <option value="">Выберите улицу</option>
                                </select>
                            </div>
                            <div class="item w7-5">
                                <label for="blocks-0-building_number">№ Дом</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="text" id="blocks-0-building_number" name="blocks[0][building_number]"
                                           value="{{ old('blocks.0.building_number') }}"
                                           autocomplete="off" placeholder="Номер">
                                </div>
                            </div>
                            <div class="item w7-5">
                                <label for="blocks-0-floors_total">Этажность</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" type="number" id="blocks-0-floors_total" name="blocks[0][floors_total]"
                                           value="{{ old('blocks.0.floors_total') }}"
                                           autocomplete="off" placeholder="Кол-во" min="1" max="200">
                                </div>
                            </div>
                            <div class="item w10">
                                <label class="item-label" for="blocks-0-year_built">Год сдачи</label>
                                <select id="blocks-0-year_built" name="blocks[0][year_built]" class="js-example-responsive3 my-select2" autocomplete="off">
                                    <option value="">Выберите</option>
                                    @foreach($yearsBuilt as $year)
                                        <option value="{{ $year->value }}" {{ old('blocks.0.year_built') == $year->value ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item w15">
                                <label class="item-label" for="blocks-0-heating_type_id">Отопление</label>
                                <select id="blocks-0-heating_type_id" name="blocks[0][heating_type_id]" class="js-example-responsive3 my-select2" autocomplete="off">
                                    <option value="">Выберите</option>
                                    @foreach($heatingTypes as $heating)
                                        <option value="{{ $heating->id }}" {{ old('blocks.0.heating_type_id') == $heating->id ? 'selected' : '' }}>
                                            {{ $heating->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item w15">
                                <label class="item-label" for="blocks-0-wall_type_id">Тип стен</label>
                                <select id="blocks-0-wall_type_id" name="blocks[0][wall_type_id]" class="js-example-responsive3 my-select2" autocomplete="off">
                                    <option value="">Выберите</option>
                                    @foreach($wallTypes as $wall)
                                        <option value="{{ $wall->id }}" {{ old('blocks.0.wall_type_id') == $wall->id ? 'selected' : '' }}>
                                            {{ $wall->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item w10">
                                <div class="loading-plan" data-plan-id="plan-0">
                                    <label for="blocks-0-plan">
                                        <input type="file" id="blocks-0-plan" name="blocks[0][plan]"
                                               accept="image/png, image/jpeg, image/webp">
                                        <span>
											<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z" fill="#3585F5"/>
											</svg>
											<span class="text">План</span>
										</span>
                                    </label>
                                </div>
                            </div>
                            <div class="item w100" data-plan-id="plan-file-0">
                                <div class="filter-tags" data-render-document></div>
                                <div class="error-container" data-error></div>
                            </div>
                        </li>
                    </ul>

                    {{-- Кнопка добавления новой секции --}}
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-block-btn">
                            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                                <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="currentColor" />
                                <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="currentColor" />
                            </svg>
                            Добавить
                        </button>
                    </div>
                </div>

                {{-- Template для новых секций --}}
                <template id="block-template">
                    <li class="create-filter-locations-item block-item" data-block-index="__INDEX__">
                        <div class="item w15">
                            <label class="green" for="blocks-__INDEX__-name">Корпус / Секция </label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" type="text" id="blocks-__INDEX__-name" name="blocks[__INDEX__][name]"
                                       autocomplete="off" placeholder="Введите название">
                            </div>
                            <div class="add_new-tel">
                                <button type="button" class="btn btn-new-tel btn-remove-block" title="Удалить секцию">
                                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#ff4444" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="item w15">
                            <label class="item-label" for="blocks-__INDEX__-street_id">Улица</label>
                            <select id="blocks-__INDEX__-street_id" name="blocks[__INDEX__][street_id]" class="js-example-responsive3 my-select2 block-street-select" autocomplete="off">
                                <option value="">Выберите улицу</option>
                            </select>
                        </div>
                        <div class="item w7-5">
                            <label for="blocks-__INDEX__-building_number">№ Дом</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" type="text" id="blocks-__INDEX__-building_number" name="blocks[__INDEX__][building_number]"
                                       autocomplete="off" placeholder="Номер">
                            </div>
                        </div>
                        <div class="item w7-5">
                            <label for="blocks-__INDEX__-floors_total">Этажность</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" type="number" id="blocks-__INDEX__-floors_total" name="blocks[__INDEX__][floors_total]"
                                       autocomplete="off" placeholder="Кол-во" min="1" max="200">
                            </div>
                        </div>
                        <div class="item w10">
                            <label class="item-label" for="blocks-__INDEX__-year_built">Год сдачи</label>
                            <select id="blocks-__INDEX__-year_built" name="blocks[__INDEX__][year_built]" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option value="">Выберите</option>
                                @foreach($yearsBuilt as $year)
                                    <option value="{{ $year->value }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item w15">
                            <label class="item-label" for="blocks-__INDEX__-heating_type_id">Отопление</label>
                            <select id="blocks-__INDEX__-heating_type_id" name="blocks[__INDEX__][heating_type_id]" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option value="">Выберите</option>
                                @foreach($heatingTypes as $heating)
                                    <option value="{{ $heating->id }}">{{ $heating->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item w15">
                            <label class="item-label" for="blocks-__INDEX__-wall_type_id">Тип стен</label>
                            <select id="blocks-__INDEX__-wall_type_id" name="blocks[__INDEX__][wall_type_id]" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option value="">Выберите</option>
                                @foreach($wallTypes as $wall)
                                    <option value="{{ $wall->id }}">{{ $wall->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item w10">
                            <div class="loading-plan" data-plan-id="plan-__INDEX__">
                                <label for="blocks-__INDEX__-plan">
                                    <input type="file" id="blocks-__INDEX__-plan" name="blocks[__INDEX__][plan]"
                                           accept="image/png, image/jpeg, image/webp">
                                    <span>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z" fill="#3585F5"/>
                                        </svg>
                                        <span class="text">План</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="item w100" data-plan-id="plan-file-__INDEX__">
                            <div class="filter-tags" data-render-document></div>
                            <div class="error-container" data-error></div>
                        </div>
                    </li>
                </template>
                <div class="create-btnGroup">
                    <div class="create-btnGroup-wrapper">
                        <div class="create-btnGroup-left">
                            <a href="{{ route('complexes.index') }}" class="btn btn-outline-primary">
                                Отменить
                            </a>
                        </div>
                        <div class="create-btnGroup-right">
                            <button class="btn btn-primary" type="submit">
                                Создать комплекс
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
@endsection
<!-- кінець main	-->




{{-- Модальное окно добавления контакта --}}
@include('pages.developers.modals.contact-modal')

{{-- Template карточки контакта --}}
<template id="contact-card-template">
    <div class="contact-card mb-3" data-contact-id="">
        <input type="hidden" name="contact_ids[]" class="contact-id-input" value="">
        <ul class="block-info">
            <li class="block-info-item">
                <div class="info-title-wrapper">
                    <h2 class="info-title">Контакт</h2>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-link p-0 me-2" type="button" data-edit-contact data-bs-toggle="modal" data-bs-target="#add-contact-modal" title="Редактировать">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 11.0833V14H2.91667L11.5173 5.39933L8.60067 2.48267L0 11.0833ZM13.7173 3.19933C14.0173 2.89933 14.0173 2.42267 13.7173 2.12267L11.8773 0.282667C11.5773 -0.0173333 11.1007 -0.0173333 10.8007 0.282667L9.37067 1.71267L12.2873 4.62933L13.7173 3.19933Z" fill="#3585F5"/>
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-link p-0 text-danger" type="button" data-remove-contact title="Удалить">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="currentColor"/>
                                <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="info-avatar">
                    <picture>
                        <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar" class="contact-avatar">
                    </picture>
                </div>
                <div class="info-contacts">
                    <p class="info-contacts-name contact-name">-</p>
                    <p class="info-description contact-type">-</p>
                    <a href="#" class="contact-phone">-</a>
                </div>
                <div class="info-messengers contact-messengers">
                    {{-- Мессенджеры будут добавлены через JS --}}
                </div>
            </li>
        </ul>
    </div>
</template>


@push('scripts')
    <!-- Спочатку залежності -->
    <script src="{{ asset('js/lib/tui-code-snippet.min.js') }}"></script>
    <script src="{{ asset('js/lib/fabric.min.js') }}"></script>
    <script src="{{ asset('js/lib/tui-color-picker.min.js') }}"></script>
    <!-- Потім основний редактор -->
    <script src="{{ asset('js/lib/tui-image-editor.min.js') }}"></script>
    <script src="{{ asset('js/lib/heic2any.min.js') }}"></script>




    {{-- Модуль контактов (порядок важен!) --}}
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/config.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/utils.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/components.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/api.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/form.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/contact-list.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/handlers.js') }}"></script>
    <script src="{{ asset('js/pages/properties/create/modal/add-contact/main.js') }}"></script>



    <script src="{{ asset('js/pages/complexes/create/function_on_pages-create.js') }}" type="module"></script>
    <script src="{{ asset('js/pages/complexes/create/page-create-complex.js') }}" type="module"></script>
    <!--<script src="./js/pages/full-filter.min.js"></script>-->


    {{--    LOcation SEARCH--}}
    <script src="{{ asset('js/pages/properties/create/location-search.js') }}" type="module"></script>



    {{--ТЕГИ И ОСОБЕННОСТИ--}}
    <script src="{{ asset('js/pages/properties/create/features-tags.js') }}" defer></script>

@endpush
