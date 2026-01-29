@extends('layouts.crm')


@section('title', 'Создание  агентства')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/companies/create/page-create-company.css') }}">

    <link rel="stylesheet" href="{{ asset('css/pages/properties/create/location-search.css') }}">
@endpush

@section('header')
    <div class="create-header">
        <div class="create-header-left">
            <a class="create-header-back" href="{{ route('companies.index') }}">
                <picture>
                    <source srcset="{{ asset('img/icon/arrow-back-link.svg') }}" type="image/webp">
                    <img src="{{ asset('img/icon/arrow-back-link.svg') }}" alt="Back">
                </picture>
            </a>
            <h2 class="create-header-title">
                <span id="developer-id">Добавление агентства</span>
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
    <!-- початок main	-->
    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" id="company-form">
        @csrf

        {{-- Скрытые поля для выбранных контактов --}}
        <div id="selected-contacts-hidden">
            {{-- JS заполняет: <input type="hidden" name="contact_ids[]" value="..."> --}}
        </div>

        <div class="block">

            <div class="block-info-list">

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

            </div>

            <div class="block-all-info">
                <h3 class="block-title">
                    <span>Общая информация</span>
                </h3>
                <div class="block-row">
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
                                            <label class="green" for="name-agency-ua">Назва агенції</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ua" id="name-agency-ua" autocomplete="off"
                                                       name="name_ua"
                                                       placeholder="Назва">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane" role="tabpanel"
                                     aria-labelledby="ru-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-ru">Название агентства</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="ru" id="name-agency-ru" autocomplete="off"
                                                       name="name_ru"
                                                       placeholder="Название">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane" role="tabpanel"
                                     aria-labelledby="en-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-en">The name of the agency</label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText" type="text"
                                                       data-input-lang="en" id="name-agency-en" autocomplete="off"
                                                       name="name_en"
                                                       placeholder="The name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




{{-- #TODO LOCATION BLOCK HERE--}}
                    @include('pages/companies/particles/create/_location_block_company')

                    <div class="item">
			<span>
				<label class="item-label" for="site-agency">Сайт агентства</label>
			</span>
                        <input class="item-inputText" id="site-agency" type="text" autocomplete="off" name="website" placeholder="https://linkname.com">
                    </div>
                </div>
                <div class="block-row">

                    <div class="item">
                        <div class="item">
				<span>
					<label class="item-label" for="code-EDRPOU-TIN">КОД ЕГРПОУ/ИНН</label>
				</span>
                            <input class="item-inputText" id="code-EDRPOU-TIN" type="text" autocomplete="off"
                                   name="edrpou_code" placeholder="1234567890">
                        </div>
                        <div class="item">
				<span class="label">
					Логотип
				</span>
                            <div class="photo-info-list-wrapper">
                                <ul class="photo-info-list">
                                    <li class="photo-info-btn-wrapper">
                                        <label class="photo-info-btn" for="loading-photo">
                                            <input type="file" id="loading-photo" name="logo"
                                                   accept="image/png, image/jpg, image/jpeg, image/webp">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z"
                                                      fill="#3585F5"/>
                                            </svg>
                                            <span>
									Загрузить лого
								</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                        </div>

                        <div class="item">
				<span>
					<label class="item-label" for="type_company">Тип агенства</label>
				</span>
                            <input class="item-inputText" id="type_company" type="text" autocomplete="off"
                                   name="company_type" placeholder="Тип агентства">
                        </div>
                    </div>


                    <div class="item w75">
                        <div class="tab-the-name">
                            <ul class="nav nav-tabs" id="tab-about-agency" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ua-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#ua-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="ua-tab-pane-about-agency" aria-selected="false">UA
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ru-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#ru-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="ru-tab-pane-about-agency" aria-selected="true">RU
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="en-tab-about-agency" data-bs-toggle="tab"
                                            data-bs-target="#en-tab-pane-about-agency" type="button" role="tab"
                                            aria-controls="en-tab-pane-about-agency" aria-selected="false">EN
                                    </button>
                                </li>

                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade" id="ua-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="ua-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
							<span>
								<label for="about-agency-ua">Про агенцію</label>
							</span>
                                            <div class="item-inputText-wrapper">
								<textarea class="item-textareaText" type="text"
                                          data-input-lang="ua" id="about-agency-ua"
                                          autocomplete="off" name="description_ua"
                                          placeholder="Введіть текст"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="ru-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
								<span>
									<label for="about-agency-ru">Об агентстве</label>
								</span>
                                            <div class="item-inputText-wrapper">
									<textarea class="item-textareaText" type="text"
                                              data-input-lang="ru" id="about-agency-ru"
                                              autocomplete="off" name="description_ru"
                                              placeholder="Введите текст"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane-about-agency" role="tabpanel"
                                     aria-labelledby="en-tab-about-agency" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
							<span>
								<label for="about-agency-en">About agency</label>
							</span>
                                            <div class="item-inputText-wrapper">
								<textarea class="item-textareaText" type="text"
                                          data-input-lang="en" id="about-agency-en"
                                          autocomplete="off" name="description_en"
                                          placeholder="Enter text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block-offices">
                <h3 class="block-title">
                    <span>Офисы</span>
                </h3>
                <div class="block-offices-list">
                    <div class="block-offices-item">
                        <div class="block-row">
                            <div class="item">
                                <div class="add_new-tel">
                                    <button type="button" class="btn btn-new-tel">
                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                    d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z"
                                                    fill="#3585F5"/>
                                            <path
                                                    d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z"
                                                    fill="#3585F5"/>
                                        </svg>
                                    </button>
                                </div>
                                <span>
						<label class="item-label green" for="agency-branch">Название офиса</label>
					</span>
                                <input class="item-inputText" id="agency-branch" type="text" autocomplete="off"
                                       name="offices[0][name]" placeholder="Название">
                            </div>

                            <div class="item w75">


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


                            </div>
                        </div>
                        <div class="block-row">


                            @include('pages/companies/particles/create/_location_block_office')
                        </div>
                    </div>
                </div>
            </div>

            <div class="block-my-btns">
                <div class="block-my-btns-left">
                    <button class="btn btn-outline-primary" type="button">
                        Отменить
                    </button>
                </div>
                <div class="block-my-btns-right">
                    <button class="btn btn-primary" type="submit">
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </form>
    <!-- кінець main	-->
    @include('pages/companies/modals/create/_contact-modal')

@endsection

@push('scripts')

    <script src="./js/pages/function_on_pages-create.min.js" type="module"></script>
    <script src="./js/pages/my-dropdown.min.js"></script>
    <script src="./js/pages/page-create-agency.js" type="module"></script>
    <script src="./js/pages/add-contact-modal.min.js" type="module"></script>

@endpush



