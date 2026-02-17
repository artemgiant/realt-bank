@extends('layouts.crm')


@section('title', 'Редактирование агентства')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
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
                <span id="developer-id">Редактирование агентства #{{ $company->id }}</span>
            </h2>
        </div>
        <div class="create-header-right">
            <div class="create-header-add">
                Добавлено:
                <span id="created-at">{{ $company->created_at?->format('d.m.Y') ?? '-' }}</span>
            </div>
            <div class="create-header-update">
                Обновлено:
                <span id="updated-at">{{ $company->updated_at?->format('d.m.Y') ?? '-' }}</span>
            </div>
        </div>
    </div>
    @endsection


@section('content')
    <!-- початок main	-->

    {{-- Сообщения об успехе/ошибке/валидации --}}
    <x-alerts />

    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" id="company-form">
        @csrf
        @method('PUT')

        {{-- Скрытые поля для выбранных контактов (заполняются через JS при добавлении новых) --}}
        <div id="selected-contacts-hidden">
            {{-- JS заполняет: <input type="hidden" name="contact_ids[]" value="..."> --}}
        </div>

        <div class="block">

            <div class="block-info-list">

                {{-- Контейнер для списка контактов --}}
                <div id="contacts-list-container">
                    {{-- Предзаполняем существующими контактами --}}
                    @foreach($company->contacts as $contact)
                        <ul class="block-info contact-card" data-contact-id="{{ $contact->id }}">
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
                                        <img src="{{ $contact->avatar_url ?? asset('img/icon/default-avatar-table.svg') }}" alt="Avatar" class="contact-avatar">
                                    </picture>
                                </div>
                                <div class="info-contacts">
                                    <p class="info-contacts-name contact-name">{{ $contact->full_name }}</p>
                                    <p class="info-description contact-type">{{ $contact->roles_names ?? '-' }}</p>
                                    <a href="tel:{{ $contact->primary_phone }}" class="info-contacts-tel contact-phone">{{ $contact->primary_phone ?? '-' }}</a>
                                </div>
                                <div class="info-links contact-messengers">
                                    {{-- Мессенджеры --}}
                                </div>
                                <input type="hidden" name="contact_ids[]" value="{{ $contact->id }}" class="contact-id-input">
                            </li>
                        </ul>
                    @endforeach
                </div>

                {{-- Блок добавления контакта (показывается если нет контактов) --}}
                <div class="item {{ $company->contacts->count() > 0 ? 'd-none' : '' }}">
                    <ul class="block-info" id="add-contact-block">
                        <li class="block-info-item">
                            <div class="info-title-wrapper">
                                <h2 class="info-title">Контакт </h2>
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
                <div id="add-more-contact-btn" class="{{ $company->contacts->count() > 0 ? '' : 'd-none' }} mb-3 mt-2">
                    <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center" type="button" data-bs-toggle="modal"
                            data-bs-target="#add-contact-modal">
                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M7 0C7.55228 0 8 0.447715 8 1V6H13C13.5523 6 14 6.44772 14 7C14 7.55228 13.5523 8 13 8H8V13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13V8H1C0.447715 8 0 7.55228 0 7C0 6.44772 0.447715 6 1 6H6V1C6 0.447715 6.44772 0 7 0Z" fill="currentColor"></path>
                        </svg>
                        Контакт
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
                                            <label class="green" for="name-agency-ua">Назва агенції <span class="text-danger">*</span></label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText @error('name_ua') is-invalid @enderror" type="text"
                                                       data-input-lang="ua" id="name-agency-ua" autocomplete="off"
                                                       name="name_ua"
                                                       value="{{ old('name_ua', $company->name_translations['ua'] ?? '') }}"
                                                       placeholder="Назва">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="ru-tab-pane" role="tabpanel"
                                     aria-labelledby="ru-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-ru">Название агентства <span class="text-danger">*</span></label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText @error('name_ru') is-invalid @enderror" type="text"
                                                       data-input-lang="ru" id="name-agency-ru" autocomplete="off"
                                                       name="name_ru"
                                                       value="{{ old('name_ru', $company->name_translations['ru'] ?? '') }}"
                                                       placeholder="Название">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en-tab-pane" role="tabpanel"
                                     aria-labelledby="en-tab" tabindex="0">
                                    <div class="tab-content-right">
                                        <div class="text_advertising-wrapper">
                                            <label class="green" for="name-agency-en">The name of the agency <span class="text-danger">*</span></label>
                                            <div class="item-inputText-wrapper">
                                                <input class="item-inputText @error('name_en') is-invalid @enderror" type="text"
                                                       data-input-lang="en" id="name-agency-en" autocomplete="off"
                                                       name="name_en"
                                                       value="{{ old('name_en', $company->name_translations['en'] ?? '') }}"
                                                       placeholder="The name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                    @include('pages/companies/particles/edit/_location_block_company')

                    <div class="item">
			<span>
				<label class="item-label" for="site-agency">Сайт агентства</label>
			</span>
                        <input class="item-inputText @error('website') is-invalid @enderror" id="site-agency" type="text" autocomplete="off" name="website" value="{{ old('website', $company->website) }}" placeholder="https://linkname.com">
                    </div>
                </div>
                <div class="block-row">

                    <div class="item">
                        <div class="item">
				<span>
					<label class="item-label" for="code-EDRPOU-TIN">КОД ЕГРПОУ/ИНН <span class="text-danger">*</span></label>
				</span>
                            <input class="item-inputText @error('edrpou_code') is-invalid @enderror" id="code-EDRPOU-TIN" type="text" autocomplete="off"
                                   name="edrpou_code" value="{{ old('edrpou_code', $company->edrpou_code) }}" placeholder="1234567890">
                        </div>
                        <div class="item">
				<span class="label">
					Логотип
				</span>
                            {{-- Текущий логотип --}}
                            @if($company->logo_path)
                                <div class="current-logo-wrapper mb-2">
                                    <img src="{{ $company->logo_url }}" alt="Текущий логотип" class="current-logo-preview" style="max-width: 100px; max-height: 100px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <p class="text-muted small mt-1">Текущий логотип</p>
                                </div>
                            @endif
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
									{{ $company->logo_path ? 'Заменить лого' : 'Загрузить лого' }}
								</span>
                                        </label>
                                    </li>
                                </ul>
                                <div class="error-container"></div>
                            </div>
                        </div>

                        <div class="item">
				<span>
					<label class="item-label" for="type_company">Тип агентства <span class="text-danger">*</span></label>
				</span>
                            <select class="item-inputText @error('company_type') is-invalid @enderror" id="type_company" name="company_type">
                                <option value="">Выберите тип</option>
                                @foreach($agencyTypes as $agencyType)
                                    <option value="{{ $agencyType->id }}" {{ old('company_type', $company->company_type) == $agencyType->id ? 'selected' : '' }}>{{ $agencyType->name }}</option>
                                @endforeach
                            </select>
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
								<textarea class="item-textareaText @error('description_ua') is-invalid @enderror" type="text"
                                          data-input-lang="ua" id="about-agency-ua"
                                          autocomplete="off" name="description_ua"
                                          placeholder="Введіть текст">{{ old('description_ua', $company->description_translations['ua'] ?? '') }}</textarea>
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
									<textarea class="item-textareaText @error('description_ru') is-invalid @enderror" type="text"
                                              data-input-lang="ru" id="about-agency-ru"
                                              autocomplete="off" name="description_ru"
                                              placeholder="Введите текст">{{ old('description_ru', $company->description_translations['ru'] ?? '') }}</textarea>
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
								<textarea class="item-textareaText @error('description_en') is-invalid @enderror" type="text"
                                          data-input-lang="en" id="about-agency-en"
                                          autocomplete="off" name="description_en"
                                          placeholder="Enter text">{{ old('description_en', $company->description_translations['en'] ?? '') }}</textarea>
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
                    <span>Офисы <span class="text-danger">*</span></span>
                    <button type="button" class="btn btn-add-office" id="btn-add-office" title="Добавить офис">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 0C7.55228 0 8 0.447715 8 1V6H13C13.5523 6 14 6.44772 14 7C14 7.55228 13.5523 8 13 8H8V13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13V8H1C0.447715 8 0 7.55228 0 7C0 6.44772 0.447715 6 1 6H6V1C6 0.447715 6.44772 0 7 0Z" fill="currentColor"/>
                        </svg>
                        <span>Добавить офис</span>
                    </button>
                </h3>

                {{-- Контейнер для офисов --}}
                <div class="block-offices-list" id="offices-container">
                    {{-- Предзаполняем существующими офисами --}}
                    @foreach($company->offices as $index => $office)
                        <div class="block-offices-item" data-office-index="{{ $index }}">
                            <div class="office-header">
                                <span class="office-number">Офис #<span class="office-num">{{ $index + 1 }}</span></span>
                                <button type="button" class="btn btn-remove-office" title="Удалить офис">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.75 3.5H12.25M5.25 6.125V10.125M8.75 6.125V10.125M2.625 3.5L3.5 11.375C3.5 11.8391 3.68437 12.2842 4.01256 12.6124C4.34075 12.9406 4.78587 13.125 5.25 13.125H8.75C9.21413 13.125 9.65925 12.9406 9.98744 12.6124C10.3156 12.2842 10.5 11.8391 10.5 11.375L11.375 3.5M4.8125 3.5V1.75C4.8125 1.51794 4.90469 1.29538 5.06909 1.13128C5.2335 0.966875 5.45625 0.875 5.6875 0.875H8.3125C8.54375 0.875 8.7665 0.966875 8.93091 1.13128C9.09531 1.29538 9.1875 1.51794 9.1875 1.75V3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="block-row">
                                <div class="item w25">
                                    <div class="tab-the-name">
                                        <ul class="nav nav-tabs" id="tab-office-name-{{ $index }}" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ua-tab-office-{{ $index }}" data-bs-toggle="tab" data-bs-target="#ua-tab-pane-office-{{ $index }}" type="button" role="tab" aria-controls="ua-tab-pane-office-{{ $index }}" aria-selected="false" tabindex="-1">UA</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="ru-tab-office-{{ $index }}" data-bs-toggle="tab" data-bs-target="#ru-tab-pane-office-{{ $index }}" type="button" role="tab" aria-controls="ru-tab-pane-office-{{ $index }}" aria-selected="true">RU</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="en-tab-office-{{ $index }}" data-bs-toggle="tab" data-bs-target="#en-tab-pane-office-{{ $index }}" type="button" role="tab" aria-controls="en-tab-pane-office-{{ $index }}" aria-selected="false" tabindex="-1">EN</button>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane fade" id="ua-tab-pane-office-{{ $index }}" role="tabpanel" aria-labelledby="ua-tab-office-{{ $index }}" tabindex="0">
                                                <div class="tab-content-right">
                                                    <div class="text_advertising-wrapper">
                                                        <label class="green" for="name-office-ua-{{ $index }}">Назва офісу</label>
                                                        <div class="item-inputText-wrapper">
                                                            <input class="item-inputText office-name-input" type="text" data-input-lang="ua" id="name-office-ua-{{ $index }}" autocomplete="off" name="offices[{{ $index }}][name_ua]" placeholder="Назва" value="{{ old("offices.{$index}.name_ua", $office->name_translations['ua'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade show active" id="ru-tab-pane-office-{{ $index }}" role="tabpanel" aria-labelledby="ru-tab-office-{{ $index }}" tabindex="0">
                                                <div class="tab-content-right">
                                                    <div class="text_advertising-wrapper">
                                                        <label class="green" for="name-office-ru-{{ $index }}">Название офиса</label>
                                                        <div class="item-inputText-wrapper">
                                                            <input class="item-inputText office-name-input" type="text" data-input-lang="ru" id="name-office-ru-{{ $index }}" autocomplete="off" name="offices[{{ $index }}][name_ru]" placeholder="Название" value="{{ old("offices.{$index}.name_ru", $office->name_translations['ru'] ?? $office->name ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="en-tab-pane-office-{{ $index }}" role="tabpanel" aria-labelledby="en-tab-office-{{ $index }}" tabindex="0">
                                                <div class="tab-content-right">
                                                    <div class="text_advertising-wrapper">
                                                        <label class="green" for="name-office-en-{{ $index }}">Office name</label>
                                                        <div class="item-inputText-wrapper">
                                                            <input class="item-inputText office-name-input" type="text" data-input-lang="en" id="name-office-en-{{ $index }}" autocomplete="off" name="offices[{{ $index }}][name_en]" placeholder="Name" value="{{ old("offices.{$index}.name_en", $office->name_translations['en'] ?? '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="item office-photos-item">
                                    <label class="item-label green">Фото офиса</label>
                                    <div class="office-photos-upload" data-office-index="{{ $index }}">
                                        <div class="office-photos-preview"></div>
                                        <label class="office-photos-add">
                                            <input type="file" name="offices[{{ $index }}][photos][]" multiple accept="image/*" class="office-photos-input">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <span>Добавить фото</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="block-row">
                                <div class="item selects w16">
                                    <label class="item-label">Регион</label>
                                    <div class="state-search-wrapper" data-office="{{ $index }}">
                                        <input type="text" class="state-search-input" placeholder="Введите регион..." autocomplete="off" value="{{ $office->state?->name }}">
                                        <span class="state-search-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="currentColor"/></svg></span>
                                        <span class="state-search-spinner"></span>
                                        <button type="button" class="state-search-clear">×</button>
                                        <div class="state-search-dropdown"></div>
                                        <input type="hidden" name="offices[{{ $index }}][state_id]" value="{{ old("offices.{$index}.state_id", $office->state_id) }}">
                                        <input type="hidden" name="offices[{{ $index }}][country_id]" value="{{ old("offices.{$index}.country_id", $office->country_id) }}">
                                    </div>
                                </div>
                                <div class="item w33">
                                    <label class="item-label">Улица</label>
                                    <div class="location-search-wrapper" data-office="{{ $index }}">
                                        <input type="text" class="location-search-input" placeholder="Введите улицу..." autocomplete="off" value="{{ $office->street?->name }}">
                                        <span class="location-search-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="currentColor"/></svg></span>
                                        <span class="location-search-spinner"></span>
                                        <button type="button" class="location-search-clear">×</button>
                                        <div class="location-search-dropdown"></div>
                                        <input type="hidden" name="offices[{{ $index }}][street_id]" value="{{ old("offices.{$index}.street_id", $office->street_id) }}">
                                        <input type="hidden" name="offices[{{ $index }}][city_id]" value="{{ old("offices.{$index}.city_id", $office->city_id) }}">
                                        <input type="hidden" name="offices[{{ $index }}][district_id]" value="{{ old("offices.{$index}.district_id", $office->district_id) }}">
                                        <input type="hidden" name="offices[{{ $index }}][zone_id]" value="{{ old("offices.{$index}.zone_id", $office->zone_id) }}">
                                    </div>
                                </div>
                                <div class="item w16">
                                    <span><label class="item-label">№ Дом</label> / <label>Офис</label></span>
                                    <div class="item-inputText-wrapper shtrih">
                                        <input class="item-inputText" name="offices[{{ $index }}][building_number]" type="text" autocomplete="off" placeholder="12" value="{{ old("offices.{$index}.building_number", $office->building_number) }}">
                                        <input class="item-inputText" name="offices[{{ $index }}][office_number]" type="text" autocomplete="off" placeholder="5" value="{{ old("offices.{$index}.office_number", $office->office_number) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Пустое состояние --}}
                <div class="offices-empty-state {{ $company->offices->count() > 0 ? 'd-none' : '' }}" id="offices-empty">
                    <div class="offices-empty-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 21H21M5 21V7L13 3V21M19 21V11L13 7M9 9V9.01M9 13V13.01M9 17V17.01" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="offices-empty-text">Офисы не добавлены</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-first-office">
                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-1">
                            <path d="M7 0C7.55228 0 8 0.447715 8 1V6H13C13.5523 6 14 6.44772 14 7C14 7.55228 13.5523 8 13 8H8V13C8 13.5523 7.55228 14 7 14C6.44772 14 6 13.5523 6 13V8H1C0.447715 8 0 7.55228 0 7C0 6.44772 0.447715 6 1 6H6V1C6 0.447715 6.44772 0 7 0Z" fill="currentColor"/>
                        </svg>
                        Добавить первый офис
                    </button>
                </div>
            </div>

            {{-- Шаблон офиса (скрытый) --}}
            <template id="office-template">
                <div class="block-offices-item" data-office-index="__INDEX__">
                    <div class="office-header">
                        <span class="office-number">Офис #<span class="office-num">__NUM__</span></span>
                        <button type="button" class="btn btn-remove-office" title="Удалить офис">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.75 3.5H12.25M5.25 6.125V10.125M8.75 6.125V10.125M2.625 3.5L3.5 11.375C3.5 11.8391 3.68437 12.2842 4.01256 12.6124C4.34075 12.9406 4.78587 13.125 5.25 13.125H8.75C9.21413 13.125 9.65925 12.9406 9.98744 12.6124C10.3156 12.2842 10.5 11.8391 10.5 11.375L11.375 3.5M4.8125 3.5V1.75C4.8125 1.51794 4.90469 1.29538 5.06909 1.13128C5.2335 0.966875 5.45625 0.875 5.6875 0.875H8.3125C8.54375 0.875 8.7665 0.966875 8.93091 1.13128C9.09531 1.29538 9.1875 1.51794 9.1875 1.75V3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <div class="block-row">


                        <div class="item w25">
                            <div class="tab-the-name">
                                <ul class="nav nav-tabs" id="tab-office-name-__INDEX__" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="ua-tab-office-__INDEX__" data-bs-toggle="tab" data-bs-target="#ua-tab-pane-office-__INDEX__" type="button" role="tab" aria-controls="ua-tab-pane-office-__INDEX__" aria-selected="false" tabindex="-1">UA</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="ru-tab-office-__INDEX__" data-bs-toggle="tab" data-bs-target="#ru-tab-pane-office-__INDEX__" type="button" role="tab" aria-controls="ru-tab-pane-office-__INDEX__" aria-selected="true">RU</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="en-tab-office-__INDEX__" data-bs-toggle="tab" data-bs-target="#en-tab-pane-office-__INDEX__" type="button" role="tab" aria-controls="en-tab-pane-office-__INDEX__" aria-selected="false" tabindex="-1">EN</button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade" id="ua-tab-pane-office-__INDEX__" role="tabpanel" aria-labelledby="ua-tab-office-__INDEX__" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name-office-ua-__INDEX__">Назва офісу</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText office-name-input" type="text" data-input-lang="ua" id="name-office-ua-__INDEX__" autocomplete="off" name="offices[__INDEX__][name_ua]" placeholder="Назва">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="ru-tab-pane-office-__INDEX__" role="tabpanel" aria-labelledby="ru-tab-office-__INDEX__" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name-office-ru-__INDEX__">Название офиса</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText office-name-input" type="text" data-input-lang="ru" id="name-office-ru-__INDEX__" autocomplete="off" name="offices[__INDEX__][name_ru]" placeholder="Название">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="en-tab-pane-office-__INDEX__" role="tabpanel" aria-labelledby="en-tab-office-__INDEX__" tabindex="0">
                                        <div class="tab-content-right">
                                            <div class="text_advertising-wrapper">
                                                <label class="green" for="name-office-en-__INDEX__">Office name</label>
                                                <div class="item-inputText-wrapper">
                                                    <input class="item-inputText office-name-input" type="text" data-input-lang="en" id="name-office-en-__INDEX__" autocomplete="off" name="offices[__INDEX__][name_en]" placeholder="Name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>






                        <div class="item office-photos-item">
                            <label class="item-label green">Фото офиса</label>
                            <div class="office-photos-upload" data-office-index="__INDEX__">
                                <div class="office-photos-preview"></div>
                                <label class="office-photos-add">
                                    <input type="file" name="offices[__INDEX__][photos][]" multiple accept="image/*" class="office-photos-input">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Добавить фото</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="block-row">
                        <div class="item selects w16">
                            <label class="item-label">Регион</label>
                            <div class="state-search-wrapper" data-office="__INDEX__">
                                <input type="text" class="state-search-input" placeholder="Введите регион..." autocomplete="off">
                                <span class="state-search-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="currentColor"/></svg></span>
                                <span class="state-search-spinner"></span>
                                <button type="button" class="state-search-clear">×</button>
                                <div class="state-search-dropdown"></div>
                                <input type="hidden" name="offices[__INDEX__][state_id]">
                                <input type="hidden" name="offices[__INDEX__][country_id]">
                            </div>
                        </div>
                        <div class="item w33">
                            <label class="item-label">Улица</label>
                            <div class="location-search-wrapper" data-office="__INDEX__">
                                <input type="text" class="location-search-input" placeholder="Введите улицу..." autocomplete="off">
                                <span class="location-search-icon"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M15.1171 16C15.0002 16.0003 14.8845 15.9774 14.7767 15.9327C14.6687 15.888 14.5707 15.8223 14.4884 15.7396L11.465 12.7218C10.224 13.6956 8.6916 14.224 7.11391 14.2222C5.70692 14.2222 4.33151 13.8052 3.16164 13.0238C1.99176 12.2424 1.07995 11.1318 0.541519 9.83244C0.00308508 8.53306 -0.137797 7.1032 0.136693 5.7238C0.411184 4.34438 1.08872 3.07731 2.08362 2.0828C3.07852 1.08829 4.34609 0.411022 5.72606 0.136639C7.106 -0.137743 8.53643 0.00308386 9.83632 0.541306C11.1362 1.07953 12.2472 1.99098 13.029 3.16039C13.8106 4.3298 14.2278 5.70467 14.2278 7.11111C14.231 8.69031 13.7023 10.2245 12.7268 11.4667L15.7458 14.4889C15.8679 14.6135 15.9508 14.7714 15.9839 14.9427C16.017 15.114 15.9988 15.2914 15.9318 15.4524C15.8647 15.6136 15.7517 15.7515 15.6069 15.8488C15.462 15.9462 15.2916 15.9988 15.1171 16ZM7.11391 1.77778C6.05867 1.77778 5.02712 2.09058 4.14971 2.67661C3.2723 3.26264 2.58844 4.0956 2.18462 5.07013C1.78079 6.04467 1.67513 7.11706 1.881 8.15155C2.08687 9.18613 2.59502 10.1364 3.34119 10.8823C4.08737 11.6283 5.03806 12.1362 6.07302 12.342C7.10796 12.5477 8.18073 12.4421 9.1557 12.0385C10.1307 11.6348 10.9639 10.9512 11.5502 10.0741C12.1364 9.19706 12.4493 8.16595 12.4493 7.11111C12.4477 5.69713 11.885 4.34154 10.8848 3.3417C9.88461 2.34186 8.52843 1.77943 7.11391 1.77778Z" fill="currentColor"/></svg></span>
                                <span class="location-search-spinner"></span>
                                <button type="button" class="location-search-clear">×</button>
                                <div class="location-search-dropdown"></div>
                                <input type="hidden" name="offices[__INDEX__][street_id]">
                                <input type="hidden" name="offices[__INDEX__][city_id]">
                                <input type="hidden" name="offices[__INDEX__][district_id]">
                                <input type="hidden" name="offices[__INDEX__][zone_id]">
                            </div>
                        </div>
                        <div class="item w16">
                            <span><label class="item-label">№ Дом</label> / <label>Офис</label></span>
                            <div class="item-inputText-wrapper shtrih">
                                <input class="item-inputText" name="offices[__INDEX__][building_number]" type="text" autocomplete="off" placeholder="12">
                                <input class="item-inputText" name="offices[__INDEX__][office_number]" type="text" autocomplete="off" placeholder="5">
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="block-my-btns">
                <div class="block-my-btns-left">
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-primary">
                        Отменить
                    </a>
                </div>
                <div class="block-my-btns-right">
                    <button class="btn btn-primary" type="submit">
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </form>
    <!-- кінець main	-->
    @include('pages/companies/modals/create/_contact-modal')

    {{-- Шаблон карточки контакта для JS (структура как в properties) --}}
    <template id="contact-card-template">
        <ul class="block-info contact-card" data-contact-id="">
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
    <script src="{{ asset('js/pages/my-dropdown.min.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/function_on_pages-create.js') }}" type="module"></script>
    <script src="{{ asset('js/pages/companies/create/page-create-agency.js') }}" type="module"></script>
    <script src="{{ asset('js/pages/companies/create/location-search.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/office-manager.js') }}"></script>

    {{-- Contact Modal JS Module --}}
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/config.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/utils.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/components.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/api.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/form.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/contact-list.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/handlers.js') }}"></script>
    <script src="{{ asset('js/pages/companies/create/modal/add-contact/main.js') }}"></script>

    {{-- Инициализация для редактирования - устанавливаем начальный индекс офисов --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Устанавливаем начальный индекс офисов для office-manager.js
            if (typeof window.OfficeManager !== 'undefined') {
                window.OfficeManager.setStartIndex({{ $company->offices->count() }});
            }
        });
    </script>
@endpush
