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
                <p>Контакт</p>
            </div>
        </th>
        <th>
            <div class="thead-wrapper offices">
                <p>Офисы</p>
            </div>
        </th>
        <th data-dt-column="5" class="dt-orderable-none dt-type-numeric" rowspan="1" colspan="1"><span
                    class="dt-column-title">
				<div class="thead-wrapper command">
					<p>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-people-fill" viewBox="0 0 16 16">
							<path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"></path>
						</svg>
						<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                              data-bs-title="Команда">
							<picture><source srcset="./img/icon/icon-info.svg" type="image/webp"><img
                                        src="./img/icon/icon-info.svg" alt=""></picture>
						</span>
					</p>
				</div>
			</span><span class="dt-column-order"></span>
        </th>


        <th data-dt-column="6" class="dt-orderable-none dt-type-numeric" rowspan="1" colspan="1"><span class="dt-column-title">
				<div class="thead-wrapper object">
					<p>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
							<path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"></path>
							<path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"></path>
						</svg>
						<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Объекты">
							<picture><source srcset="./img/icon/icon-info.svg" type="image/webp"><img src="./img/icon/icon-info.svg" alt=""></picture>
						</span>
					</p>
				</div>
			</span><span class="dt-column-order"></span></th>


        <th data-dt-column="7" class="dt-orderable-none dt-type-numeric" rowspan="1" colspan="1"><span class="dt-column-title">
				<div class="thead-wrapper client">
					<p>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
							<path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"></path>
						</svg>
						<span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Сделки">
							<picture><source srcset="./img/icon/icon-info.svg" type="image/webp"><img src="./img/icon/icon-info.svg" alt=""></picture>
						</span>
					</p>
				</div>
			</span><span class="dt-column-order"></span></th>



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
