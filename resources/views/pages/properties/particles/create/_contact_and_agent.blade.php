{{-- Основная информация (Контакт и Агент) --}}
<div class="create-filter-row row0">
    <div class="left">
        {{-- Контейнер для списка контактов --}}
        <div id="contacts-list-container">
            {{-- Контакты будут добавляться через JS --}}
        </div>

        {{-- Блок добавления контакта (показывается если нет контактов) --}}
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

        {{-- Кнопка добавления еще одного контакта (показывается когда есть хотя бы один) --}}
        <div id="add-more-contact-btn" class="d-none">
            <button class="btn btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"></path>
                    <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"></path>
                </svg>
            </button>
        </div>


        <div class="left-items-wrapper">
            <div class="item">
                <label for="link-on-the-ad">Ссылка на объявление</label>
                <div class="item-inputText-wrapper">
                    <input class="item-inputText" type="url" id="link-on-the-ad" name="external_url" autocomplete="off" placeholder="Вставьте ссылку">
                </div>
            </div>
            <div class="item">
                <label class="my-custom-input">
                    <input type="checkbox" name="is_advertised" value="1" checked>
                    <span class="my-custom-box"></span>
                    <span class="my-custom-text">Рекламировать объект</span>
                </label>
            </div>
            <div class="item">
                <label class="my-custom-input">
                    <input type="checkbox" name="is_visible_to_agents" value="1">
                    <span class="my-custom-box"></span>
                    <span class="my-custom-text">Открыть контакты и адрес объекта для агентов моей компании</span>
                </label>
            </div>
        </div>
    </div>


    <div class="right">
        <ul class="block-info">
            <li class="block-info-item">
                <div class="info-title-wrapper">
                    <h2 class="info-title">Агент</h2>
                    <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#transfer-to-agent">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#EF9629" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                            <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"></path>
                            <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"></path>
                        </svg>
                    </button>
                </div>
                <div class="info-avatar">
                    <picture>
                        @if($agent && $agent->photo_url)
                            <img src="{{ $agent->photo_url }}" alt="Avatar">
                        @else
                            <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar">
                        @endif
                    </picture>
                </div>
                <div class="info-contacts">
                    @if($agent)
                        <p class="info-contacts-name">{{ $agent->full_name }}</p>
                        <p class="info-description">{{ $agent->company?->name ?? '' }}</p>
                        @if($agent->phone)
                            <a href="tel:{{ $agent->phone }}" class="info-contacts-tel">{{ $agent->phone }}</a>
                        @endif
                    @else
                        <p class="info-contacts-name text-muted">Агент не найден</p>
                    @endif
                </div>
            </li>
        </ul>
        <div class="item">
            <label for="personal-notes">Заметки</label>
            <div class="item-inputText-wrapper">
                <textarea class="item-textareaText " id="personal-notes" name="personal_notes" autocomplete="off" placeholder="Введите текст">{{ old('personal_notes') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- Шаблон для карточки контакта (используется JS) --}}
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
