<table id="companies-table" class="table table-hover">
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
            <div class="thead-wrapper photo">
                <p>Фото</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper company">
                <p>Компания</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper director">
                <p>Директор</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper offices">
                <p>Офисы</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper team">
                <p>Команда</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper properties">
                <p>Объекты</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper commission">
                <p>Комиссия</p>
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
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="name" data-sort-dir="asc">По названию (А-Я)</a></li>
                            <li><a class="dropdown-item sort-option" href="#" data-sort-field="name" data-sort-dir="desc">По названию (Я-А)</a></li>
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
