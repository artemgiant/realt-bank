@extends('layouts.crm')


@section('title', 'Создание девелопера - Realt Bank')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/developers/page-create-developers.css') }}">
@endpush

@section('header')
    <div class="create-header">
        <div class="create-header-left">
            <a class="create-header-back" href="{{ route('developers.index') }}">
                <picture>
                    <source srcset="{{ asset('img/icon/arrow-back-link.svg') }}" type="image/webp">
                    <img src="{{ asset('img/icon/arrow-back-link.svg') }}" alt="Back">
                </picture>
            </a>
            <h2 class="create-header-title">
                Девелопер
                <span id="developer-id">Новый девелопер</span>
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

        <form id="developer-form" action="{{ route('developers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="create-filter">
                <h3 class="create-filter-title">
                    <span>Общая информация</span>
                </h3>

                <div class="create-filter-wrapper">
                    <div class="create-filter-left">
                        {{-- Название компании (мультиязычное) --}}
                        <div class="item">
                            <div class="tab-the-name">
                                <ul class="nav nav-tabs" id="tab-the-name" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ua-tab-name" data-bs-toggle="tab"
                                                data-bs-target="#ua-tab-pane-name" type="button" role="tab"
                                                aria-controls="ua-tab-pane-name" aria-selected="false">UA
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="ru-tab-name" data-bs-toggle="tab"
                                                data-bs-target="#ru-tab-pane-name" type="button" role="tab"
                                                aria-controls="ru-tab-pane-name" aria-selected="true">RU
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="en-tab-name" data-bs-toggle="tab"
                                                data-bs-target="#en-tab-pane-name" type="button" role="tab"
                                                aria-controls="en-tab-pane-name" aria-selected="false">EN
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-name" role="tabpanel"
                                         aria-labelledby="ua-tab-name" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name_ua">Назва</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="ua" id="name_ua" name="name_ua"
                                                           autocomplete="off"
                                                           value="{{ old('name_ua') }}"
                                                           placeholder="Назва компанії">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-name" role="tabpanel"
                                         aria-labelledby="ru-tab-name" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name_ru">Название</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="ru" id="name_ru" name="name_ru"
                                                           autocomplete="off"
                                                           value="{{ old('name_ru') }}"
                                                           placeholder="Название компании">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="en-tab-pane-name" role="tabpanel"
                                         aria-labelledby="en-tab-name" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name_en">The name</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText" type="text"
                                                           data-input-lang="en" id="name_en" name="name_en"
                                                           autocomplete="off"
                                                           value="{{ old('name_en') }}"
                                                           placeholder="The name of the company">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- О девелопере (мультиязычное) --}}
                        <div class="item">
                            <div class="tab-the-name">
                                <ul class="nav nav-tabs" id="tab-about-developer" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ua-tab-about" data-bs-toggle="tab"
                                                data-bs-target="#ua-tab-pane-about" type="button" role="tab"
                                                aria-controls="ua-tab-pane-about" aria-selected="false">UA
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="ru-tab-about" data-bs-toggle="tab"
                                                data-bs-target="#ru-tab-pane-about" type="button" role="tab"
                                                aria-controls="ru-tab-pane-about" aria-selected="true">RU
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="en-tab-about" data-bs-toggle="tab"
                                                data-bs-target="#en-tab-pane-about" type="button" role="tab"
                                                aria-controls="en-tab-pane-about" aria-selected="false">EN
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-about" role="tabpanel"
                                         aria-labelledby="ua-tab-about" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ua">Про девелопера</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText"
                                                              data-input-lang="ua" id="description_ua" name="description_ua"
                                                              autocomplete="off"
                                                              placeholder="Введіть текст">{{ old('description_ua') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-about" role="tabpanel"
                                         aria-labelledby="ru-tab-about" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_ru">О девелопере</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText"
                                                              data-input-lang="ru" id="description_ru" name="description_ru"
                                                              autocomplete="off"
                                                              placeholder="Введите текст">{{ old('description_ru') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="en-tab-pane-about" role="tabpanel"
                                         aria-labelledby="en-tab-about" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label for="description_en">About the developer</label>
                                                <div class="item-inputText-wrapper">
                                                    <textarea class="item-textareaText"
                                                              data-input-lang="en" id="description_en" name="description_en"
                                                              autocomplete="off"
                                                              placeholder="Enter text">{{ old('description_en') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Год основания и материалы --}}
                        <div class="item-wrappers">
                            <div class="item">
                                <label for="year_founded">Основана в</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="year_founded" name="year_founded" type="text"
                                           value="{{ old('year_founded') }}"
                                           autocomplete="off" placeholder="год">
                                </div>
                            </div>
                            <div class="item">
                                <label for="materials_url">Материалы девелопера</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="materials_url" name="materials_url" type="url"
                                           value="{{ old('materials_url') }}"
                                           autocomplete="off" placeholder="https://linkname.com">
                                </div>
                            </div>
                        </div>

                        {{-- Локации девелопера --}}
                        <div class="item locations-block">
                            <label>Локации</label>
                            <div class="locations-container" id="locations-container">
                                {{-- Первая локация --}}
                                <div class="location-item" data-location-index="0">
                                    <div class="location-search-wrapper">
                                        <select class="location-search" id="location-search-0" name="locations[0][location]">
                                            <option value="">Выберите страну, область или город</option>
                                        </select>
                                        <button type="button" class="btn-remove-location" onclick="removeLocation(this)" style="display: none;">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.5 3.5L3.5 12.5M3.5 3.5L12.5 12.5" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn-add-location" id="btn-add-location" onclick="addLocation()">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 3.5V12.5M3.5 8H12.5" stroke="#3585F5" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <span>Добавить локацию</span>
                            </button>
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
                                        <button class="btn btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"></path>
                                                <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"></path>
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
                                        <p class="info-contacts-name text-muted">Контакт не выбран</p>
                                        <p class="info-description text-muted">Нажмите + чтобы добавить</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        {{-- Кнопка добавления еще одного контакта (показывается когда есть хотя бы один) --}}
                        <div id="add-more-contact-btn" class="d-none mb-3">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                                    <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="currentColor"></path>
                                    <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="currentColor"></path>
                                </svg>
                                Добавить контакт
                            </button>
                        </div>

                        {{-- Загрузка логотипа --}}
                        <div class="loading-documents loading-logo">
                            <label for="logo">
                                <input type="file" id="logo" name="logo" accept="image/png, image/jpeg, image/webp">
                                <span>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.50627 13.2938C7.81303 13.9437 6.89417 14.2986 5.94403 14.2833C4.99388 14.2679 4.08694 13.8837 3.41499 13.2117C2.74305 12.5398 2.35879 11.6329 2.34348 10.6827C2.32817 9.73256 2.683 8.81371 3.33294 8.12046L9.17294 2.28713C9.52011 1.94269 9.96138 1.70858 10.4412 1.61425C10.9211 1.51991 11.4181 1.56956 11.8699 1.75695C12.3216 1.94433 12.7078 2.2611 12.98 2.6674C13.2522 3.0737 13.3982 3.55141 13.3996 4.04046C13.3992 4.36567 13.3342 4.68757 13.2083 4.98743C13.0824 5.28729 12.8982 5.55912 12.6663 5.78713L7.11294 11.3338C6.93932 11.4722 6.72095 11.5421 6.49924 11.5302C6.27753 11.5183 6.06789 11.4254 5.91008 11.2692C5.75226 11.1131 5.65723 10.9044 5.64302 10.6828C5.6288 10.4613 5.69638 10.2422 5.83294 10.0671L11.3929 4.50713L10.4463 3.56713L4.88627 9.12713C4.48502 9.55082 4.26495 10.1144 4.27289 10.6978C4.28082 11.2813 4.51614 11.8387 4.92876 12.2513C5.34138 12.6639 5.89874 12.8992 6.48222 12.9072C7.0657 12.9151 7.62925 12.695 8.05294 12.2938L13.6196 6.73379C14.3357 6.01859 14.7383 5.04822 14.739 4.03615C14.7396 3.02408 14.3381 2.05321 13.6229 1.33713C12.9077 0.621043 11.9374 0.218399 10.9253 0.217774C9.91323 0.217149 8.94236 0.618593 8.22627 1.33379L2.38627 7.18046C1.48313 8.1234 0.985245 9.38258 0.999314 10.6882C1.01338 11.9938 1.53828 13.2419 2.46154 14.1652C3.38479 15.0885 4.63295 15.6133 5.93855 15.6274C7.24416 15.6415 8.50334 15.1436 9.44627 14.2405L14.7263 8.95379L13.7863 8.00046L8.50627 13.2938Z" fill="#3585F5"/>
                                    </svg>
                                    <span class="text">Загрузить лого</span>
                                </span>
                            </label>
                            <div class="filter-tags" data-render-document id="logo-preview"></div>
                            <div class="error-container" data-error></div>
                        </div>

                        {{-- Примечание для агентов --}}
                        <div class="item">
                            <label for="agent_notes">Примечание для агентов</label>
                            <div class="item-inputText-wrapper">
                                <textarea class="item-textareaText" id="agent_notes" name="agent_notes"
                                          autocomplete="off" placeholder="Введите текст">{{ old('agent_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Кнопки действий --}}
                <div class="create-filter-photo">
                    <div class="create-btnGroup">
                        <div class="create-btnGroup-wrapper">
                            <div class="create-btnGroup-left">
                                <button class="btn btn-outline-primary" type="button" onclick="window.history.back()">
                                    Отменить изменения
                                </button>
                                <button class="btn btn-outline-danger" type="button" id="btn-delete-developer" style="display: none;">
                                    Удалить девелопера
                                </button>
                            </div>
                            <div class="create-btnGroup-right">
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

    {{-- Модальное окно добавления контакта --}}
    @include('pages.developers.modals.contact-modal')

    {{-- Шаблон для карточки контакта (используется JS) --}}
    <template id="contact-card-template">
        <ul class="block-info contact-card mb-3" data-contact-id="">
            <li class="block-info-item">
                <div class="info-title-wrapper">
                    <h2 class="info-title">Контакт</h2>
                    <button class="btn btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal" data-edit-contact>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA"></path>
                        </svg>
                    </button>
                    <button class="btn btn-remove-client" type="button" data-remove-contact>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.878207 9.87891C0.653517 9.87891 0.428717 9.79318 0.257263 9.62173C-0.0857544 9.27871 -0.0857544 8.72285 0.257263 8.37984L8.37992 0.257181C8.72294 -0.085727 9.27879 -0.085727 9.62181 0.257181C9.96472 0.600089 9.96472 1.15605 9.62181 1.49896L1.49915 9.62162C1.3277 9.79318 1.1029 9.87891 0.878207 9.87891Z" fill="#AAAAAA"/>
                            <path d="M9.00086 9.8788C8.77606 9.8788 8.55137 9.79307 8.37992 9.62162L0.257263 1.49896C-0.0857544 1.15605 -0.0857544 0.600089 0.257263 0.257181C0.600171 -0.085727 1.15613 -0.085727 1.49904 0.257181L9.6217 8.37984C9.96461 8.72285 9.96461 9.27871 9.6217 9.62173C9.45035 9.79307 9.22566 9.8788 9.00086 9.8788Z" fill="#AAAAAA"/>
                        </svg>
                    </button>
                </div>
                <div class="info-avatar">
                    <picture>
                        <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar" class="contact-avatar">
                    </picture>
                </div>
                <div class="info-contacts">
                    <p class="info-contacts-name contact-name">-</p>
                    <p class="info-description contact-type">-</p>
                    <a href="tel:" class="info-contacts-tel contact-phone">-</a>
                </div>
                <div class="info-links contact-messengers">
                    {{-- Мессенджеры будут добавлены через JS --}}
                </div>
                {{-- Hidden input для передачи ID контакта при сохранении формы --}}
                <input type="hidden" name="contact_ids[]" value="" class="contact-id-input">
            </li>
        </ul>
    </template>
@endsection

@push('scripts')
    {{-- Общие функции (PhoneInputManager, PhotoLoaderMini и т.д.) + Модуль контактов --}}
    <script type="module">
        import { PhoneInputManager, PhotoLoaderMini } from '{{ asset('js/pages/function_on_pages-create.js') }}';
        window.PhoneInputManager = PhoneInputManager;
        window.PhotoLoaderMini = PhotoLoaderMini;

        // Загружаем остальные скрипты после того как классы доступны
        const scripts = [
            '{{ asset('js/pages/properties/create/modal/add-contact/config.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/utils.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/api.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/form.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/contact-list.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/components.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/handlers.js') }}',
            '{{ asset('js/pages/properties/create/modal/add-contact/main.js') }}',
            '{{ asset('js/pages/developers/page-create-developers.js') }}'
        ];

        for (const src of scripts) {
            await new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.body.appendChild(script);
            });
        }
    </script>
@endpush
