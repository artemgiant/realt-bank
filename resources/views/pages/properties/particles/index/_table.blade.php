<table id="example" class="table table-hover">

    <thead>
    <tr>
        <th>
            <div class="thead-wrapper checkBox">
                <label class="my-custom-input">
                    <input type="checkbox" id="select-all-checkbox">
                    <span class="my-custom-box"></span>
                </label>
            </div>
        </th>

        <th>
            <div class="thead-wrapper location">
                <p>Локация</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper type">
                <p>Тип</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper area">
                <p>Площадь</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper condition">
                <p>Состояние</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper floor">
                <p>Этаж</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper photo">
                <p>Фото</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper price">
                <p>Цена</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper contact">
                <p>Контакт</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper block-actions">
                <div class="menu-burger">
                    <div class="dropdown">
                        <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <picture><source srcset="{{ asset('img/icon/burger.svg') }}" type="image/webp"><img src="{{ asset('img/icon/burger.svg') }}" alt=""></picture>
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
                        <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <picture><source srcset="{{ asset('img/icon/sorting.svg') }}" type="image/webp"><img src="{{ asset('img/icon/sorting.svg') }}" alt=""></picture>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-sort">
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="created_at" data-sort-dir="desc">Самые новые</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="price" data-sort-dir="asc">Самые дешевые</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="price" data-sort-dir="desc">Самые дорогие</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="price_per_m2" data-sort-dir="asc">Самые дешевые/м<sup>2</sup></a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="price_per_m2" data-sort-dir="desc">Самые дорогие/м<sup>2</sup></a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="area_total" data-sort-dir="asc">Наименьшая площадь</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="area_total" data-sort-dir="desc">Наибольшая площадь</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="created_at" data-sort-dir="asc">Самые старые</a></li>
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
