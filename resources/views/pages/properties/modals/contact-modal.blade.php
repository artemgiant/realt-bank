<div class="modal fade" id="add-contact-modal" tabindex="-1" aria-labelledby="add-contact-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="contact-modal-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="modal-body-l d-flex align-items-center mb-0 justify-content-between">
                        <h2 class="modal-title" id="add-contact-modal-label">
                            <span>Контакт</span>
                        </h2>
                        {{-- Индикатор найденного контакта --}}
                        <div id="contact-found-indicator" class="badge bg-success d-none">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="me-1">
                                <path
                                    d="M13.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.793l6.646-6.647a.5.5 0 0 1 .708 0z"
                                    fill="currentColor" />
                            </svg>
                            Контакт найден
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Hidden field для ID существующего контакта --}}
                    <input type="hidden" id="contact-id-modal" name="contact_id" value="">

                    <div class="modal-body-l">
                        <h3 class="modal-body-title">
                            <span>Основное</span>
                        </h3>
                        <div class="modal-row">
                            <div class="item">
                                <label for="first-name-contact-modal" class="green">Имя <span
                                        class="text-danger">*</span></label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="first-name-contact-modal" name="first_name"
                                        type="text" autocomplete="off" placeholder="Имя" required>
                                </div>
                            </div>
                            <div class="item">
                                <label for="last-name-contact-modal">Фамилия</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="last-name-contact-modal" name="last_name"
                                        type="text" autocomplete="off" placeholder="Фамилия">
                                </div>
                            </div>
                            <div class="item">
                                <label for="middle-name-contact-modal">Отчество</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="middle-name-contact-modal" name="middle_name"
                                        type="text" autocomplete="off" placeholder="Отчество">
                                </div>
                            </div>
                        </div>
                        <div class="modal-row">
                            <div class="item phone">
                                <div class="item" data-phone-item>
                                    <div class="add_new-tel">
                                        <button type="button" class="btn btn-new-tel">
                                            <svg width="11" height="11" viewBox="0 0 11 11" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z"
                                                    fill="#3585F5" />
                                                <path
                                                    d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z"
                                                    fill="#3585F5" />
                                            </svg>
                                        </button>
                                    </div>
                                    <label for="tel-contact1-modal" class="green">Телефон <span
                                            class="text-danger">*</span></label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText tel-contact" id="tel-contact1-modal"
                                            name="phones[0][phone]" type="tel" autocomplete="off" required>
                                        <input type="hidden" name="phones[0][is_primary]" value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <label for="email-contact-modal">Email</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="email-contact-modal" name="email" type="email"
                                        autocomplete="off" placeholder="email@gmail.com">
                                </div>
                            </div>
                            <div class="item selects">
                                <label class="item-label green" for="roles-contact-modal">Роли контакта</label>
                                <select id="roles-contact-modal" name="roles[]"
                                    class="js-example-responsive2 my-select2" autocomplete="off" multiple>
                                    @if(isset($contactRoles))
                                        @foreach($contactRoles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-row">
                            <div class="item w25 selects">
                                <label class="item-label green" for="tags-client-modal">Теги</label>
                                <select id="tags-client-modal" name="tags" class="js-example-responsive2 my-select2"
                                    autocomplete="off">
                                    <option value="">Выберите тег</option>
                                    @if(isset($contactTags))
                                        @foreach($contactTags as $tag)
                                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="item w75">
                                <label for="comment-contact-modal">Комментарий</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="comment-contact-modal" name="comment" type="text"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="modal-row files">
                            <div class="item photo-loader">
                                <span class="label">Фото</span>
                                <div class="photo-info-list-wrapper">
                                    <ul class="photo-info-list">
                                        <li class="photo-info-btn-wrapper">
                                            <label class="photo-info-btn" for="loading-photo-contact-modal">
                                                <input type="file" id="loading-photo-contact-modal" name="photo"
                                                    accept="image/png, image/jpg, image/jpeg, image/heic">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M8.50725 13.2938C7.814 13.9437 6.89515 14.2986 5.945 14.2833C4.99486 14.2679 4.08791 13.8837 3.41597 13.2117C2.74403 12.5398 2.35977 11.6329 2.34446 10.6827C2.32914 9.73256 2.68398 8.81371 3.33392 8.12046L9.17392 2.28713C9.52109 1.94269 9.96235 1.70858 10.4422 1.61425C10.9221 1.51991 11.4191 1.56956 11.8708 1.75695C12.3226 1.94433 12.7088 2.2611 12.981 2.6674C13.2532 3.0737 13.3992 3.55141 13.4006 4.04046C13.4002 4.36567 13.3352 4.68757 13.2093 4.98743C13.0834 5.28729 12.8992 5.55912 12.6672 5.78713L7.11392 11.3338C6.94029 11.4722 6.72193 11.5421 6.50022 11.5302C6.27851 11.5183 6.06887 11.4254 5.91105 11.2692C5.75324 11.1131 5.65821 10.9044 5.64399 10.6828C5.62977 10.4613 5.69735 10.2422 5.83392 10.0671L11.3939 4.50713L10.4472 3.56713L4.88725 9.12713C4.486 9.55082 4.26593 10.1144 4.27387 10.6978C4.2818 11.2813 4.51712 11.8387 4.92974 12.2513C5.34236 12.6639 5.89971 12.8992 6.4832 12.9072C7.06668 12.9151 7.63022 12.695 8.05392 12.2938L13.6206 6.73379C14.3367 6.01859 14.7393 5.04822 14.7399 4.03615C14.7406 3.02408 14.3391 2.05321 13.6239 1.33713C12.9087 0.621043 11.9383 0.218399 10.9263 0.217774C9.9142 0.217149 8.94333 0.618593 8.22725 1.33379L2.38725 7.18046C1.4841 8.1234 0.986222 9.38258 1.00029 10.6882C1.01436 11.9938 1.53926 13.2419 2.46251 14.1652C3.38577 15.0885 4.63393 15.6133 5.93953 15.6274C7.24513 15.6415 8.50431 15.1436 9.44725 14.2405L14.7272 8.95379L13.7872 8.00046L8.50725 13.2938Z"
                                                        fill="#3585F5" />
                                                </svg>
                                                <span>Загрузить фото</span>
                                            </label>
                                        </li>
                                    </ul>
                                    <div class="error-container"></div>
                                </div>
                            </div>
                            <div class="item-row">
                                <div class="item w33">
                                    <span><label class="item-label" for="telegram-contact-modal">Telegram</label></span>
                                    <input class="item-inputText" id="telegram-contact-modal" name="telegram"
                                        type="text" autocomplete="off" placeholder="@profilename">
                                </div>
                                <div class="item w33">
                                    <span><label class="item-label" for="viber-contact-modal">Viber</label></span>
                                    <input class="item-inputText" id="viber-contact-modal" name="viber" type="text"
                                        autocomplete="off" placeholder="@profilename">
                                </div>
                                <div class="item w33">
                                    <span><label class="item-label" for="whatsapp-contact-modal">Whatsapp</label></span>
                                    <input class="item-inputText" id="whatsapp-contact-modal" name="whatsapp"
                                        type="text" autocomplete="off" placeholder="@profilename">
                                </div>
                                <div class="item w50">
                                    <label for="passport-contact-modal">Паспорт</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" id="passport-contact-modal" name="passport"
                                            type="text" autocomplete="off" placeholder="АА123456">
                                    </div>
                                </div>
                                <div class="item w50">
                                    <label for="inn-contact-modal">ИНН</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" id="inn-contact-modal" name="inn" type="text"
                                            autocomplete="off" placeholder="1234567890">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body-l mb-0">
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">
                            Отменить
                        </button>
                        <button class="btn btn-primary" type="submit" id="save-contact-btn">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                            Сохранить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>