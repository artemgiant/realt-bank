<div class="modal fade" id="add-employee-modal" tabindex="-1" aria-labelledby="add-employee-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center mb-0 justify-content-between">
                    <h2 class="modal-title" id="add-employee-modal-label">
                        <span>Сотрудник</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l">
                    <h3 class="modal-body-title">
                        <span>Основное</span>
                    </h3>
                    <div class="modal-row">
                        <div class="item">
                            <label for="name-employee-modal" class="green">Имя</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="name-employee-modal" type="text" autocomplete="off" placeholder="Имя">
                            </div>
                        </div>
                        <div class="item">
                            <label for="surname-employee-modal" class="green">Фамилия</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="surname-employee-modal" type="text" autocomplete="off" placeholder="Фамилия">
                            </div>
                        </div>
                        <div class="item">
                            <label for="father-name-employee-modal">Отчество</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="father-name-employee-modal" type="text" autocomplete="off" placeholder="Отчество">
                            </div>
                        </div>
                    </div>
                    <div class="modal-row">
                        <div class="item phone">
                            <div class="item" data-phone-item>
                                <div class="add_new-tel">
                                    <button type="button" class="btn btn-new-tel">
                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="#3585F5" />
                                            <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="#3585F5" />
                                        </svg>
                                    </button>
                                </div>
                                <label for="tel-contact-employee-modal" class="green">Телефон / Логин</label>
                                <div class="item-inputText-wrapper">
                                    <input class="item-inputText tel-contact" id="tel-contact-employee-modal" type="tel" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <label for="email-employee-modal" class="green">Email</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="email-employee-modal" type="email" autocomplete="off">
                            </div>
                        </div>
                        <div class="item selects">
                            <label class="item-label" for="tags-employee-modal">Теги</label>
                            <select id="tags-employee-modal" class="js-example-responsive2 my-select2" autocomplete="off">
                                <option></option>
                                <option value="agent">Агент</option>
                                <option value="manager">Менеджер</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-row">
                        <div class="item selects">
                            <label class="item-label" for="role-employee-modal">Роль</label>
                            <select id="role-employee-modal" class="js-example-responsive2 my-select2" autocomplete="off">
                                <option></option>
                                <option value="agent">Агент</option>
                                <option value="manager">Менеджер</option>
                                <option value="admin">Администратор</option>
                            </select>
                        </div>
                        <div class="item selects">
                            <label class="item-label" for="offices-employee-modal">Офис</label>
                            <select id="offices-employee-modal" class="js-example-responsive2 my-select2" autocomplete="off">
                                <option></option>
                                <option value="office-1">Офис 1</option>
                                <option value="office-2">Офис 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-row">
                        <div class="item w100">
                            <label for="comment-employee-modal">Комментарий</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="comment-employee-modal" type="text" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body-l mb-0">
                    <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">
                        Отменить
                    </button>
                    <button class="btn btn-primary" type="button" id="save-employee-btn">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
