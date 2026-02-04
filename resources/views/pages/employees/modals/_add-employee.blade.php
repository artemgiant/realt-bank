<div class="modal fade" id="add-employee-modal" tabindex="-1" aria-labelledby="addEmployeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="add-employee-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    {{-- Заголовок модалки --}}
                    <div class="modal-body-l d-flex align-items-center mb-0 justify-content-between">
                        <h2 class="modal-title" id="addEmployeeModalLabel">
                            <span>Новый сотрудник</span>
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body-l">
                        <h3 class="modal-body-title">
                            <span>Основное</span>
                        </h3>

                        {{-- Имя, Фамилия, Отчество --}}
                        <div class="modal-row">
                            <div class="item">
                                <label for="first_name" class="green">Имя <span class="text-danger">*</span></label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="first_name" name="first_name" type="text"
                                        autocomplete="off" placeholder="Имя" required>
                                </div>
                                <div class="invalid-feedback" data-field="first_name"></div>
                            </div>
                            <div class="item">
                                <label for="last_name" class="green">Фамилия <span class="text-danger">*</span></label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="last_name" name="last_name" type="text"
                                        autocomplete="off" placeholder="Фамилия" required>
                                </div>
                                <div class="invalid-feedback" data-field="last_name"></div>
                            </div>
                            <div class="item">
                                <label for="middle_name">Отчество</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="middle_name" name="middle_name" type="text"
                                        autocomplete="off" placeholder="Отчество">
                                </div>
                            </div>
                        </div>

                        {{-- Телефон, Email, Теги --}}
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
                                    <label for="phone" class="green">Телефон</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText tel-contact" id="phone" name="phone" type="tel"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="item">
                                <label for="email" class="green">Email <span class="text-danger">*</span></label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="email" name="email" type="email"
                                        autocomplete="new-email" required>
                                </div>
                                <div class="invalid-feedback" data-field="email"></div>
                            </div>
                            <div class="item">
                                <label for="password" class="green">Пароль <span class="text-danger">*</span></label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText" id="password" name="password" type="password"
                                        autocomplete="new-password" required>
                                </div>
                                <div class="invalid-feedback" data-field="password"></div>
                            </div>
                        </div>



                        {{-- Должность, Офис, День рождения --}}
                        <div class="modal-row">
                            <div class="item selects">
                                <label class="item-label" for="position_id">Должность</label>
                                <select id="position_id" name="position_id" class="js-example-responsive2 my-select2"
                                    autocomplete="off">
                                    <option></option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item selects">
                                <label class="item-label" for="office_id">Офис</label>
                                <select id="office_id" name="office_id" class="js-example-responsive2 my-select2"
                                    autocomplete="off">
                                    <option></option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item data">
                                <span>
                                    <label for="birthday">День рождения</label>
                                </span>
                                <span>
                                    <input class="item-inputText date-piker" type="text" id="birthday" name="birthday"
                                        autocomplete="off">
                                    <picture>
                                        <source srcset="{{ asset('img/icon/calendar.svg') }}" type="image/webp">
                                        <img src="{{ asset('img/icon/calendar.svg') }}" alt="">
                                    </picture>
                                </span>
                            </div>
                        </div>

                        {{-- Компания, Статус, Активен до --}}
                        <div class="modal-row">
                            <div class="item selects">
                                <label class="item-label" for="company_id">Компания</label>
                                <select id="company_id" name="company_id" class="js-example-responsive2 my-select2"
                                    autocomplete="off">
                                    <option></option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item selects">
                                <label class="item-label" for="status_id">Статус</label>
                                <select id="status_id" name="status_id" class="js-example-responsive2 my-select2"
                                    autocomplete="off">
                                    <option></option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="item data">
                                <span>
                                    <label for="active_until">Активен до</label>
                                </span>
                                <span>
                                    <input class="item-inputText date-piker" type="text" id="active_until"
                                        name="active_until" autocomplete="off">
                                    <picture>
                                        <source srcset="{{ asset('img/icon/calendar.svg') }}" type="image/webp">
                                        <img src="{{ asset('img/icon/calendar.svg') }}" alt="">
                                    </picture>
                                </span>
                            </div>
                        </div>


                        {{-- Теги --}}
                        <div class="modal-row">
                            <div class="item  w100 selects">
                                <label class="item-label" for="tag_ids">Теги</label>
                                <select id="tag_ids" name="tag_ids[]" class="js-example-responsive2 my-select2" multiple
                                    autocomplete="off">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Комментарий --}}
                        <div class="modal-row">
                            <div class="item w100">
                                <label for="comment">Комментарий</label>
                                <div class="item-inputText-wrapper">
                                    <textarea class="item-inputText" id="comment" name="comment" rows="2"
                                        autocomplete="off"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Фото, Паспорт, ИНН --}}
                        <div class="modal-row files">
                            <div class="item photo-loader">
                                <span class="label">Фото</span>
                                <div class="photo-info-list-wrapper">
                                    <ul class="photo-info-list">
                                        <li class="photo-info-btn-wrapper">
                                            <label class="photo-info-btn" for="photo">
                                                <input type="file" id="photo" name="photo"
                                                    accept="image/png, image/jpg, image/jpeg, image/webp">
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
                                    <div class="photo-preview" id="photo-preview" style="display: none;">
                                        <img src="" alt="Preview"
                                            style="max-width: 100px; max-height: 100px; margin-top: 10px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                            id="remove-photo">X</button>
                                    </div>
                                    <div class="error-container"></div>
                                </div>
                            </div>
                            <div class="item-row">
                                <div class="item w50">
                                    <label for="passport">Паспорт</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" id="passport" name="passport" type="text"
                                            autocomplete="off" placeholder="АА123456">
                                    </div>
                                </div>
                                <div class="item w50">
                                    <label for="inn">ИНН</label>
                                    <div class="item-inputText-wrapper">
                                        <input class="item-inputText" id="inn" name="inn" type="text" autocomplete="off"
                                            placeholder="1234567890">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Кнопки действий --}}
                    <div class="modal-body-l mb-0">
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">
                            Отменить
                        </button>
                        <button class="btn btn-primary" type="submit" id="save-employee-btn">
                            <span class="btn-text">Сохранить</span>
                            <span class="btn-loader d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>