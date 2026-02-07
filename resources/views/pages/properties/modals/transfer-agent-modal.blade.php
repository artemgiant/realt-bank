<div class="modal fade transfer-to-agent" id="transfer-to-agent" tabindex="-1" aria-labelledby="transfer-to-agent-label" aria-hidden="true"
     data-company-id="{{ $agent->company_id ?? '' }}">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center mb-0 justify-content-between">
                    <h2 class="modal-title" id="transfer-to-agent-label">
                        <span>Передача агенту</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l mt-2 info-user">
                    <h3 class="modal-body-title">
                        <span>Новый агент</span>
                    </h3>
                    <div class="d-flex wrapper-modal-row">
                        <div class="item selects w-50">
                            <label class="item-label" for="transfer-to-agent-office">Офис</label>
                            <select id="transfer-to-agent-office" class="js-example-responsive3 my-select2" autocomplete="off">
                                <option value="">Выберите офис</option>
                            </select>
                        </div>
                        <div class="item selects w-50">
                            <label class="item-label" for="transfer-to-agent-name">ФИО</label>
                            <select id="transfer-to-agent-name" class="js-example-responsive3 my-select2" autocomplete="off" disabled>
                                <option value="">Сначала выберите офис</option>
                            </select>
                        </div>
                    </div>

                    <div class="info-user-item">
                        <ul class="block-info">
                            <li class="block-info-item">
                                <div class="info-avatar">
                                    <picture>
                                        <source srcset="{{ asset('img/icon/default-avatar-table.svg') }}" type="image/webp">
                                        <img src="{{ asset('img/icon/default-avatar-table.svg') }}" alt="" id="transfer-agent-avatar">
                                    </picture>
                                </div>
                                <div class="info-contacts">
                                    <p class="info-contacts-name" id="transfer-agent-name">Выберите агента</p>
                                    <p class="info-description" id="transfer-agent-description">-</p>
                                    <a href="tel:" class="info-contacts-tel" id="transfer-agent-tel">-</a>
                                </div>
                                <div class="info-links">
                                    <a href="#" class="transfer-link whatsapp" id="transfer-whatsapp" style="display: none;">
                                        <picture>
                                            <source srcset="{{ asset('img/icon/icon-table/cnapchat.svg') }}" type="image/webp">
                                            <img src="{{ asset('img/icon/icon-table/cnapchat.svg') }}" alt="">
                                        </picture>
                                    </a>
                                    <a href="#" class="transfer-link viber" id="transfer-viber" style="display: none;">
                                        <picture>
                                            <source srcset="{{ asset('img/icon/icon-table/viber.svg') }}" type="image/webp">
                                            <img src="{{ asset('img/icon/icon-table/viber.svg') }}" alt="">
                                        </picture>
                                    </a>
                                    <a href="#" class="transfer-link telegram" id="transfer-telegram" style="display: none;">
                                        <picture>
                                            <source srcset="{{ asset('img/icon/icon-table/tg.svg') }}" type="image/webp">
                                            <img src="{{ asset('img/icon/icon-table/tg.svg') }}" alt="">
                                        </picture>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="comments-row">
                        <div class="item">
                            <label for="transfer-comment">Комментарий</label>
                            <div class="item-inputText-wrapper">
                                <input class="item-inputText" id="transfer-comment" type="text" autocomplete="off" placeholder="Введите текст">
                            </div>
                        </div>
                        <div class="item">
                            <label class="my-custom-input transfer-tasks-disabled" style="opacity: 0.5; cursor: not-allowed; position: relative;">
                                <input type="checkbox" id="transfer-tasks" name="transfer_tasks" disabled>
                                <span class="my-custom-box" style="border-color: #999 !important;"></span>
                                <span class="my-custom-text" style="color: #999;">Перенести активные задачи</span>
                                <span style="display: inline-block; background: #999; color: #fff; font-size: 10px; padding: 1px 6px; border-radius: 3px; margin-left: 6px; vertical-align: middle;">Скоро</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-body-l d-flex justify-content-between mb-0">
                    <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Отменить</button>
                    <button class="btn btn-primary" type="button" id="transfer-agent-btn" disabled>Передать</button>
                </div>
            </div>
        </div>
    </div>
</div>
