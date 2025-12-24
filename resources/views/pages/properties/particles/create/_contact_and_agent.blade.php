{{-- Основна інформація (Контакт і Агент) --}}
<div class="create-filter-row row0">
    <div class="left">
        <ul class="block-info">
            <li class="block-info-item">
                <div class="info-title-wrapper">
                    <h2 class="info-title">Контакт</h2>
                    <button class="btn  btn-edit-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.33398 10.9996H5.16065C5.24839 11.0001 5.33536 10.9833 5.41659 10.9501C5.49781 10.917 5.57169 10.8681 5.63398 10.8063L10.2473 6.1863L12.1406 4.33297C12.2031 4.27099 12.2527 4.19726 12.2866 4.11602C12.3204 4.03478 12.3378 3.94764 12.3378 3.85963C12.3378 3.77163 12.3204 3.68449 12.2866 3.60325C12.2527 3.52201 12.2031 3.44828 12.1406 3.3863L9.31398 0.5263C9.25201 0.463815 9.17828 0.414219 9.09704 0.380373C9.0158 0.346527 8.92866 0.329102 8.84065 0.329102C8.75264 0.329102 8.66551 0.346527 8.58427 0.380373C8.50303 0.414219 8.42929 0.463815 8.36732 0.5263L6.48732 2.41297L1.86065 7.03297C1.79886 7.09526 1.74998 7.16914 1.7168 7.25036C1.68363 7.33159 1.66681 7.41856 1.66732 7.5063V10.333C1.66732 10.5098 1.73756 10.6793 1.86258 10.8044C1.9876 10.9294 2.15717 10.9996 2.33398 10.9996ZM8.84065 1.93963L10.7273 3.8263L9.78065 4.77297L7.89398 2.8863L8.84065 1.93963ZM3.00065 7.77963L6.95398 3.8263L8.84065 5.71297L4.88732 9.6663H3.00065V7.77963ZM13.0007 12.333H1.00065C0.82384 12.333 0.654271 12.4032 0.529246 12.5282C0.404222 12.6533 0.333984 12.8228 0.333984 12.9996C0.333984 13.1764 0.404222 13.346 0.529246 13.471C0.654271 13.5961 0.82384 13.6663 1.00065 13.6663H13.0007C13.1775 13.6663 13.347 13.5961 13.4721 13.471C13.5971 13.346 13.6673 13.1764 13.6673 12.9996C13.6673 12.8228 13.5971 12.6533 13.4721 12.5282C13.347 12.4032 13.1775 12.333 13.0007 12.333Z" fill="#AAAAAA"></path>
                        </svg>
                    </button>
                    <button class="btn  btn-add-client" type="button" data-bs-toggle="modal" data-bs-target="#add-contact-modal">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z" fill="#AAAAAA"></path>
                            <path d="M8.583 13.5848C8.46922 13.471 8.39889 13.3139 8.39889 13.1403L8.39889 4.91816C8.39883 4.571 8.68022 4.28961 9.02739 4.28967C9.37449 4.28967 9.65588 4.57106 9.65588 4.91816L9.65588 13.1403C9.65583 13.4875 9.37449 13.7688 9.02733 13.7689C8.85389 13.7689 8.69678 13.6985 8.583 13.5848Z" fill="#AAAAAA"></path>
                            <!--											тут закоментований мінус-->
                            <!--											<path-->
                            <!--												d="M4.47186 9.47374C4.35814 9.36002 4.28775 9.20285 4.28775 9.0293C4.28775 8.68208 4.56908 8.40075 4.9163 8.40075L13.1385 8.40075C13.4856 8.4008 13.767 8.68213 13.767 9.0293C13.767 9.3764 13.4856 9.65779 13.1385 9.65779H4.91636C4.74275 9.65785 4.58558 9.58746 4.47186 9.47374Z"-->
                            <!--												fill="#AAAAAA"/>-->
                        </svg>
                    </button>
                </div>
                <div class="info-avatar">
                    <picture>
                        <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}" type="image/webp">
                        <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="Avatar">
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
                    <input class="item-inputText" type="url" id="link-on-the-ad" autocomplete="off" placeholder="Вставьте ссылку">
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
    {{-- Інформація про агента --}}
</div>
