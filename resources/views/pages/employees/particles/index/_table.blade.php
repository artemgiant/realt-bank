<table id="example" class="display" style="width:100%">
    <col width="3.0%" valign="middle">
    <col width="6.0%" valign="middle">
    <col width="15.038%" valign="middle">
    <col width="17.587%" valign="middle">
    <col width="17.587%" valign="middle">
    <col width="6.0%" valign="middle">
    <col width="6.0%" valign="middle">
    <col width="6.0%" valign="middle">
    <col width="8.0%" valign="middle">
    <col width="9.223%" valign="middle">
    <col width="6%" valign="middle">

    <thead>
    <tr>
        {{-- Checkbox --}}
        <th>
            <div class="thead-wrapper checkBox">
                <label class="my-custom-input">
                    <input type="checkbox">
                    <span class="my-custom-box"></span>
                </label>
            </div>
        </th>

        {{-- Фото --}}
        <th>
            <div class="thead-wrapper photo">
                <p>Фото</p>
            </div>
        </th>

        {{-- Агент --}}
        <th>
            <div class="thead-wrapper agent">
                <p>Агент</p>
            </div>
        </th>

        {{-- Должность --}}
        <th>
            <div class="thead-wrapper position">
                <p>Должность</p>
            </div>
        </th>

        {{-- Офис --}}
        <th>
            <div class="thead-wrapper offices">
                <p>Офис</p>
            </div>
        </th>

        {{-- Объекты --}}
        <th>
            <div class="thead-wrapper object">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                        <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                    </svg>
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Объекты">
                        <picture>
                            <source srcset="{{ asset('img/icon/icon-info.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/icon-info.svg') }}" alt="">
                        </picture>
                    </span>
                </p>
            </div>
        </th>

        {{-- Клиенты --}}
        <th>
            <div class="thead-wrapper client">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                        <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                    </svg>
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Сделки">
                        <picture>
                            <source srcset="{{ asset('img/icon/icon-info.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/icon-info.svg') }}" alt="">
                        </picture>
                    </span>
                </p>
            </div>
        </th>

        {{-- Успешные сделки --}}
        <th>
            <div class="thead-wrapper succeed">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                        <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                    </svg>
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Успешные сделки">
                        <picture>
                            <source srcset="{{ asset('img/icon/icon-info.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/icon-info.svg') }}" alt="">
                        </picture>
                    </span>
                </p>
            </div>
        </th>

        {{-- Неуспешные сделки --}}
        <th>
            <div class="thead-wrapper nosucceed">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                        <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.38 1.38 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51q.205.03.443.051c.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.9 1.9 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2 2 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.2 3.2 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.8 4.8 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591"/>
                    </svg>
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Неуспешные сделки">
                        <picture>
                            <source srcset="{{ asset('img/icon/icon-info.svg') }}" type="image/webp">
                            <img src="{{ asset('img/icon/icon-info.svg') }}" alt="">
                        </picture>
                    </span>
                </p>
            </div>
        </th>

        {{-- Активный до --}}
        <th>
            <div class="thead-wrapper activeuntil">
                <p>Активный до</p>
            </div>
        </th>

        {{-- Действия --}}
        <th>
            <div class="thead-wrapper block-actions">


                {{-- Кнопка добавления сотрудника --}}
                <div class="header-btn">
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#add-employee-modal" title="Добавить сотрудника">
                        <svg width="14" height="14" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.471859 5.47374C0.358138 5.36002 0.28775 5.20285 0.287749 5.0293C0.28775 4.68208 0.569081 4.40075 0.916301 4.40075L9.13847 4.40075C9.48563 4.4008 9.76697 4.68213 9.76702 5.0293C9.76702 5.3764 9.48563 5.65779 9.13853 5.65779H0.916357C0.742747 5.65785 0.585581 5.58746 0.471859 5.47374Z" fill="white"/>
                            <path d="M4.583 9.58476C4.46922 9.47098 4.39889 9.31387 4.39889 9.14032L4.39889 0.918164C4.39883 0.571001 4.68022 0.289614 5.02739 0.28967C5.37449 0.28967 5.65588 0.571056 5.65588 0.918164L5.65588 9.14032C5.65583 9.48748 5.37449 9.76881 5.02733 9.76887C4.85389 9.76887 4.69678 9.69853 4.583 9.58476Z" fill="white"/>
                        </svg>
                    </button>
                </div>



                <div class="menu-burger">
                    <div class="dropdown">
                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <picture>
                                <source srcset="{{ asset('img/icon/burger.svg') }}" type="image/webp">
                                <img src="{{ asset('img/icon/burger.svg') }}" alt="">
                            </picture>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Обновить</a></li>
                            <li><a class="dropdown-item" href="#">Редактировать</a></li>
                            <li><a class="dropdown-item" href="#">Удалить</a></li>
                            <li><a class="dropdown-item" href="#">Отложить</a></li>
                            <li><a class="dropdown-item" href="#">Передать</a></li>
                        </ul>
                    </div>
                </div>
                <div class="menu-burger">
                    <div class="dropdown">
                        <button class="btn " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <picture>
                                <source srcset="{{ asset('img/icon/sorting.svg') }}" type="image/webp">
                                <img src="{{ asset('img/icon/sorting.svg') }}" alt="">
                            </picture>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Самые новые</a></li>
                            <li><a class="dropdown-item" href="#">Самые дешевые</a></li>
                            <li><a class="dropdown-item" href="#">Самые дорогие</a></li>
                            <li><a class="dropdown-item" href="#">Самые дешевые/м<sup>2</sup></a></li>
                            <li><a class="dropdown-item" href="#">Самые дорогие/м<sup>2</sup></a></li>
                            <li><a class="dropdown-item" href="#">Наименьшая площадь</a></li>
                            <li><a class="dropdown-item" href="#">Наибольшая площадь</a></li>
                            <li><a class="dropdown-item" href="#">Самые старые</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </th>
    </tr>
    </thead>

    <tbody>
    {{-- Данные загружаются через AJAX --}}
    </tbody>
</table>
